<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'slug_role';
    protected $returnType = 'array';
    protected $allowedFields = ['slug_role', 'nama_role', 'deskripsi'];
}