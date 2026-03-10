<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="hero-section position-relative d-flex align-items-center justify-content-center"
    style="min-height: 100vh; background: url('<?= !empty($settings['hero_image']) ? base_url('uploads/hero/' . $settings['hero_image']) : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.3); z-index: 0;"></div>
    <div class="container position-relative z-1 text-center pt-5 pt-lg-0">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8" data-aos="fade-up" data-aos-duration="1000">
                <span class="badge backdrop-blur text-white mb-4 px-3 py-2 rounded-pill border border-light border-opacity-25 fw-semibold tracking-wide">
                    <?= $settings['hero_badge'] ?? '✨ Penerimaan Siswa Baru 2026/2027' ?>
                </span>
                <h1 class="display-3 fw-extrabold mb-4 text-white lh-sm">
                    <?= $settings['hero_title'] ?? 'Mulai Karier Hebatmu, <br> Ciptakan <span class="text-gradient">Karya Nyata.</span>' ?>
                </h1>
                <p class="lead text-white-50 mb-5 mx-auto" style="max-width: 650px; font-size: 1.15rem;">
                    <?= $settings['hero_desc'] ?? 'Kami tidak hanya mengajarkan teori. Di sini, kamu dididik untuk menjadi ahli, praktisi kreatif, dan technopreneur masa depan. Siap melangkah maju?' ?>
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= base_url('jurusan') ?>" class="btn btn-primary-custom px-4 py-3 rounded-pill fw-semibold shadow-lg">Jelajahi Jurusan</a>
                    <a href="<?= base_url('galeri') ?>" class="btn btn-outline-light px-4 py-3 rounded-pill fw-semibold backdrop-blur">Lihat Karya Siswa</a>
                </div>
            </div>
        </div>
    </div>
    <div class="floating-card glass-card p-3 rounded-4 shadow-lg position-absolute bottom-0 end-0 mb-5 me-4 me-lg-5 d-none d-lg-block z-1 text-start" data-aos="fade-left" data-aos-delay="600" style="width: 260px;">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-box bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow" style="width: 45px; height: 45px; flex-shrink: 0;">✓</div>
            <div>
                <h6 class="mb-0 fw-bold text-dark">Siap Kerja</h6>
                <small class="text-muted">Kurikulum Standar Industri</small>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-white position-relative stats-section bg-gradient-stats">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <h2 class="display-5 fw-extrabold text-accent mb-0">
                    <span class="counter-value" data-target="<?= $settings['stat_mitra'] ?? '50' ?>" data-suffix="+">0</span>
                </h2>
                <p class="mb-0 text-white-50">Mitra Industri</p>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <h2 class="display-5 fw-extrabold text-accent mb-0">
                    <span class="counter-value" data-target="<?= $settings['stat_fasilitas'] ?? '100' ?>" data-suffix="%">0</span>
                </h2>
                <p class="mb-0 text-white-50">Fasilitas Praktik</p>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <h2 class="display-5 fw-extrabold text-accent mb-0">
                    <span class="counter-value" data-target="<?= $settings['stat_program'] ?? '6' ?>" data-suffix="">0</span>
                </h2>
                <p class="mb-0 text-white-50">Program Keahlian</p>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <h2 class="display-5 fw-extrabold text-accent mb-0">
                    <span class="counter-value" data-target="<?= $settings['stat_alumni'] ?? '2000' ?>" data-suffix="+">0</span>
                </h2>
                <p class="mb-0 text-white-50">Alumni Sukses</p>
            </div>
        </div>
    </div>
</section>

