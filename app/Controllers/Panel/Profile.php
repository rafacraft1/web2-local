<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Profil Saya | Panel Admin',
            'user'  => $user
        ];

        return view('backend/profile', $data);
    }

    public function updateInfo()
    {
        $userId = session()->get('user_id');
        $userLama = $this->userModel->find($userId);

        // Keamanan tambahan: pastikan sesi user masih valid di database
        if (!$userLama) {
            return redirect()->to('/panel/login')->with('error', 'Sesi tidak valid.');
        }

        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'username'     => "required|alpha_numeric_punct|is_unique[users.username,id,{$userId}]",
            'email'        => "required|valid_email|is_unique[users.email,id,{$userId}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Username/Email mungkin sudah dipakai orang lain.');
        }

        $postData = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email')
        ];

        $this->userModel->update($userId, $postData);

        // Update session agar nama di pojok kanan atas Header ikut berubah secara realtime
        session()->set([
            'nama_lengkap' => $postData['nama_lengkap'],
            'username'     => $postData['username'],
            'email'        => $postData['email']
        ]);

        log_activity('UPDATE', 'users', $userId, ['nama_lengkap' => $userLama['nama_lengkap']], ['nama_lengkap' => $postData['nama_lengkap']]);

        return redirect()->to('/panel/profile')->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // Keamanan tambahan: pastikan sesi user masih valid di database
        if (!$user) {
            return redirect()->to('/panel/login')->with('error', 'Sesi tidak valid.');
        }

        $rules = [
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[6]',
            'konfirmasi_password' => 'matches[password_baru]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/panel/profile?tab=password')->withInput()->with('error', 'Gagal. Pastikan konfirmasi password sama dan minimal 6 karakter.');
        }

        // CASTING (string) agar Intelephense tahu persis ini adalah teks
        $passwordLama = (string) $this->request->getPost('password_lama');
        $passwordBaru = (string) $this->request->getPost('password_baru');
        $hashSimpanan = (string) $user['password_hash'];

        // Verifikasi password lama (Error P1006 Intelephense hilang karena kita sudah deklarasikan sebagai string)
        if (!password_verify($passwordLama, $hashSimpanan)) {
            return redirect()->to('/panel/profile?tab=password')->with('error', 'Password lama yang Anda masukkan salah.');
        }

        // Simpan password baru
        $this->userModel->update($userId, [
            'password_hash' => password_hash($passwordBaru, PASSWORD_BCRYPT)
        ]);

        log_activity('UPDATE', 'users', $userId, ['aksi' => 'Ubah Password'], null);

        return redirect()->to('/panel/profile?tab=password')->with('success', 'Password berhasil diperbarui.');
    }
}
