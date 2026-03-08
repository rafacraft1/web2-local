<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        // ==========================================
        // 1. INSERT DATA ROLES (Hak Akses)
        // ==========================================
        $rolesData = [
            [
                'slug_role'  => 'admin',
                'nama_role'  => 'Admin',
                'deskripsi'  => 'Administrator sistem penuh',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug_role'  => 'kepala-sekolah',
                'nama_role'  => 'Kepala Sekolah',
                'deskripsi'  => 'Akses khusus Kepala Sekolah',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug_role'  => 'guru',
                'nama_role'  => 'Guru',
                'deskripsi'  => 'Tenaga Pendidik SMKN 1 Tegalbuleud',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug_role'  => 'staff-tu',
                'nama_role'  => 'Staff TU',
                'deskripsi'  => 'Staff Tata Usaha',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]
        ];
        $this->db->table('roles')->insertBatch($rolesData);

        // ==========================================
        // 2. INSERT DATA SUPER ADMIN
        // ==========================================
        $userData = [
            'username'      => 'admin',
            'nama_lengkap'  => 'Administrator',
            'email'         => 'admin@local.id',
            'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
            'role'          => 'admin',
            'is_active'     => 1, // Pastikan admin langsung aktif
            'created_at'    => $timestamp,
            'updated_at'    => $timestamp,
        ];
        $this->db->table('users')->insert($userData);

        // ==========================================
        // 3. INSERT PENGATURAN WEBSITE (Disesuaikan dengan Frontend)
        // ==========================================
        $settingsData = [
            // --- GROUP: GENERAL ---
            ['setting_group' => 'general', 'setting_key' => 'nama_web', 'setting_value' => 'SMK Kreatif Nusantara', 'created_at' => $timestamp],
            ['setting_group' => 'general', 'setting_key' => 'logo_text_highlight', 'setting_value' => 'SMK', 'created_at' => $timestamp],
            ['setting_group' => 'general', 'setting_key' => 'logo_text_normal', 'setting_value' => 'Kreatif.', 'created_at' => $timestamp],
            ['setting_group' => 'general', 'setting_key' => 'logo_image', 'setting_value' => '', 'created_at' => ''],
            // --- GROUP: HERO SECTION ---
            ['setting_group' => 'hero', 'setting_key' => 'hero_badge', 'setting_value' => '✨ Penerimaan Siswa Baru 2026/2027', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'hero_title', 'setting_value' => 'Mulai Karier Hebatmu, <br> Ciptakan <span class="text-gradient">Karya Nyata.</span>', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'hero_desc', 'setting_value' => 'Kami tidak hanya mengajarkan teori. Di sini, kamu dididik untuk menjadi ahli, praktisi kreatif, dan technopreneur masa depan. Siap melangkah maju?', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'header_berita_image', 'setting_value' => '', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'header_jurusan_image', 'setting_value' => '', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'header_profil_image', 'setting_value' => '', 'created_at' => $timestamp],
            ['setting_group' => 'hero', 'setting_key' => 'header_kontak_image', 'setting_value' => '', 'created_at' => $timestamp],

            // --- GROUP: STATISTIK COUNTER ---
            ['setting_group' => 'stats', 'setting_key' => 'stat_mitra', 'setting_value' => '50', 'created_at' => $timestamp],
            ['setting_group' => 'stats', 'setting_key' => 'stat_fasilitas', 'setting_value' => '100', 'created_at' => $timestamp],
            ['setting_group' => 'stats', 'setting_key' => 'stat_program', 'setting_value' => '6', 'created_at' => $timestamp],
            ['setting_group' => 'stats', 'setting_key' => 'stat_alumni', 'setting_value' => '2000', 'created_at' => $timestamp],

            // --- GROUP: KONTAK & FOOTER ---
            ['setting_group' => 'kontak', 'setting_key' => 'email_kontak', 'setting_value' => 'info@smkkreatif.sch.id', 'created_at' => $timestamp],
            ['setting_group' => 'kontak', 'setting_key' => 'telepon', 'setting_value' => '(022) 1234-5678', 'created_at' => $timestamp],
            ['setting_group' => 'kontak', 'setting_key' => 'whatsapp', 'setting_value' => '6281234567890', 'created_at' => $timestamp],
            ['setting_group' => 'kontak', 'setting_key' => 'alamat', 'setting_value' => 'Jl. Kreativitas No. 99, Bandung', 'created_at' => $timestamp],
            ['setting_group' => 'footer', 'setting_key' => 'footer_desc', 'setting_value' => 'Mencetak generasi unggul yang siap kerja, berkarakter, dan memiliki jiwa wirausaha di era digital.', 'created_at' => $timestamp],

            // --- GROUP: SOSMED ---
            ['setting_group' => 'sosmed', 'setting_key' => 'instagram', 'setting_value' => '#', 'created_at' => $timestamp],
            ['setting_group' => 'sosmed', 'setting_key' => 'facebook', 'setting_value' => '#', 'created_at' => $timestamp],
            ['setting_group' => 'sosmed', 'setting_key' => 'youtube', 'setting_value' => '#', 'created_at' => $timestamp],
            ['setting_group' => 'sosmed', 'setting_key' => 'tiktok', 'setting_value' => '#', 'created_at' => $timestamp],
        ];
        $this->db->table('settings')->insertBatch($settingsData);

        // ==========================================
        // 4. INSERT DATA MITRA (Untuk Animasi Marquee)
        // ==========================================
        $mitraData = [
            ['nama' => 'Gojek', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nama' => 'Tokopedia', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nama' => 'Telkom Indonesia', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nama' => 'Dicoding', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nama' => 'Shopee', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['nama' => 'Bank Mandiri', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];
        $this->db->table('mitra')->insertBatch($mitraData);

        echo "✅ Seeder selesai! Database sudah siap dengan data yang sesuai untuk Layout Frontend.\n";
    }
}
