<?php

namespace App\Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class FullAppTest extends CIUnitTestCase
{
    // Mengaktifkan fitur test database & simulasi request browser
    use DatabaseTestTrait, FeatureTestTrait;

    // 1. BAGIAN OTOMATISASI DATABASE & SEEDER
    protected $migrate     = true;  // Jalankan semua migration otomatis
    protected $migrateOnce = false; // Reset database setiap kali test berjalan
    protected $refresh     = true;  // Refresh tabel
    protected $namespace   = 'App'; // Wajib tambahkan ini agar membaca folder app/
    protected $seed        = 'App\Database\Seeds\InitSeeder'; // Jalankan InitSeeder.php otomatis

    public function testAlurSistemSecaraPenuh()
    {
        // 2. TEST HALAMAN LOGIN
        // Mengecek apakah halaman login bisa diakses dengan baik (Status 200 OK)
        $halamanLogin = $this->get('/panel/login');
        $halamanLogin->assertOK();

        // 3. TEST PROSES LOGIN
        // Simulasi user submit form login (method POST) menggunakan password dari seeder
        $prosesLogin = $this->post('/panel/login/process', [
            'username' => 'admin',
            'password' => 'admin123'
        ]);

        // Cek apakah setelah login diarahkan ke dashboard
        $prosesLogin->assertRedirectTo('/panel/dashboard');

        // 4. TEST FORM INPUT (Menambah Data Berita)
        // Simulasi kita sudah login dengan menyisipkan session lengkap agar lolos RoleFilter
        $simpanBerita = $this->withSession([
            'isLoggedIn' => true, // KUNCI UTAMA AGAR LOLOS FILTER
            'user_id'    => 1,
            'username'   => 'admin',
            'role'       => 'admin'
        ])
            ->post('/panel/berita/store', [
                'title'        => 'Berita Testing CI4',
                'excerpt'      => 'Ringkasan testing otomatis.',
                'content'      => 'Ini adalah isi konten yang dikirim otomatis oleh PHPUnit.',
                'category'     => 'Umum',
                'status'       => 'published',
                // Gambar PNG transparan 1x1 pixel (base64) agar lolos pengecekan controller
                'image_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
            ]);

        // Pastikan proses simpan berhasil dan di-redirect kembali (tidak ada error 500)
        $simpanBerita->assertRedirect();

        // 5. TEST CEK DATABASE (Verifikasi Data Masuk)
        // Membuktikan bahwa input dari simulasi form di atas benar-benar masuk ke tabel berita
        $this->seeInDatabase('berita', [
            'title' => 'Berita Testing CI4'
        ]);

        // (Opsional) Membuktikan Seeder benar-benar masuk
        $this->seeInDatabase('users', [
            'username' => 'admin'
        ]);
    }
}
