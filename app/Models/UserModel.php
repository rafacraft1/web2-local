<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Matikan Soft Delete agar menjadi Hard Delete
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'username',
        'nama_lengkap',
        'email',
        'password_hash',
        'role',
        'avatar',
        'is_active' // Daftarkan kolom is_active
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';
    // deletedField dihapus karena kita pakai Hard Delete

    public function getUserWithRole()
    {
        return $this->select('users.*, roles.nama_role')
            ->join('roles', 'roles.slug_role = users.role')
            ->findAll();
    }
}
