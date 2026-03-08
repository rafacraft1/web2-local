<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-envelope me-2"></i> <?= esc($title) ?></h4>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold text-secondary">Daftar Pesan Masuk</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Pengirim</th>
                        <th width="40%">Subjek Pesan</th>
                        <th width="15%">Tanggal</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($pesan as $row) :
                        $encrypter = \Config\Services::encrypter();
                        $safeId = bin2hex($encrypter->encrypt($row['id']));

                        // Buat teks tebal jika pesan belum dibaca
                        $isUnread = $row['is_read'] == 0;
                        $textWeight = $isUnread ? 'fw-bold text-dark' : 'text-muted';
                    ?>
                        <tr class="<?= $isUnread ? 'bg-light' : '' ?>">
                            <td class="text-center <?= $textWeight ?>"><?= $no++ ?></td>
                            <td>
                                <span class="d-block <?= $textWeight ?>"><?= esc($row['name']) ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= esc($row['email']) ?></small>
                            </td>
                            <td>
                                <span class="<?= $textWeight ?>"><?= esc($row['subject']) ?></span>
                            </td>
                            <td>
                                <small class="<?= $textWeight ?>"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></small>
                            </td>
                            <td class="text-center">
                                <?php if ($isUnread): ?>
                                    <span class="badge bg-primary rounded-pill">Baru</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border">Dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('panel/pesan/read/' . $safeId) ?>" class="btn btn-sm btn-info text-white" title="Baca Pesan">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="<?= base_url('panel/pesan/delete/' . $safeId) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesan dari <?= esc($row['name']) ?>?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($pesan)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada pesan masuk saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>