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
        $inputData   = $this->request->getPost(['name', 'short_desc', 'description', 'icon']);
        $imageBase64 = $this->request->getPost('image_base64');

        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar jurusan wajib diisi.');
        }

        $inputData['slug'] = url_title($inputData['name'], '-', true);
        $namaGambar = $inputData['slug'] . '-' . time() . '.webp';
        $inputData['image'] = $namaGambar;

        if (!$this->jurusanModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        $uploadProses = $this->processBase64Image($imageBase64, 'jurusan', $namaGambar);
        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        $this->jurusanModel->db->transStart();
        $this->jurusanModel->skipValidation(true)->insert($inputData);
        $insertId = $this->jurusanModel->getInsertID();
        $this->jurusanModel->db->transComplete();

        if ($this->jurusanModel->db->transStatus() === false) {
            $this->hapusGambarFisik($namaGambar);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }

        log_activity('CREATE', 'jurusan', $insertId, null, ['name' => $inputData['name']]);
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

        $inputData = $this->request->getPost(['name', 'short_desc', 'description', 'icon']);
        $inputData['id']   = $id;
        $inputData['slug'] = url_title($inputData['name'], '-', true);

        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambarFinal = $jurusanLama['image'];

        if (!$this->jurusanModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        $gambarBaruBerhasilUpload = false;

        if (!empty($imageBase64)) {
            $namaGambarBaru = $inputData['slug'] . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'jurusan', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            $namaGambarFinal = $namaGambarBaru;
            $gambarBaruBerhasilUpload = true;
        }

        $inputData['image'] = $namaGambarFinal;

        $this->jurusanModel->db->transStart();
        $this->jurusanModel->skipValidation(true)->update($id, $inputData);
        $this->jurusanModel->db->transComplete();

        if ($this->jurusanModel->db->transStatus() === false) {
            if ($gambarBaruBerhasilUpload) $this->hapusGambarFisik($namaGambarFinal);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        if ($gambarBaruBerhasilUpload) {
            $this->hapusGambarFisik($jurusanLama['image']);
        }

        log_activity('UPDATE', 'jurusan', $id, ['name' => $jurusanLama['name']], ['name' => $inputData['name']]);
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
            $this->jurusanModel->db->transComplete();

            if ($this->jurusanModel->db->transStatus() !== false) {
                log_activity('DELETE', 'jurusan', $id, ['name' => $jurusan['name']], null);
                $this->hapusGambarFisik($jurusan['image']);
                return redirect()->to('panel/jurusan')->with('success', 'Jurusan berhasil dihapus.');
            }
            return redirect()->to('panel/jurusan')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/jurusan')->with('error', 'Data tidak ditemukan.');
    }

    private function hapusGambarFisik($namaFile)
    {
        if (!empty($namaFile)) {
            $path = FCPATH . 'uploads/jurusan/' . $namaFile;
            if (is_file($path)) unlink($path);
        }
    }
}
