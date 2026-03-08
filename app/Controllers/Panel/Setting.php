<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;

class Setting extends BaseController
{
    protected $db;

    public function __construct()
    {
        // Inisialisasi koneksi database
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman pengaturan
     */
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

    /**
     * Memperbarui pengaturan website
     */
    public function update()
    {
        $postData = $this->request->getPost();

        // Data untuk pencatatan Audit Log
        $oldValuesLog = [];
        $newValuesLog = [];
        $berhasilDiupdate = 0;

        // Daftar file yang diproses dan foldernya
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

        // --- Task 2.3: Menggunakan Database Transaction ---
        $this->db->transStart();

        // 1. Pemrosesan Upload Gambar
        foreach ($filesToProcess as $inputName => $folder) {
            $file = $this->request->getFile($inputName);

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validasi tipe file gambar
                $validationRule = [
                    $inputName => "is_image[{$inputName}]|mime_in[{$inputName},image/webp,image/png,image/jpeg,image/jpg]"
                ];

                if ($this->validate($validationRule)) {
                    // Ambil data lama untuk dihapus filenya
                    $existing = $this->db->table('settings')->where('setting_key', $inputName)->get()->getRowArray();

                    if ($existing && !empty($existing['setting_value'])) {
                        // Task 2.2: Gunakan fungsi DRY untuk hapus file fisik
                        $this->hapusFileLama($folder, $existing['setting_value']);
                    }

                    $newName = $file->getRandomName();
                    $file->move(FCPATH . $folder, $newName);

                    // Masukkan ke postData agar ikut diproses di loop update database
                    $postData[$inputName] = $newName;
                } else {
                    $this->db->transRollback();
                    return redirect()->back()->withInput()->with('error', "Gagal mengunggah {$inputName}. Format file tidak didukung.");
                }
            }
        }

        // 2. Update Database (Text & Nama File Baru)
        foreach ($postData as $key => $value) {
            // Abaikan token CSRF
            if ($key === csrf_token()) continue;

            $existing = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();

            if ($existing) {
                // Hanya update jika nilainya berubah
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
                // Jika key baru (Insert)
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

        // 3. Simpan Log Aktivitas jika ada perubahan
        if (!empty($newValuesLog)) {
            log_activity('UPDATE', 'settings', null, $oldValuesLog, $newValuesLog);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pengaturan.');
        }

        return redirect()->to('/panel/settings')->with('success', "Berhasil memperbarui {$berhasilDiupdate} pengaturan.");
    }

    /**
     * Task 2.2: Fungsi DRY (Private) untuk menghapus file lama
     */
    private function hapusFileLama($folder, $filename)
    {
        $path = FCPATH . $folder . '/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function toggleMaintenance()
    {
        // Pastikan request benar-benar dari AJAX
        if ($this->request->isAJAX()) {
            $status = $this->request->getPost('status'); // '1' atau '0'

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

            // Catat log aktivitas secara spesifik
            log_activity(
                'UPDATE',
                'settings',
                null,
                ['maintenance_mode' => $existing['setting_value'] ?? '0'],
                ['maintenance_mode' => $status]
            );

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success'  => false,
                    'message'  => 'Terjadi kesalahan pada database.',
                    'csrfHash' => csrf_hash() // Kirim hash baru untuk keamanan
                ]);
            }

            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Status maintenance berhasil diubah.',
                'status'   => $status,
                'csrfHash' => csrf_hash() // Kirim hash baru untuk keamanan request selanjutnya
            ]);
        }

        return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
    }
}
