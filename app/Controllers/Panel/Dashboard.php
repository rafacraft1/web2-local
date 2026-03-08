<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\BeritaModel;
use App\Models\GaleriModel;
use App\Models\PesanModel;
use App\Models\MitraModel;
use App\Models\AuditLogModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Inisiasi Models
        $beritaModel = new BeritaModel();
        $galeriModel = new GaleriModel();
        $pesanModel  = new PesanModel();
        $mitraModel  = new MitraModel();
        $auditModel  = new AuditLogModel();
        $userModel   = new UserModel();

        // 1. Ambil Statistik Ringkas menggunakan Model
        $totalBerita = $beritaModel->where('status', 'published')->countAllResults();
        $totalGaleri = $galeriModel->countAllResults();
        $pesanBaru   = $pesanModel->where('is_read', 0)->countAllResults();
        $totalMitra  = $mitraModel->countAllResults();

        // 2. Ambil 5 Pesan Terbaru
        $recentPesan = $pesanModel->orderBy('created_at', 'DESC')->limit(5)->find();

        // 3. Ambil 5 Aktivitas Terakhir (Audit Log)
        $recentLogs = $auditModel->orderBy('created_at', 'DESC')->limit(5)->find();

        // 4. Data untuk Grafik: Menghitung jumlah pengguna berdasarkan Role
        $usersByRole = $userModel->select('role, COUNT(id) as total')->groupBy('role')->find();

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
