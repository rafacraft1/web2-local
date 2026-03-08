<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // Kita hanya perlu izin membaca (read), tidak perlu allowedFields untuk update dari sisi aplikasi
    protected $useTimestamps = false;

    // Fungsi untuk mengambil data log lengkap dengan Filter
    public function getLogsWithFilter($filters = [])
    {
        $builder = $this->orderBy('created_at', 'DESC');

        // Filter berdasarkan Akun (User)
        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        // Filter berdasarkan Modul
        if (!empty($filters['module'])) {
            $builder->where('module', $filters['module']);
        }

        // Filter berdasarkan Aksi
        if (!empty($filters['action'])) {
            $builder->where('action', $filters['action']);
        }

        // Gunakan pagination (15 data per halaman)
        return $this->paginate(15, 'audit');
    }

    // Mengambil daftar user unik yang pernah melakukan aktivitas (untuk dropdown filter)
    public function getUniqueUsers()
    {
        // Gunakan MAX(nama_user) untuk menghindari error sql_mode=only_full_group_by
        return $this->select('user_id, MAX(nama_user) as nama_user')
            ->where('user_id IS NOT NULL')
            ->groupBy('user_id')
            ->findAll();
    }
}
