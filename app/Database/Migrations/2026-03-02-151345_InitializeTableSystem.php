<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitializeTableSystem extends Migration
{
    public function up()
    {
        // ==========================================
        // 1. TABEL ROLES
        // ==========================================
        $this->forge->addField([
            'slug_role'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_role'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('slug_role', true);
        $this->forge->createTable('roles');

        // ==========================================
        // 2. TABEL ROLE PERMISSIONS
        // ==========================================
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug_role'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_modul'  => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['slug_role', 'nama_modul']);
        $this->forge->addForeignKey('slug_role', 'roles', 'slug_role', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions');

        // ==========================================
        // 3. TABEL USERS
        // ==========================================
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'nama_lengkap'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'guru'],
            'avatar'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], // 1 = Aktif, 0 = Nonaktif
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role', 'roles', 'slug_role', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');

        // ==========================================
        // 4. TABEL BERITA
        // ==========================================
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'excerpt'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // [BARU] Untuk teks pendek di Card Frontend
            'content'    => ['type' => 'TEXT'],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'     => ['type' => 'ENUM', 'constraint' => ['draft', 'published'], 'default' => 'draft'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('category');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('berita');

        // ==========================================
        // 5. TABEL GALERI (Bisa untuk Karya Siswa & Fasilitas)
        // ==========================================
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255], // Untuk nama karya
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // Untuk nama pembuat (Cth: Tim RPL)
            'type'        => ['type' => 'ENUM', 'constraint' => ['karya', 'kegiatan', 'fasilitas'], 'default' => 'kegiatan'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('type');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('galeri');

        // ==========================================
        // 6. TABEL JURUSAN
        // ==========================================
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'short_desc'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // [BARU] Teks pendek untuk Card
            'description' => ['type' => 'TEXT'], // Untuk halaman detail jurusan (bisa HTML)
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // Untuk emoji/font-awesome
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // Gambar ilustrasi
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jurusan');

        // ==========================================
        // 7. TABEL PESAN (Inbox/Kontak)
        // ==========================================
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'subject'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'message'    => ['type' => 'TEXT'],
            'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('is_read');
        $this->forge->createTable('pesan');

        // ==========================================
        // 8. TABEL SETTINGS
        // ==========================================
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'setting_group' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'general'],
            'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'setting_value' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('setting_group');
        $this->forge->createTable('settings');

        // ==========================================
        // 9. TABEL MITRA (Untuk Marquee/Logo Perusahaan)
        // ==========================================
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 255], // Contoh: Tokopedia
            'logo'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mitra');

        // ==========================================
        // 10. TABEL AUDIT LOGS
        // ==========================================
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_user'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'module'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'record_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'old_values'  => ['type' => 'TEXT', 'null' => true],
            'new_values'  => ['type' => 'TEXT', 'null' => true],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('module');
        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('mitra', true);
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('pesan', true);
        $this->forge->dropTable('jurusan', true);
        $this->forge->dropTable('galeri', true);
        $this->forge->dropTable('berita', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('roles', true);
    }
}
