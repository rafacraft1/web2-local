<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\BeritaModel;

class BeritaController extends BaseController
{
    protected $beritaModel;

    public function __construct()
    {
        $this->beritaModel = new BeritaModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'Kelola Berita & Artikel',
            'berita' => $this->beritaModel->getBeritaWithAuthor()
        ];
        return view('backend/berita/index', $data);
    }

    public function create()
    {
        return view('backend/berita/create', [
            'title' => 'Tulis Berita Baru'
        ]);
    }

    public function store()
    {
        $rules = [
            'title'        => 'required|min_length[5]|is_unique[berita.title]',
            'category'     => 'required',
            'status'       => 'required|in_list[draft,published]',
            'excerpt'      => 'required|max_length[255]',
            'content'      => 'required|min_length[20]',
            'image_base64' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('title'), '-', true);

        // --- PROSES UPLOAD BASE64 DENGAN HELPER (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambar  = $slug . '-' . time() . '.webp';

        $uploadProses = $this->processBase64Image($imageBase64, 'berita', $namaGambar);

        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }
        // ---------------------------------------------------------

        $data = [
            'user_id'  => session()->get('user_id'),
            'title'    => $this->request->getPost('title'),
            'slug'     => $slug,
            'excerpt'  => $this->request->getPost('excerpt'),
            'content'  => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'status'   => $this->request->getPost('status'),
            'image'    => $namaGambar
        ];

        $this->beritaModel->insert($data);
        $insertId = $this->beritaModel->getInsertID();

        log_activity('CREATE', 'berita', $insertId, null, ['title' => $data['title']]);

        return redirect()->to('/panel/berita')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');

        $berita = $this->beritaModel->find($id);
        if (!$berita) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/berita/edit', [
            'title'  => 'Edit Berita',
            'berita' => $berita,
            'safeId' => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');

        $beritaLama = $this->beritaModel->find($id);

        $rules = [
            'title'    => "required|min_length[5]|is_unique[berita.title,id,{$id}]",
            'category' => 'required',
            'status'   => 'required|in_list[draft,published]',
            'excerpt'  => 'required|max_length[255]',
            'content'  => 'required|min_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambarFinal = $beritaLama['image'];

        // --- PROSES JIKA ADA GAMBAR BARU (SUPER CLEAN) ---
        $imageBase64 = $this->request->getPost('image_base64');
        if (!empty($imageBase64)) {
            $namaGambarBaru = $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'berita', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            if (file_exists(FCPATH . 'uploads/berita/' . $beritaLama['image'])) {
                unlink(FCPATH . 'uploads/berita/' . $beritaLama['image']);
            }

            $namaGambarFinal = $namaGambarBaru;
        }
        // -------------------------------------------------

        $data = [
            'title'    => $this->request->getPost('title'),
            'slug'     => $slug,
            'excerpt'  => $this->request->getPost('excerpt'),
            'content'  => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'status'   => $this->request->getPost('status'),
            'image'    => $namaGambarFinal
        ];

        $this->beritaModel->update($id, $data);
        log_activity('UPDATE', 'berita', $id, ['title' => $beritaLama['title']], ['title' => $data['title']]);

        return redirect()->to('/panel/berita')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('/panel/berita')->with('error', 'Akses ditolak.');

        $berita = $this->beritaModel->find($id);

        if ($berita) {
            if (file_exists(FCPATH . 'uploads/berita/' . $berita['image'])) {
                unlink(FCPATH . 'uploads/berita/' . $berita['image']);
            }
            $this->beritaModel->delete($id);
            log_activity('DELETE', 'berita', $id, ['title' => $berita['title']], null);
            return redirect()->to('/panel/berita')->with('success', 'Berita berhasil dihapus.');
        }

        return redirect()->to('/panel/berita')->with('error', 'Data tidak ditemukan.');
    }
}
