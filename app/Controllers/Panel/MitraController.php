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

        // --- PROSES GAMBAR BASE64 DENGAN PENGAMANAN KETAT ---
        $logoBase64 = $this->request->getPost('logo_base64');
        if (strlen($logoBase64) > 100000) return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran logo terlalu besar.');

        $imageParts = explode(';base64,', $logoBase64);
        if (count($imageParts) != 2) return redirect()->back()->withInput()->with('error', 'Keamanan: Format logo tidak valid.');

        $imageDecoded = base64_decode($imageParts[1]);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageDecoded);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: Data ditolak! Bukan gambar murni.');
        }

        $namaLogo = 'mitra-' . $slug . '-' . time() . '.webp';
        $uploadPath = FCPATH . 'uploads/mitra/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        file_put_contents($uploadPath . $namaLogo, $imageDecoded);
        // ----------------------------------------------------

        $data = [
            'nama' => $this->request->getPost('nama'),
            'logo' => $namaLogo
        ];

        $this->mitraModel->insert($data);
        $insertId = $this->mitraModel->getInsertID();

        log_activity('CREATE', 'mitra', $insertId, null, ['nama' => $data['nama']]);

        return redirect()->to('/panel/mitra')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/mitra');

        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');
        }

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
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');
        }

        $mitraLama = $this->mitraModel->find($id);

        $rules = [
            'nama' => 'required|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('nama'), '-', true);
        $namaLogoFinal = $mitraLama['logo'];

        // --- PROSES JIKA ADA LOGO BARU ---
        $logoBase64 = $this->request->getPost('logo_base64');
        if (!empty($logoBase64)) {
            if (strlen($logoBase64) > 100000) return redirect()->back()->withInput()->with('error', 'Keamanan: Ukuran logo terlalu besar.');

            $imageParts = explode(';base64,', $logoBase64);
            if (count($imageParts) != 2) return redirect()->back()->withInput()->with('error', 'Keamanan: Format logo tidak valid.');

            $imageDecoded = base64_decode($imageParts[1]);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageDecoded);
            finfo_close($finfo);

            if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'Keamanan: Data ditolak! Bukan gambar murni.');
            }

            $uploadPath = FCPATH . 'uploads/mitra/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $namaLogoFinal = 'mitra-' . $slug . '-' . time() . '.webp';
            file_put_contents($uploadPath . $namaLogoFinal, $imageDecoded);

            if (!empty($mitraLama['logo']) && is_file($uploadPath . $mitraLama['logo'])) {
                unlink($uploadPath . $mitraLama['logo']);
            }
        }
        // ------------------------------------

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
        $encrypter = \Config\Services::encrypter();
        try {
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/mitra')->with('error', 'Akses ditolak.');
        }

        $mitra = $this->mitraModel->find($id);

        if ($mitra) {
            if (!empty($mitra['logo']) && is_file(FCPATH . 'uploads/mitra/' . $mitra['logo'])) {
                unlink(FCPATH . 'uploads/mitra/' . $mitra['logo']);
            }
            $this->mitraModel->delete($id);
            log_activity('DELETE', 'mitra', $id, ['nama' => $mitra['nama']], null);
            return redirect()->to('/panel/mitra')->with('success', 'Mitra berhasil dihapus.');
        }

        return redirect()->to('/panel/mitra')->with('error', 'Data tidak ditemukan.');
    }
}
