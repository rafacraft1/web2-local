<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\GaleriModel;

class GaleriController extends BaseController
{
    protected $galeriModel;

    public function __construct()
    {
        $this->galeriModel = new GaleriModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'Kelola Galeri',
            'galeri' => $this->galeriModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/galeri/index', $data);
    }

    public function create()
    {
        return view('backend/galeri/create', [
            'title' => 'Tambah Foto Galeri'
        ]);
    }

    public function store()
    {
        $rules = [
            'title'        => 'required|min_length[3]',
            'description'  => 'required|max_length[255]',
            'image_base64' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('title'), '-', true);

        // --- PROSES GAMBAR BASE64 DENGAN PENGAMANAN KETAT ---
        $imageBase64 = $this->request->getPost('image_base64');
        if (strlen($imageBase64) > 100000) return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran gambar terlalu besar.');

        $imageParts = explode(';base64,', $imageBase64);
        if (count($imageParts) != 2) return redirect()->back()->withInput()->with('error', 'Keamanan: Format gambar tidak valid.');

        $imageDecoded = base64_decode($imageParts[1]);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageDecoded);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: Data ditolak! Bukan gambar murni.');
        }

        $namaGambar = 'galeri-' . $slug . '-' . time() . '.webp';
        $uploadPath = FCPATH . 'uploads/galeri/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        file_put_contents($uploadPath . $namaGambar, $imageDecoded);
        // ----------------------------------------------------

        $data = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'image'       => $namaGambar
        ];

        $this->galeriModel->insert($data);
        $insertId = $this->galeriModel->getInsertID();

        // 🎥 CATAT LOG
        log_activity('CREATE', 'galeri', $insertId, null, ['title' => $data['title']]);

        return redirect()->to('/panel/galeri')->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function edit($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/galeri');

        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');
        }

        $galeri = $this->galeriModel->find($id);
        if (!$galeri) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/galeri/edit', [
            'title'  => 'Edit Galeri',
            'galeri' => $galeri,
            'safeId' => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');
        }

        $galeriLama = $this->galeriModel->find($id);

        $rules = [
            'title'       => 'required|min_length[3]',
            'description' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambarFinal = $galeriLama['image'];

        // --- PROSES JIKA ADA GAMBAR BARU ---
        $imageBase64 = $this->request->getPost('image_base64');
        if (!empty($imageBase64)) {
            if (strlen($imageBase64) > 100000) return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran gambar terlalu besar.');

            $imageParts = explode(';base64,', $imageBase64);
            if (count($imageParts) != 2) return redirect()->back()->withInput()->with('error', 'Keamanan: Format gambar tidak valid.');

            $imageDecoded = base64_decode($imageParts[1]);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageDecoded);
            finfo_close($finfo);

            if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'Keamanan: Data ditolak! Bukan gambar murni.');
            }

            $uploadPath = FCPATH . 'uploads/galeri/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $namaGambarFinal = 'galeri-' . $slug . '-' . time() . '.webp';
            file_put_contents($uploadPath . $namaGambarFinal, $imageDecoded);

            if (file_exists($uploadPath . $galeriLama['image'])) {
                unlink($uploadPath . $galeriLama['image']);
            }
        }
        // ------------------------------------

        $data = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'image'       => $namaGambarFinal
        ];

        $this->galeriModel->update($id, $data);
        log_activity('UPDATE', 'galeri', $id, ['title' => $galeriLama['title']], ['title' => $data['title']]);

        return redirect()->to('/panel/galeri')->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');
        }

        $galeri = $this->galeriModel->find($id);

        if ($galeri) {
            if (file_exists(FCPATH . 'uploads/galeri/' . $galeri['image'])) {
                unlink(FCPATH . 'uploads/galeri/' . $galeri['image']);
            }
            $this->galeriModel->delete($id);
            log_activity('DELETE', 'galeri', $id, ['title' => $galeri['title']], null);
            return redirect()->to('/panel/galeri')->with('success', 'Foto galeri berhasil dihapus.');
        }

        return redirect()->to('/panel/galeri')->with('error', 'Data tidak ditemukan.');
    }
}
