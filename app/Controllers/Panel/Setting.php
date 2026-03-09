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

        $oldValuesLog = [];
        $newValuesLog = [];
        $berhasilDiupdate = 0;

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

        // 1. Task 2: Pindah Pemrosesan Upload Gambar ke LUAR transaksi DB
        foreach ($filesToProcess as $inputName => $folder) {
            $file = $this->request->getFile($inputName);

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $validationRule = [
                    $inputName => "is_image[{$inputName}]|mime_in[{$inputName},image/webp,image/png,image/jpeg,image/jpg]"
                ];

                if ($this->validate($validationRule)) {
                    $existing = $this->db->table('settings')->where('setting_key', $inputName)->get()->getRowArray();

                    if ($existing && !empty($existing['setting_value'])) {
                        $this->hapusFileLama($folder, $existing['setting_value']);
                    }

                    $newName = $file->getRandomName();
                    $file->move(FCPATH . $folder, $newName);

                    $postData[$inputName] = $newName;
                } else {
                    return redirect()->back()->withInput()->with('error', "Gagal mengunggah {$inputName}. Format file tidak didukung.");
                }
            }
        }

        // --- Transaksi Database Dimulai setelah I/O selesai ---
        $this->db->transStart();

        // 2. Update Database (Text & Nama File Baru)
        foreach ($postData as $key => $value) {
            if ($key === csrf_token()) continue;

            $existing = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();

            if ($existing) {
                if ($existing['setting_value'] !== $value) {
                    $oldValuesLog[$key] = $existing['setting_value'];
                    $newValuesLog[$key] = $value;

                    $this->db->table('settings')
                        ->where('setting_key', $key)
                        ->update([
                            'setting_value' => $value,
                            'updated_at'    => date('Y-m-d H:i:s')
                        ]);
                    $berhasilDiupdate++;
                }
            } else {
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

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pengaturan.');
        }

        // 3. Task 6: Simpan Log Aktivitas setelah transaksi DB sukses
        if (!empty($newValuesLog)) {
            log_activity('UPDATE', 'settings', null, $oldValuesLog, $newValuesLog);
        }

        return redirect()->to('/panel/settings')->with('success', "Berhasil memperbarui {$berhasilDiupdate} pengaturan.");
    }

    private function hapusFileLama($folder, $filename)
    {
        $path = FCPATH . $folder . '/' . $filename;
        if (is_file($path)) unlink($path);
    }

    public function toggleMaintenance()
    {
        if ($this->request->isAJAX()) {
            $status = $this->request->getPost('status');

            $this->db->transStart();

            $existing = $this->db->table('settings')->where('setting_key', 'maintenance_mode')->get()->getRowArray();

            if ($existing) {
                $this->db->table('settings')
                    ->where('setting_key', 'maintenance_mode')
                    ->update(['setting_value' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $this->db->table('settings')->insert([
                    'setting_group' => 'general',
                    'setting_key'   => 'maintenance_mode',
                    'setting_value' => $status,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success'  => false,
                    'message'  => 'Terjadi kesalahan pada database.',
                    'csrfHash' => csrf_hash()
                ]);
            }

            // Task 6: Log aktivitas dipindah ke sini
            log_activity('UPDATE', 'settings', null, ['maintenance_mode' => $existing['setting_value'] ?? '0'], ['maintenance_mode' => $status]);

            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Status maintenance berhasil diubah.',
                'status'   => $status,
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
    }
}