<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-images me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/galeri/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Foto
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%" class="text-center">Foto</th>
                        <th width="25%">Judul</th>
                        <th width="35%">Deskripsi</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($galeri as $row) :
                        $encrypter = \Config\Services::encrypter();
                        $safeId = bin2hex($encrypter->encrypt($row['id']));
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <img src="<?= base_url('uploads/galeri/' . $row['image']) ?>" alt="Galeri" class="rounded-3 object-fit-cover shadow-sm" width="120" height="80">
                            </td>
                            <td>
                                <h6 class="mb-1 fw-bold text-dark"><?= esc($row['title']) ?></h6>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($row['created_at'])) ?></small>
                            </td>
                            <td>
                                <p class="text-muted small mb-0"><?= esc($row['description']) ?></p>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('panel/galeri/edit/' . $safeId) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= base_url('panel/galeri/delete/' . $safeId) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto galeri ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($galeri)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada foto di galeri.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>