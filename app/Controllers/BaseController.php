<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['audit'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // =======================================================
        // [BARU] LOGIKA PENARIKAN MENU DINAMIS UNTUK SIDEBAR
        // =======================================================
        $role = session()->get('role'); // Ambil role user dari session
        $dynamicMenus = [];

        // Jika ada user yang login (memiliki role)
        if ($role) {
            $menuModel = new \App\Models\MenuModel();
            $dynamicMenus = $menuModel->getMenuForRole($role);
        }

        // Bagikan variabel $dynamicMenus ini ke semua file View (.php) di sistem
        \Config\Services::renderer()->setData(['dynamicMenus' => $dynamicMenus]);
    }

    /**
     * Mendekripsi $safeId (Hex/Encrypted) menjadi ID asli (Integer).
     */
    protected function decryptId($safeId)
    {
        if (!$safeId) return false;

        $encrypter = \Config\Services::encrypter();
        try {
            if (!ctype_xdigit($safeId)) throw new \Exception('Format bukan Hexadecimal');
            return $encrypter->decrypt(hex2bin($safeId));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Memproses, memvalidasi keamanan, dan menyimpan gambar Base64.
     * Dapat digunakan oleh seluruh Controller.
     * * @return array ['success' => bool, 'error' => string|null]
     */
    protected function processBase64Image($imageBase64, $folderName, $fileName)
    {
        // 1. Cek ukuran batas wajar (Sekitar 1.5MB - 2MB)
        if (strlen($imageBase64) > 2000000) {
            return ['success' => false, 'error' => 'Keamanan: Ukuran gambar terlalu besar (Maksimal ~1.5MB).'];
        }

        // 2. Cek format payload Base64
        $imageParts = explode(';base64,', $imageBase64);
        if (count($imageParts) != 2) {
            return ['success' => false, 'error' => 'Keamanan: Format data gambar tidak valid.'];
        }

        // 3. Decode gambar
        $imageDecoded = base64_decode($imageParts[1]);

        // 4. Verifikasi MIME Type secara murni (Modern PHP 8+)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageDecoded);

        if (!in_array($mimeType, ['image/webp', 'image/jpeg', 'image/png'])) {
            return ['success' => false, 'error' => 'Keamanan: File ditolak! Bukan gambar murni (Terdeteksi: ' . $mimeType . ').'];
        }

        // 5. Pastikan direktori tujuan tersedia
        $uploadPath = FCPATH . 'uploads/' . $folderName . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // 6. Simpan gambar secara fisik
        file_put_contents($uploadPath . $fileName, $imageDecoded);

        return ['success' => true, 'error' => null];
    }
}
