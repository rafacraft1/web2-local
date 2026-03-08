<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/mitra') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/mitra/update/' . $safeId) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama Instansi / Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= old('nama', $mitra['nama']) ?>">
                    <div class="invalid-feedback"><?= $errors['nama'] ?? '' ?></div>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label fw-semibold d-block">Logo Saat Ini</label>
                    <div class="bg-light p-3 rounded-3 border d-inline-block mb-3">
                        <img src="<?= base_url('uploads/mitra/' . $mitra['logo']) ?>" alt="Logo Mitra" class="object-fit-contain" width="150" height="80">
                    </div>

                    <label for="image_input" class="form-label fw-semibold d-block">Ganti Logo <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="file" class="form-control" id="image_input" accept="image/*">
                    <div id="compression_status" class="mt-2 small fw-semibold"></div>
                    <input type="hidden" name="logo_base64" id="logo_base64">
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
        const hiddenInput = document.getElementById('logo_base64');

        if (!file) {
            hiddenInput.value = '';
            btnSubmit.disabled = false;
            statusText.innerHTML = '';
            return;
        }

        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar!');
            this.value = '';
            return;
        }

        statusText.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Mengompresi logo...</span>';
        btnSubmit.disabled = true;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const MAX_SIZE = 500;
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
                let maxLength = 68000;

                while (dataUrl.length > maxLength && quality > 0.1) {
                    quality -= 0.1;
                    dataUrl = canvas.toDataURL('image/webp', quality);
                }

                hiddenInput.value = dataUrl;

                let finalSizeKb = Math.round((dataUrl.length * 3 / 4) / 1024);
                statusText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Logo siap diunggah! (${finalSizeKb} KB WebP).</span>`;

                btnSubmit.disabled = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
<?= $this->endSection() ?>