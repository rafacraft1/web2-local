<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-person-plus me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/users') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/users') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="Masukkan nama lengkap">
                    <div class="invalid-feedback"><?= $errors['nama_lengkap'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username') ?>" placeholder="Pilih username">
                    <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" placeholder="email@contoh.com">
                    <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>

                    <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role">
                        <option value="">-- Pilih Role --</option>
                        <?php $currentRole = old('role'); ?>
                        <?php foreach ($roles as $r): ?>
                            <?php
                            // Disable opsi admin HANYA jika admin sudah ada di database
                            $disableAdminOption = ($r['slug_role'] === 'admin' && $adminExists);
                            ?>
                            <option value="<?= $r['slug_role'] ?>" <?= $currentRole == $r['slug_role'] ? 'selected' : '' ?> <?= $disableAdminOption ? 'disabled' : '' ?>>
                                <?= $r['nama_role'] ?> <?= $disableAdminOption ? '(Kuota Penuh)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="invalid-feedback"><?= $errors['role'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Minimal 8 karakter">
                    <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                </div>
            </div>

            <hr class="mt-0 mb-4">

            <div class="text-end">
                <button type="reset" class="btn btn-light me-2">Reset</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>