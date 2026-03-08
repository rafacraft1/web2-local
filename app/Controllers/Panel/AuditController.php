<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditController extends BaseController
{
    public function index(): string
    {
        $auditModel = new AuditLogModel();

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
            'users'   => $auditModel->getUniqueUsers(),
        ];

        return view('backend/audit/index', $data);
    }
}
