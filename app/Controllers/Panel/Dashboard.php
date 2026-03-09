<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        $data = [
            'title' => 'Dashboard Panel'
        ];

        // ==========================================
        // 1. STATISTIK BERITA (Bisa dilihat: Admin, Kepala Sekolah, Guru)
        // ==========================================
        if (in_array($role, ['admin', 'kepala-sekolah', 'guru'])) {
            $beritaModel = new \App\Models\BeritaModel();
            $data['total_berita'] = $beritaModel->countAllResults();
        }

        // ==========================================
        // 2. STATISTIK GALERI (Bisa dilihat: Admin, Guru)
        // ==========================================
        if (in_array($role, ['admin', 'guru'])) {
            $galeriModel = new \App\Models\GaleriModel();
            $data['total_galeri'] = $galeriModel->countAllResults();
        }

        // ==========================================
        // 3. STATISTIK PESAN MASUK (Bisa dilihat: Admin, Staff TU)
        // ==========================================
        if (in_array($role, ['admin', 'staff-tu'])) {
            $pesanModel = new \App\Models\PesanModel();
            // Hanya menghitung pesan yang belum dibaca (is_read = 0)
            $data['pesan_baru'] = $pesanModel->where('is_read', 0)->countAllResults();
        }

        // ==========================================
        // 4. STATISTIK USER (Hanya dilihat: Admin)
        // ==========================================
        if ($role === 'admin') {
            $userModel = new \App\Models\UserModel();
            $data['total_user'] = $userModel->countAllResults();
        }

        return view('backend/dashboard', $data);
    }
}
