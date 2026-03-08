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
        // Validasi khusus base64 tetap di controller
        $imageBase64 = $this->request->getPost('image_base64');
        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar sampul wajib diisi.');
        }

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambar  = $slug . '-' . time() . '.webp';

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

        // --- Task 2.3: Menggunakan Database Transaction ---
        $this->beritaModel->db->transStart();

        // Menyimpan data. Jika gagal validasi dari Model, ambil errornya
        if (!$this->beritaModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->beritaModel->errors());
        }

        $insertId = $this->beritaModel->getInsertID();

        // --- PROSES UPLOAD BASE64 ---
        $uploadProses = $this->processBase64Image($imageBase64, 'berita', $namaGambar);

        if (!$uploadProses['success']) {
            $this->beritaModel->db->transRollback(); // Batalkan insert DB jika gambar gagal diupload
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        log_activity('CREATE', 'berita', $insertId, null, ['title' => $data['title']]);

        $this->beritaModel->db->transComplete();

        if ($this->beritaModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }

        return redirect()->to('panel/berita')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/berita')->with('error', 'Akses ditolak.');

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
        if (!$id) return redirect()->to('panel/berita')->with('error', 'Akses ditolak.');

        $beritaLama = $this->beritaModel->find($id);
        if (!$beritaLama) return redirect()->to('panel/berita')->with('error', 'Data tidak ditemukan.');

        $slug = url_title($this->request->getPost('title'), '-', true);
        $namaGambarFinal = $beritaLama['image'];
        $imageBase64 = $this->request->getPost('image_base64');

        $data = [
            'id'       => $id, // ID disertakan agar validasi is_unique berfungsi benar saat update
            'title'    => $this->request->getPost('title'),
            'slug'     => $slug,
            'excerpt'  => $this->request->getPost('excerpt'),
            'content'  => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'status'   => $this->request->getPost('status'),
            'image'    => $namaGambarFinal
        ];

        $this->beritaModel->db->transStart();

        if (!$this->beritaModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->beritaModel->errors());
        }

        // --- PROSES JIKA ADA GAMBAR BARU ---
        if (!empty($imageBase64)) {
            $namaGambarBaru = $slug . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'berita', $namaGambarBaru);

            if (!$uploadProses['success']) {
                $this->beritaModel->db->transRollback();
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }

            // Task 2.2: Gunakan fungsi DRY untuk hapus gambar fisik
            $this->hapusGambarFisik($beritaLama['image']);

            // Update nama gambar yang baru secara spesifik
            $this->beritaModel->update($id, ['image' => $namaGambarBaru]);
        }

        log_activity('UPDATE', 'berita', $id, ['title' => $beritaLama['title']], ['title' => $data['title']]);

        $this->beritaModel->db->transComplete();

        if ($this->beritaModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('panel/berita')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/berita')->with('error', 'Akses ditolak.');

        $berita = $this->beritaModel->find($id);

        if ($berita) {
            $this->beritaModel->db->transStart();

            $this->beritaModel->delete($id);
            log_activity('DELETE', 'berita', $id, ['title' => $berita['title']], null);

            $this->beritaModel->db->transComplete();

            // Jika transaksi db sukses, baru hapus file fisiknya
            if ($this->beritaModel->db->transStatus() !== false) {
                $this->hapusGambarFisik($berita['image']);
                return redirect()->to('panel/berita')->with('success', 'Berita berhasil dihapus.');
            }

            return redirect()->to('panel/berita')->with('error', 'Terjadi kesalahan, gagal menghapus data.');
        }

        return redirect()->to('panel/berita')->with('error', 'Data tidak ditemukan.');
    }

    // --- Task 2.2: Fungsi DRY (Private) untuk menghapus gambar ---
    private function hapusGambarFisik($namaFile)
    {
        if ($namaFile) {
            $path = FCPATH . 'uploads/berita/' . $namaFile;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
