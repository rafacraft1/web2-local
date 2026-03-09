<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Panel Admin') ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                <li class="sidebar-heading mt-3 px-3 text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Menu Navigasi</li>

                <?php if (isset($dynamicMenus) && !empty($dynamicMenus)): ?>
                    <?php foreach ($dynamicMenus as $menu): ?>
                        <li class="<?= (url_is($menu['url'] . '*')) || (url_is('panel') && $menu['url'] == 'panel/dashboard') ? 'active' : '' ?>">
                            <a href="<?= base_url($menu['url']) ?>" class="menu-link">
                                <i class="<?= esc($menu['icon']) ?>"></i> <?= esc($menu['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>
                        <a href="<?= base_url('auth/login') ?>" class="menu-link text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Sesi Habis / Menu Kosong
                        </a>
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
                                <span class="fw-semibold d-block lh-1" style="font-size: 14px;"><?= esc(session()->get('nama_lengkap')) ?></span>
                                <small class="text-muted text-capitalize" style="font-size: 11px;"><?= esc(session()->get('role')) ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-3" aria-labelledby="dropdownUser">
                            <li class="px-3 py-2 text-center d-md-none border-bottom mb-2">
                                <span class="fw-bold d-block"><?= esc(session()->get('nama_lengkap')) ?></span>
                                <span class="badge bg-soft-primary text-primary text-capitalize"><?= esc(session()->get('role')) ?></span>
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
                &copy; <?= date('Y') ?> Panel Admin <?= isset($settings['nama_web']) ? esc($settings['nama_web']) : 'SMK Kreatif' ?>. Dibuat dengan CodeIgniter 4.
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/panel.js') ?>"></script>

    <?= $this->renderSection('extra_scripts') ?>
</body>

</html>