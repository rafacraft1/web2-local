<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();

        $data = [
            'title' => 'Profil Saya | Panel Admin',
            'user'  => $user
        ];

        return view('backend/profile', $data);
    }

    public function updateInfo()
    {
        $userId = session()->get('user_id');
        $userLama = $this->db->table('users')->where('id', $userId)->get()->getRowArray();

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
            'email'        => $this->request->getPost('email'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        // Proses Avatar (Jika Ada)
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            if ($this->validate(['avatar' => 'is_image[avatar]|mime_in[avatar,image/webp,image/png,image/jpeg,image/jpg]'])) {
                // Hapus avatar lama jika bukan bawaan default
                if (!empty($userLama['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $userLama['avatar'])) {
                    unlink(FCPATH . 'uploads/avatars/' . $userLama['avatar']);
                }

                $newAvatarName = $avatarFile->getRandomName();
                $avatarFile->move(FCPATH . 'uploads/avatars', $newAvatarName);
                $postData['avatar'] = $newAvatarName;

                // Perbarui Session Avatar
                session()->set('avatar', $newAvatarName);
            }
        }

        $this->db->table('users')->where('id', $userId)->update($postData);

        // Perbarui Session Nama Lengkap
        session()->set('nama_lengkap', $postData['nama_lengkap']);

        log_activity('UPDATE', 'users', $userId, ['nama_lengkap' => $userLama['nama_lengkap']], ['nama_lengkap' => $postData['nama_lengkap']]);

        return redirect()->to('/panel/profile')->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();

        $rules = [
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[6]',
            'konfirmasi_password' => 'matches[password_baru]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/panel/profile?tab=password')->withInput()->with('error', 'Gagal. Pastikan konfirmasi password sama dan minimal 6 karakter.');
        }

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');

        // Verifikasi password lama
        if (!password_verify($passwordLama, $user['password_hash'])) {
            return redirect()->to('/panel/profile?tab=password')->with('error', 'Password lama yang Anda masukkan salah.');
        }

        // Simpan password baru
        $this->db->table('users')->where('id', $userId)->update([
            'password_hash' => password_hash($passwordBaru, PASSWORD_BCRYPT),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        log_activity('UPDATE', 'users', $userId, ['aksi' => 'Ubah Password'], null);

        return redirect()->to('/panel/profile?tab=password')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
