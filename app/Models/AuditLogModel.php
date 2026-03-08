<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    /**
     * Mengambil data log lengkap dengan Filter
     */
    public function getLogsWithFilter($filters = []): array
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['module'])) {
            $builder->where('module', $filters['module']);
        }
        if (!empty($filters['action'])) {
            $builder->where('action', $filters['action']);
        }

        // paginate() mengembalikan array record berdasarkan returnType kelas
        return $this->paginate(15, 'audit');
    }

    /**
     * Mengambil daftar user unik yang pernah melakukan aktivitas
     */
    public function getUniqueUsers(): array
    {
        return $this->select('user_id, MAX(nama_user) as nama_user')
            ->where('user_id IS NOT NULL')
            ->groupBy('user_id')
            ->findAll();
    }
}
