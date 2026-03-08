<?php

namespace App\Models;

use CodeIgniter\Model;

class GaleriModel extends Model
{
    protected $table            = 'galeri';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'title',
        'description',
        'image'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // --- Validasi dipindah ke Model ---
    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'title'       => 'required|min_length[3]',
        'description' => 'required|max_length[255]',
    ];
}
