<?php

namespace App\Models;

use CodeIgniter\Model;

class PesanModel extends Model
{
    protected $table            = 'pesan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['nama', 'email', 'subjek', 'pesan', 'is_read'];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // Validasi dipindah ke Model untuk penggunaan di Frontend/Backend
    protected $validationRules = [
        'nama'   => 'required|min_length[3]|max_length[100]',
        'email'  => 'required|valid_email',
        'subjek' => 'required|min_length[5]|max_length[200]',
        'pesan'  => 'required|min_length[10]',
    ];
}
