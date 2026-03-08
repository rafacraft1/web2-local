<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/berita') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php $errors = session()->getFlashdata('errors'); ?>

        <form action="<?= base_url('panel/berita/update/' . $safeId) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="title" class="form-label fw-semibold">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= old('title', $berita['title']) ?>">
                    <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" id="category" name="category">
                        <?php $kat = old('category', $berita['category']); ?>
                        <option value="Prestasi" <?= $kat == 'Prestasi' ? 'selected' : '' ?>>Prestasi</option>
                        <option value="Kegiatan" <?= $kat == 'Kegiatan' ? 'selected' : '' ?>>Kegiatan Sekolah</option>
                        <option value="Akademik" <?= $kat == 'Akademik' ? 'selected' : '' ?>>Akademik</option>
                        <option value="Pengumuman" <?= $kat == 'Pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                    </select>
                    <div class="invalid-feedback"><?= $errors['category'] ?? '' ?></div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="excerpt" class="form-label fw-semibold">Kutipan Singkat (Excerpt) <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= isset($errors['excerpt']) ? 'is-invalid' : '' ?>" id="excerpt" name="excerpt" rows="2"><?= old('excerpt', $berita['excerpt']) ?></textarea>
                    <div class="invalid-feedback"><?= $errors['excerpt'] ?? '' ?></div>
                </div>

                <div class="col-12 mb-4">
                    <label for="content" class="form-label fw-semibold">Isi Berita Lengkap <span class="text-danger">*</span></label>
                    <?php if (isset($errors['content'])): ?>
                        <div class="text-danger small mb-2"><i class="bi bi-exclamation-triangle"></i> <?= $errors['content'] ?></div>
                    <?php endif; ?>
                    <textarea id="summernote" name="content"><?= old('content', $berita['content']) ?></textarea>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label fw-semibold d-block">Cover Saat Ini</label>
                    <img src="<?= base_url('uploads/berita/' . $berita['image']) ?>" alt="Cover" class="img-thumbnail mb-3" width="200">

                    <label for="image_input" class="form-label fw-semibold d-block">Ganti Cover <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="file" class="form-control" id="image_input" accept="image/*">
                    <div id="compression_status" class="mt-2 small fw-semibold"></div>
                    <input type="hidden" name="image_base64" id="image_base64">
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label fw-semibold text-transparent d-none d-md-block">.</label>
                    <label for="status" class="form-label fw-semibold">Status Publikasi <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status">
                        <?php $stat = old('status', $berita['status']); ?>
                        <option value="published" <?= $stat == 'published' ? 'selected' : '' ?>>Diterbitkan (Published)</option>
                        <option value="draft" <?= $stat == 'draft' ? 'selected' : '' ?>>Kembalikan ke Draft</option>
                    </select>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Ketik isi artikel atau berita di sini...',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    document.getElementById('image_input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const statusText = document.getElementById('compression_status');
        const btnSubmit = document.getElementById('btnSubmit');
        const hiddenInput = document.getElementById('image_base64');

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

        statusText.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Mengompresi gambar cover...</span>';
        btnSubmit.disabled = true;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

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
                let maxLength = 95000;

                while (dataUrl.length > maxLength && quality > 0.1) {
                    quality -= 0.1;
                    dataUrl = canvas.toDataURL('image/webp', quality);
                }

                hiddenInput.value = dataUrl;
                let finalSizeKb = Math.round((dataUrl.length * 3 / 4) / 1024);
                statusText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Cover siap! (${finalSizeKb} KB WebP).</span>`;
                btnSubmit.disabled = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
<?= $this->endSection() ?>