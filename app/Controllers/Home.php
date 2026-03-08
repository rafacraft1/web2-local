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

        $data = [
            'title'    => 'Beranda | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif Nusantara'),
            'settings' => $this->settings,
            'jurusan'  => $jurusanModel->findAll(),
            'galeri'   => $galeriModel->orderBy('created_at', 'DESC')->limit(3)->find(),
            'berita'   => $beritaModel->where('status', 'published')->orderBy('created_at', 'DESC')->limit(3)->find(),
            'mitra'    => $mitraModel->findAll()
        ];

        return view('frontend/home', $data);
    }

    public function profil()
    {
        $data = [
            'title'    => 'Profil Sekolah | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ];

        return view('frontend/profil', $data);
    }

    public function jurusan()
    {
        $jurusanModel = new JurusanModel();

        $data = [
            'title'    => 'Program Keahlian | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'jurusan'  => $jurusanModel->orderBy('id', 'ASC')->findAll()
        ];

        return view('frontend/jurusan', $data);
    }

    public function berita()
    {
        $beritaModel = new BeritaModel();

        $data = [
            'title'    => 'Berita & Artikel | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'berita'   => $beritaModel->where('status', 'published')
                ->orderBy('created_at', 'DESC')
                ->paginate(9, 'berita'),
            'pager'    => $beritaModel->pager
        ];

        return view('frontend/berita', $data);
    }

    public function detailBerita($slug)
    {
        $beritaModel = new BeritaModel();

        // Menggunakan method dari model atau Query Builder melalui Model
        $berita = $beritaModel->select('berita.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = berita.user_id', 'left')
            ->where('berita.slug', $slug)
            ->where('berita.status', 'published')
            ->first();

        if (!$berita) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        // Ambil 6 berita terbaru lainnya untuk rekomendasi
        $recentBerita = $beritaModel->where('status', 'published')
            ->where('id !=', $berita['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->find();

        $data = [
            'title'         => $berita['title'] . ' | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings'      => $this->settings,
            'berita'        => $berita,
            'recent_berita' => $recentBerita
        ];

        return view('frontend/berita_detail', $data);
    }

    public function galeri()
    {
        $galeriModel = new GaleriModel();

        $data = [
            'title'    => 'Galeri & Karya | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
            'galeri'   => $galeriModel->orderBy('created_at', 'DESC')->paginate(9, 'galeri'),
            'pager'    => $galeriModel->pager
        ];

        return view('frontend/galeri', $data);
    }

    public function kontak()
    {
        $data = [
            'title'    => 'Kontak Kami | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ];

        return view('frontend/kontak', $data);
    }

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

    public function error404()
    {
        $data = [
            'title'    => 'Halaman Tidak Ditemukan | ' . ($this->settings['nama_web'] ?? 'SMK Kreatif'),
            'settings' => $this->settings,
        ];

        return $this->response->setStatusCode(404)->setBody(view('frontend/404', $data));
    }
}
