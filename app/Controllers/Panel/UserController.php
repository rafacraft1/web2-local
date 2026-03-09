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
        $inputData = $this->request->getPost(['nama_lengkap', 'username', 'role']);
        $password  = $this->request->getPost('password');

        if (empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Password wajib diisi.');
        }

        $inputData['password']  = password_hash($password, PASSWORD_DEFAULT);
        $inputData['is_active'] = 1;

        if (!$this->userModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $this->userModel->db->transStart();
        $this->userModel->skipValidation(true)->insert($inputData);
        $insertId = $this->userModel->getInsertID();
        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data user.');
        }

        log_activity('CREATE', 'users', $insertId, null, ['username' => $inputData['username']]);
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

        $inputData = $this->request->getPost(['nama_lengkap', 'username', 'role']);
        $inputData['id'] = $id;

        $passwordBaru = $this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $inputData['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        if (!$this->userModel->validate($inputData)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $this->userModel->db->transStart();
        $this->userModel->skipValidation(true)->update($id, $inputData);
        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data user.');
        }

        log_activity('UPDATE', 'users', $id, ['username' => $userLama['username']], ['username' => $inputData['username']]);
        return redirect()->to('panel/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function toggleStatus($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        $user = $this->userModel->find($id);
        if ($user) {
            $newStatus = ($user['is_active'] == 1) ? 0 : 1;

            $this->userModel->db->transStart();
            $this->userModel->skipValidation(true)->update($id, ['is_active' => $newStatus]);
            $this->userModel->db->transComplete();

            if ($this->userModel->db->transStatus() !== false) {
                log_activity('UPDATE', 'users', $id, ['is_active' => $user['is_active']], ['is_active' => $newStatus]);
                return redirect()->to('panel/users')->with('success', 'Status user berhasil diubah.');
            }
            return redirect()->to('panel/users')->with('error', 'Gagal merubah status user.');
        }

        return redirect()->to('panel/users')->with('error', 'User tidak ditemukan.');
    }

    public function delete($safeId = null)
    {
        $id = $this->decryptId($safeId);
        if (!$id) return redirect()->to('panel/users')->with('error', 'Akses ditolak.');

        if ($id == session()->get('user_id')) {
            return redirect()->to('panel/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->db->transStart();
            $this->userModel->delete($id);
            $this->userModel->db->transComplete();

            if ($this->userModel->db->transStatus() !== false) {
                log_activity('DELETE', 'users', $id, ['username' => $user['username']], null);
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
