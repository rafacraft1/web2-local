<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="position-relative d-flex align-items-center justify-content-center"
    style="height: 50vh; min-height: 400px; background: url('<?= !empty($settings['header_kontak_image']) ? base_url('uploads/hero/' . $settings['header_kontak_image']) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.8); z-index: 0;"></div>

    <div class="container position-relative z-1 text-center text-white mt-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill fw-semibold tracking-wide border border-light border-opacity-25" data-aos="fade-up">
            Mari Berbincang
        </span>
        <h1 class="display-4 fw-extrabold mb-3" data-aos="fade-up" data-aos-delay="100">
            Hubungi <span class="text-accent">Kami</span>
        </h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="200">
            Punya pertanyaan seputar program sekolah, PPDB, atau kemitraan industri? Kami siap membantu dan menjawab pertanyaan Anda.
        </p>
    </div>
</section>

<section class="py-6 section-padding bg-light">
    <div class="container">

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-5" role="alert" data-aos="fade-down">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-5" role="alert" data-aos="fade-down">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right" data-aos-delay="100">
                <div class="pe-lg-4">
                    <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Informasi Kontak</span>
                    <h3 class="fw-extrabold mt-2 mb-4">Tetap Terhubung Bersama Kami</h3>
                    <p class="text-secondary mb-5">
                        Anda dapat menghubungi kami melalui formulir pesan di samping, atau menggunakan informasi kontak langsung di bawah ini pada jam kerja operasional sekolah.
                    </p>

                    <div class="d-flex align-items-start mb-4 bg-white p-4 rounded-4 shadow-sm">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow" style="width: 50px; height: 50px;">
                            <i class="bi bi-geo-alt fs-5"></i>
                        </div>
                        <div class="ms-4">
                            <h5 class="fw-bold mb-1">Alamat Sekolah</h5>
                            <p class="text-muted mb-0"><?= $settings['alamat'] ?? 'Jl. Kreativitas No. 99, Bandung, Jawa Barat' ?></p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4 bg-white p-4 rounded-4 shadow-sm">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow" style="width: 50px; height: 50px;">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        <div class="ms-4">
                            <h5 class="fw-bold mb-1">Telepon & WhatsApp</h5>
                            <p class="text-muted mb-0"><?= $settings['telepon'] ?? '(022) 1234-5678' ?></p>
                            <a href="https://wa.me/<?= $settings['whatsapp'] ?? '6281234567890' ?>" target="_blank" class="text-decoration-none text-primary-custom fw-semibold small">
                                <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start bg-white p-4 rounded-4 shadow-sm">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow" style="width: 50px; height: 50px;">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div class="ms-4">
                            <h5 class="fw-bold mb-1">Alamat Email</h5>
                            <p class="text-muted mb-0"><?= $settings['email_kontak'] ?? 'info@smkkreatif.sch.id' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-4">Kirim Pesan Langsung</h4>

                        <form action="<?= base_url('kontak/kirim') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg rounded-3 bg-light border-0" id="name" name="name" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg rounded-3 bg-light border-0" id="email" name="email" placeholder="john@email.com" required>
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label fw-semibold text-secondary small">Subjek Pesan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg rounded-3 bg-light border-0" id="subject" name="subject" placeholder="Contoh: Info Pendaftaran Siswa Baru" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-semibold text-secondary small">Isi Pesan <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-lg rounded-3 bg-light border-0" id="message" name="message" rows="5" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                        <i class="bi bi-send-fill"></i> Kirim Pesan Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>