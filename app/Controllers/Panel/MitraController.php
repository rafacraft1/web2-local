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
        $rules = [
            'nama'        => 'required|min_length[2]',
            'logo_base64' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('nama'), '-', true);

        // --- PROSES UPLOAD BASE64 DENGAN HELPER (SUPER CLEAN) ---
        $logoBase64 = $this->request->getPost('logo_base64');
        $namaLogo   = 'mitra-' . $slug . '-' . time() . '.webp';

        $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogo);

        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }
        // ---------------------------------------------------------

        $data = [
            'nama' => $this->request->getPost('nama'),
            'logo' => $namaLogo
        ];

        $this->mitraModel->insert($data);
        $insertId = $this->mitraModel->getInsertID();

        log_activity('CREATE', 'mitra', $insertId, null, ['nama' => $data['nama']]);

        return redirect()->to('/panel/mitra')->with('success', 'Data mitra berhasil ditambahkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');

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
        if (!$id) return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');

        $mitraLama = $this->mitraModel->find($id);

        $rules = [
            'nama' => 'required|min_length[2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('nama'), '-', true);
        $namaLogoFinal = $mitraLama['logo'];

        // --- PROSES JIKA ADA GAMBAR BARU (SUPER CLEAN) ---
        $logoBase64 = $this->request->getPost('logo_base64');
        if (!empty($logoBase64)) {
            $namaLogoBaru = 'mitra-' . $slug . '-' . time() . '.webp';
            $uploadProses = $this->processBase64Image($logoBase64, 'mitra', $namaLogoBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            if (!empty($mitraLama['logo']) && is_file(FCPATH . 'uploads/mitra/' . $mitraLama['logo'])) {
                unlink(FCPATH . 'uploads/mitra/' . $mitraLama['logo']);
            }

            $namaLogoFinal = $namaLogoBaru;
        }
        // -------------------------------------------------

        $data = [
            'nama' => $this->request->getPost('nama'),
            'logo' => $namaLogoFinal
        ];

        $this->mitraModel->update($id, $data);
        log_activity('UPDATE', 'mitra', $id, ['nama' => $mitraLama['nama']], ['nama' => $data['nama']]);

        return redirect()->to('/panel/mitra')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');

        $mitra = $this->mitraModel->find($id);

        if ($mitra) {
            if (!empty($mitra['logo']) && is_file(FCPATH . 'uploads/mitra/' . $mitra['logo'])) {
                unlink(FCPATH . 'uploads/mitra/' . $mitra['logo']);
            }
            $this->mitraModel->delete($id);
            log_activity('DELETE', 'mitra', $id, ['nama' => $mitra['nama']], null);
            return redirect()->to('/panel/mitra')->with('success', 'Data mitra berhasil dihapus.');
        }

        return redirect()->to('/panel/mitra')->with('error', 'Data tidak ditemukan.');
    }
}
