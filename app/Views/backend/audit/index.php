<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-text me-2"></i> <?= esc($title) ?></h4>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
        <form action="<?= base_url('panel/audit-logs') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Filter Akun</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">-- Semua Akun --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['user_id'] ?>" <?= ($filters['user_id'] == $u['user_id']) ? 'selected' : '' ?>>
                            <?= esc($u['nama_user']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Filter Modul</label>
                <select name="module" class="form-select form-select-sm">
                    <option value="">-- Semua Modul --</option>
                    <?php
                    $modules = ['auth' => 'Otentikasi (Login/Logout)', 'users' => 'Manajemen Pengguna', 'settings' => 'Pengaturan Web'];
                    foreach ($modules as $key => $val): ?>
                        <option value="<?= $key ?>" <?= ($filters['module'] == $key) ? 'selected' : '' ?>><?= $val ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1">Filter Aksi</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">-- Semua Aksi --</option>
                    <option value="LOGIN" <?= ($filters['action'] == 'LOGIN') ? 'selected' : '' ?>>Login / Masuk</option>
                    <option value="LOGOUT" <?= ($filters['action'] == 'LOGOUT') ? 'selected' : '' ?>>Logout / Keluar</option>
                    <option value="CREATE" <?= ($filters['action'] == 'CREATE') ? 'selected' : '' ?>>Tambah Data</option>
                    <option value="UPDATE" <?= ($filters['action'] == 'UPDATE') ? 'selected' : '' ?>>Ubah Data</option>
                    <option value="DELETE" <?= ($filters['action'] == 'DELETE') ? 'selected' : '' ?>>Hapus Data</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary me-2"><i class="bi bi-search me-1"></i> Terapkan</button>
                <a href="<?= base_url('panel/audit-logs') ?>" class="btn btn-sm btn-light border"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="15%" class="ps-4">Waktu</th>
                        <th width="15%">Pengguna</th>
                        <th width="10%">Label Aksi</th>
                        <th width="45%">Deskripsi Aktivitas</th>
                        <th width="15%" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada catatan aktivitas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log) :

                            // 1. Pewarnaan Badge (Color Coding)
                            $badgeColor = 'bg-secondary';
                            if ($log['action'] == 'CREATE') $badgeColor = 'bg-success';
                            if ($log['action'] == 'UPDATE' || $log['action'] == 'TOGGLE_STATUS') $badgeColor = 'bg-warning text-dark';
                            if ($log['action'] == 'DELETE') $badgeColor = 'bg-danger';
                            if ($log['action'] == 'LOGIN' || $log['action'] == 'LOGOUT') $badgeColor = 'bg-info text-dark';

                            // 2. Terjemahan kalimat aktivitas
                            $modulStr = ucfirst($log['module']);
                            $aksiStr = '';
                            if ($log['action'] == 'CREATE') $aksiStr = 'menambahkan data baru di modul';
                            elseif ($log['action'] == 'UPDATE') $aksiStr = 'mengubah data pada modul';
                            elseif ($log['action'] == 'DELETE') $aksiStr = 'menghapus data dari modul';
                            elseif ($log['action'] == 'TOGGLE_STATUS') $aksiStr = 'mengubah status (aktif/nonaktif) pada modul';
                            elseif ($log['action'] == 'LOGIN') $aksiStr = 'berhasil masuk ke dalam sistem.';
                            elseif ($log['action'] == 'LOGOUT') $aksiStr = 'keluar dari sistem.';

                            // 3. Format Waktu (Contoh: "10 menit yang lalu")
                            $timeAgo = \CodeIgniter\I18n\Time::parse($log['created_at'])->humanize();
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="d-block fw-semibold text-dark"><?= $timeAgo ?></span>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></small>
                                </td>
                                <td><span class="fw-semibold text-primary"><?= esc($log['nama_user']) ?></span></td>
                                <td><span class="badge <?= $badgeColor ?>"><?= esc($log['action']) ?></span></td>
                                <td>
                                    <?= esc($log['nama_user']) ?> <?= $aksiStr ?>
                                    <?= !in_array($log['action'], ['LOGIN', 'LOGOUT']) ? '<strong>' . $modulStr . '</strong>' : '' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($log['old_values'] || $log['new_values']): ?>
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $log['id'] ?>">
                                            <i class="bi bi-search"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if ($log['old_values'] || $log['new_values']): ?>
                                <div class="modal fade" id="modalDetail<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header border-0 bg-light">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-file-diff text-primary me-2"></i> Detail Rekaman Aktivitas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row mb-3 pb-3 border-bottom">
                                                    <div class="col-md-6"><span class="text-muted small d-block">IP Address:</span> <strong><?= esc($log['ip_address']) ?></strong></div>
                                                    <div class="col-md-6"><span class="text-muted small d-block">User Agent / Browser:</span> <strong><?= esc($log['user_agent']) ?></strong></div>
                                                </div>

                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-arrow-left-circle me-1"></i> Data Lama</h6>
                                                        <?php if ($log['old_values']): ?>
                                                            <div class="bg-danger bg-opacity-10 p-3 rounded-3 border border-danger border-opacity-25" style="font-family: monospace; font-size: 0.85rem;">
                                                                <?php foreach (json_decode($log['old_values'], true) as $key => $val): ?>
                                                                    <div class="mb-1"><strong><?= esc($key) ?>:</strong> <span class="text-dark"><?= esc(is_array($val) ? json_encode($val) : $val) ?></span></div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="text-muted fst-italic">Tidak ada data (Pembuatan baru)</div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-arrow-right-circle me-1"></i> Data Baru</h6>
                                                        <?php if ($log['new_values']): ?>
                                                            <div class="bg-success bg-opacity-10 p-3 rounded-3 border border-success border-opacity-25" style="font-family: monospace; font-size: 0.85rem;">
                                                                <?php foreach (json_decode($log['new_values'], true) as $key => $val): ?>
                                                                    <div class="mb-1"><strong><?= esc($key) ?>:</strong> <span class="text-dark"><?= esc(is_array($val) ? json_encode($val) : $val) ?></span></div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="text-muted fst-italic">Tidak ada data (Aksi hapus)</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3 border-top-0">
        <?= $pager->links('audit', 'default_full') ?>
    </div>
</div>

<?= $this->endSection() ?>