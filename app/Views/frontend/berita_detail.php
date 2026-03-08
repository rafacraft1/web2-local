<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<div style="height: 90px; background-color: #111827;"></div>

<section class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-down">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('berita') ?>" class="text-decoration-none text-muted">Berita</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($berita['category']) ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="bg-white rounded-4 shadow-sm overflow-hidden">
                    <img src="<?= base_url('uploads/berita/' . $berita['image']) ?>" alt="<?= esc($berita['title']) ?>" class="w-100 object-fit-cover" style="height: 400px;">

                    <div class="p-4 p-md-5">
                        <span class="badge bg-soft-primary text-primary-custom mb-3 px-3 py-2 rounded-pill fw-semibold">
                            <?= esc($berita['category']) ?>
                        </span>

                        <h1 class="fw-extrabold mb-4 lh-sm text-dark" style="font-size: 2.2rem;">
                            <?= esc($berita['title']) ?>
                        </h1>

                        <div class="d-flex align-items-center text-muted small mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center me-4">
                                <i class="bi bi-person-circle fs-5 me-2 text-primary-custom"></i>
                                <span class="fw-medium text-dark"><?= esc($berita['penulis']) ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 fs-6 me-2"></i>
                                <span><?= date('d M Y, H:i', strtotime($berita['created_at'])) ?> WIB</span>
                            </div>
                        </div>

                        <div class="article-content text-secondary lh-lg fs-6" style="text-align: justify;">
                            <?= $berita['content'] ?>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex align-items-center justify-content-between">
                            <span class="fw-semibold text-dark">Bagikan artikel ini:</span>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= current_url() ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
                                <a href="https://twitter.com/intent/tweet?url=<?= current_url() ?>&text=<?= urlencode($berita['title']) ?>" target="_blank" class="btn btn-outline-info btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-twitter-x"></i></a>
                                <a href="https://api.whatsapp.com/send?text=<?= urlencode($berita['title'] . ' - ' . current_url()) ?>" target="_blank" class="btn btn-outline-success btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-left" data-aos-delay="200">
                <div class="bg-white rounded-4 shadow-sm p-4 sticky-top" style="top: 110px;">
                    <h5 class="fw-bold mb-4 border-bottom pb-3 text-dark">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i> Berita Terbaru
                    </h5>

                    <div class="d-flex flex-column gap-4">
                        <?php if (!empty($recent_berita)) : ?>
                            <?php foreach ($recent_berita as $rb) : ?>
                                <div class="d-flex gap-3 align-items-center">
                                    <img src="<?= base_url('uploads/berita/' . $rb['image']) ?>" alt="<?= esc($rb['title']) ?>" class="rounded-3 object-fit-cover shadow-sm" style="width: 80px; height: 80px; flex-shrink: 0;">
                                    <div>
                                        <small class="text-primary-custom fw-semibold mb-1 d-block"><?= esc($rb['category']) ?></small>
                                        <h6 class="fw-bold mb-1 lh-sm" style="font-size: 0.95rem;">
                                            <a href="<?= base_url('berita/' . $rb['slug']) ?>" class="text-dark text-decoration-none text-hover-primary">
                                                <?= esc($rb['title']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> <?= date('d M Y', strtotime($rb['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted small mb-0">Belum ada berita terbaru lainnya.</p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <a href="<?= base_url('berita') ?>" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-semibold">Lihat Indeks Berita</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<style>
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .article-content iframe {
        max-width: 100%;
        border-radius: 8px;
    }

    .text-hover-primary:hover {
        color: var(--bs-primary) !important;
    }
</style>
<?= $this->endSection() ?>