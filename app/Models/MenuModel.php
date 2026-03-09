<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menus';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['title', 'url', 'icon', 'is_active', 'urutan'];

    // Fungsi khusus untuk mengambil menu sesuai Role (Hak Akses)
    public function getMenuForRole($slug_role)
    {
        return $this->select('menus.*')
            ->join('role_menu_access', 'role_menu_access.menu_id = menus.id')
            ->where('role_menu_access.slug_role', $slug_role)
            ->where('menus.is_active', 1)
            ->orderBy('menus.urutan', 'ASC')
            ->findAll();
    }
}