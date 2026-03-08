<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<section class="position-relative d-flex align-items-center justify-content-center"
    style="height: 50vh; min-height: 400px; background: url('<?= !empty($settings['header_profil_image']) ? base_url('uploads/hero/' . $settings['header_profil_image']) : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1920&auto=format&fit=crop' ?>') center/cover no-repeat;">

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(17, 24, 39, 0.8); z-index: 0;"></div>

    <div class="container position-relative z-1 text-center text-white mt-5">
        <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill fw-semibold tracking-wide border border-light border-opacity-25" data-aos="fade-up">
            Mengenal Lebih Dekat
        </span>
        <h1 class="display-4 fw-extrabold mb-3" data-aos="fade-up" data-aos-delay="100">
            Profil <span class="text-accent">Sekolah</span>
        </h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem;" data-aos="fade-up" data-aos-delay="200">
            Mencetak lulusan yang tidak hanya unggul secara akademik, namun juga memiliki karakter, kompetensi, dan daya saing global.
        </p>
    </div>
</section>

<section class="py-6 bg-light section-padding">
    <div class="container">

        <nav aria-label="breadcrumb" class="mb-5" data-aos="fade-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item active text-primary-custom fw-semibold" aria-current="page">Profil</li>
            </ol>
        </nav>

        <div class="row g-5 align-items-center mb-5 pb-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg">
                    <img src="<?= !empty($settings['profil_sejarah_image']) ? base_url('uploads/hero/' . $settings['profil_sejarah_image']) : 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=800&auto=format&fit=crop' ?>" alt="Gedung Sekolah" class="w-100 object-fit-cover" style="height: 400px;">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                        <h4 class="text-white fw-bold mb-0"><?= $settings['nama_web'] ?? 'SMK Kreatif Nusantara' ?></h4>
                        <p class="text-white-50 small mb-0">Berdiri sejak <?= esc($settings['profil_tahun_berdiri'] ?? '2005') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Tentang Kami</span>
                <h2 class="fw-extrabold mt-2 mb-4"><?= esc($settings['profil_tentang_judul'] ?? 'Membangun Generasi Vokasi yang Inspiratif') ?></h2>
                <div class="text-secondary lh-lg mb-4 text-justify">
                    <?= nl2br(esc($settings['profil_tentang_teks'] ?? "SMK Kreatif Nusantara adalah lembaga pendidikan kejuruan yang berkomitmen penuh untuk menjembatani kesenjangan antara dunia pendidikan dan kebutuhan industri nyata. Kami memadukan kurikulum nasional dengan standar kompetensi industri terkini.\n\nDengan fasilitas praktik yang modern, tenaga pengajar dari kalangan profesional, serta kemitraan yang luas dengan puluhan perusahaan terkemuka, kami memastikan setiap siswa mendapatkan pengalaman belajar berbasis proyek yang sesungguhnya.")) ?>
                </div>
            </div>
        </div>

        <div class="row g-5 align-items-center mb-5 pb-4">
            <div class="col-lg-4 text-center text-lg-start" data-aos="fade-right">
                <div class="position-relative d-inline-block">
                    <img src="<?= !empty($settings['profil_kepsek_image']) ? base_url('uploads/hero/' . $settings['profil_kepsek_image']) : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=400&auto=format&fit=crop' ?>" alt="Kepala Sekolah" class="rounded-circle object-fit-cover border border-5 border-white shadow-lg" style="width: 280px; height: 280px;">
                    <div class="position-absolute bottom-0 end-0 bg-primary-custom text-white rounded-circle d-flex align-items-center justify-content-center border border-3 border-white shadow" style="width: 60px; height: 60px; transform: translate(-20px, -10px);">
                        <i class="bi bi-quote fs-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                <span class="text-primary-custom fw-bold text-uppercase tracking-wide">Sambutan Pimpinan</span>
                <h3 class="fw-extrabold mt-2 mb-4">Pesan Inspiratif</h3>
                <blockquote class="blockquote text-secondary fs-5 mb-4" style="line-height: 1.8; font-style: italic;">
                    "<?= nl2br(esc($settings['profil_kepsek_quote'] ?? 'Pendidikan vokasi bukan hanya tentang mentransfer ilmu, melainkan tentang menyalakan api kreativitas dan membentuk mental baja. Kami berdedikasi membimbing setiap siswa menemukan potensi terbaiknya.')) ?>"
                </blockquote>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc($settings['profil_kepsek_nama'] ?? 'Dr. Budi Santoso, M.Pd.') ?></h5>
                    <p class="text-muted small mb-0"><?= esc($settings['profil_kepsek_jabatan'] ?? 'Kepala SMK Kreatif Nusantara') ?></p>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-up">
                <div class="bg-primary-custom text-white rounded-4 p-5 h-100 shadow-lg" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded-circle mb-4" style="width: 70px; height: 70px;">
                        <i class="bi bi-eye fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-extrabold mb-4">Visi Kami</h3>
                    <p class="lead mb-0" style="font-weight: 500;">
                        "<?= nl2br(esc($settings['profil_visi'] ?? 'Menjadi Sekolah Menengah Kejuruan rujukan nasional yang menghasilkan lulusan berkarakter mulia, inovatif, kompeten, dan siap memimpin di era ekonomi digital.')) ?>"
                    </p>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white rounded-4 p-5 h-100 shadow-sm border">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary-custom rounded-circle mb-4" style="width: 70px; height: 70px;">
                        <i class="bi bi-bullseye fs-2"></i>
                    </div>
                    <h3 class="fw-extrabold mb-4 text-dark">Misi Kami</h3>
                    <ul class="list-unstyled text-secondary lh-lg mb-0">
                        <?php
                        // Pecah baris baru menjadi list item
                        $defaultMisi = "Menyelenggarakan pendidikan kejuruan yang bermutu.\nMenanamkan budaya kerja industri (soft/hard skills).\nMengembangkan jiwa kewirausahaan (technopreneurship).\nMeningkatkan kemitraan strategis dengan Industri (DUDI).";
                        $misiText = $settings['profil_misi'] ?? $defaultMisi;
                        $misiArray = explode("\n", trim($misiText));

                        foreach ($misiArray as $misi) :
                            if (!empty(trim($misi))) :
                        ?>
                                <li class="d-flex mb-3">
                                    <i class="bi bi-check-circle-fill text-primary-custom me-3 mt-1"></i>
                                    <span><?= esc(trim($misi)) ?></span>
                                </li>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>