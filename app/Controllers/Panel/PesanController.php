<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\PesanModel;

class PesanController extends BaseController
{
    protected $pesanModel;

    public function __construct()
    {
        $this->pesanModel = new PesanModel();
    }

    // 1. Menampilkan Daftar Kotak Masuk
    public function index()
    {
        $data = [
            'title' => 'Kotak Masuk',
            // Urutkan pesan dari yang paling baru
            'pesan' => $this->pesanModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('backend/pesan/index', $data);
    }

    // 2. Membaca Detail Pesan
    public function show($safeId = null)
    {
        // Gunakan helper dekripsi dari BaseController
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/pesan')->with('error', 'Akses ditolak: URL tidak valid.');

        $pesan = $this->pesanModel->find($id);
        if (!$pesan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Jika pesan belum dibaca, ubah statusnya jadi sudah dibaca (1)
        if ($pesan['is_read'] == 0) {
            $this->pesanModel->update($id, ['is_read' => 1]);

            // 🎥 CATAT LOG: READ
            log_activity('READ', 'pesan', $id, null, ['subject' => $pesan['subject'], 'pengirim' => $pesan['name']]);
        }

        return view('backend/pesan/show', [
            'title'  => 'Baca Pesan',
            'pesan'  => $pesan,
            'safeId' => $safeId
        ]);
    }

    // 3. Menghapus Pesan
    public function delete($safeId = null)
    {
        // Gunakan helper dekripsi dari BaseController
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/pesan')->with('error', 'Akses ditolak: URL tidak valid.');

        $pesan = $this->pesanModel->find($id);
        if (!$pesan) {
            return redirect()->to('/panel/pesan')->with('error', 'Pesan tidak ditemukan.');
        }

        $this->pesanModel->delete($id);

        // 🎥 CATAT LOG: DELETE
        log_activity('DELETE', 'pesan', $id, ['subject' => $pesan['subject'], 'pengirim' => $pesan['name']], null);

        return redirect()->to('/panel/pesan')->with('success', 'Pesan berhasil dihapus.');
    }
}
