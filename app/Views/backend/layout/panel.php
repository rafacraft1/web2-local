<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Panel Admin' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* --- SIDEBAR --- */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            background: #111827;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1040;
            display: flex;
            flex-direction: column;
        }

        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #111827;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #4F46E5;
        }

        #sidebar.toggled {
            margin-left: -260px;
        }

        /* HEADER SIDEBAR (Fix 70px) */
        #sidebar .sidebar-header {
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            padding: 0 24px;
            background: #1f2937;
            border-bottom: 1px solid #374151;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        /* LIST MENU */
        #sidebar ul.components {
            padding: 15px 0 0 0;
            margin-bottom: 0;
        }

        #sidebar ul li a.menu-link {
            padding: 12px 24px;
            font-size: 14.5px;
            display: flex;
            align-items: center;
            color: #9ca3af;
            text-decoration: none;
            transition: 0.2s;
        }

        #sidebar ul li a.menu-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        #sidebar ul li a.menu-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        #sidebar ul li.active>a.menu-link {
            color: #fff;
            background: rgba(79, 70, 229, 0.15);
            border-right: 4px solid #4F46E5;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6b7280;
            font-weight: 700;
            padding: 15px 24px 5px;
            margin-top: 5px;
        }

        /* --- KUNCI MATI UKURAN FOOTER & LOGOUT AGAR 100% SAMA & SIMETRIS --- */
        .sidebar-footer,
        .main-footer {
            height: 45px !important;
            min-height: 45px !important;
            max-height: 45px !important;
            box-sizing: border-box !important;
            display: flex;
            align-items: center;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 1. KHUSUS STICKY LOGOUT DI SIDEBAR */
        .sidebar-footer {
            position: sticky;
            bottom: 0;
            background-color: #111827;
            /* Tutupi bayangan scroll menu */
            border-top: 1px solid #374151;
            z-index: 20;
            margin-top: auto !important;
            /* Dorong ke dasar jika menu sedikit */
        }

        .sidebar-footer a {
            width: 100%;
            height: 100%;
            padding: 0 24px;
            display: flex;
            align-items: center;
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .sidebar-footer a i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar-footer a:hover {
            background: rgba(220, 53, 69, 0.15);
            color: #ff6b6b !important;
            border-right: 4px solid #dc3545;
        }

        /* 2. KHUSUS FOOTER DI KONTEN UTAMA */
        .main-footer {
            justify-content: center;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            color: #6c757d;
            font-size: 0.875em;
            margin-top: auto !important;
            /* Dorong ke dasar jika konten sedikit */
        }

        /* ------------------------------------------------------------------ */


        /* --- CONTENT AREA --- */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        /* TOP NAVBAR (Fix 70px) */
        .top-navbar {
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            padding: 0 24px;
            background: #fff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            z-index: 1030;
        }

        .main-content {
            padding: 24px;
            flex-grow: 1;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        /* OVERLAY MOBILE */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
                position: fixed;
                height: 100%;
            }

            #sidebar.toggled {
                margin-left: 0 !important;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
            }
        }
    </style>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function() {
                    sidebar.classList.toggle('toggled');
                    if (window.innerWidth <= 768) {
                        sidebarOverlay.classList.toggle('active');
                    }
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    sidebar.classList.remove('toggled');
                    sidebarOverlay.classList.remove('active');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('toggled');
                    sidebarOverlay.classList.remove('active');
                });
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebarOverlay.classList.remove('active');
                }
            });
        });
    </script>
    <?= $this->renderSection('extra_scripts') ?>
</body>

</html>