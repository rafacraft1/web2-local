<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Ambil Statistik Ringkas
        $totalBerita = $db->table('berita')->where('status', 'published')->countAllResults();
        $totalGaleri = $db->table('galeri')->countAllResults();
        $pesanBaru   = $db->table('pesan')->where('is_read', 0)->countAllResults();
        $totalMitra  = $db->table('mitra')->countAllResults();

        // 2. Ambil 5 Pesan Terbaru
        $recentPesan = $db->table('pesan')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 3. Ambil 5 Aktivitas Terakhir (Audit Log)
        $recentLogs = $db->table('audit_logs')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 4. Data untuk Grafik: Menghitung jumlah pengguna berdasarkan Role
        $usersByRole = $db->table('users')
            ->select('role, COUNT(id) as total')
            ->groupBy('role')
            ->get()
            ->getResultArray();

        $data = [
            'title'        => 'Dashboard | Panel Admin',
            'total_berita' => $totalBerita,
            'total_galeri' => $totalGaleri,
            'pesan_baru'   => $pesanBaru,
            'total_mitra'  => $totalMitra,
            'recent_pesan' => $recentPesan,
            'recent_logs'  => $recentLogs,
            'users_role'   => $usersByRole
        ];

        return view('backend/dashboard', $data);
    }
}