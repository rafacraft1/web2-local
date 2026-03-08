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

        // --- PROSES UPLOAD BASE64 DENGAN HELPER (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambar  = 'galeri-' . $slug . '-' . time() . '.webp';

        $uploadProses = $this->processBase64Image($imageBase64, 'galeri', $namaGambar);

        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }
        // ---------------------------------------------------------

        $data = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'image'       => $namaGambar
        ];

        $this->galeriModel->insert($data);
        $insertId = $this->galeriModel->getInsertID();

        log_activity('CREATE', 'galeri', $insertId, null, ['title' => $data['title']]);

        return redirect()->to('/panel/galeri')->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');

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
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');

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

        // --- PROSES JIKA ADA GAMBAR BARU (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');

        if (!empty($imageBase64)) {
            $namaGambarBaru = 'galeri-' . $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'galeri', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            // Jika berhasil upload baru, hapus gambar lama
            if (file_exists(FCPATH . 'uploads/galeri/' . $galeriLama['image'])) {
                unlink(FCPATH . 'uploads/galeri/' . $galeriLama['image']);
            }

            $namaGambarFinal = $namaGambarBaru; // Gunakan nama gambar yang baru
        }
        // -------------------------------------------------

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
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/galeri')->with('error', 'Akses ditolak.');

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
