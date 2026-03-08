<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="position-relative d-flex align-items-center justify-content-center"
    style="height: 50vh; min-height: 400px; background: url('<?= !empty($settings['header_jurusan_image']) ? base_url('uploads/hero/' . $settings['header_jurusan_image']) : 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.8); z-index: 0;"></div>

    <div class="container position-relative z-1 text-center text-white mt-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill fw-semibold tracking-wide border border-light border-opacity-25" data-aos="fade-up">
            Kompetensi Unggulan
        </span>
        <h1 class="display-4 fw-extrabold mb-3" data-aos="fade-up" data-aos-delay="100">
            Program <span class="text-accent">Keahlian</span>
        </h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="200">
            Temukan jurusan yang tepat untuk mengembangkan potensimu dan bersiaplah menghadapi dunia industri profesional.
        </p>
    </div>
</section>

<section class="py-6 bg-light section-padding">
    <div class="container">

        <nav aria-label="breadcrumb" class="mb-5" data-aos="fade-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item active text-primary-custom fw-semibold" aria-current="page">Jurusan</li>
            </ol>
        </nav>

        <div class="text-center mb-5 pb-3" data-aos="fade-up">
            <h3 class="fw-bold">Pilih Jalan Suksesmu</h3>
            <p class="text-secondary mx-auto" style="max-width: 700px;">
                Setiap program keahlian di sekolah kami didesain bersama dengan para praktisi ahli dari mitra industri. Kami memastikan kurikulum selalu relevan dengan kebutuhan pasar kerja masa kini dan masa depan.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($jurusan)) : ?>
                <?php $delay = 100;
                foreach ($jurusan as $j) : ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="jurusan-card rounded-4 overflow-hidden position-relative h-100 shadow-sm bg-white card-hover-effect">
                            <img src="<?= base_url('uploads/jurusan/' . $j['image']) ?>" class="w-100 object-fit-cover" alt="<?= esc($j['name']) ?>" style="height: 250px;">

                            <div class="jurusan-content p-4 position-relative bg-white d-flex flex-column h-100">
                                <div class="icon-jurusan bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow" style="margin-top: -45px; width: 60px; height: 60px; font-size: 1.5rem; border: 4px solid #fff;">
                                    <?= esc($j['icon']) ?>
                                </div>
                                <h4 class="fw-bold mb-3"><?= esc($j['name']) ?></h4>
                                <p class="text-muted small mb-4 flex-grow-1"><?= esc($j['description']) ?></p>

                                <div class="mt-auto pt-3 border-top">
                                    <a href="<?= base_url('kontak') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold w-100">Konsultasi Jurusan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 100;
                    if ($delay > 300) $delay = 100;
                endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5 my-4" data-aos="fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-mortarboard fs-1 text-muted"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Belum Ada Program Keahlian</h4>
                    <p class="text-muted mb-0">Belum ada kompetensi keahlian atau jurusan yang di tampilkan saat ini.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<style>
    .card-hover-effect {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
</style>
<?= $this->endSection() ?>