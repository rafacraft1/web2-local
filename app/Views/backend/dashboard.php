<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="container-fluid pt-2 pb-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Selamat Datang, <?= esc(session()->get('nama_lengkap')) ?>! 👋</h3>
        <p class="text-muted">
            Anda login sebagai <span class="badge bg-primary text-uppercase px-2 py-1"><?= esc(session()->get('role')) ?></span>
        </p>
    </div>

    <div class="row g-4 mb-4">

        <?php if (isset($total_berita)): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100 border-start border-primary border-4 rounded-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Berita</p>
                            <h3 class="fw-bold mb-0"><?= $total_berita ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-newspaper fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($total_galeri)): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100 border-start border-success border-4 rounded-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Karya/Galeri</p>
                            <h3 class="fw-bold mb-0"><?= $total_galeri ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-images fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($pesan_baru)): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100 border-start border-warning border-4 rounded-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Pesan Baru (Unread)</p>
                            <h3 class="fw-bold mb-0 text-warning"><?= $pesan_baru ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-envelope-exclamation fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($total_user)): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100 border-start border-info border-4 rounded-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Pengguna</p>
                            <h3 class="fw-bold mb-0"><?= $total_user ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-people fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-4 bg-light d-flex align-items-center">
            <div class="me-4 d-none d-md-block">
                <i class="bi bi-rocket-takeoff text-primary" style="font-size: 3rem;"></i>
            </div>
            <div>
                <h5 class="fw-bold">Butuh bantuan menggunakan panel ini?</h5>
                <p class="mb-0 text-muted">Aplikasi ini sudah dilindungi dengan sistem Hak Akses. Anda hanya dapat melihat dan mengubah data yang diizinkan untuk peran (role) Anda.</p>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>