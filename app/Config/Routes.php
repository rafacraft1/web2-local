<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->set404Override('App\Controllers\Home::error404');


// ==========================================
// 1. RUTE FRONTEND (Area Publik)
// ==========================================
$routes->get('/', 'Home::index');
$routes->get('kontak', 'Home::kontak');
$routes->post('kontak/kirim', 'Home::kirimPesan');
$routes->get('berita', 'Home::berita');
$routes->get('berita/(:segment)', 'Home::detailBerita/$1');
$routes->get('jurusan', 'Home::jurusan');
$routes->get('profil', 'Home::profil');
$routes->get('galeri', 'Home::galeri');

// ==========================================
// 2. RUTE AUTENTIKASI (Login & Logout)
// ==========================================
$routes->get('panel/login', 'Auth::index');
$routes->post('panel/login/process', 'Auth::process');
$routes->get('panel/logout', 'Auth::logout');

// ==========================================
// 3. RUTE PANEL ADMIN (Dengan Filter Role)
// ==========================================
$routes->group('panel', ['filter' => 'roleCheck', 'namespace' => 'App\Controllers\Panel'], function ($routes) {

    // Akses '/panel' akan otomatis dialihkan ke '/panel/dashboard'
    $routes->get('/', static function () {
        return redirect()->to('panel/dashboard');
    });
    $routes->get('dashboard', 'Dashboard::index');

    // --- Profil Saya & Keamanan ---
    $routes->group('profile', function ($routes) {
        $routes->get('/', 'Profile::index');
        $routes->post('updateInfo', 'Profile::updateInfo');
        $routes->post('updatePassword', 'Profile::updatePassword');
    });

    // --- Settings (Pengaturan) ---
    $routes->group('settings', function ($routes) {
        $routes->get('/', 'Setting::index');
        $routes->post('update', 'Setting::update');
        $routes->post('toggle-maintenance', 'Setting::toggleMaintenance');
    });

    // --- Log Aktivitas ---
    $routes->get('audit-logs', 'AuditController::index');

    // --- Pesan Masuk ---
    $routes->group('pesan', function ($routes) {
        $routes->get('/', 'PesanController::index');
        $routes->get('read/(:segment)', 'PesanController::show/$1');
        $routes->delete('delete/(:segment)', 'PesanController::delete/$1');
    });


    // ==========================================
    // Refactor Rute CRUD (Array Looping)
    // ==========================================

    // Manajemen Pengguna (Dipisah karena punya rute tambahan seperti 'toggle' dan post '/')
    $routes->group('users', function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('/', 'UserController::store');
        $routes->get('edit/(:segment)', 'UserController::edit/$1');
        $routes->post('update/(:segment)', 'UserController::update/$1');
        $routes->post('toggle/(:segment)', 'UserController::toggleStatus/$1');
        $routes->delete('delete/(:segment)', 'UserController::delete/$1');
    });

    // Modul Standar CRUD (Berita, Jurusan, Galeri, Mitra)
    $crudModules = [
        'berita'  => 'BeritaController',
        'jurusan' => 'JurusanController',
        'galeri'  => 'GaleriController',
        'mitra'   => 'MitraController'
    ];

    // Melakukan perulangan untuk membuat rute secara otomatis
    foreach ($crudModules as $path => $controller) {
        $routes->group($path, function ($routes) use ($controller) {
            $routes->get('/', "$controller::index");
            $routes->get('create', "$controller::create");
            $routes->post('store', "$controller::store");
            $routes->get('edit/(:segment)', "$controller::edit/$1");
            $routes->post('update/(:segment)', "$controller::update/$1");
            $routes->delete('delete/(:segment)', "$controller::delete/$1");
        });
    }

    // --- Manajemen Role & Hak Akses ---
    $routes->group('roles', function ($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->get('access/(:segment)', 'RoleController::access/$1');
        $routes->post('saveAccess/(:segment)', 'RoleController::saveAccess/$1');
    });
});
