<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="position-relative d-flex align-items-center justify-content-center"
    style="height: 50vh; min-height: 400px; background: url('<?= !empty($settings['header_galeri_image']) ? base_url('uploads/hero/' . $settings['header_galeri_image']) : 'https://images.unsplash.com/photo-1492538368677-f6e0afe31dcc?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.8); z-index: 0;"></div>

    <div class="container position-relative z-1 text-center text-white mt-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill fw-semibold tracking-wide border border-light border-opacity-25" data-aos="fade-up">
            Dokumentasi & Karya
        </span>
        <h1 class="display-4 fw-extrabold mb-3" data-aos="fade-up" data-aos-delay="100">
            Galeri <span class="text-accent">Siswa</span>
        </h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="200">
            Kumpulan rekam jejak, momen tak terlupakan, dan hasil karya nyata dari para siswa-siswi terbaik kami.
        </p>
    </div>
</section>

<section class="py-6 bg-light section-padding">
    <div class="container">

        <nav aria-label="breadcrumb" class="mb-5" data-aos="fade-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item active text-primary-custom fw-semibold" aria-current="page">Galeri</li>
            </ol>
        </nav>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($galeri)) : ?>
                <?php $delay = 100;
                foreach ($galeri as $g) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                        <div class="karya-item rounded-4 overflow-hidden position-relative shadow-sm galeri-card" style="height: 320px;">
                            <img src="<?= base_url('uploads/galeri/' . $g['image']) ?>" alt="<?= esc($g['title']) ?>" class="w-100 h-100 object-fit-cover transition-img">

                            <div class="karya-overlay d-flex flex-column justify-content-end p-4 text-white w-100 h-100 position-absolute top-0 start-0 transition-overlay">
                                <h5 class="fw-bold mb-1 galeri-title"><?= esc($g['title']) ?></h5>
                                <small class="text-white-75 galeri-desc"><?= esc($g['description']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 50;
                    if ($delay > 300) $delay = 100;
                endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5 my-5" data-aos="fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-images fs-1 text-muted"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Galeri Masih Kosong</h4>
                    <p class="text-muted mb-0">Belum ada dokumentasi atau karya yang diunggah ke galeri saat ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($galeri)) : ?>
            <div class="mt-5 pt-4 d-flex justify-content-center custom-pagination" data-aos="fade-up">
                <?= $pager->links('galeri', 'default_full') ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<style>
    /* Efek Zoom & Overlay untuk Galeri */
    .galeri-card {
        cursor: pointer;
    }

    .transition-img {
        transition: transform 0.5s ease;
    }

    .transition-overlay {
        background: linear-gradient(to top, rgba(17, 24, 39, 0.9) 0%, rgba(17, 24, 39, 0.2) 50%, transparent 100%);
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    .galeri-card:hover .transition-img {
        transform: scale(1.08);
    }

    .galeri-card:hover .transition-overlay {
        background: linear-gradient(to top, rgba(17, 24, 39, 0.95) 0%, rgba(17, 24, 39, 0.5) 60%, transparent 100%);
        opacity: 1;
    }

    /* Teks dalam Galeri */
    .galeri-title {
        transform: translateY(5px);
        transition: transform 0.3s ease;
    }

    .galeri-desc {
        opacity: 0.8;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .galeri-card:hover .galeri-title {
        transform: translateY(0);
    }

    .galeri-card:hover .galeri-desc {
        opacity: 1;
        transform: translateY(0);
    }

    /* Styling Paginasi */
    .custom-pagination ul.pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .custom-pagination ul.pagination li.active a {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }

    .custom-pagination ul.pagination li a {
        color: var(--bs-primary);
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        border: 1px solid #dee2e6;
        transition: 0.2s;
    }

    .custom-pagination ul.pagination li a:hover {
        background-color: #f8f9fa;
        color: #0f172a;
    }
</style>
<?= $this->endSection() ?>