<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nama_lengkap',
        'username',
        'email',          // [BARU] Kolom email wajib ditambahkan agar bisa disimpan
        'password_hash',  // [DIUBAH] Sesuai dengan nama kolom di Database
        'role',
        'avatar',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id'            => 'permit_empty|is_natural_no_zero',
        'nama_lengkap'  => 'required|min_length[3]|max_length[100]',
        'username'      => 'required|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'email'         => 'required|valid_email|is_unique[users.email,id,{id}]', // [BARU] Validasi Email
        'password_hash' => 'permit_empty|min_length[6]', // [DIUBAH]
        'role'          => 'required|is_not_unique[roles.slug_role]',
    ];
}
