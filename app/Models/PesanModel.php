<?php

namespace App\Models;

use CodeIgniter\Model;

class PesanModel extends Model
{
    protected $table            = 'pesan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Hard delete untuk pesan

    protected $allowedFields    = [
        'name',
        'email',
        'subject',
        'message',
        'is_read'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // Fungsi tambahan untuk menghitung jumlah pesan yang belum dibaca (Berguna untuk badge di Sidebar menu nanti)
    public function getUnreadCount()
    {
        return $this->where('is_read', 0)->countAllResults();
    }
}
