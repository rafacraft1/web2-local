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
        'password',
        'role',
        'avatar',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // --- Task: Validasi dipindah ke Model (Fat Model) ---
    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'username'     => 'required|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'password'     => 'permit_empty|min_length[6]', // permit_empty digunakan saat update jika password tidak diubah
        'role'         => 'required|in_list[admin,operator]',
    ];
}
