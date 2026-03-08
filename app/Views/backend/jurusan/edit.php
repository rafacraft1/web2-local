<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/jurusan') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/jurusan/update/' . $safeId) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name', $jurusan['name']) ?>">
                    <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="icon" class="form-label fw-semibold">Ikon (Emoji) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['icon']) ? 'is-invalid' : '' ?>" id="icon" name="icon" value="<?= old('icon', $jurusan['icon']) ?>">
                    <div class="invalid-feedback"><?= $errors['icon'] ?? '' ?></div>
                </div>

                <div class="col-12 mb-3">
                    <label for="short_desc" class="form-label fw-semibold">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['short_desc']) ? 'is-invalid' : '' ?>" id="short_desc" name="short_desc" value="<?= old('short_desc', $jurusan['short_desc']) ?>">
                    <div class="invalid-feedback"><?= $errors['short_desc'] ?? '' ?></div>
                </div>

                <div class="col-12 mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5"><?= old('description', $jurusan['description']) ?></textarea>
                    <div class="invalid-feedback"><?= $errors['description'] ?? '' ?></div>
                </div>

                <div class="col-12 mb-4">
                    <label class="form-label fw-semibold d-block">Gambar Saat Ini</label>
                    <img src="<?= base_url('uploads/jurusan/' . $jurusan['image']) ?>" alt="Gambar Jurusan" class="img-thumbnail mb-3" width="200">

                    <label for="image_input" class="form-label fw-semibold d-block">Ganti Gambar <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="file" class="form-control" id="image_input" accept="image/*">
                    <small class="text-muted">Pilih gambar baru jika ingin mengganti. Maks ukuran target otomatis: 60KB.</small>

                    <div id="compression_status" class="mt-2 small fw-semibold"></div>

                    <input type="hidden" name="image_base64" id="image_base64">
                </div>
            </div>

            <hr class="mt-0 mb-4">

            <div class="text-end">
                <button type="submit" id="btnSubmit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
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
            btnSubmit.disabled = false; // Tombol tetap aktif jika tidak pilih gambar baru
            statusText.innerHTML = '';
            return;
        }

        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar!');
            this.value = '';
            return;
        }

        statusText.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Sedang memproses dan mengompresi gambar...</span>';
        btnSubmit.disabled = true;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const MAX_SIZE = 1024;
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
                let maxLength = 81000;

                while (dataUrl.length > maxLength && quality > 0.1) {
                    quality -= 0.1;
                    dataUrl = canvas.toDataURL('image/webp', quality);
                }

                hiddenInput.value = dataUrl;

                let finalSizeKb = Math.round((dataUrl.length * 3 / 4) / 1024);
                statusText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Berhasil dikompresi ke WebP (${finalSizeKb} KB). Siap disimpan!</span>`;

                btnSubmit.disabled = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
<?= $this->endSection() ?>