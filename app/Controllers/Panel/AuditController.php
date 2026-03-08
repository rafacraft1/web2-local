<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditController extends BaseController
{
    public function index()
    {
        $auditModel = new AuditLogModel();

        // Tangkap parameter filter dari URL (jika ada)
        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'module'  => $this->request->getGet('module'),
            'action'  => $this->request->getGet('action'),
        ];

        $data = [
            'title'   => 'Log Aktivitas Sistem',
            'logs'    => $auditModel->getLogsWithFilter($filters),
            'pager'   => $auditModel->pager,
            'filters' => $filters,
            'users'   => $auditModel->getUniqueUsers(), // Daftar user untuk dropdown filter
        ];

        return view('backend/audit/index', $data);
    }
}
