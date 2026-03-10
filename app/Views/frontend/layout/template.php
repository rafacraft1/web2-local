<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($settings['nama_web'] ?? 'SMK Kreatif Nusantara') ?></title>

    <meta name="description" content="<?= esc($meta_desc ?? $settings['footer_desc'] ?? 'Mencetak generasi unggul yang siap kerja, berkarakter, dan memiliki jiwa wirausaha di era digital.') ?>">
    <meta property="og:title" content="<?= esc($title ?? ($settings['nama_web'] ?? 'SMK Kreatif Nusantara')) ?>">
    <meta property="og:description" content="<?= esc($meta_desc ?? $settings['footer_desc'] ?? 'Mencetak generasi unggul yang siap kerja, berkarakter, dan memiliki jiwa wirausaha di era digital.') ?>">
    <meta property="og:image" content="<?= isset($meta_image) ? $meta_image : base_url(!empty($settings['logo_image']) ? 'uploads/logo/' . $settings['logo_image'] : 'assets/logo-brand.webp') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:type" content="website">

    <link rel="shortcut icon" href="<?= base_url(!empty($settings['logo_image']) ? 'uploads/logo/' . $settings['logo_image'] : 'assets/logo-brand.webp') ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?= base_url(!empty($settings['logo_image']) ? 'uploads/logo/' . $settings['logo_image'] : 'assets/logo-brand.webp') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm" data-bs-theme="light">
        <div class="container">
            <a class="navbar-brand fw-extrabold d-flex align-items-center gap-2" href="<?= base_url() ?>">
                <img src="<?= base_url(!empty($settings['logo_image']) ? 'uploads/logo/' . $settings['logo_image'] : 'assets/logo-brand.webp') ?>" alt="Logo <?= $settings['nama_web'] ?? 'SMK Kreatif' ?>" width="40" height="40" style="object-fit: contain;">
                <span>
                    <span class="text-primary-custom"><?= $settings['logo_text_highlight'] ?? 'SMK' ?></span> <span class="text-dark"><?= $settings['logo_text_normal'] ?? 'Kreatif.' ?></span>
                </span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center fw-medium">
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url() ?>">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url('profil') ?>">Profil</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url('jurusan') ?>">Jurusan</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url('berita') ?>">Berita</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url('galeri') ?>">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="<?= base_url('kontak') ?>">Kontak</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-primary-custom px-4 py-2 rounded-pill fw-semibold shadow-sm" href="<?= $settings['link_ppdb'] ?? base_url('ppdb') ?>">Daftar PPDB</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content') ?>

    <a href="https://wa.me/<?= $settings['whatsapp'] ?? '6281234567890' ?>" target="_blank" id="btnWhatsApp" aria-label="Hubungi kami via WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
    <button id="btnBackToTop" aria-label="Kembali ke atas"><i class="fa-solid fa-arrow-up"></i></button>

    <footer class="bg-dark text-white pt-6 pb-4 mt-auto section-padding">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-12 pe-lg-5">
                    <h3 class="fw-extrabold mb-3"><span class="text-accent"><?= $settings['logo_text_highlight'] ?? 'SMK' ?></span> <?= $settings['logo_text_normal'] ?? 'Kreatif.' ?></h3>
                    <p class="text-white-50 mb-4"><?= $settings['footer_desc'] ?? 'Mencetak generasi unggul yang siap kerja, berkarakter, dan memiliki jiwa wirausaha di era digital.' ?></p>
                    <div class="d-flex gap-3">
                        <a href="<?= $settings['facebook'] ?? '#' ?>" aria-label="Kunjungi Facebook Kami" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height:40px;"><i class="bi bi-facebook"></i></a>
                        <a href="<?= $settings['instagram'] ?? '#' ?>" aria-label="Kunjungi Instagram Kami" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height:40px;"><i class="bi bi-instagram"></i></a>
                        <a href="<?= $settings['tiktok'] ?? '#' ?>" aria-label="Kunjungi TikTok Kami" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height:40px;"><i class="bi bi-tiktok"></i></a>
                        <a href="<?= $settings['youtube'] ?? '#' ?>" aria-label="Kunjungi YouTube Kami" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height:40px;"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mt-5 mt-lg-0">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <ul class="list-unstyled text-white-50 lh-lg">
                        <li><i class="bi bi-geo-alt-fill me-2"></i> <?= $settings['alamat'] ?? 'Jl. Kreativitas No. 99, Bandung' ?></li>
                        <li><i class="bi bi-telephone-fill me-2"></i> <?= $settings['telepon'] ?? '(022) 1234-5678' ?></li>
                        <li><i class="bi bi-envelope-fill me-2"></i> <?= $settings['email_kontak'] ?? 'info@smkkreatif.sch.id' ?></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 mt-5 mt-lg-0">
                    <h5 class="fw-bold mb-3">Peta Lokasi</h5>
                    <div class="rounded overflow-hidden shadow-sm">
                        <?= $settings['iframe_maps'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56347863118!2d107.57311684305417!3d-6.903444341673859!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e6398252477f%3A0x146a1f93d3e815b2!2sBandung%2C%20Bandung%20City%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="160" style="border:0;" allowfullscreen="" loading="lazy"></iframe>' ?>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 text-center text-white-50 small">
                © <?= date('Y') ?> <?= $settings['nama_web'] ?? 'SMK Kreatif Nusantara' ?>. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?= base_url('js/main.js') ?>"></script>

    <?= $this->renderSection('extra_scripts') ?>
</body>

</html>