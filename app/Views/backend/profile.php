<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>

<?php
// Mendapatkan parameter tab aktif dari URL (jika ada)
$activeTab = isset($_GET['tab']) && $_GET['tab'] == 'password' ? 'password' : 'info';
?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 text-center pb-4 pt-5">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm border border-4 border-white" style="width: 130px; height: 130px;">
                <?php else: ?>
                    <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-white mx-auto" style="width: 130px; height: 130px;">
                        <i class="bi bi-person" style="font-size: 4rem;"></i>
                    </div>
                <?php endif; ?>
                <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" title="Aktif">
                    <span class="visually-hidden">Status Aktif</span>
                </span>
            </div>
            <h5 class="fw-bold mb-1 text-dark"><?= esc($user['nama_lengkap']) ?></h5>
            <p class="text-muted small mb-3">@<?= esc($user['username']) ?></p>
            <span class="badge bg-soft-primary text-primary text-capitalize px-3 py-2 rounded-pill"><?= esc(str_replace('-', ' ', $user['role'])) ?></span>

            <hr class="mt-4 mx-4">
            <div class="text-start px-4">
                <small class="text-muted fw-bold d-block mb-1">BERGABUNG SEJAK</small>
                <p class="mb-0 text-dark fw-semibold"><i class="bi bi-calendar3 me-2 text-primary"></i> <?= date('d M Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 rounded-top-4">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold <?= $activeTab == 'info' ? 'active' : '' ?>" id="info-tab" data-bs-toggle="tab" href="#info" role="tab">Informasi Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-danger <?= $activeTab == 'password' ? 'active' : '' ?>" id="password-tab" data-bs-toggle="tab" href="#password" role="tab"><i class="bi bi-shield-lock me-1"></i> Keamanan</a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabsContent">

                    <div class="tab-pane fade <?= $activeTab == 'info' ? 'show active' : '' ?>" id="info" role="tabpanel">
                        <form action="<?= base_url('panel/profile/updateInfo') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-4 pb-4 border-bottom">
                                <label class="form-label fw-semibold">Ubah Foto Profil (Avatar)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div id="avatarPreviewContainer" class="bg-light border rounded-circle d-flex align-items-center justify-content-center overflow-hidden shadow-sm flex-shrink-0" style="width: 80px; height: 80px;">
                                        <?php if (!empty($user['avatar'])): ?>
                                            <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" class="w-100 h-100 object-fit-cover">
                                        <?php else: ?>
                                            <i class="bi bi-camera text-muted fs-3"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control form-control-sm" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                                        <div id="avatarCompressMsg" class="mt-1 small"></div>
                                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Otomatis dikompres & dipotong (rasio 1:1).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_lengkap" value="<?= esc($user['nama_lengkap']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control bg-light" name="username" value="<?= esc($user['username']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= esc($user['email']) ?>" required>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 text-end border-top">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold"><i class="bi bi-save me-1"></i> Simpan Profil</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $activeTab == 'password' ? 'show active' : '' ?>" id="password" role="tabpanel">
                        <form action="<?= base_url('panel/profile/updatePassword') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="alert alert-warning bg-opacity-10 border-0 rounded-3 mb-4 d-flex">
                                <i class="bi bi-exclamation-triangle fs-4 text-warning me-3 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-warning mb-1">Perhatian Keamanan</h6>
                                    <p class="mb-0 small text-muted">Pastikan kata sandi baru Anda minimal 6 karakter. Jangan gunakan kata sandi yang sama dengan situs lain demi keamanan akun panel Anda.</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Lama</label>
                                <input type="password" class="form-control" name="password_lama" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <input type="password" class="form-control" name="password_baru" placeholder="Minimal 6 karakter" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Ulangi Password Baru</label>
                                    <input type="password" class="form-control" name="konfirmasi_password" placeholder="Ketik ulang password baru" required>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 text-end border-top">
                                <button type="submit" class="btn btn-danger px-4 rounded-pill fw-semibold"><i class="bi bi-shield-check me-1"></i> Perbarui Kata Sandi</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Skrip Kompresi Avatar (Client-side)
        async function handleAvatarCompression(inputElement, previewContainerId, msgElementId, maxDim, targetSizeKB) {
            const file = inputElement.files[0];
            if (!file) return;

            const msgElement = document.getElementById(msgElementId);
            const previewContainer = document.getElementById(previewContainerId);
            msgElement.innerHTML = '<span class="text-primary fw-semibold"><i class="spinner-border spinner-border-sm me-1"></i> Memproses...</span>';

            try {
                const img = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const image = new Image();
                        image.onload = () => resolve(image);
                        image.onerror = reject;
                        image.src = e.target.result;
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });

                // Auto Crop ke Rasio 1:1 (Square) untuk Avatar
                const size = Math.min(img.width, img.height);
                const startX = (img.width - size) / 2;
                const startY = (img.height - size) / 2;

                let finalDim = size > maxDim ? maxDim : size;
                const canvas = document.createElement('canvas');
                canvas.width = finalDim;
                canvas.height = finalDim;
                const ctx = canvas.getContext('2d');

                // Draw dengan crop di tengah
                ctx.drawImage(img, startX, startY, size, size, 0, 0, finalDim, finalDim);

                let quality = 0.9;
                let blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', quality));

                while (blob.size > (targetSizeKB * 1024) && quality > 0.3) {
                    quality -= 0.1;
                    blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', quality));
                }

                const newFile = new File([blob], "avatar_compressed.webp", {
                    type: 'image/webp',
                    lastModified: Date.now()
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                inputElement.files = dataTransfer.files;

                previewContainer.innerHTML = `<img src="${URL.createObjectURL(blob)}" class="w-100 h-100 object-fit-cover rounded-circle">`;
                msgElement.innerHTML = `<span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Selesai.</span>`;
            } catch (error) {
                msgElement.innerHTML = '<span class="text-danger small fw-semibold">Gagal memproses.</span>';
            }
        }

        const avatarInput = document.getElementById('avatarInput');
        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                // Dimensi max 400x400px, target size ~50KB
                handleAvatarCompression(this, 'avatarPreviewContainer', 'avatarCompressMsg', 400, 50);
            });
        }
    });
</script>
<?= $this->endSection() ?>