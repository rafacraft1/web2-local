<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\MitraModel;

class MitraController extends BaseController
{
    protected $mitraModel;

    public function __construct()
    {
        $this->mitraModel = new MitraModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Mitra Industri',
            'mitra' => $this->mitraModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/mitra/index', $data);
    }

    public function create()
    {
        return view('backend/mitra/create', [
            'title' => 'Tambah Mitra Baru'
        ]);
    }

    public function store()
    {
        $logoBase64 = $this->request->getPost('logo_base64');
        if (empty($logoBase64)) {
            return redirect()->back()->withInput()->with('error', 'Logo mitra wajib diisi.');
        }

        $slug = url_title($this->request->getPost('nama'), '-', true);
        $namaLogo   = 'mitra-' . $slug . '-' . time() . '.webp';

        $data = [
            'nama' => $this->request->getPost('nama'),
            'logo' => $namaLogo
        ];

        $this->mitraModel->db->transStart();

        if (!$this->mitraModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->mitraModel->errors());
        }

        $insertId = $this->mitraModel->getInsertID();

        $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogo);
        if (!$uploadProses['success']) {
            $this->mitraModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        log_activity('CREATE', 'mitra', $insertId, null, ['nama' => $data['nama']]);
        $this->mitraModel->db->transComplete();

        if ($this->mitraModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/mitra')->with('success', 'Data mitra berhasil ditambahkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/mitra')->with('error', 'Akses ditolak.');

        $mitra = $this->mitraModel->find($id);
        if (!$mitra) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/mitra/edit', [
            'title'  => 'Edit Mitra',
            'mitra'  => $mitra,
            'safeId' => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/mitra')->with('error', 'Akses ditolak.');

        $mitraLama = $this->mitraModel->find($id);
        if (!$mitraLama) return redirect()->to('panel/mitra')->with('error', 'Data tidak ditemukan.');

        $slug = url_title($this->request->getPost('nama'), '-', true);
        $logoBase64 = $this->request->getPost('logo_base64');

        $data = [
            'id'   => $id,
            'nama' => $this->request->getPost('nama'),
            'logo' => $mitraLama['logo']
        ];

        $this->mitraModel->db->transStart();

        if (!$this->mitraModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->mitraModel->errors());
        }

        if (!empty($logoBase64)) {
            $namaLogoBaru = 'mitra-' . $slug . '-' . time() . '.webp';
            $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogoBaru);

            if (!$uploadProses['success']) {
                $this->mitraModel->db->transRollback();
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            $this->hapusGambarFisik($mitraLama['logo']);
            $this->mitraModel->update($id, ['logo' => $namaLogoBaru]);
        }

        log_activity('UPDATE', 'mitra', $id, ['nama' => $mitraLama['nama']], ['nama' => $data['nama']]);
        $this->mitraModel->db->transComplete();

        if ($this->mitraModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('panel/mitra')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/mitra')->with('error', 'Akses ditolak.');

        $mitra = $this->mitraModel->find($id);

        if ($mitra) {
            $this->mitraModel->db->transStart();

            $this->mitraModel->delete($id);
            log_activity('DELETE', 'mitra', $id, ['nama' => $mitra['nama']], null);

            $this->mitraModel->db->transComplete();

            if ($this->mitraModel->db->transStatus() !== false) {
                $this->hapusGambarFisik($mitra['logo']);
                return redirect()->to('panel/mitra')->with('success', 'Data mitra berhasil dihapus.');
            }
            return redirect()->to('panel/mitra')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/mitra')->with('error', 'Data tidak ditemukan.');
    }

    // --- Fungsi DRY untuk hapus gambar ---
    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/mitra/' . $namaFile;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
