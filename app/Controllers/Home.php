<?php

namespace App\Controllers;

use App\Models\PesanModel;

class Home extends BaseController
{
    // Deklarasikan variabel global untuk class ini
    protected $db;
    protected $settings;

    // Fungsi Construct otomatis berjalan setiap kali controller ini dipanggil
    public function __construct()
    {
        $this->db = \Config\Database::connect();

        // Ambil data pengaturan web sekali saja di sini
        $rawSettings = $this->db->table('settings')->get()->getResultArray();
        $this->settings = [];
        foreach ($rawSettings as $row) {
            $this->settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    public function index()
    {
        // Panggil data lainnya untuk halaman Home menggunakan $this->db
        $jurusan = $this->db->table('jurusan')->get()->getResultArray();

        $galeri = $this->db->table('galeri')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $berita = $this->db->table('berita')
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $mitra = $this->db->table('mitra')->get()->getResultArray();

        $data = [
            'title'    => 'Beranda | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif Nusantara'),
            'settings' => $this->settings, // Langsung panggil variabel global
            'jurusan'  => $jurusan,
            'galeri'   => $galeri,
            'berita'   => $berita,
            'mitra'    => $mitra
        ];

        return view('frontend/home', $data);
    }

    // Fungsi untuk menampilkan Halaman Profil Sekolah
    public function profil()
    {
        $data = [
            'title'    => 'Profil Sekolah | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ];

        return view('frontend/profil', $data);
    }

    // Fungsi untuk menampilkan Daftar Jurusan (Indeks)
    public function jurusan()
    {
        $jurusanModel = new \App\Models\JurusanModel();

        $data = [
            'title'    => 'Program Keahlian | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            // Ambil semua jurusan, urutkan berdasarkan ID atau nama (di sini berdasarkan ID ascending)
            'jurusan'  => $jurusanModel->orderBy('id', 'ASC')->findAll()
        ];

        return view('frontend/jurusan', $data);
    }

    // Fungsi untuk menampilkan Daftar Berita (Indeks)
    public function berita()
    {
        $beritaModel = new \App\Models\BeritaModel();

        $data = [
            'title'    => 'Berita & Artikel | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            // Ambil berita yang di-publish, urutkan dari terbaru, batasi 9 per halaman
            'berita'   => $beritaModel->where('status', 'published')
                ->orderBy('created_at', 'DESC')
                ->paginate(9, 'berita'),
            // Kirim data pager untuk membuat tombol navigasi halaman 1, 2, 3, dst
            'pager'    => $beritaModel->pager
        ];

        return view('frontend/berita', $data);
    }

    // Fungsi untuk menampilkan Detail Berita
    public function detailBerita($slug)
    {
        // Cari berita berdasarkan slug dan pastikan statusnya published
        $berita = $this->db->table('berita')
            ->select('berita.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = berita.user_id', 'left') // Ambil nama penulis
            ->where('berita.slug', $slug)
            ->where('berita.status', 'published')
            ->get()
            ->getRowArray();

        // Jika berita tidak ditemukan atau masih draft, tampilkan error 404
        if (!$berita) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        // Ambil 5 berita terbaru lainnya untuk rekomendasi (Kecuali berita yang sedang dibaca)
        $recentBerita = $this->db->table('berita')
            ->where('status', 'published')
            ->where('id !=', $berita['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $data = [
            'title'         => $berita['title'] . ' | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings'      => $this->settings,
            'berita'        => $berita,
            'recent_berita' => $recentBerita
        ];

        return view('frontend/berita_detail', $data);
    }

    // Fungsi untuk menampilkan Daftar Galeri (Indeks)
    public function galeri()
    {
        $galeriModel = new \App\Models\GaleriModel();

        $data = [
            'title'    => 'Galeri & Karya | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            // Ambil galeri, urutkan dari terbaru, batasi 9 foto per halaman
            'galeri'   => $galeriModel->orderBy('created_at', 'DESC')->paginate(9, 'galeri'),
            'pager'    => $galeriModel->pager
        ];

        return view('frontend/galeri', $data);
    }

    public function kontak()
    {
        $data = [
            'title'    => 'Kontak Kami | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings, // Langsung panggil variabel global
        ];

        return view('frontend/kontak', $data);
    }

    // Fungsi untuk memproses pengiriman pesan
    public function kirimPesan()
    {
        $rules = [
            'name'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'subject' => 'required|min_length[5]',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pesan. Pastikan semua kolom diisi dengan benar (minimal 10 karakter untuk pesan).');
        }

        $pesanModel = new PesanModel();

        $pesanModel->insert([
            'name'    => esc($this->request->getPost('name')),
            'email'   => esc($this->request->getPost('email')),
            'subject' => esc($this->request->getPost('subject')),
            'message' => esc($this->request->getPost('message')),
            'is_read' => 0
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Pesan Anda berhasil dikirim. Kami akan segera merespons melalui email Anda.');
    }

    // Fungsi untuk Halaman 404 Kustom
    public function error404()
    {
        $data = [
            'title'    => 'Halaman Tidak Ditemukan | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ];

        // Pastikan response code HTTP tetap 404 agar sesuai standar SEO
        return $this->response->setStatusCode(404)->setBody(view('frontend/404', $data));
    }
}
