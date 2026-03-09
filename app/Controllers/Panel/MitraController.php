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
        $inputData  = $this->request->getPost(['nama']);
        $logoBase64 = $this->request->getPost('logo_base64');
        
        if (empty($logoBase64)) {
            return redirect()->back()->withInput()->with('error', 'Logo mitra wajib diisi.');
        }

        $slug = url_title($inputData['nama'], '-', true);
        $namaLogo = 'mitra-' . $slug . '-' . time() . '.webp';
        $inputData['logo'] = $namaLogo;

        if (!$this->mitraModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->mitraModel->errors());
        }

        $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogo);
        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        $this->mitraModel->db->transStart();
        $this->mitraModel->skipValidation(true)->insert($inputData);
        $insertId = $this->mitraModel->getInsertID();
        $this->mitraModel->db->transComplete();

        if ($this->mitraModel->db->transStatus() === false) {
            $this->hapusGambarFisik($namaLogo);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        log_activity('CREATE', 'mitra', $insertId, null, ['nama' => $inputData['nama']]);
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

        $inputData = $this->request->getPost(['nama']);
        $inputData['id'] = $id;
        $slug = url_title($inputData['nama'], '-', true);
        
        $logoBase64 = $this->request->getPost('logo_base64');
        $namaLogoFinal = $mitraLama['logo'];

        if (!$this->mitraModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->mitraModel->errors());
        }

        $logoBaruBerhasilUpload = false;

        if (!empty($logoBase64)) {
            $namaLogoBaru = 'mitra-' . $slug . '-' . time() . '.webp';
            $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogoBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }
            
            $namaLogoFinal = $namaLogoBaru;
            $logoBaruBerhasilUpload = true;
        }

        $inputData['logo'] = $namaLogoFinal;

        $this->mitraModel->db->transStart();
        $this->mitraModel->skipValidation(true)->update($id, $inputData);
        $this->mitraModel->db->transComplete();

        if ($this->mitraModel->db->transStatus() === false) {
            if ($logoBaruBerhasilUpload) $this->hapusGambarFisik($namaLogoFinal);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        if ($logoBaruBerhasilUpload) {
            $this->hapusGambarFisik($mitraLama['logo']);
        }

        log_activity('UPDATE', 'mitra', $id, ['nama' => $mitraLama['nama']], ['nama' => $inputData['nama']]);
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
            $this->mitraModel->db->transComplete();

            if ($this->mitraModel->db->transStatus() !== false) {
                log_activity('DELETE', 'mitra', $id, ['nama' => $mitra['nama']], null);
                $this->hapusGambarFisik($mitra['logo']);
                return redirect()->to('panel/mitra')->with('success', 'Data mitra berhasil dihapus.');
            }
            return redirect()->to('panel/mitra')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/mitra')->with('error', 'Data tidak ditemukan.');
    }

    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/mitra/' . $namaFile;
            if (is_file($path)) unlink($path);
        }
    }
}