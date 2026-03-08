<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Setting extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $rawSettings = $this->db->table('settings')->get()->getResultArray();

        $settings = [];
        foreach ($rawSettings as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $data = [
            'title'    => 'Pengaturan Website | Panel Admin',
            'settings' => $settings
        ];

        return view('backend/settings', $data);
    }

    public function update()
    {
        $postData = $this->request->getPost();

        // Array untuk menampung data lama dan baru khusus untuk Audit Log
        $oldValuesLog = [];
        $newValuesLog = [];

        // --- PROSES UPLOAD GAMBAR YANG SUDAH ADA SEBELUMNYA ---
        $filesToProcess = [
            'logo_image'           => 'uploads/logo',
            'hero_image'           => 'uploads/hero',
            'header_berita_image'  => 'uploads/hero',
            'header_jurusan_image' => 'uploads/hero',
            'header_profil_image'  => 'uploads/hero',
            'header_galeri_image'  => 'uploads/hero',
            'header_kontak_image'  => 'uploads/hero',
            'profil_sejarah_image' => 'uploads/hero',
            'profil_kepsek_image'  => 'uploads/hero'
        ];

        foreach ($filesToProcess as $inputName => $folder) {
            $file = $this->request->getFile($inputName);

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $aturanValidasi = [
                    $inputName => "is_image[{$inputName}]|mime_in[{$inputName},image/webp,image/png,image/jpeg,image/jpg]"
                ];

                if ($this->validate($aturanValidasi)) {
                    $oldData = $this->db->table('settings')->where('setting_key', $inputName)->get()->getRowArray();

                    if ($oldData && !empty($oldData['setting_value']) && file_exists(FCPATH . $folder . '/' . $oldData['setting_value'])) {
                        unlink(FCPATH . $folder . '/' . $oldData['setting_value']);
                    }

                    $newName = $file->getRandomName();
                    $file->move(FCPATH . $folder, $newName);

                    // Masukkan nama file baru ke postData agar ikut diproses di loop bawah
                    $postData[$inputName] = $newName;
                } else {
                    return redirect()->back()->withInput()->with('error', "Gagal mengunggah gambar {$inputName}. File tidak valid.");
                }
            }
        }

        $berhasilDiupdate = 0;

        // Loop untuk mengecek setiap data yang dikirimkan
        foreach ($postData as $key => $value) {
            if ($key !== csrf_token()) {

                $existing = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();

                if ($existing) {
                    // Hanya lakukan update dan pencatatan log JIKA nilainya benar-benar berubah
                    if ($existing['setting_value'] !== $value) {

                        // Catat ke array log
                        $oldValuesLog[$key] = $existing['setting_value'];
                        $newValuesLog[$key] = $value;

                        // Update ke database
                        $this->db->table('settings')
                            ->where('setting_key', $key)
                            ->update([
                                'setting_value' => $value,
                                'updated_at'    => date('Y-m-d H:i:s')
                            ]);
                        $berhasilDiupdate++;
                    }
                } else {
                    // Jika data belum ada (Insert Baru)
                    $oldValuesLog[$key] = null;
                    $newValuesLog[$key] = $value;

                    $this->db->table('settings')->insert([
                        'setting_group' => 'general',
                        'setting_key'   => $key,
                        'setting_value' => $value,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    ]);
                    $berhasilDiupdate++;
                }
            }
        }

        // --- SIMPAN LOG AKTIVITAS ---
        // Jika array $newValuesLog tidak kosong (artinya ada data yang benar-benar dirubah)
        if (!empty($newValuesLog)) {
            // Catat menggunakan helper audit. record_id bernilai null karena ini tabel settings
            log_activity('UPDATE', 'settings', null, $oldValuesLog, $newValuesLog);
        }

        return redirect()->to('/panel/settings')->with('success', 'Berhasil memperbarui ' . $berhasilDiupdate . ' pengaturan!');
    }
}
