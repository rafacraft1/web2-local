<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $this->userModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('backend/users/index', $data);
    }

    public function create()
    {
        return view('backend/users/create', [
            'title' => 'Tambah User Baru'
        ]);
    }

    public function store()
    {
        // Validasi tambahan untuk password wajib di 'create'
        if (empty($this->request->getPost('password'))) {
            return redirect()->back()->withInput()->with('error', 'Password wajib diisi.');
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'         => $this->request->getPost('role'),
            'is_active'    => 1
        ];

        $this->userModel->db->transStart();

        if (!$this->userModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $insertId = $this->userModel->getInsertID();
        log_activity('CREATE', 'users', $insertId, null, ['username' => $data['username']]);

        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data user.');
        }

        return redirect()->to('panel/users')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        $user = $this->userModel->find($id);
        if (!$user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/users/edit', [
            'title'  => 'Edit User',
            'user'   => $user,
            'safeId' => $safeId
        ]);
    }

    public function update($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        $userLama = $this->userModel->find($id);
        if (!$userLama) return redirect()->to('panel/users')->with('error', 'Data tidak ditemukan.');

        $data = [
            'id'           => $id,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'role'         => $this->request->getPost('role'),
        ];

        // Hanya update password jika diisi
        $passwordBaru = $this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $data['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $this->userModel->db->transStart();

        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        log_activity('UPDATE', 'users', $id, ['username' => $userLama['username']], ['username' => $data['username']]);

        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data user.');
        }

        return redirect()->to('panel/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function toggleStatus($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        $user = $this->userModel->find($id);
        if ($user) {
            $newStatus = ($user['is_active'] == 1) ? 0 : 1;
            $this->userModel->update($id, ['is_active' => $newStatus]);

            log_activity('UPDATE', 'users', $id, ['is_active' => $user['is_active']], ['is_active' => $newStatus]);

            return redirect()->to('panel/users')->with('success', 'Status user berhasil diubah.');
        }

        return redirect()->to('panel/users')->with('error', 'User tidak ditemukan.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        // Proteksi agar admin tidak menghapus dirinya sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('panel/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->db->transStart();

            $this->userModel->delete($id);
            log_activity('DELETE', 'users', $id, ['username' => $user['username']], null);

            $this->userModel->db->transComplete();

            if ($this->userModel->db->transStatus() !== false) {
                // Task: Hapus file avatar fisik jika ada
                if (!empty($user['avatar'])) {
                    $path = FCPATH . 'uploads/avatars/' . $user['avatar'];
                    if (is_file($path)) unlink($path);
                }
                return redirect()->to('panel/users')->with('success', 'User berhasil dihapus.');
            }
            return redirect()->to('panel/users')->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('panel/users')->with('error', 'Data tidak ditemukan.');
    }
}
