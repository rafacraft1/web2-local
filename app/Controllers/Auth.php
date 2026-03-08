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

        $data = [
            'title' => 'Login | Panel Admin SMK Kreatif'
        ];

        return view('auth/login', $data);
    }

    public function process()
    {
        // 1. Inisiasi UserModel (bukan lagi \Config\Database::connect())
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // 2. Gunakan method bawaan Model untuk pencarian data
        $user = $userModel->where('username', $username)
                          ->orWhere('email', $username)
                          ->first();

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {

                // --- PROTEKSI AKUN NONAKTIF ---
                if ($user['is_active'] == 0) {
                    return redirect()->back()->withInput()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.');
                }
                // ------------------------------

                $sessionData = [
                    'user_id'      => $user['id'],
                    'username'     => $user['username'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'email'        => $user['email'],
                    'role'         => $user['role'],
                    'avatar'       => $user['avatar'],
                    'isLoggedIn'   => true
                ];
                session()->set($sessionData);

                // 3. Gunakan Helper log_activity agar seragam dan rapi
                helper('audit');
                log_activity('LOGIN', 'auth', $user['id']);

                return redirect()->to('/panel/dashboard');
            } else {
                return redirect()->back()->withInput()->with('error', 'Password yang Anda masukkan salah.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Username atau Email tidak ditemukan.');
        }
    }

    public function logout()
    {
        if (session()->get('isLoggedIn')) {
            // 4. Catat log sebelum session dihancurkan agar nama & ID tetap terekam
            helper('audit');
            log_activity('LOGOUT', 'auth', session()->get('user_id'));
        }

        session()->destroy();
        return redirect()->to('/panel/login')->with('success', 'Anda berhasil logout.');
    }
}