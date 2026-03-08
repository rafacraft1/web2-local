<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-4 position-relative">
                <div class="position-relative z-1">
                    <h3 class="fw-bold mb-2">Selamat datang kembali, <?= session()->get('nama_lengkap') ?>! 👋</h3>
                    <p class="mb-0 opacity-75">Ini adalah halaman utama panel kendali Anda. Anda masuk sebagai <strong class="text-capitalize"><?= str_replace('-', ' ', session()->get('role')) ?></strong>.</p>
                </div>
                <i class="bi bi-hexagon position-absolute opacity-25" style="font-size: 8rem; right: -20px; bottom: -40px;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('panel/berita/create') ?>" class="btn btn-outline-primary fw-semibold rounded-pill"><i class="bi bi-pencil-square me-1"></i> Tulis Artikel</a>
            <a href="<?= base_url('panel/galeri/create') ?>" class="btn btn-outline-success fw-semibold rounded-pill"><i class="bi bi-image me-1"></i> Tambah Foto Galeri</a>
            <a href="<?= base_url('panel/settings') ?>" class="btn btn-outline-secondary fw-semibold rounded-pill"><i class="bi bi-gear me-1"></i> Pengaturan Web</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                    <i class="bi bi-newspaper fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Berita Aktif</h6>
                    <h3 class="fw-bold mb-0"><?= $total_berita ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success">
                    <i class="bi bi-images fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Galeri</h6>
                    <h3 class="fw-bold mb-0"><?= $total_galeri ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3 text-info">
                    <i class="bi bi-building fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Mitra Industri</h6>
                    <h3 class="fw-bold mb-0"><?= $total_mitra ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm rounded-4 <?= ($pesan_baru > 0) ? 'border border-warning border-2' : '' ?>">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3 text-warning">
                    <i class="bi bi-envelope fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Pesan Baru</h6>
                    <h3 class="fw-bold mb-0 text-dark">
                        <?= $pesan_baru ?>
                        <?php if ($pesan_baru > 0): ?>
                            <span class="badge bg-danger ms-2 fs-6 blink-anim">New</span>
                        <?php endif; ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-inbox me-2 text-warning"></i>Kotak Masuk Terbaru</h6>
                <a href="<?= base_url('panel/pesan') ?>" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Pengirim</th>
                                <th>Subjek</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_pesan)): ?>
                                <?php foreach ($recent_pesan as $p): ?>
                                    <tr class="<?= ($p['is_read'] == 0) ? 'fw-bold bg-light' : '' ?>">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-opacity-25 text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                    <?= strtoupper(substr($p['name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="text-dark"><?= esc($p['name']) ?></div>
                                                    <small class="text-muted fw-normal"><?= esc($p['email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($p['subject']) ?></td>
                                        <td><small class="text-muted"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></small></td>
                                        <td>
                                            <?php if ($p['is_read'] == 0): ?>
                                                <span class="badge bg-danger rounded-pill">Belum Dibaca</span>
                                            <?php else: ?>
                                                <span class="badge bg-success rounded-pill">Dibaca</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada pesan masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-activity me-2 text-primary"></i>Log Aktivitas</h6>
                <a href="<?= base_url('panel/audit-logs') ?>" class="text-decoration-none small">Detail</a>
            </div>
            <div class="card-body">
                <div class="timeline-widget">
                    <?php if (!empty($recent_logs)): ?>
                        <?php foreach ($recent_logs as $log): ?>
                            <?php
                            // Tentukan warna dan ikon berdasarkan action
                            $icon = 'bi-record-circle';
                            $color = 'text-secondary';
                            if ($log['action'] == 'LOGIN') {
                                $icon = 'bi-box-arrow-in-right';
                                $color = 'text-success';
                            }
                            if ($log['action'] == 'LOGOUT') {
                                $icon = 'bi-box-arrow-left';
                                $color = 'text-warning';
                            }
                            if ($log['action'] == 'UPDATE') {
                                $icon = 'bi-pencil';
                                $color = 'text-primary';
                            }
                            if ($log['action'] == 'CREATE') {
                                $icon = 'bi-plus-circle';
                                $color = 'text-info';
                            }
                            if ($log['action'] == 'DELETE') {
                                $icon = 'bi-trash';
                                $color = 'text-danger';
                            }
                            ?>
                            <div class="d-flex mb-3 align-items-start">
                                <div class="me-3 mt-1 <?= $color ?>">
                                    <i class="bi <?= $icon ?> fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 small fw-bold">
                                        <?= esc($log['nama_user']) ?>
                                        <span class="fw-normal text-muted">melakukan</span>
                                        <?= esc($log['action']) ?>
                                    </h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Modul: <?= esc($log['module']) ?> • <?= date('H:i', strtotime($log['created_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center small mb-0">Belum ada aktivitas terekam.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart me-2 text-success"></i>Komposisi Pengguna</h6>
                <div style="height: 200px; position: relative;">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<style>
    .blink-anim {
        animation: blinker 1.5s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Data dari PHP Controller
        const rawRoleData = <?= json_encode($users_role) ?>;

        let labels = [];
        let dataCounts = [];
        let backgroundColors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'];

        rawRoleData.forEach(item => {
            labels.push(item.role.replace('-', ' ').toUpperCase());
            dataCounts.push(item.total);
        });

        // Inisialisasi Chart
        const ctx = document.getElementById('roleChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataCounts,
                    backgroundColor: backgroundColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
<?= $this->endSection() ?>