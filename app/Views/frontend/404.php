<?= $this->extend('frontend/layout/template') ?>

<?= $this->section('content') ?>

<div style="height: 90px; background-color: #111827;"></div>

<section class="d-flex align-items-center justify-content-center bg-light" style="min-height: calc(100vh - 90px);">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in" data-aos-duration="800">
                <div class="mb-4 position-relative d-inline-block">
                    <h1 class="fw-extrabold text-primary-custom" style="font-size: 8rem; line-height: 1; text-shadow: 4px 4px 0px rgba(79, 70, 229, 0.1);">404</h1>
                    <div class="position-absolute top-50 start-50 translate-middle bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-emoji-frown text-warning" style="font-size: 3rem;"></i>
                    </div>
                </div>

                <h2 class="display-6 fw-bold text-dark mb-3">Waduh, Kesasar Ya?</h2>
                <p class="lead text-secondary mb-5 mx-auto" style="max-width: 500px;">
                    Halaman yang Anda cari mungkin telah dihapus, diubah namanya, atau memang tidak pernah ada. Mari kita kembali ke tempat yang aman.
                </p>

                <a href="<?= base_url() ?>" class="btn btn-primary-custom px-5 py-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2 transition-hover">
                    <i class="bi bi-house-door-fill"></i> Kembali ke Beranda
                </a>

                <div class="mt-5 pt-4">
                    <p class="text-muted small">Atau coba jelajahi menu lain di atas.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<style>
    .transition-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2) !important;
    }
</style>
<?= $this->endSection() ?>