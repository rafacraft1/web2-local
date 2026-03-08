<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // 1. Menampilkan Daftar Pengguna
    public function index()
    {
        $users = $this->userModel->getUserWithRole();

        return view('backend/users/index', [
            'title' => 'Manajemen Akun',
            'users' => $users
        ]);
    }

    // 2. Form Tambah Pengguna 
    public function create()
    {
        $roleModel = new RoleModel();
        $adminExists = $this->userModel->where('role', 'admin')->countAllResults() > 0;

        return view('backend/users/create', [
            'title' => 'Tambah Pengguna',
            'roles' => $roleModel->findAll(),
            'adminExists' => $adminExists
        ]);
    }

    // 3. Proses Simpan Pengguna Baru
    public function store()
    {
        $rules = [
            'username'     => 'required|is_unique[users.username]|alpha_numeric',
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'role'         => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleInput = $this->request->getPost('role');

        if ($roleInput === 'admin') {
            $adminExists = $this->userModel->where('role', 'admin')->countAllResults() > 0;
            if ($adminExists) {
                return redirect()->back()->withInput()->with('error', 'Sistem menolak: Hanya diizinkan memiliki 1 akun Administrator.');
            }
        }

        $data = [
            'username'      => $this->request->getPost('username'),
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'          => $roleInput,
            'is_active'     => 1
        ];

        $this->userModel->insert($data);
        $insertId = $this->userModel->getInsertID(); // Ambil ID pengguna yang baru dibuat

        // 🎥 CATAT LOG: CREATE
        $logData = $data;
        unset($logData['password_hash']); // Jangan simpan password di log
        log_activity('CREATE', 'users', $insertId, null, $logData);

        return redirect()->to('/panel/users')->with('success', 'Akun berhasil ditambahkan.');
    }

    // 4. Form Edit Pengguna 
    public function edit($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/users');

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Format tidak valid');
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/users')->with('error', 'Akses ditolak: URL tidak valid atau dimanipulasi.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $roleModel = new RoleModel();
        $adminExists = $this->userModel->where('role', 'admin')->countAllResults() > 0;

        return view('backend/users/edit', [
            'title'       => 'Edit Pengguna',
            'user'        => $user,
            'roles'       => $roleModel->findAll(),
            'adminExists' => $adminExists,
            'safeId'      => $safeId
        ]);
    }

    // 5. Proses Update Pengguna 
    public function update($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/users');

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Format tidak valid');
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/users')->with('error', 'Akses ditolak: URL tidak valid atau dimanipulasi.');
        }

        $userTarget = $this->userModel->find($id);
        if (!$userTarget) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'username'     => "required|alpha_numeric|is_unique[users.username,id,{$id}]",
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'         => 'required'
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleInput = $this->request->getPost('role');
        $isSelf = session()->get('user_id') == $id;
        $isAdmin = $userTarget['role'] === 'admin';

        if ($isSelf || $isAdmin) {
            $roleInput = $userTarget['role'];
        }

        if ($roleInput === 'admin' && !$isAdmin) {
            $adminExists = $this->userModel->where('role', 'admin')->countAllResults() > 0;
            if ($adminExists) {
                return redirect()->back()->withInput()->with('error', 'Sistem menolak: Tidak bisa menaikkan akun menjadi Administrator karena kuota Admin (1) sudah penuh.');
            }
        }

        $data = [
            'username'     => $this->request->getPost('username'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
            'role'         => $roleInput,
        ];

        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);

        // 🎥 CATAT LOG: UPDATE
        $oldData = $userTarget;
        unset($oldData['password_hash'], $oldData['created_at'], $oldData['updated_at']); // Bersihkan data log
        $newData = $data;
        unset($newData['password_hash']);
        log_activity('UPDATE', 'users', $id, $oldData, $newData);

        return redirect()->to('/panel/users')->with('success', 'Akun berhasil diperbarui.');
    }

    // 6. Hapus Pengguna (HARD DELETE)
    public function delete($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/users');

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Format tidak valid');
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/users')->with('error', 'Akses ditolak: URL tidak valid atau dimanipulasi.');
        }

        if (session()->get('user_id') == $id) {
            return redirect()->to('/panel/users')->with('error', 'Aksi ditolak: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userTarget = $this->userModel->find($id);

        if (!$userTarget) {
            return redirect()->to('/panel/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($userTarget['role'] === 'admin') {
            return redirect()->to('/panel/users')->with('error', 'Aksi ditolak: Akun Administrator tidak boleh dihapus.');
        }

        $this->userModel->delete($id);

        // 🎥 CATAT LOG: DELETE
        $oldData = $userTarget;
        unset($oldData['password_hash']);
        log_activity('DELETE', 'users', $id, $oldData, null);

        return redirect()->to('/panel/users')->with('success', 'Akun berhasil dihapus permanen.');
    }

    // 7. Toggle Status (Aktif/Nonaktif)
    public function toggleStatus($safeId = null)
    {
        if (!$safeId) return redirect()->to('/panel/users');

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Invalid');
            $id = $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return redirect()->to('/panel/users')->with('error', 'Akses ditolak.');
        }

        $userTarget = $this->userModel->find($id);

        if (session()->get('user_id') == $id) {
            return redirect()->to('/panel/users')->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }

        if ($userTarget['role'] === 'admin') {
            return redirect()->to('/panel/users')->with('error', 'Akun Administrator Utama tidak boleh dinonaktifkan.');
        }

        $newStatus = $userTarget['is_active'] == 1 ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        // 🎥 CATAT LOG: TOGGLE STATUS
        $oldData = ['is_active' => $userTarget['is_active']];
        $newData = ['is_active' => $newStatus];
        log_activity('TOGGLE_STATUS', 'users', $id, $oldData, $newData);

        $pesan = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/panel/users')->with('success', "Akun berhasil $pesan.");
    }
}
