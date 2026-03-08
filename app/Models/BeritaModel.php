<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaModel extends Model
{
    protected $table            = 'berita';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Hard delete

    protected $allowedFields    = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'status'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // Mengambil semua berita beserta nama penulisnya
    public function getBeritaWithAuthor()
    {
        return $this->select('berita.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = berita.user_id', 'left') // LEFT JOIN dengan tabel users
            ->orderBy('berita.created_at', 'DESC')
            ->findAll();
    }
}
