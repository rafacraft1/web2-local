<?php

namespace App\Controllers;

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
        $db = \Config\Database::connect();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $db->table('users')
            ->where('username', $username)
            ->orWhere('email', $username)
            ->get()
            ->getRowArray();

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

                $db->table('audit_logs')->insert([
                    'user_id'    => $user['id'],
                    'nama_user'  => $user['nama_lengkap'],
                    'action'     => 'LOGIN',
                    'module'     => 'auth',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

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
            $db = \Config\Database::connect();
            $db->table('audit_logs')->insert([
                'user_id'    => session()->get('user_id'),
                'nama_user'  => session()->get('nama_lengkap'),
                'action'     => 'LOGOUT',
                'module'     => 'auth',
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        session()->destroy();
        return redirect()->to('/panel/login')->with('success', 'Anda berhasil logout.');
    }
}
