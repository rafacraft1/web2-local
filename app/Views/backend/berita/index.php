<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-newspaper me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/berita/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tulis Berita
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%" class="text-center">Cover</th>
                        <th width="30%">Judul & Kategori</th>
                        <th width="15%">Penulis</th>
                        <th width="15%" class="text-center">Status / Waktu</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($berita as $row) :
                        $encrypter = \Config\Services::encrypter();
                        $safeId = bin2hex($encrypter->encrypt($row['id']));
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <img src="<?= base_url('uploads/berita/' . esc($row['image'], 'url')) ?>" alt="Cover" class="rounded-3 object-fit-cover shadow-sm" width="80" height="60">
                            </td>
                            <td>
                                <h6 class="mb-1 fw-bold text-dark"><?= esc($row['title']) ?></h6>
                                <span class="badge bg-light text-primary border border-primary border-opacity-25"><?= esc($row['category']) ?></span>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-secondary small"><i class="bi bi-person me-1"></i> <?= esc($row['penulis']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($row['status'] == 'published'): ?>
                                    <span class="badge bg-success mb-1">Diterbitkan</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mb-1">Draft</span>
                                <?php endif; ?>
                                <small class="d-block text-muted" style="font-size: 0.7rem;"><?= date('d M Y', strtotime($row['created_at'])) ?></small>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('panel/berita/edit/' . $safeId) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= base_url('panel/berita/delete/' . $safeId) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini secara permanen?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($berita)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada berita atau artikel yang ditulis.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>