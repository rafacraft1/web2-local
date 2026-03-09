<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/panel/dashboard');
        }

        return view('auth/login', [
            'title' => 'Login | Panel Admin SMK Kreatif'
        ]);
    }

    public function process()
    {
        // ========================================================
        // OPTIMASI #4: Rate Limiting (Maksimal 5 percobaan per menit)
        // ========================================================
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();

        // Membatasi 5 request per menit (60 detik) untuk IP yang sama
        if ($throttler->check("login_attempt_{$ipAddress}", 5, MINUTE) === false) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan login gagal. Silakan coba lagi dalam 1 menit.');
        }

        // Validasi input form dasar
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan Password wajib diisi.');
        }

        $userModel = new UserModel();
        $username  = $this->request->getPost('username');
        $password  = (string) $this->request->getPost('password');

        $user = $userModel->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        // Pengecekan disatukan agar lebih bersih
        if ($user && password_verify($password, $user['password_hash'])) {

            if ($user['is_active'] == 0) {
                return redirect()->back()->withInput()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.');
            }

            // ========================================================
            // OPTIMASI #1: Ambil hak akses modul (permissions) dari database
            // ========================================================
            $db = \Config\Database::connect();
            $permissions = $db->table('role_permissions')
                ->where('slug_role', $user['role'])
                ->select('nama_modul')
                ->get()
                ->getResultArray();

            // Ubah array multidimensi menjadi array 1 dimensi (hanya list nama_modul)
            $allowedModules = array_column($permissions, 'nama_modul');

            $sessionData = [
                'user_id'         => $user['id'],
                'username'        => $user['username'],
                'nama_lengkap'    => $user['nama_lengkap'],
                'email'           => $user['email'],
                'role'            => $user['role'],
                'avatar'          => $user['avatar'],
                'isLoggedIn'      => true,
                'allowed_modules' => $allowedModules // Simpan list modul yang diizinkan ke session
            ];
            session()->set($sessionData);

            log_activity('LOGIN', 'auth', $user['id']);

            return redirect()->to('/panel/dashboard');
        }

        // Pesan error digeneralisasi untuk menghindari "User Enumeration Attack"
        return redirect()->back()->withInput()->with('error', 'Username/Email atau Password salah.');
    }

    public function logout()
    {
        if (session()->get('isLoggedIn')) {
            log_activity('LOGOUT', 'auth', session()->get('user_id'));
            session()->destroy();
        }

        return redirect()->to('/panel/login')->with('success', 'Anda berhasil logout.');
    }
}
