<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-envelope-open me-2"></i> <?= esc($title) ?></h4>
    <div>
        <a href="<?= base_url('panel/pesan') ?>" class="btn btn-outline-secondary shadow-sm me-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <form action="<?= base_url('panel/pesan/delete/' . $safeId) ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                <i class="bi bi-trash me-1"></i> Hapus Pesan
            </button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-person fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><?= esc($pesan['name']) ?></h5>
                    <a href="mailto:<?= esc($pesan['email']) ?>" class="text-decoration-none text-muted small"><i class="bi bi-envelope-at me-1"></i> <?= esc($pesan['email']) ?></a>
                </div>
            </div>
            <div class="text-end text-muted">
                <small class="d-block"><i class="bi bi-calendar3 me-1"></i> <?= date('d F Y', strtotime($pesan['created_at'])) ?></small>
                <small><i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($pesan['created_at'])) ?> WIB</small>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-secondary mb-3">Subjek: <span class="text-dark"><?= esc($pesan['subject']) ?></span></h6>

            <div class="bg-light p-4 rounded-3 border">
                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.6;"><?= esc($pesan['message']) ?></p>
            </div>
        </div>

        <div class="text-end mt-4 pt-3 border-top">
            <a href="mailto:<?= esc($pesan['email']) ?>?subject=Balasan: <?= esc($pesan['subject']) ?>" class="btn btn-primary">
                <i class="bi bi-reply-fill me-1"></i> Balas via Email
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>