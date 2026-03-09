<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleMenuAccessModel extends Model
{
    protected $table            = 'role_menu_access';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['slug_role', 'menu_id'];
}
