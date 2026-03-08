<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\BeritaModel;

class BeritaController extends BaseController
{
    protected $beritaModel;

    public function __construct()
    {
        $this->beritaModel = new BeritaModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'Kelola Berita & Artikel',
            'berita' => $this->beritaModel->getBeritaWithAuthor()
        ];
        return view('backend/berita/index', $data);
    }

    public function create()
    {
        return view('backend/berita/create', [
            'title' => 'Tulis Berita Baru'
        ]);
    }

    public function store()
    {
        $rules = [
            'title'        => 'required|min_length[5]|is_unique[berita.title]',
            'category'     => 'required',
            'status'       => 'required|in_list[draft,published]',
            'excerpt'      => 'required|max_length[255]',
            'content'      => 'required|min_length[20]',
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

        $namaGambar = $slug . '-' . time() . '.webp';
        $uploadPath = FCPATH . 'uploads/berita/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        file_put_contents($uploadPath . $namaGambar, $imageDecoded);
        // ----------------------------------------------------

        $data = [
            'user_id'  => session()->get('user_id'), // Ambil ID pembuat dari Session
            'title'    => $this->request->getPost('title'),
            'slug'     => $slug,
            'excerpt'  => $this->request->getPost('excerpt'),
            'content'  => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'status'   => $this->request->getPost('status'),
            'image'    => $namaGambar
        ];

        $this->beritaModel->insert($data);
        $insertId = $this->beritaModel->getInsertID();

        // 🎥 CATAT LOG
        log_activity('CREATE', 'berita', $insertId, null, ['title' => $data['title']]);

        return redirect()->to('/panel/berita')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/berita');

        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');
        }

        $berita = $this->beritaModel->find($id);
        if (!$berita) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/berita/edit', [
            'title'  => 'Edit Berita',
            'berita' => $berita,
            'safeId' => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');
        }

        $beritaLama = $this->beritaModel->find($id);

        $rules = [
            'title'    => "required|min_length[5]|is_unique[berita.title,id,{$id}]",
            'category' => 'required',
            'status'   => 'required|in_list[draft,published]',
            'excerpt'  => 'required|max_length[255]',
            'content'  => 'required|min_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambarFinal = $beritaLama['image'];

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

            $uploadPath = FCPATH . 'uploads/berita/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $namaGambarFinal = $slug . '-' . time() . '.webp';
            file_put_contents($uploadPath . $namaGambarFinal, $imageDecoded);

            if (file_exists($uploadPath . $beritaLama['image'])) {
                unlink($uploadPath . $beritaLama['image']);
            }
        }
        // ------------------------------------

        $data = [
            'title'    => $this->request->getPost('title'),
            'slug'     => $slug,
            'excerpt'  => $this->request->getPost('excerpt'),
            'content'  => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'status'   => $this->request->getPost('status'),
            'image'    => $namaGambarFinal
        ];

        $this->beritaModel->update($id, $data);
        log_activity('UPDATE', 'berita', $id, ['title' => $beritaLama['title']], ['title' => $data['title']]);

        return redirect()->to('/panel/berita')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');
        }

        $berita = $this->beritaModel->find($id);

        if ($berita) {
            if (file_exists(FCPATH . 'uploads/berita/' . $berita['image'])) {
                unlink(FCPATH . 'uploads/berita/' . $berita['image']);
            }
            $this->beritaModel->delete($id);
            log_activity('DELETE', 'berita', $id, ['title' => $berita['title']], null);
            return redirect()->to('/panel/berita')->with('success', 'Berita berhasil dihapus.');
        }

        return redirect()->to('/panel/berita')->with('error', 'Data tidak ditemukan.');
    }
}