<section class="jurusan-section bg-light">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Program Keahlian</span>
            <h2 class="display-6 fw-extrabold mt-2">Pilih Jalan Suksesmu</h2>
            <p class="text-secondary mx-auto" style="max-width: 600px;">Setiap jurusan didesain bersama praktisi industri untuk memastikan lulusan kami relevan dengan kebutuhan pasar kerja.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($jurusan)) : ?>
                <?php $delay = 100;
                foreach ($jurusan as $j) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="jurusan-card rounded-4 overflow-hidden position-relative h-100 shadow-sm bg-white">
                            <img src="<?= base_url('uploads/jurusan/' . $j['image']) ?>" class="w-100 card-img-custom object-fit-cover" alt="<?= esc($j['name']) ?>" style="height: 250px;" loading="lazy">
                            <div class="jurusan-content p-4 position-relative bg-white">
                                <div class="icon-jurusan bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow">
                                    <?= esc($j['icon']) ?>
                                </div>
                                <h4 class="fw-bold mb-2"><?= esc($j['name']) ?></h4>
                                <p class="text-muted small mb-0"><?= esc($j['short_desc']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php $delay += 100;
                endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <p class="text-muted mb-0">Belum ada kompetensi keahlian atau jurusan yang di tampilkan.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($jurusan)) : ?>
            <div class="text-center mt-5" data-aos="fade-up">
                <a href="<?= base_url('jurusan') ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">Lihat Semua Jurusan</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-6 section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Galeri</span>
                <h2 class="display-6 fw-extrabold mt-2 mb-0">Karya Nyata Siswa</h2>
            </div>
            <?php if (!empty($galeri)) : ?>
                <a href="<?= base_url('galeri') ?>" class="btn btn-outline-dark rounded-pill d-none d-md-block">Lihat Semua Galeri</a>
            <?php endif; ?>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($galeri)) : ?>
                <?php $delay = 100;
                foreach ($galeri as $g) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                        <div class="karya-item rounded-4 overflow-hidden position-relative shadow-sm" style="height: 300px;">
                            <img src="<?= base_url('uploads/galeri/' . $g['image']) ?>" alt="<?= esc($g['title']) ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                            <div class="karya-overlay d-flex flex-column justify-content-end p-4 text-white">
                                <h5 class="fw-bold mb-1"><?= esc($g['title']) ?></h5>
                                <small><?= esc($g['description']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php $delay += 100;
                endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <p class="text-muted mb-0">Belum ada dokumentasi di galeri.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($galeri)) : ?>
            <div class="text-center mt-4 d-md-none">
                <a href="<?= base_url('galeri') ?>" class="btn btn-outline-dark rounded-pill">Lihat Semua Galeri</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="berita-section py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Berita Terkini</span>
                <h2 class="display-6 fw-extrabold mt-2 mb-0">Kabar Terbaru Sekolah</h2>
            </div>
            <?php if (!empty($berita)) : ?>
                <a href="<?= base_url('berita') ?>" class="btn btn-outline-primary rounded-pill d-none d-md-block">Lihat Semua Berita</a>
            <?php endif; ?>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($berita)) : ?>
                <?php $delay = 100;
                foreach ($berita as $b) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                            <img src="<?= base_url('uploads/berita/' . $b['image']) ?>" class="card-img-top object-fit-contain bg-light" alt="<?= esc($b['title']) ?>" style="height: 220px; width: 100%; padding: 0.5rem;" loading="lazy">
                            <div class="card-body p-4">
                                <span class="badge bg-soft-primary text-primary-custom mb-2 px-2 py-1"><?= esc($b['category']) ?></span>
                                <h5 class="card-title fw-bold"><?= esc($b['title']) ?></h5>
                                <p class="card-text text-muted small mb-3"><?= esc($b['excerpt']) ?></p>
                                <a href="<?= base_url('berita/' . $b['slug']) ?>" class="text-primary-custom fw-semibold text-decoration-none">Baca selengkapnya <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                <?php $delay += 100;
                endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <p class="text-muted mb-0">Belum ada artikel atau berita yang dibuat.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-light overflow-hidden border-top border-bottom">
    <div class="container text-center mb-4" data-aos="fade-up">
        <h6 class="text-muted fw-bold text-uppercase tracking-wide">Dipercaya oleh Perusahaan Terkemuka</h6>
    </div>
    <div class="marquee-container">
        <div class="marquee-content d-flex align-items-center">
            <?php if (!empty($mitra)) : ?>
                <?php for ($i = 0; $i < 2; $i++) : ?>
                    <?php foreach ($mitra as $m) : ?>
                        <div class="mx-5 d-flex align-items-center gap-3">
                            <img src="<?= base_url('uploads/mitra/' . $m['logo']) ?>" alt="<?= esc($m['nama'] ?? '') ?>" height="45" class="object-fit-contain" loading="lazy">
                            <h4 class="text-muted fw-bold mb-0"><?= esc($m['nama'] ?? '') ?></h4>
                        </div>
                    <?php endforeach; ?>
                <?php endfor; ?>
            <?php else : ?>
                <h5 class="mx-5 text-muted fw-bold">Belum ada mitra industri yang ditambahkan.</h5>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>