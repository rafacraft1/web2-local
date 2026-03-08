<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Panel Admin' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?= base_url('css/panel.css') ?>">
</head>

<body>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded p-1 me-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-hexagon-fill fs-5 text-white"></i>
                    </div>
                    <h6 class="mb-0 fw-bold tracking-wide">Panel Admin</h6>
                </div>
                <button type="button" id="sidebarClose" class="btn btn-sm text-white d-md-none p-0">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <ul class="list-unstyled components flex-grow-1 mb-0">
                <li class="<?= url_is('panel/dashboard') || url_is('panel') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/dashboard') ?>" class="menu-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                </li>

                <div class="sidebar-heading">Manajemen Konten</div>
                <li class="<?= url_is('panel/berita*') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/berita') ?>" class="menu-link"><i class="bi bi-newspaper"></i> Berita & Artikel</a>
                </li>
                <li class="<?= url_is('panel/jurusan*') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/jurusan') ?>" class="menu-link"><i class="bi bi-mortarboard"></i> Program Keahlian</a>
                </li>
                <li class="<?= url_is('panel/galeri*') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/galeri') ?>" class="menu-link"><i class="bi bi-images"></i> Galeri & Karya</a>
                </li>

                <div class="sidebar-heading">Interaksi Publik</div>
                <li class="<?= url_is('panel/pesan*') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/pesan') ?>" class="menu-link"><i class="bi bi-envelope"></i> Pesan Masuk</a>
                </li>
                <li class="<?= url_is('panel/mitra*') ? 'active' : '' ?>">
                    <a href="<?= base_url('panel/mitra') ?>" class="menu-link"><i class="bi bi-building"></i> Mitra Industri</a>
                </li>

                <?php if (session()->get('role') === 'admin'): ?>
                    <div class="sidebar-heading">Sistem & Pengaturan</div>
                    <li class="<?= url_is('panel/users*') ? 'active' : '' ?>">
                        <a href="<?= base_url('panel/users') ?>" class="menu-link"><i class="bi bi-people"></i> Manajemen Users</a>
                    </li>
                    <li class="<?= url_is('panel/settings*') ? 'active' : '' ?>">
                        <a href="<?= base_url('panel/settings') ?>" class="menu-link"><i class="bi bi-sliders"></i> Pengaturan Web</a>
                    </li>
                    <li class="<?= url_is('panel/audit-logs*') ? 'active' : '' ?>">
                        <a href="<?= base_url('panel/audit-logs') ?>" class="menu-link"><i class="bi bi-clipboard-data"></i> Log Aktivitas</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="sidebar-footer">
                <a href="<?= base_url('panel/logout') ?>">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </div>
        </nav>

        <div id="content">

            <nav class="top-navbar d-flex justify-content-between align-items-center sticky-top">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-light shadow-sm me-3 border">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <a href="<?= base_url() ?>" target="_blank" class="btn btn-sm btn-light border shadow-sm d-none d-sm-inline-flex align-items-center fw-semibold text-secondary">
                        <i class="bi bi-globe text-primary me-2"></i> Lihat Website
                    </a>
                </div>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (session()->get('avatar')): ?>
                                <img src="<?= base_url('uploads/avatars/' . session()->get('avatar')) ?>" alt="User" width="36" height="36" class="rounded-circle me-2 object-fit-cover shadow-sm">
                            <?php else: ?>
                                <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person fs-5"></i>
                                </div>
                            <?php endif; ?>

                            <div class="d-none d-md-block text-start">
                                <span class="fw-semibold d-block lh-1" style="font-size: 14px;"><?= session()->get('nama_lengkap') ?></span>
                                <small class="text-muted text-capitalize" style="font-size: 11px;"><?= session()->get('role') ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-3" aria-labelledby="dropdownUser">
                            <li class="px-3 py-2 text-center d-md-none border-bottom mb-2">
                                <span class="fw-bold d-block"><?= session()->get('nama_lengkap') ?></span>
                                <span class="badge bg-soft-primary text-primary text-capitalize"><?= session()->get('role') ?></span>
                            </li>
                            <li><a class="dropdown-item py-2" href="<?= base_url('panel/profile') ?>"><i class="bi bi-person me-2 text-muted"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item py-2" href="<?= base_url('panel/profile?tab=password') ?>"><i class="bi bi-shield-lock me-2 text-muted"></i> Ubah Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 text-danger fw-semibold" href="<?= base_url('panel/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="main-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>

            <footer class="main-footer">
                &copy; <?= date('Y') ?> Panel Admin <?= $settings['nama_web'] ?? 'SMK Kreatif' ?>. Dibuat dengan CodeIgniter 4.
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?= base_url('js/panel.js') ?>"></script>

    <?= $this->renderSection('extra_scripts') ?>
</body>

</html>