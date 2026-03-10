<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="position-relative d-flex align-items-center justify-content-center"
    style="height: 50vh; min-height: 400px; background: url('<?= !empty($settings['header_berita_image']) ? base_url('uploads/hero/' . $settings['header_berita_image']) : 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.8); z-index: 0;"></div>

    <div class="container position-relative z-1 text-center text-white mt-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill fw-semibold tracking-wide border border-light border-opacity-25" data-aos="fade-up">
            Informasi & Pengumuman
        </span>
        <h1 class="display-4 fw-extrabold mb-3" data-aos="fade-up" data-aos-delay="100">
            Berita & <span class="text-accent">Artikel</span>
        </h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="200">
            Ikuti perkembangan terbaru, prestasi, dan berbagai kegiatan seru dari siswa-siswi kami.
        </p>
    </div>
</section>

<section class="py-6 bg-light section-padding">
    <div class="container">

        <nav aria-label="breadcrumb" class="mb-5" data-aos="fade-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item active text-primary-custom fw-semibold" aria-current="page">Berita</li>
            </ol>
        </nav>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($berita)) : ?>
                <?php $delay = 100;
                foreach ($berita as $b) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover-effect">
                            <img src="<?= base_url('uploads/berita/' . $b['image']) ?>" class="card-img-top object-fit-contain bg-light" alt="<?= esc($b['title']) ?>" style="height: 220px; width: 100%; padding: 0.5rem;" loading="lazy">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-soft-primary text-primary-custom px-2 py-1"><?= esc($b['category']) ?></span>
                                    <small class="text-muted" style="font-size: 0.8rem;"><i class="bi bi-clock me-1"></i> <?= date('d M Y', strtotime($b['created_at'])) ?></small>
                                </div>

                                <h5 class="card-title fw-bold mb-3 lh-sm">
                                    <a href="<?= base_url('berita/' . $b['slug']) ?>" class="text-dark text-decoration-none text-hover-primary">
                                        <?= esc($b['title']) ?>
                                    </a>
                                </h5>

                                <p class="card-text text-muted small mb-4 flex-grow-1"><?= esc($b['excerpt']) ?></p>

                                <a href="<?= base_url('berita/' . $b['slug']) ?>" class="text-primary-custom fw-semibold text-decoration-none mt-auto">
                                    Baca selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                </a>
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
                        <i class="bi bi-journal-x fs-1 text-muted"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Belum Ada Publikasi</h4>
                    <p class="text-muted mb-0">Belum ada artikel atau berita yang dibuat dan diterbitkan saat ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($berita)) : ?>
            <div class="mt-5 pt-4 d-flex justify-content-center custom-pagination" data-aos="fade-up">
                <?= $pager->links('berita', 'default_full') ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->endSection() ?>