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

    // --- Task 1.2: Hapus Duplikasi Rute Default ---
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
    // Task 1.1: Refactor Rute CRUD (Route Grouping)
    // ==========================================

    // Manajemen Pengguna
    $routes->group('users', function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('/', 'UserController::store');
        $routes->get('edit/(:segment)', 'UserController::edit/$1');
        $routes->post('update/(:segment)', 'UserController::update/$1');
        $routes->post('toggle/(:segment)', 'UserController::toggleStatus/$1');
        $routes->delete('delete/(:segment)', 'UserController::delete/$1');
    });

    // Program Keahlian (Jurusan)
    $routes->group('jurusan', function ($routes) {
        $routes->get('/', 'JurusanController::index');
        $routes->get('create', 'JurusanController::create');
        $routes->post('store', 'JurusanController::store');
        $routes->get('edit/(:segment)', 'JurusanController::edit/$1');
        $routes->post('update/(:segment)', 'JurusanController::update/$1');
        $routes->delete('delete/(:segment)', 'JurusanController::delete/$1');
    });

    // Kelola Berita / Artikel
    $routes->group('berita', function ($routes) {
        $routes->get('/', 'BeritaController::index');
        $routes->get('create', 'BeritaController::create');
        $routes->post('store', 'BeritaController::store');
        $routes->get('edit/(:segment)', 'BeritaController::edit/$1');
        $routes->post('update/(:segment)', 'BeritaController::update/$1');
        $routes->delete('delete/(:segment)', 'BeritaController::delete/$1');
    });

    // Kelola Galeri
    $routes->group('galeri', function ($routes) {
        $routes->get('/', 'GaleriController::index');
        $routes->get('create', 'GaleriController::create');
        $routes->post('store', 'GaleriController::store');
        $routes->get('edit/(:segment)', 'GaleriController::edit/$1');
        $routes->post('update/(:segment)', 'GaleriController::update/$1');
        $routes->delete('delete/(:segment)', 'GaleriController::delete/$1');
    });

    // Kelola Mitra
    $routes->group('mitra', function ($routes) {
        $routes->get('/', 'MitraController::index');
        $routes->get('create', 'MitraController::create');
        $routes->post('store', 'MitraController::store');
        $routes->get('edit/(:segment)', 'MitraController::edit/$1');
        $routes->post('update/(:segment)', 'MitraController::update/$1');
        $routes->delete('delete/(:segment)', 'MitraController::delete/$1');
    });
});
