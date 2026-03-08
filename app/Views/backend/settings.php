<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-sliders me-2"></i>Konfigurasi Website</h6>
    </div>
    <div class="card-body">

        <form action="<?= base_url('panel/settings/update') ?>" method="POST" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <ul class="nav nav-tabs mb-4 flex-wrap" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">Informasi Umum</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="kontak-tab" data-bs-toggle="tab" data-bs-target="#kontak" type="button" role="tab">Kontak & Lokasi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="sosmed-tab" data-bs-toggle="tab" data-bs-target="#sosmed" type="button" role="tab">Sosial Media</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">Banner (Hero)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-primary" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab">Konten Profil</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab">Statistik</button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabContent">

                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">

                        <div class="col-md-12 mb-4 border-bottom pb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-danger mb-0"><i class="bi bi-shield-exclamation me-2"></i> Mode Pemeliharaan (Maintenance)</h6>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input shadow-none" type="checkbox" role="switch" id="maintenanceToggle" style="cursor: pointer;" <?= (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label small fs-6 ms-1" for="maintenanceToggle" id="maintenanceLabel">
                                        <?= (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? '<span class="text-danger fw-bold">Aktif</span>' : '<span class="text-secondary fw-bold">Nonaktif</span>' ?>
                                    </label>
                                </div>
                            </div>
                            <div class="alert alert-warning border-0 small mt-2 mb-0">
                                <i class="bi bi-info-circle-fill me-1"></i> Jika <strong>Aktif</strong>, pengunjung tidak bisa mengakses halaman depan web. Perubahan pada tombol saklar di atas akan <strong>langsung tersimpan otomatis</strong> tanpa perlu menekan tombol Simpan di bawah.
                            </div>
                        </div>
                        <div class="col-md-12 mb-4 border-bottom pb-4">
                            <label class="form-label fw-bold text-dark">Logo Website</label>
                            <div class="d-flex align-items-center gap-4 mt-2">
                                <div id="logoPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center p-2 shadow-sm" style="width: 120px; height: 120px;">
                                    <?php if (!empty($settings['logo_image'])): ?>
                                        <img src="<?= base_url('uploads/logo/' . $settings['logo_image']) ?>" alt="Logo Web" class="img-fluid object-fit-contain h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50" style="font-size: 3rem;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" name="logo_image" id="logoInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                    <div id="compressMsg" class="mt-2 small"></div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle me-1"></i> Sistem otomatis mengompresi logo menjadi <strong>WEBP (Target ~20KB)</strong> sebelum diunggah.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Nama Website / Sekolah</label>
                            <input type="text" class="form-control" name="nama_web" value="<?= esc($settings['nama_web'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Logo Text (Highlight)</label>
                            <input type="text" class="form-control" name="logo_text_highlight" value="<?= esc($settings['logo_text_highlight'] ?? '') ?>">
                            <small class="text-muted">Teks cadangan jika logo gambar tidak digunakan.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Logo Text (Normal)</label>
                            <input type="text" class="form-control" name="logo_text_normal" value="<?= esc($settings['logo_text_normal'] ?? '') ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Deskripsi Footer</label>
                            <textarea class="form-control" name="footer_desc" rows="3"><?= esc($settings['footer_desc'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="kontak" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email Utama</label>
                            <input type="email" class="form-control" name="email_kontak" value="<?= esc($settings['email_kontak'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" class="form-control" name="telepon" value="<?= esc($settings['telepon'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor WhatsApp (Gunakan awalan 62)</label>
                            <input type="text" class="form-control" name="whatsapp" value="<?= esc($settings['whatsapp'] ?? '') ?>" placeholder="6281234567890">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea class="form-control" name="alamat" rows="2"><?= esc($settings['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-12 mb-3 mt-2">
                            <h6 class="fw-bold border-bottom pb-2">Peta Lokasi (Google Maps)</h6>
                            <label class="form-label fw-semibold">Kode Iframe Maps</label>
                            <textarea class="form-control text-monospace" name="iframe_maps" rows="4" placeholder='<iframe src="..."></iframe>' style="font-family: monospace;"><?= esc($settings['iframe_maps'] ?? '') ?></textarea>
                            <small class="text-muted">Salin dan tempel kode "Sematkan Peta" (Embed Map) dari Google Maps.</small>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="sosmed" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-facebook text-primary me-2"></i>Facebook URL</label>
                            <input type="url" class="form-control" name="facebook" value="<?= esc($settings['facebook'] ?? '') ?>" placeholder="https://facebook.com/namasekolah">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-instagram text-danger me-2"></i>Instagram URL</label>
                            <input type="url" class="form-control" name="instagram" value="<?= esc($settings['instagram'] ?? '') ?>" placeholder="https://instagram.com/namasekolah">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-youtube text-danger me-2"></i>YouTube URL</label>
                            <input type="url" class="form-control" name="youtube" value="<?= esc($settings['youtube'] ?? '') ?>" placeholder="https://youtube.com/@namasekolah">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-tiktok text-dark me-2"></i>TikTok URL</label>
                            <input type="url" class="form-control" name="tiktok" value="<?= esc($settings['tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@namasekolah">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="hero" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 mb-4 border-bottom pb-4">
                            <h6 class="fw-bold bg-light p-2 rounded">Banner Beranda Utama</h6>
                            <label class="form-label fw-bold text-dark mt-2">Gambar Background Beranda</label>
                            <div class="d-flex align-items-center gap-4 mt-2 mb-3">
                                <div id="heroPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm" style="width: 240px; height: 120px;">
                                    <?php if (!empty($settings['hero_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['hero_image']) ?>" alt="Hero Web" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50" style="font-size: 3rem;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" name="hero_image" id="heroInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                    <div id="heroCompressMsg" class="mt-2 small"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Teks Badge (Atas Judul)</label>
                                    <input type="text" class="form-control" name="hero_badge" value="<?= esc($settings['hero_badge'] ?? '') ?>" placeholder="Contoh: ✨ Penerimaan Siswa Baru">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Judul Banner</label>
                                    <input type="text" class="form-control" name="hero_title" value="<?= esc($settings['hero_title'] ?? '') ?>">
                                    <small class="text-muted">Gunakan &lt;span class="text-gradient"&gt; untuk efek gradien warna.</small>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Deskripsi / Sub-judul</label>
                                    <textarea class="form-control" name="hero_desc" rows="3"><?= esc($settings['hero_desc'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold bg-light p-2 rounded mb-3">Banner Halaman Sub-Menu</h6>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark">Background Halaman Berita</label>
                                <div id="headerBeritaPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 120px;">
                                    <?php if (!empty($settings['header_berita_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['header_berita_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="header_berita_image" id="headerBeritaInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div id="headerBeritaCompressMsg" class="mt-1 small"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark">Background Halaman Jurusan</label>
                                <div id="headerJurusanPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 120px;">
                                    <?php if (!empty($settings['header_jurusan_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['header_jurusan_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="header_jurusan_image" id="headerJurusanInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div id="headerJurusanCompressMsg" class="mt-1 small"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark">Background Halaman Profil</label>
                                <div id="headerProfilPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 120px;">
                                    <?php if (!empty($settings['header_profil_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['header_profil_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="header_profil_image" id="headerProfilInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div id="headerProfilCompressMsg" class="mt-1 small"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark">Background Halaman Galeri</label>
                                <div id="headerGaleriPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 120px;">
                                    <?php if (!empty($settings['header_galeri_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['header_galeri_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="header_galeri_image" id="headerGaleriInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div id="headerGaleriCompressMsg" class="mt-1 small"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark">Background Halaman Kontak</label>
                                <div id="headerKontakPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 120px;">
                                    <?php if (!empty($settings['header_kontak_image'])): ?>
                                        <img src="<?= base_url('uploads/hero/' . $settings['header_kontak_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control form-control-sm" name="header_kontak_image" id="headerKontakInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div id="headerKontakCompressMsg" class="mt-1 small"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="profil" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 mb-4 border-bottom pb-4">
                            <h6 class="fw-bold bg-light p-2 rounded text-primary"><i class="bi bi-building me-2"></i>Bagian Identitas & Sejarah</h6>
                            <div class="row mt-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Foto Gedung / Lingkungan</label>
                                    <div id="profilSejarahPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 150px;">
                                        <?php if (!empty($settings['profil_sejarah_image'])): ?>
                                            <img src="<?= base_url('uploads/hero/' . $settings['profil_sejarah_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                        <?php else: ?>
                                            <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" class="form-control form-control-sm" name="profil_sejarah_image" id="profilSejarahInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                    <div id="profilSejarahCompressMsg" class="mt-1 small"></div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tahun Berdiri</label>
                                        <input type="text" class="form-control" name="profil_tahun_berdiri" value="<?= esc($settings['profil_tahun_berdiri'] ?? '') ?>" placeholder="Contoh: 2005">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul 'Tentang Kami'</label>
                                        <input type="text" class="form-control" name="profil_tentang_judul" value="<?= esc($settings['profil_tentang_judul'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Teks 'Tentang Kami'</label>
                                        <textarea class="form-control" name="profil_tentang_teks" rows="6" placeholder="Bisa menggunakan Enter (baris baru)..."><?= esc($settings['profil_tentang_teks'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4 border-bottom pb-4">
                            <h6 class="fw-bold bg-light p-2 rounded text-primary"><i class="bi bi-person-badge me-2"></i>Bagian Pimpinan / Kepala Sekolah</h6>
                            <div class="row mt-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Foto Pimpinan</label>
                                    <div id="profilKepsekPreviewContainer" class="bg-light border rounded-3 d-flex align-items-center justify-content-center overflow-hidden shadow-sm mb-2" style="height: 150px;">
                                        <?php if (!empty($settings['profil_kepsek_image'])): ?>
                                            <img src="<?= base_url('uploads/hero/' . $settings['profil_kepsek_image']) ?>" class="img-fluid object-fit-cover w-100 h-100">
                                        <?php else: ?>
                                            <i class="bi bi-person text-muted opacity-50 fs-1"></i>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" class="form-control form-control-sm" name="profil_kepsek_image" id="profilKepsekInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                    <div id="profilKepsekCompressMsg" class="mt-1 small"></div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Gelar & Nama Lengkap</label>
                                        <input type="text" class="form-control" name="profil_kepsek_nama" value="<?= esc($settings['profil_kepsek_nama'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Jabatan Pimpinan</label>
                                        <input type="text" class="form-control" name="profil_kepsek_jabatan" value="<?= esc($settings['profil_kepsek_jabatan'] ?? '') ?>" placeholder="Contoh: Kepala SMK Kreatif Nusantara">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Pesan / Kutipan (Quote)</label>
                                        <textarea class="form-control" name="profil_kepsek_quote" rows="4"><?= esc($settings['profil_kepsek_quote'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <h6 class="fw-bold bg-light p-2 rounded text-primary"><i class="bi bi-bullseye me-2"></i>Visi & Misi</h6>
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Visi Sekolah</label>
                                    <textarea class="form-control" name="profil_visi" rows="3"><?= esc($settings['profil_visi'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Misi Sekolah</label>
                                    <textarea class="form-control" name="profil_misi" rows="6" placeholder="Ketik satu misi, lalu tekan Enter untuk misi selanjutnya..."><?= esc($settings['profil_misi'] ?? '') ?></textarea>
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Gunakan "Enter" (baris baru) untuk memisahkan setiap poin misi.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="stats" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Mitra Industri</label>
                            <input type="number" class="form-control" name="stat_mitra" value="<?= esc($settings['stat_mitra'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Fasilitas (%)</label>
                            <input type="number" class="form-control" name="stat_fasilitas" value="<?= esc($settings['stat_fasilitas'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Program Keahlian</label>
                            <input type="number" class="form-control" name="stat_program" value="<?= esc($settings['stat_program'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Alumni Sukses</label>
                            <input type="number" class="form-control" name="stat_alumni" value="<?= esc($settings['stat_alumni'] ?? '') ?>">
                        </div>
                    </div>
                </div>

            </div>

            <hr class="my-4">
            <div class="d-flex justify-content-end pb-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        async function handleImageCompression(inputElement, previewContainerId, msgElementId, maxDim, targetSizeKB, fileType = 'image/webp') {
            const file = inputElement.files[0];
            if (!file) return;

            const msgElement = document.getElementById(msgElementId);
            const previewContainer = document.getElementById(previewContainerId);

            msgElement.innerHTML = '<span class="text-primary fw-semibold"><i class="spinner-border spinner-border-sm me-1"></i> Memproses...</span>';

            const maxFileSize = targetSizeKB * 1024;

            try {
                const img = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const image = new Image();
                        image.onload = () => resolve(image);
                        image.onerror = reject;
                        image.src = event.target.result;
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });

                let width = img.width;
                let height = img.height;

                if (width > height && width > maxDim) {
                    height *= maxDim / width;
                    width = maxDim;
                } else if (height > maxDim) {
                    width *= maxDim / height;
                    height = maxDim;
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                let quality = 0.9;
                let blob = await new Promise(resolve => canvas.toBlob(resolve, fileType, quality));

                while (blob.size > maxFileSize && quality > 0.3) {
                    quality -= 0.1;
                    blob = await new Promise(resolve => canvas.toBlob(resolve, fileType, quality));
                }

                const ext = fileType === 'image/webp' ? '.webp' : (file.name.match(/\.[0-9a-z]+$/i) || ['.jpg'])[0];
                const newFileName = file.name.replace(/\.[^/.]+$/, "") + "_compressed" + ext;
                const newFile = new File([blob], newFileName, {
                    type: fileType,
                    lastModified: Date.now()
                });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                inputElement.files = dataTransfer.files;

                const previewUrl = URL.createObjectURL(blob);
                if (previewContainer) {
                    previewContainer.innerHTML = `<img src="${previewUrl}" class="img-fluid object-fit-cover w-100 h-100 rounded-3">`;
                }

                const finalSizeKB = (blob.size / 1024).toFixed(2);
                msgElement.innerHTML = `<span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> ${finalSizeKB} KB.</span>`;
            } catch (error) {
                console.error("Compression error:", error);
                msgElement.innerHTML = '<span class="text-danger small fw-semibold">Gagal memproses.</span>';
            }
        }

        // --- BINDING EVENT LISTENERS UNTUK SEMUA INPUT GAMBAR ---

        // 1. Logo
        const logoInput = document.getElementById('logoInput');
        if (logoInput) logoInput.addEventListener('change', function() {
            handleImageCompression(this, 'logoPreviewContainer', 'compressMsg', 400, 20);
        });

        // 2. Banner/Hero Utama
        const heroInput = document.getElementById('heroInput');
        if (heroInput) heroInput.addEventListener('change', function() {
            handleImageCompression(this, 'heroPreviewContainer', 'heroCompressMsg', 1200, 500);
        });

        // 3. Header Halaman Berita
        const headerBeritaInput = document.getElementById('headerBeritaInput');
        if (headerBeritaInput) headerBeritaInput.addEventListener('change', function() {
            handleImageCompression(this, 'headerBeritaPreviewContainer', 'headerBeritaCompressMsg', 1920, 500);
        });

        // 4. Header Halaman Jurusan
        const headerJurusanInput = document.getElementById('headerJurusanInput');
        if (headerJurusanInput) headerJurusanInput.addEventListener('change', function() {
            handleImageCompression(this, 'headerJurusanPreviewContainer', 'headerJurusanCompressMsg', 1920, 500);
        });

        // 5. Header Halaman Profil
        const headerProfilInput = document.getElementById('headerProfilInput');
        if (headerProfilInput) headerProfilInput.addEventListener('change', function() {
            handleImageCompression(this, 'headerProfilPreviewContainer', 'headerProfilCompressMsg', 1920, 500);
        });

        // 6. Header Halaman Galeri
        const headerGaleriInput = document.getElementById('headerGaleriInput');
        if (headerGaleriInput) headerGaleriInput.addEventListener('change', function() {
            handleImageCompression(this, 'headerGaleriPreviewContainer', 'headerGaleriCompressMsg', 1920, 500);
        });

        // 7. Header Halaman Kontak
        const headerKontakInput = document.getElementById('headerKontakInput');
        if (headerKontakInput) headerKontakInput.addEventListener('change', function() {
            handleImageCompression(this, 'headerKontakPreviewContainer', 'headerKontakCompressMsg', 1920, 500);
        });

        // 8. Gambar Sejarah (Gedung/Lingkungan)
        const profilSejarahInput = document.getElementById('profilSejarahInput');
        if (profilSejarahInput) profilSejarahInput.addEventListener('change', function() {
            handleImageCompression(this, 'profilSejarahPreviewContainer', 'profilSejarahCompressMsg', 1000, 400);
        });

        // 9. Gambar Pimpinan / Kepala Sekolah
        const profilKepsekInput = document.getElementById('profilKepsekInput');
        if (profilKepsekInput) profilKepsekInput.addEventListener('change', function() {
            handleImageCompression(this, 'profilKepsekPreviewContainer', 'profilKepsekCompressMsg', 800, 300);
        });

        // --- 10. AJAX TOGGLE MAINTENANCE MODE ---
        const maintenanceToggle = document.getElementById('maintenanceToggle');
        const maintenanceLabel = document.getElementById('maintenanceLabel');
        const csrfName = '<?= csrf_token() ?>'; // Ambil nama token CSRF CI4

        if (maintenanceToggle) {
            maintenanceToggle.addEventListener('change', function() {
                const status = this.checked ? '1' : '0';
                const originalState = !this.checked;
                let currentCsrfHash = document.querySelector(`input[name="${csrfName}"]`).value;

                // Animasi Loading
                maintenanceLabel.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>';
                maintenanceToggle.disabled = true; // Kunci tombol saat memproses

                fetch('<?= base_url('panel/settings/toggle-maintenance') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `status=${status}&${csrfName}=${currentCsrfHash}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        maintenanceToggle.disabled = false; // Buka kunci tombol

                        // Update token CSRF untuk keamanan request selanjutnya jika di-klik lagi
                        if (data.csrfHash) {
                            document.querySelector(`input[name="${csrfName}"]`).value = data.csrfHash;
                        }

                        if (data.success) {
                            // Jika Sukses, ubah teks label
                            if (status === '1') {
                                maintenanceLabel.innerHTML = '<span class="text-danger fw-bold">Aktif</span>';
                            } else {
                                maintenanceLabel.innerHTML = '<span class="text-secondary fw-bold">Nonaktif</span>';
                            }
                        } else {
                            // Jika gagal dari server, kembalikan posisi tombol
                            alert(data.message || 'Terjadi kesalahan.');
                            maintenanceToggle.checked = originalState;
                            maintenanceLabel.innerHTML = originalState ? '<span class="text-danger fw-bold">Aktif</span>' : '<span class="text-secondary fw-bold">Nonaktif</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal terhubung ke server.');
                        maintenanceToggle.disabled = false;
                        maintenanceToggle.checked = originalState;
                        maintenanceLabel.innerHTML = originalState ? '<span class="text-danger fw-bold">Aktif</span>' : '<span class="text-secondary fw-bold">Nonaktif</span>';
                    });
            });
        }

    });
</script>
<?= $this->endSection() ?>