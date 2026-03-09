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

    // --- Task 3.1 & Task 5: Return Type Declarations & Hindari Hardcode Nama Tabel ---
    public function getBeritaWithAuthor(): array
    {
        return $this->select($this->table . '.*, users.nama_lengkap AS penulis')
            ->join('users', 'users.id = ' . $this->table . '.user_id', 'left')
            ->orderBy($this->table . '.created_at', 'DESC')
            ->findAll();
    }
}