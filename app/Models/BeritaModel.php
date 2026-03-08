<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaModel extends Model
{
    protected $table            = 'berita';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

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

    // --- Task 2.1: Validasi dipindah ke Model (Fat Model) ---
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'title'    => 'required|min_length[5]|is_unique[berita.title,id,{id}]',
        'category' => 'required',
        'status'   => 'required|in_list[draft,published]',
        'excerpt'  => 'required|max_length[255]',
        'content'  => 'required|min_length[20]',
    ];

    // --- Task 3.1: Tambahkan Return Type Declarations ---
    public function getBeritaWithAuthor(): array
    {
        return $this->select('berita.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = berita.user_id', 'left')
            ->orderBy('berita.created_at', 'DESC')
            ->findAll();
    }
}
