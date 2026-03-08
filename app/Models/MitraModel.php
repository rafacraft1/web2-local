<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraModel extends Model
{
    protected $table            = 'mitra';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nama',
        'logo'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // --- Validasi dipindah ke Model ---
    protected $validationRules = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'nama' => 'required|min_length[2]'
    ];
}
