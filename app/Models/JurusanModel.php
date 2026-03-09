<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table            = 'jurusan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
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

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => 'required|min_length[3]|is_unique[jurusan.name,id,{id}]',
        'short_desc'  => 'required|max_length[255]',
        'description' => 'required',
        'icon'        => 'required',
    ];
}