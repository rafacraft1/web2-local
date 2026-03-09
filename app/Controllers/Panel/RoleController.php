<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\MenuModel;
use App\Models\RoleMenuAccessModel;
use App\Models\RolePermissionModel;

class RoleController extends BaseController
{
    public function index()
    {
        $roleModel = new RoleModel();

        $data = [
            'title' => 'Manajemen Hak Akses Role',
            'roles' => $roleModel->findAll()
        ];

        return view('backend/roles/index', $data);
    }

    public function access($slug_role)
    {
        $roleModel     = new RoleModel();
        $menuModel     = new MenuModel();
        $roleMenuModel = new RoleMenuAccessModel();

        $role = $roleModel->find($slug_role);
        if (!$role) {
            return redirect()->to('panel/roles')->with('error', 'Role tidak ditemukan.');
        }

        // Ambil semua menu yang aktif
        $menus = $menuModel->where('is_active', 1)->orderBy('urutan', 'ASC')->findAll();

        // Ambil ID menu yang saat ini dimiliki oleh role tersebut
        $currentAccess = $roleMenuModel->where('slug_role', $slug_role)->findAll();
        $roleMenus     = array_column($currentAccess, 'menu_id');

        $data = [
            'title'      => 'Atur Akses: ' . $role['nama_role'],
            'role'       => $role,
            'menus'      => $menus,
            'role_menus' => $roleMenus
        ];

        return view('backend/roles/access', $data);
    }

    public function saveAccess($slug_role)
    {
        $roleMenuModel = new RoleMenuAccessModel();
        $rolePermModel = new RolePermissionModel();
        $menuModel     = new MenuModel();

        // Array ID menu yang dicentang dari form
        $menu_ids = $this->request->getPost('menu_id') ?? [];

        // 1. Kosongkan akses lama untuk role ini
        $roleMenuModel->where('slug_role', $slug_role)->delete();
        $rolePermModel->where('slug_role', $slug_role)->delete();

        if (!empty($menu_ids)) {
            $menuData = [];
            $permData = [];

            // Modul default yang selalu harus bisa diakses (Mencegah Infinite Loop)
            $permData[] = ['slug_role' => $slug_role, 'nama_modul' => 'dashboard'];
            $permData[] = ['slug_role' => $slug_role, 'nama_modul' => 'profile'];

            foreach ($menu_ids as $menu_id) {
                // Siapkan data insert untuk role_menu_access (Sidebar UI)
                $menuData[] = [
                    'slug_role' => $slug_role,
                    'menu_id'   => $menu_id
                ];

                // Auto-sync ke tabel role_permissions (Filter Security)
                $menu = $menuModel->find($menu_id);
                if ($menu) {
                    $urlParts = explode('/', trim($menu['url'], '/'));
                    // Contoh: 'panel/berita' -> index ke-1 adalah 'berita'
                    if (isset($urlParts[1])) {
                        $moduleName = $urlParts[1];

                        // Cek agar tidak duplikat modul (jika ada 2 menu dengan modul yang sama)
                        if (!in_array($moduleName, array_column($permData, 'nama_modul'))) {
                            $permData[] = [
                                'slug_role'  => $slug_role,
                                'nama_modul' => $moduleName
                            ];
                        }
                    }
                }
            }

            // 2. Insert akses menu dan permission yang baru
            if (!empty($menuData)) $roleMenuModel->insertBatch($menuData);
            if (!empty($permData)) $rolePermModel->insertBatch($permData);
        }

        return redirect()->to('/panel/roles')->with('success', 'Hak akses menu berhasil diperbarui. User dengan role ini mungkin perlu relogin untuk melihat perubahan.');
    }
}
