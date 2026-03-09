<?php

namespace App\Controllers;

use App\Models\PesanModel;
use App\Models\JurusanModel;
use App\Models\GaleriModel;
use App\Models\BeritaModel;
use App\Models\MitraModel;

class Home extends BaseController
{
    protected $settings;

    public function __construct()
    {
        // Ambil data pengaturan web khusus untuk layout frontend
        $db = \Config\Database::connect();
        $rawSettings = $db->table('settings')->get()->getResultArray();

        $this->settings = [];
        foreach ($rawSettings as $row) {
            $this->settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    public function index()
    {
        // Inisiasi Models
        $jurusanModel = new JurusanModel();
        $galeriModel  = new GaleriModel();
        $beritaModel  = new BeritaModel();
        $mitraModel   = new MitraModel();

        return view('frontend/home', [
            'title'    => 'Beranda | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif Nusantara'),
            'settings' => $this->settings,
            'jurusan'  => $jurusanModel->findAll(),
            'galeri'   => $galeriModel->orderBy('created_at', 'DESC')->limit(3)->find(),
            'berita'   => $beritaModel->where('status', 'published')->orderBy('created_at', 'DESC')->limit(3)->find(),
            'mitra'    => $mitraModel->findAll()
        ]);
    }

    public function profil()
    {
        return view('frontend/profil', [
            'title'    => 'Profil Sekolah | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ]);
    }

    public function jurusan()
    {
        $jurusanModel = new JurusanModel();

        return view('frontend/jurusan', [
            'title'    => 'Program Keahlian | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'jurusan'  => $jurusanModel->orderBy('id', 'ASC')->findAll()
        ]);
    }

    public function berita()
    {
        $beritaModel = new BeritaModel();

        return view('frontend/berita', [
            'title'    => 'Berita & Artikel | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'berita'   => $beritaModel->where('status', 'published')
                ->orderBy('created_at', 'DESC')
                ->paginate(9, 'berita'),
            'pager'    => $beritaModel->pager
        ]);
    }

public function detailBerita($slug)
    {
        $beritaModel = new BeritaModel();
        $tabelBerita = $beritaModel->table; // Ambil nama tabel secara dinamis

        // Task 5: Gunakan variabel $tabelBerita alih-alih hardcode string 'berita'
        $berita = $beritaModel->select($tabelBerita . '.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = ' . $tabelBerita . '.user_id', 'left')
            ->where($tabelBerita . '.slug', $slug)
            ->where($tabelBerita . '.status', 'published')
            ->first();

        if (!$berita) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        $recentBerita = $beritaModel->where('status', 'published')
            ->where('id !=', $berita['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->find();

        return view('frontend/berita_detail', [
            'title'         => $berita['title'] . ' | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings'      => $this->settings,
            'berita'        => $berita,
            'recent_berita' => $recentBerita
        ]);
    }

    public function galeri()
    {
        $galeriModel = new GaleriModel();

        return view('frontend/galeri', [
            'title'    => 'Galeri & Karya | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'galeri'   => $galeriModel->orderBy('created_at', 'DESC')->paginate(9, 'galeri'),
            'pager'    => $galeriModel->pager
        ]);
    }

    public function kontak()
    {
        return view('frontend/kontak', [
            'title'    => 'Kontak Kami | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ]);
    }

    public function kirimPesan()
    {
        $pesanModel = new PesanModel();

        // 1. Menerapkan fungsi esc() untuk proteksi serangan XSS dari pengunjung
        // 2. Menyesuaikan field form (name, subject) menjadi kolom DB yang benar di PesanModel (nama, subjek)
        $dataPesan = [
            'nama'    => esc($this->request->getPost('name')),
            'email'   => esc($this->request->getPost('email')),
            'subjek'  => esc($this->request->getPost('subject')),
            'pesan'   => esc($this->request->getPost('message')),
            'is_read' => 0
        ];

        // 3. Validasi otomatis dari Model (Fat Model, Thin Controller)
        if (!$pesanModel->insert($dataPesan)) {
            // Jika validasi gagal, kembalikan array errors dari model
            return redirect()->back()->withInput()->with('errors', $pesanModel->errors());
        }

        return redirect()->back()->with('success', 'Terima kasih! Pesan Anda berhasil dikirim. Kami akan segera merespons melalui email Anda.');
    }

    public function error404()
    {
        return $this->response->setStatusCode(404)->setBody(view('frontend/404', [
            'title'    => 'Halaman Tidak Ditemukan | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ]));
    }
}
