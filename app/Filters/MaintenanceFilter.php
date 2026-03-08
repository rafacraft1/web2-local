<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Abaikan filter jika yang diakses adalah rute Admin Panel (/panel)
        $uri = $request->getUri()->getPath();
        if (strpos($uri, 'panel') === 0 || strpos($uri, '/panel') === 0) {
            return;
        }

        // 2. Izinkan akses (bypass) jika pengguna sedang login sebagai Admin/Operator
        if (session()->get('isLoggedIn')) {
            return;
        }

        // 3. Ambil SELURUH pengaturan dari database
        $db = \Config\Database::connect();
        $rawSettings = $db->table('settings')->get()->getResultArray();

        $settings = [];
        foreach ($rawSettings as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // 4. Jika maintenance aktif, tampilkan halaman perbaikan dan kirimkan data $settings
        if (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') {
            echo view('errors/html/maintenance', ['settings' => $settings]);
            exit;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada yang perlu dilakukan setelahnya
    }
}
