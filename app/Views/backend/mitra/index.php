<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-building me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/mitra/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mitra
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%" class="text-center">Logo</th>
                        <th width="55%">Nama Mitra / Instansi</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($mitra as $row) :
                        $encrypter = \Config\Services::encrypter();
                        $safeId = bin2hex($encrypter->encrypt($row['id']));
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <div class="bg-light p-2 rounded-3 border d-inline-block">
                                    <img src="<?= base_url('uploads/mitra/' . $row['logo']) ?>" alt="<?= esc($row['nama']) ?>" class="object-fit-contain" width="100" height="60">
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 fw-bold text-dark"><?= esc($row['nama']) ?></h6>
                                <small class="text-muted">Ditambahkan: <?= date('d M Y', strtotime($row['created_at'])) ?></small>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('panel/mitra/edit/' . $safeId) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= base_url('panel/mitra/delete/' . $safeId) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($mitra)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data mitra industri.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>