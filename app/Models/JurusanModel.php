<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table            = 'jurusan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kita biarkan hard delete untuk jurusan agar bersih
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'name',
        'slug',
        'short_desc',
        'description',
        'icon',
        'image'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';
}
