<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Atur Menu untuk: <span class="text-primary"><?= esc($role['nama_role']) ?></span></h5>
                <a href="<?= base_url('panel/roles') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 mb-4">
                    <small><i class="bi bi-info-circle me-1"></i> Centang menu yang ingin ditampilkan di sidebar untuk role ini.</small>
                </div>

                <form action="<?= base_url('panel/roles/saveAccess/' . $role['slug_role']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="list-group mb-4">
                        <?php foreach ($menus as $menu): ?>
                            <label class="list-group-item d-flex gap-3 align-items-center cursor-pointer">
                                <input class="form-check-input flex-shrink-0 fs-5" type="checkbox" name="menu_id[]" value="<?= $menu['id'] ?>"
                                    <?= in_array($menu['id'], $role_menus) ? 'checked' : '' ?>>

                                <span class="pt-1 form-checked-content d-flex align-items-center">
                                    <i class="<?= esc($menu['icon']) ?> fs-5 me-3 text-muted"></i>
                                    <div>
                                        <strong class="d-block"><?= esc($menu['title']) ?></strong>
                                        <small class="text-muted d-block mt-1">URL: <code><?= esc($menu['url']) ?></code></small>
                                    </div>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Hak Akses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .cursor-pointer:hover {
        background-color: #f8f9fa;
    }
</style>
<?= $this->endSection() ?>