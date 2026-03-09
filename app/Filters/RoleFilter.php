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

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('panel/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role');

        // ========================================================
        // OPTIMASI: Jika perannya admin, langsung loloskan tanpa perlu cek modul
        // ========================================================
        if ($userRole === 'admin') {
            return;
        }

        $uri = $request->getUri();

        // Ambil segment 2 (contoh: dari /panel/settings ambil 'settings')
        // Gunakan pengecekan getTotalSegments untuk mencegah error 'Out of bounds'
        if ($uri->getTotalSegments() >= 2) {
            $module = $uri->getSegment(2);
        } else {
            // Jika hanya mengakses /panel, asumsikan modulnya adalah dashboard
            $module = 'dashboard';
        }

        // ========================================================
        // OPTIMASI: Ambil daftar modul dari session, BUKAN dari database
        // ========================================================
        $allowedModules = $session->get('allowed_modules') ?? [];

        // Cek apakah modul yang diakses ada di dalam array session yang diizinkan
        if (!in_array($module, $allowedModules)) {
            // PERBAIKAN: Hindari redirect()->back() untuk mencegah infinite loop
            return redirect()->to('panel/dashboard')->with('error', 'Anda tidak memiliki hak akses ke modul ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah request
    }
}
