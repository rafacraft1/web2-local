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

    public function index()
    {
        $data = [
            'title' => 'Pesan Masuk',
            'pesan' => $this->pesanModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/pesan/index', $data);
    }

    public function show($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/pesan')->with('error', 'Akses ditolak.');

        $pesan = $this->pesanModel->find($id);
        if (!$pesan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        if ($pesan['is_read'] == 0) {
            $this->pesanModel->update($id, ['is_read' => 1]);
        }

        return view('backend/pesan/show', [
            'title' => 'Detail Pesan',
            'pesan' => $pesan
        ]);
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/pesan')->with('error', 'Akses ditolak.');

        $pesan = $this->pesanModel->find($id);
        if ($pesan) {
            $this->pesanModel->db->transStart();
            $this->pesanModel->delete($id);
            $this->pesanModel->db->transComplete();

            if ($this->pesanModel->db->transStatus() !== false) {
                // Task 6: Log dipindah ke sini setelah dipastikan sukses
                log_activity('DELETE', 'pesan', $id, ['subjek' => $pesan['subjek']], null);
                return redirect()->to('panel/pesan')->with('success', 'Pesan berhasil dihapus.');
            }
            return redirect()->to('panel/pesan')->with('error', 'Gagal menghapus pesan.');
        }

        return redirect()->to('panel/pesan')->with('error', 'Data tidak ditemukan.');
    }
}
