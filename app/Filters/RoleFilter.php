<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Pastikan user sudah login
        if (!$session->get('isLoggedIn')) {
            // Asumsi route login Anda adalah auth/login (Sesuaikan jika panel/login)
            return redirect()->to('panel/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role');

        // 2. Role 'admin' (Superadmin) bebas hambatan, bisa akses semuanya
        if ($userRole === 'admin') {
            return;
        }

        // 3. Ambil URL yang sedang diakses
        $uri = $request->getUri();
        $segments = $uri->getSegments();
        $baseModuleUrl = '';

        // Kita ambil 2 segmen pertama saja (Misal: dari 'panel/users/create' kita cuma ambil 'panel/users')
        if (count($segments) >= 2 && $segments[0] === 'panel') {
            $baseModuleUrl = $segments[0] . '/' . $segments[1];
        } elseif (count($segments) == 1 && $segments[0] === 'panel') {
            $baseModuleUrl = 'panel/dashboard';
        } else {
            return; // Biarkan lolos jika bukan area /panel
        }

        // 4. Pengecualian (Whitelist): Semua role yang login pasti boleh akses halaman ini
        $allowedRoutes = [
            'panel/dashboard',
            'panel/profile',
            'panel/logout'
        ];

        if (in_array($baseModuleUrl, $allowedRoutes)) {
            return;
        }

        // 5. PENGECEKAN DINAMIS KE DATABASE (Tahap Inti)
        // Cek apakah 'panel/namamodul' ini ada diizinkan untuk Role user yang sedang login
        $db = \Config\Database::connect();
        $hasAccess = $db->table('role_menu_access')
            ->join('menus', 'menus.id = role_menu_access.menu_id')
            ->where('role_menu_access.slug_role', $userRole)
            ->where('menus.url', $baseModuleUrl)
            ->countAllResults();

        // 6. Jika tidak ada izin di database, TENDANG KELUAR!
        if ($hasAccess === 0) {
            // Tangani jika request berasal dari AJAX (misal DataTables atau fetch API)
            if ($request->isAJAX()) {
                return \Config\Services::response()->setStatusCode(403)->setJSON(['error' => 'Keamanan: Anda tidak memiliki akses ke data ini.']);
            }

            // Tangani akses normal lewat ketik URL
            return redirect()->to('panel/dashboard')->with('error', 'Keamanan Sistem: Anda tidak memiliki hak akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu diisi
    }
}
