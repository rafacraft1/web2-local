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

$routes->group('panel', ['filter' => 'roleCheck'], function ($routes) {

    // --- Dashboard ---
    $routes->get('/', 'Panel\Dashboard::index');
    $routes->get('dashboard', 'Panel\Dashboard::index');

    // --- Profil Saya & Keamanan ---
    $routes->get('profile', 'Panel\Profile::index');
    $routes->post('profile/updateInfo', 'Panel\Profile::updateInfo');
    $routes->post('profile/updatePassword', 'Panel\Profile::updatePassword');

    // --- Settings (Pengaturan) ---
    $routes->get('settings', 'Panel\Setting::index');
    $routes->post('settings/update', 'Panel\Setting::update');

    // --- Manajemen Pengguna ---
    $routes->get('users', 'Panel\UserController::index');
    $routes->get('users/create', 'Panel\UserController::create');
    $routes->post('users', 'Panel\UserController::store');

    $routes->get('users/edit/(:segment)', 'Panel\UserController::edit/$1');
    $routes->post('users/update/(:segment)', 'Panel\UserController::update/$1');

    // Rute Baru: Toggle Aktif/Nonaktif
    $routes->post('users/toggle/(:segment)', 'Panel\UserController::toggleStatus/$1');

    $routes->delete('users/delete/(:segment)', 'Panel\UserController::delete/$1');

    // --- Log Aktivitas ---
    $routes->get('audit-logs', 'Panel\AuditController::index');

    $routes->get('pesan', 'Panel\PesanController::index');
    $routes->get('pesan/read/(:segment)', 'Panel\PesanController::show/$1');
    $routes->delete('pesan/delete/(:segment)', 'Panel\PesanController::delete/$1');

    $routes->get('jurusan', 'Panel\JurusanController::index');
    $routes->get('jurusan/create', 'Panel\JurusanController::create');
    $routes->post('jurusan/store', 'Panel\JurusanController::store');
    $routes->get('jurusan/edit/(:segment)', 'Panel\JurusanController::edit/$1');
    $routes->post('jurusan/update/(:segment)', 'Panel\JurusanController::update/$1');
    $routes->delete('jurusan/delete/(:segment)', 'Panel\JurusanController::delete/$1');

    // --- Kelola Berita / Artikel ---
    $routes->get('berita', 'Panel\BeritaController::index');
    $routes->get('berita/create', 'Panel\BeritaController::create');
    $routes->post('berita/store', 'Panel\BeritaController::store');
    $routes->get('berita/edit/(:segment)', 'Panel\BeritaController::edit/$1');
    $routes->post('berita/update/(:segment)', 'Panel\BeritaController::update/$1');
    $routes->delete('berita/delete/(:segment)', 'Panel\BeritaController::delete/$1');

    // --- Kelola Galeri ---
    $routes->get('galeri', 'Panel\GaleriController::index');
    $routes->get('galeri/create', 'Panel\GaleriController::create');
    $routes->post('galeri/store', 'Panel\GaleriController::store');
    $routes->get('galeri/edit/(:segment)', 'Panel\GaleriController::edit/$1');
    $routes->post('galeri/update/(:segment)', 'Panel\GaleriController::update/$1');
    $routes->delete('galeri/delete/(:segment)', 'Panel\GaleriController::delete/$1');

    // --- Kelola Mitra ---
    $routes->get('mitra', 'Panel\MitraController::index');
    $routes->get('mitra/create', 'Panel\MitraController::create');
    $routes->post('mitra/store', 'Panel\MitraController::store');
    $routes->get('mitra/edit/(:segment)', 'Panel\MitraController::edit/$1');
    $routes->post('mitra/update/(:segment)', 'Panel\MitraController::update/$1');
    $routes->delete('mitra/delete/(:segment)', 'Panel\MitraController::delete/$1');
});
