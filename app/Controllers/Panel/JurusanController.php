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
        $rules = [
            'name'         => 'required|min_length[3]|is_unique[jurusan.name]',
            'short_desc'   => 'required|max_length[255]',
            'description'  => 'required',
            'icon'         => 'required',
            'image_base64' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('name'), '-', true);

        // --- PROSES UPLOAD BASE64 DENGAN HELPER (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambar  = $slug . '-' . time() . '.webp';

        $uploadProses = $this->processBase64Image($imageBase64, 'jurusan', $namaGambar);

        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }
        // ---------------------------------------------------------

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $namaGambar
        ];

        $this->jurusanModel->insert($data);
        $insertId = $this->jurusanModel->getInsertID();

        log_activity('CREATE', 'jurusan', $insertId, null, ['name' => $data['name']]);

        return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil ditambahkan dengan aman.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');

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
        if (!$id) return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');

        $jurusanLama = $this->jurusanModel->find($id);

        $rules = [
            'name'        => "required|min_length[3]|is_unique[jurusan.name,id,{$id}]",
            'short_desc'  => 'required|max_length[255]',
            'description' => 'required',
            'icon'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('name'), '-', true);
        $namaGambarFinal = $jurusanLama['image'];

        // --- PROSES JIKA ADA GAMBAR BARU (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');
        if (!empty($imageBase64)) {
            $namaGambarBaru = $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'jurusan', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            if (file_exists(FCPATH . 'uploads/jurusan/' . $jurusanLama['image'])) {
                unlink(FCPATH . 'uploads/jurusan/' . $jurusanLama['image']);
            }

            $namaGambarFinal = $namaGambarBaru;
        }
        // -------------------------------------------------

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'short_desc'  => $this->request->getPost('short_desc'),
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon'),
            'image'       => $namaGambarFinal
        ];

        $this->jurusanModel->update($id, $data);
        log_activity('UPDATE', 'jurusan', $id, ['name' => $jurusanLama['name']], ['name' => $data['name']]);

        return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/jurusan')->with('error', 'Akses ditolak.');

        $jurusan = $this->jurusanModel->find($id);

        if ($jurusan) {
            if (file_exists(FCPATH . 'uploads/jurusan/' . $jurusan['image'])) {
                unlink(FCPATH . 'uploads/jurusan/' . $jurusan['image']);
            }

            $this->jurusanModel->delete($id);
            log_activity('DELETE', 'jurusan', $id, ['name' => $jurusan['name']], null);

            return redirect()->to('/panel/jurusan')->with('success', 'Jurusan berhasil dihapus.');
        }

        return redirect()->to('/panel/jurusan')->with('error', 'Data tidak ditemukan.');
    }
}
