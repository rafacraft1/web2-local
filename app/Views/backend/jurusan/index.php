<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-bookmark-star me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/jurusan/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jurusan
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%" class="text-center">Gambar</th>
                        <th width="25%">Nama Jurusan</th>
                        <th width="40%">Deskripsi Singkat</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($jurusan as $row) :
                        $encrypter = \Config\Services::encrypter();
                        $safeId = bin2hex($encrypter->encrypt($row['id']));
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <img src="<?= base_url('uploads/jurusan/' . $row['image']) ?>" alt="<?= esc($row['name']) ?>" class="rounded-3 object-fit-cover shadow-sm" width="80" height="60">
                            </td>
                            <td>
                                <h6 class="mb-1 fw-bold"><?= esc($row['icon']) ?> <?= esc($row['name']) ?></h6>
                                <span class="badge bg-light text-muted border">/jurusan/<?= esc($row['slug']) ?></span>
                            </td>
                            <td><small class="text-muted"><?= esc($row['short_desc']) ?></small></td>
                            <td class="text-center">
                                <a href="<?= base_url('panel/jurusan/edit/' . $safeId) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= base_url('panel/jurusan/delete/' . $safeId) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jurusan ini beserta gambarnya?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($jurusan)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data jurusan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>