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
        $inputData   = $this->request->getPost(['title', 'description']);
        $imageBase64 = $this->request->getPost('image_base64');

        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar galeri wajib diisi.');
        }

        $slug = url_title($inputData['title'], '-', true);
        $namaGambar = 'galeri-' . $slug . '-' . time() . '.webp';
        $inputData['image'] = $namaGambar;

        // Validasi Model sebelum upload gambar
        if (!$this->galeriModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->galeriModel->errors());
        }

        // Proses upload file di luar transaksi database
        $uploadProses = $this->processBase64Image($imageBase64, 'galeri', $namaGambar);
        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        $this->galeriModel->db->transStart();
        // Insert dengan skipValidation agar tidak divalidasi 2 kali
        $this->galeriModel->skipValidation(true)->insert($inputData);
        $insertId = $this->galeriModel->getInsertID();
        $this->galeriModel->db->transComplete();

        // Pengecekan status transaksi & Logging
        if ($this->galeriModel->db->transStatus() === false) {
            $this->hapusGambarFisik($namaGambar); // Rollback gambar jika DB gagal
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        log_activity('CREATE', 'galeri', $insertId, null, ['title' => $inputData['title']]);
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

        $inputData = $this->request->getPost(['title', 'description']);
        $inputData['id'] = $id;
        $slug = url_title($inputData['title'], '-', true);

        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambarFinal = $galeriLama['image'];

        if (!$this->galeriModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->galeriModel->errors());
        }

        $gambarBaruBerhasilUpload = false;

        // Proses file jika ada gambar baru (hindari double query)
        if (!empty($imageBase64)) {
            $namaGambarBaru = 'galeri-' . $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'galeri', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            $namaGambarFinal = $namaGambarBaru;
            $gambarBaruBerhasilUpload = true;
        }

        $inputData['image'] = $namaGambarFinal;

        // Transaksi Database (Cukup 1x Update)
        $this->galeriModel->db->transStart();
        $this->galeriModel->skipValidation(true)->update($id, $inputData);
        $this->galeriModel->db->transComplete();

        // Pengecekan status & penghapusan file lama
        if ($this->galeriModel->db->transStatus() === false) {
            if ($gambarBaruBerhasilUpload) $this->hapusGambarFisik($namaGambarFinal);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        if ($gambarBaruBerhasilUpload) {
            $this->hapusGambarFisik($galeriLama['image']);
        }

        log_activity('UPDATE', 'galeri', $id, ['title' => $galeriLama['title']], ['title' => $inputData['title']]);
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
            $this->galeriModel->db->transComplete();

            if ($this->galeriModel->db->transStatus() !== false) {
                log_activity('DELETE', 'galeri', $id, ['title' => $galeri['title']], null);
                $this->hapusGambarFisik($galeri['image']);
                return redirect()->to('panel/galeri')->with('success', 'Foto galeri berhasil dihapus.');
            }
            return redirect()->to('panel/galeri')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/galeri')->with('error', 'Data tidak ditemukan.');
    }

    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/galeri/' . $namaFile;
            if (is_file($path)) unlink($path);
        }
    }
}
