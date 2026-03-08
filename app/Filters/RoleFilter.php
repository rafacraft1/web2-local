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
        $uri = $request->getUri();

        // Ambil segment 2 (contoh: dari /panel/settings ambil 'settings')
        // Gunakan pengecekan getTotalSegments untuk mencegah error 'Out of bounds'
        if ($uri->getTotalSegments() >= 2) {
            $module = $uri->getSegment(2);
        } else {
            // Jika hanya mengakses /panel, asumsikan modulnya adalah dashboard
            $module = 'dashboard';
        }

        // 3. Cek izin ke database (Tabel role_permissions)
        $db = \Config\Database::connect();
        $hasAccess = $db->table('role_permissions')
            ->where('slug_role', $userRole)
            ->where('nama_modul', $module)
            ->countAllResults();

        if ($hasAccess === 0 && $userRole !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses ke modul ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah request
    }
}
