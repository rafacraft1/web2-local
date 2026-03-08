<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-plus-circle me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/galeri') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/galeri/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label fw-semibold">Judul Foto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= old('title') ?>" placeholder="Contoh: Kunjungan Industri ke PT. Telkom">
                    <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label fw-semibold">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="3" placeholder="Jelaskan momen di dalam foto ini (Maks. 255 karakter)"><?= old('description') ?></textarea>
                    <div class="invalid-feedback"><?= $errors['description'] ?? '' ?></div>
                </div>

                <div class="col-md-12 mb-4">
                    <label for="image_input" class="form-label fw-semibold">Unggah Foto <span class="text-danger">*</span></label>
                    <input type="file" class="form-control <?= isset($errors['image_base64']) ? 'is-invalid' : '' ?>" id="image_input" accept="image/*" required>
                    <div class="invalid-feedback"><?= $errors['image_base64'] ?? '' ?></div>

                    <div id="compression_status" class="mt-2 small fw-semibold"></div>

                    <input type="hidden" name="image_base64" id="image_base64">
                </div>
            </div>

            <hr class="mt-0 mb-4">

            <div class="text-end">
                <button type="submit" id="btnSubmit" class="btn btn-primary" disabled><i class="bi bi-upload me-1"></i> Simpan ke Galeri</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('extra_scripts') ?>
<script>
    document.getElementById('image_input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const statusText = document.getElementById('compression_status');
        const btnSubmit = document.getElementById('btnSubmit');
        const hiddenInput = document.getElementById('image_base64');

        if (!file) {
            hiddenInput.value = '';
            btnSubmit.disabled = true;
            statusText.innerHTML = '';
            return;
        }

        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar!');
            this.value = '';
            return;
        }

        statusText.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Sedang memproses dan mengompresi foto...</span>';
        btnSubmit.disabled = true;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Resolusi maksimal 1200px agar foto galeri tetap tajam
                const MAX_SIZE = 1200;
                let width = img.width;
                let height = img.height;

                if (width > height && width > MAX_SIZE) {
                    height *= MAX_SIZE / width;
                    width = MAX_SIZE;
                } else if (height > MAX_SIZE) {
                    width *= MAX_SIZE / height;
                    height = MAX_SIZE;
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                let quality = 0.9;
                let dataUrl = canvas.toDataURL('image/webp', quality);

                // Target kompresi sekitar maksimal 100KB untuk galeri (~135000 karakter base64)
                let maxLength = 135000;

                while (dataUrl.length > maxLength && quality > 0.1) {
                    quality -= 0.1;
                    dataUrl = canvas.toDataURL('image/webp', quality);
                }

                hiddenInput.value = dataUrl;

                let finalSizeKb = Math.round((dataUrl.length * 3 / 4) / 1024);
                statusText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Foto berhasil dikompresi ke WebP (${finalSizeKb} KB). Siap diunggah!</span>`;

                btnSubmit.disabled = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
<?= $this->endSection() ?>