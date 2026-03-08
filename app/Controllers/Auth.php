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
