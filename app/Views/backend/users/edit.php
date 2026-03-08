<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/users') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/users/update/' . $safeId) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>">
                    <div class="invalid-feedback"><?= $errors['nama_lengkap'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username', $user['username']) ?>">
                    <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email', $user['email']) ?>">
                    <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>

                    <?php
                    $isSelf = session()->get('user_id') == $user['id'];
                    $isAdmin = $user['role'] === 'admin';
                    $isProtected = $isSelf || $isAdmin;
                    ?>

                    <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" <?= $isProtected ? 'disabled' : 'name="role"' ?>>
                        <option value="">-- Pilih Role --</option>
                        <?php $currentRole = old('role', $user['role']); ?>
                        <?php foreach ($roles as $r): ?>
                            <?php
                            // Disable opsi admin jika admin sudah ada di database DAN user yang sedang diedit ini BUKAN admin
                            $disableAdminOption = ($r['slug_role'] === 'admin' && $adminExists && !$isAdmin);
                            ?>
                            <option value="<?= $r['slug_role'] ?>" <?= $currentRole == $r['slug_role'] ? 'selected' : '' ?> <?= $disableAdminOption ? 'disabled' : '' ?>>
                                <?= $r['nama_role'] ?> <?= $disableAdminOption ? '(Kuota Penuh)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if ($isProtected): ?>
                        <input type="hidden" name="role" value="<?= $user['role'] ?>">
                        <small class="text-warning mt-1 d-block">
                            <i class="bi bi-shield-lock"></i> Role akun ini tidak dapat diubah demi keamanan.
                        </small>
                    <?php endif; ?>

                    <div class="invalid-feedback"><?= $errors['role'] ?? '' ?></div>
                </div>

                <div class="col-12 mb-4">
                    <label for="password" class="form-label fw-semibold">Password Baru <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                    <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                    <small class="text-muted">Isi hanya jika Anda ingin mereset password pengguna ini.</small>
                </div>
            </div>

            <hr class="mt-0 mb-4">

            <div class="text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>