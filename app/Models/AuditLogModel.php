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
        $builder = $this->orderBy($this->table . '.created_at', 'DESC');

        if (!empty($filters['user_id'])) {
            $builder->where($this->table . '.user_id', $filters['user_id']);
        }
        if (!empty($filters['module'])) {
            $builder->where($this->table . '.module', $filters['module']);
        }
        if (!empty($filters['action'])) {
            $builder->where($this->table . '.action', $filters['action']);
        }

        // paginate() mengembalikan array record berdasarkan returnType kelas
        return $this->paginate(15, 'audit');
    }

    /**
     * Mengambil daftar user unik yang pernah melakukan aktivitas
     */
    public function getUniqueUsers(): array
    {
        return $this->select($this->table . '.user_id, MAX(' . $this->table . '.nama_user) as nama_user')
            ->where($this->table . '.user_id IS NOT NULL')
            ->groupBy($this->table . '.user_id')
            ->findAll();
    }
}