<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;

class JurusanController extends BaseController
{
    protected $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Kelola Jurusan',
            'jurusan' => $this->jurusanModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/jurusan/index', $data);
    }

    public function create()
    {
        return view('backend/jurusan/create', [
            'title' => 'Tambah Jurusan'
        ]);
    }

    public function store()
    {
        $imageBase64 = $this->request->getPost('image_base64');
        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar jurusan wajib diisi.');
        }

        $slug = url_title($this->request->getPost('name'), '-', true);
        $namaGambar  = $slug . '-' . time() . '.webp';

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $namaGambar
        ];

        $this->jurusanModel->db->transStart();

        if (!$this->jurusanModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        $insertId = $this->jurusanModel->getInsertID();

        // --- PROSES UPLOAD BASE64 ---
        $uploadProses = $this->processBase64Image($imageBase64, 'jurusan', $namaGambar);
        if (!$uploadProses['success']) {
            $this->jurusanModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        log_activity('CREATE', 'jurusan', $insertId, null, ['name' => $data['name']]);
        $this->jurusanModel->db->transComplete();

        if ($this->jurusanModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/jurusan')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/jurusan')->with('error', 'Akses ditolak.');

        $jurusan = $this->jurusanModel->find($id);
        if (!$jurusan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/jurusan/edit', [
            'title'   => 'Edit Jurusan',
            'jurusan' => $jurusan,
            'safeId'  => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/jurusan')->with('error', 'Akses ditolak.');

        $jurusanLama = $this->jurusanModel->find($id);
        if (!$jurusanLama) return redirect()->to('panel/jurusan')->with('error', 'Data tidak ditemukan.');

        $slug = url_title($this->request->getPost('name'), '-', true);
        $imageBase64 = $this->request->getPost('image_base64');

        $data = [
            'id'          => $id,
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $jurusanLama['image']
        ];

        $this->jurusanModel->db->transStart();

        if (!$this->jurusanModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        if (!empty($imageBase64)) {
            $namaGambarBaru = $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'jurusan', $namaGambarBaru);

            if (!$uploadProses['success']) {
                $this->jurusanModel->db->transRollback();
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            $this->hapusGambarFisik($jurusanLama['image']);
            $this->jurusanModel->update($id, ['image' => $namaGambarBaru]);
        }

        log_activity('UPDATE', 'jurusan', $id, ['name' => $jurusanLama['name']], ['name' => $data['name']]);
        $this->jurusanModel->db->transComplete();

        if ($this->jurusanModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('panel/jurusan')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/jurusan')->with('error', 'Akses ditolak.');

        $jurusan = $this->jurusanModel->find($id);

        if ($jurusan) {
            $this->jurusanModel->db->transStart();

            $this->jurusanModel->delete($id);
            log_activity('DELETE', 'jurusan', $id, ['name' => $jurusan['name']], null);

            $this->jurusanModel->db->transComplete();

            if ($this->jurusanModel->db->transStatus() !== false) {
                $this->hapusGambarFisik($jurusan['image']);
                return redirect()->to('panel/jurusan')->with('success', 'Jurusan berhasil dihapus.');
            }
            return redirect()->to('panel/jurusan')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/jurusan')->with('error', 'Data tidak ditemukan.');
    }

    // --- Fungsi DRY untuk hapus gambar ---
    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/jurusan/' . $namaFile;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
