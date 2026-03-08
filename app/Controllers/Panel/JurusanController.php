<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;

class JurusanController extends BaseController
{
    protected $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    // 1. Tampilkan Daftar Jurusan
    public function index()
    {
        $data = [
            'title'   => 'Kelola Jurusan',
            'jurusan' => $this->jurusanModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/jurusan/index', $data);
    }

    // 2. Form Tambah Jurusan
    public function create()
    {
        return view('backend/jurusan/create', [
            'title' => 'Tambah Jurusan'
        ]);
    }

    // 3. Proses Simpan Jurusan
    public function store()
    {
        $rules = [
            'name'         => 'required|min_length[3]|is_unique[jurusan.name]',
            'short_desc'   => 'required|max_length[255]',
            'description'  => 'required',
            'icon'         => 'required',
            'image_base64' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('name'), '-', true);

        // --- PROSES GAMBAR BASE64 DENGAN PENGAMANAN KETAT ---
        $imageBase64 = $this->request->getPost('image_base64');

        // Proteksi 1: Batasi ukuran payload Base64 untuk mencegah DoS (Maksimal ~100KB string)
        if (strlen($imageBase64) > 100000) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran gambar terlalu besar atau payload tidak wajar.');
        }

        // Proteksi 2: Pastikan format string valid mengandung ';base64,'
        $imageParts = explode(';base64,', $imageBase64);
        if (count($imageParts) != 2) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: Format data gambar tidak valid.');
        }

        // Decode string menjadi file biner murni
        $imageDecoded = base64_decode($imageParts[1]);

        // Proteksi 3: Pindai MIME Type asli dari data biner (Mencegah PHP/Malware disamarkan jadi gambar)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageDecoded);
        finfo_close($finfo);

        // Daftar MIME type yang benar-benar kita izinkan masuk
        $allowedMimeTypes = ['image/webp', 'image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: File ditolak! Data yang dikirim bukan murni gambar (Terdeteksi: ' . $mimeType . ').');
        }

        // Jika lolos semua proteksi, baru simpan ke server
        $namaGambar = $slug . '-' . time() . '.webp';
        $uploadPath = FCPATH . 'uploads/jurusan/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        file_put_contents($uploadPath . $namaGambar, $imageDecoded);
        // ----------------------------------------------------

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $namaGambar
        ];

        $this->jurusanModel->insert($data);
        $insertId = $this->jurusanModel->getInsertID();

        // 🎥 CATAT LOG
        log_activity('CREATE', 'jurusan', $insertId, null, ['name' => $data['name']]);

        return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil ditambahkan dengan aman.');
    }

    // 4. Form Edit Jurusan
    public function edit($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/jurusan');

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Invalid');
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');
        }

        $jurusan = $this->jurusanModel->find($id);
        if (!$jurusan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/jurusan/edit', [
            'title'   => 'Edit Jurusan',
            'jurusan' => $jurusan,
            'safeId'  => $safeId
        ]);
    }

    // 5. Proses Update Jurusan
    public function update($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/jurusan');

        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');
        }

        $jurusanLama = $this->jurusanModel->find($id);

        $rules = [
            'name'        => "required|min_length[3]|is_unique[jurusan.name,id,{$id}]",
            'short_desc'  => 'required|max_length[255]',
            'description' => 'required',
            'icon'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('name'), '-', true);
        $namaGambarFinal = $jurusanLama['image'];

        // --- PROSES JIKA ADA GAMBAR BARU (BASE64) DENGAN PENGAMANAN KETAT ---
        $imageBase64 = $this->request->getPost('image_base64');
        if (!empty($imageBase64)) {

            // Proteksi 1: Anti-DoS ukuran payload
            if (strlen($imageBase64) > 100000) {
                return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran gambar terlalu besar atau payload tidak wajar.');
            }

            // Proteksi 2: Cek Format Pemisah Base64
            $imageParts = explode(';base64,', $imageBase64);
            if (count($imageParts) != 2) {
                return redirect()->back()->withInput()->with('error', 'Keamanan: Format data gambar tidak valid.');
            }

            $imageDecoded = base64_decode($imageParts[1]);

            // Proteksi 3: Pindai MIME Type murni (Binary Scanning)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageDecoded);
            finfo_close($finfo);

            if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'Keamanan: File ditolak! Data yang dikirim bukan murni gambar (Terdeteksi: ' . $mimeType . ').');
            }

            // Jika aman, lanjutkan proses penyimpanan
            $uploadPath = FCPATH . 'uploads/jurusan/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $namaGambarFinal = $slug . '-' . time() . '.webp';

            // Simpan gambar baru
            file_put_contents($uploadPath . $namaGambarFinal, $imageDecoded);

            // Hapus file fisik gambar lama
            if (file_exists($uploadPath . $jurusanLama['image'])) {
                unlink($uploadPath . $jurusanLama['image']);
            }
        }
        // ------------------------------------------------------------------

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $namaGambarFinal
        ];

        $this->jurusanModel->update($id, $data);

        // 🎥 CATAT LOG
        log_activity('UPDATE', 'jurusan', $id, ['name' => $jurusanLama['name']], ['name' => $data['name']]);

        return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil diperbarui.');
    }

    // 6. Hapus Jurusan
    public function delete($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/jurusan');

        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');
        }

        $jurusan = $this->jurusanModel->find($id);

        if ($jurusan) {
            if (file_exists(FCPATH . 'uploads/jurusan/' . $jurusan['image'])) {
                unlink(FCPATH . 'uploads/jurusan/' . $jurusan['image']);
            }

            $this->jurusanModel->delete($id);
            log_activity('DELETE', 'jurusan', $id, ['name' => $jurusan['name']], null);

            return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil dihapus.');
        }

        return redirect()->to('/panel/jurusan')->with('error', 'Data tidak ditemukan.');
    }
}
