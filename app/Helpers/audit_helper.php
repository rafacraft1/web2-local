<?php

/**
 * Fungsi untuk merekam log aktivitas pengguna
 * * @param string $action      Contoh: CREATE, UPDATE, DELETE, LOGIN, dll
 * @param string $module      Contoh: users, berita, settings, dll
 * @param int|null $record_id ID dari data yang diubah/dihapus (Opsional)
 * @param array|null $old     Data lama sebelum diubah (Opsional)
 * @param array|null $new     Data baru setelah diubah (Opsional)
 */
function log_activity($action, $module, $record_id = null, $old = null, $new = null)
{
    $db = \Config\Database::connect();
    $request = \Config\Services::request();

    // Siapkan data log
    $logData = [
        'user_id'    => session()->get('user_id'),
        'nama_user'  => session()->get('nama_lengkap'), // Diambil dari session login
        'action'     => strtoupper($action),
        'module'     => strtolower($module),
        'record_id'  => $record_id,
        'old_values' => $old ? json_encode($old) : null, // Ubah array ke JSON
        'new_values' => $new ? json_encode($new) : null, // Ubah array ke JSON
        'ip_address' => $request->getIPAddress(),
        'user_agent' => $request->getUserAgent()->getAgentString(),
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Simpan ke tabel audit_logs
    $db->table('audit_logs')->insert($logData);
}
