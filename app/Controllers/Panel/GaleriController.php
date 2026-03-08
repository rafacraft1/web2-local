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
        $imageBase64 = $this->request->getPost('image_base64');
        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar galeri wajib diisi.');
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambar  = 'galeri-' . $slug . '-' . time() . '.webp';

        $data = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'image'       => $namaGambar
        ];

        $this->galeriModel->db->transStart();

        if (!$this->galeriModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->galeriModel->errors());
        }

        $insertId = $this->galeriModel->getInsertID();

        $uploadProses = $this->processBase64Image($imageBase64, 'galeri', $namaGambar);
        if (!$uploadProses['success']) {
            $this->galeriModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        log_activity('CREATE', 'galeri', $insertId, null, ['title' => $data['title']]);
        $this->galeriModel->db->transComplete();

        if ($this->galeriModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/galeri')->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/galeri')->with('error', 'Akses ditolak.');

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
        if (!$id) return redirect()->to('panel/galeri')->with('error', 'Akses ditolak.');

        $galeriLama = $this->galeriModel->find($id);
        if (!$galeriLama) return redirect()->to('panel/galeri')->with('error', 'Data tidak ditemukan.');

        $slug = url_title($this->request->getPost('title'), '-', true);
        $imageBase64 = $this->request->getPost('image_base64');

        $data = [
            'id'          => $id,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'image'       => $galeriLama['image']
        ];

        $this->galeriModel->db->transStart();

        if (!$this->galeriModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->galeriModel->errors());
        }

        if (!empty($imageBase64)) {
            $namaGambarBaru = 'galeri-' . $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'galeri', $namaGambarBaru);

            if (!$uploadProses['success']) {
                $this->galeriModel->db->transRollback();
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            $this->hapusGambarFisik($galeriLama['image']);
            $this->galeriModel->update($id, ['image' => $namaGambarBaru]);
        }

        log_activity('UPDATE', 'galeri', $id, ['title' => $galeriLama['title']], ['title' => $data['title']]);
        $this->galeriModel->db->transComplete();

        if ($this->galeriModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('panel/galeri')->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/galeri')->with('error', 'Akses ditolak.');

        $galeri = $this->galeriModel->find($id);

        if ($galeri) {
            $this->galeriModel->db->transStart();

            $this->galeriModel->delete($id);
            log_activity('DELETE', 'galeri', $id, ['title' => $galeri['title']], null);

            $this->galeriModel->db->transComplete();

            if ($this->galeriModel->db->transStatus() !== false) {
                $this->hapusGambarFisik($galeri['image']);
                return redirect()->to('panel/galeri')->with('success', 'Foto galeri berhasil dihapus.');
            }
            return redirect()->to('panel/galeri')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/galeri')->with('error', 'Data tidak ditemukan.');
    }

    // --- Fungsi DRY untuk hapus gambar ---
    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/galeri/' . $namaFile;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
