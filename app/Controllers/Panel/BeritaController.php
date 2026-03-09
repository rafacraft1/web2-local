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
        // --- Task 4: Refactoring Mass Assignment Input ---
        $inputData = $this->request->getPost(['title', 'excerpt', 'content', 'category', 'status']);
        $imageBase64 = $this->request->getPost('image_base64');
        
        if (empty($imageBase64)) {
            return redirect()->back()->withInput()->with('error', 'Gambar sampul wajib diisi.');
        }

        $inputData['slug']    = url_title($inputData['title'], '-', true);
        $inputData['user_id'] = session()->get('user_id');
        $namaGambar           = $inputData['slug'] . '-' . time() . '.webp';
        $inputData['image']   = $namaGambar;

        // Validasi Model (sebelum upload gambar untuk menghindari file sampah jika validasi teks gagal)
        if (!$this->beritaModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->beritaModel->errors());
        }

        // --- Task 2: Pindahkan Proses Upload File ke Luar Transaksi Database ---
        $uploadProses = $this->processBase64Image($imageBase64, 'berita', $namaGambar);
        if (!$uploadProses['success']) {
            return redirect()->back()->withInput()->with('error', $uploadProses['error']);
        }

        // --- Memulai Transaksi Database ---
        $this->beritaModel->db->transStart();
        
        $this->beritaModel->insert($inputData);
        $insertId = $this->beritaModel->getInsertID();

        $this->beritaModel->db->transComplete();

        // --- Task 6: Pengecekan Eksekusi pada Log Aktivitas ---
        if ($this->beritaModel->db->transStatus() === false) {
            // Jika DB gagal, hapus gambar yang sudah terlanjur diupload
            $this->hapusGambarFisik($namaGambar);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }

        // Catat log jika transaksi db sukses
        log_activity('CREATE', 'berita', $insertId, null, ['title' => $inputData['title']]);

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

        // --- Task 4: Mass Assignment Input ---
        $inputData = $this->request->getPost(['title', 'excerpt', 'content', 'category', 'status']);
        $inputData['id']   = $id; // ID untuk validasi is_unique
        $inputData['slug'] = url_title($inputData['title'], '-', true);
        
        $imageBase64 = $this->request->getPost('image_base64');
        $namaGambarFinal = $beritaLama['image']; // Default pakai gambar lama

        // Validasi Model awal (cek teks dll sebelum proses gambar)
        // Set validasi khusus untuk bypass rule image jika tidak ada gambar baru
        if (!$this->beritaModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->beritaModel->errors());
        }

        $gambarBaruBerhasilUpload = false;

        // --- Task 2: Pindahkan I/O Keluar Transaksi DB ---
        if (!empty($imageBase64)) {
            $namaGambarBaru = $inputData['slug'] . '-' . time() . '.webp';
            $uploadProses   = $this->processBase64Image($imageBase64, 'berita', $namaGambarBaru);

            if (!$uploadProses['success']) {
                return redirect()->back()->withInput()->with('error', $uploadProses['error']);
            }
            
            // Task 1: Update array data dengan gambar baru, BUKAN query terpisah
            $namaGambarFinal = $namaGambarBaru;
            $gambarBaruBerhasilUpload = true;
        }

        $inputData['image'] = $namaGambarFinal;

        // --- Transaksi Database ---
        $this->beritaModel->db->transStart();
        
        $this->beritaModel->update($id, $inputData);
        
        $this->beritaModel->db->transComplete();

        // --- Task 6: Pengecekan Transaksi DB ---
        if ($this->beritaModel->db->transStatus() === false) {
            // Rollback gambar fisik BILA transaksi DB gagal tapi gambar baru sempat terupload
            if ($gambarBaruBerhasilUpload) {
                $this->hapusGambarFisik($namaGambarFinal);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }

        // Jika sukses dan ada gambar baru, hapus gambar lama
        if ($gambarBaruBerhasilUpload) {
            $this->hapusGambarFisik($beritaLama['image']);
        }

        log_activity('UPDATE', 'berita', $id, ['title' => $beritaLama['title']], ['title' => $inputData['title']]);

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
