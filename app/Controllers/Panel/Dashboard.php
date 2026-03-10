<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        // [OPTIMASI] Panggil layanan Cache CodeIgniter
        $cache = \Config\Services::cache();

        $data = [
            'title' => 'Dashboard Panel'
        ];

        // ==========================================
        // 1. STATISTIK BERITA (Bisa dilihat: Admin, Kepala Sekolah, Guru)
        // ==========================================
        if (in_array($role, ['admin', 'kepala-sekolah', 'guru'])) {
            // Cek cache dulu, jika kosong baru query ke DB lalu simpan cache 5 menit (300 detik)
            if (($total_berita = $cache->get('panel_total_berita')) === null) {
                $beritaModel = new \App\Models\BeritaModel();
                $total_berita = $beritaModel->countAllResults();
                $cache->save('panel_total_berita', $total_berita, 300);
            }
            $data['total_berita'] = $total_berita;
        }

        // ==========================================
        // 2. STATISTIK GALERI (Bisa dilihat: Admin, Guru)
        // ==========================================
        if (in_array($role, ['admin', 'guru'])) {
            // Simpan cache 5 menit (300 detik)
            if (($total_galeri = $cache->get('panel_total_galeri')) === null) {
                $galeriModel = new \App\Models\GaleriModel();
                $total_galeri = $galeriModel->countAllResults();
                $cache->save('panel_total_galeri', $total_galeri, 300);
            }
            $data['total_galeri'] = $total_galeri;
        }

        // ==========================================
        // 3. STATISTIK PESAN MASUK (Bisa dilihat: Admin, Staff TU)
        // ==========================================
        if (in_array($role, ['admin', 'staff-tu'])) {
            // Simpan cache 1 menit (60 detik) agar notifikasi pesan baru tetap responsif
            if (($pesan_baru = $cache->get('panel_pesan_baru')) === null) {
                $pesanModel = new \App\Models\PesanModel();
                // Hanya menghitung pesan yang belum dibaca (is_read = 0)
                $pesan_baru = $pesanModel->where('is_read', 0)->countAllResults();
                $cache->save('panel_pesan_baru', $pesan_baru, 60);
            }
            $data['pesan_baru'] = $pesan_baru;
        }

        // ==========================================
        // 4. STATISTIK USER (Hanya dilihat: Admin)
        // ==========================================
        if ($role === 'admin') {
            // Simpan cache 1 jam (3600 detik) karena data user tidak bertambah setiap saat
            if (($total_user = $cache->get('panel_total_user')) === null) {
                $userModel = new \App\Models\UserModel();
                $total_user = $userModel->countAllResults();
                $cache->save('panel_total_user', $total_user, 3600);
            }
            $data['total_user'] = $total_user;
        }

        return view('backend/dashboard', $data);
    }
}
