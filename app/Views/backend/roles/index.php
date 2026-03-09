<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold">Daftar Role Pengguna</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Slug Role</th>
                        <th width="30%">Nama Role</th>
                        <th width="30%">Deskripsi</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($roles as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge bg-secondary"><?= esc($row['slug_role']) ?></span></td>
                            <td class="fw-semibold"><?= esc($row['nama_role']) ?></td>
                            <td><?= esc($row['deskripsi']) ?></td>
                            <td class="text-center">
                                <?php if ($row['slug_role'] !== 'admin'): // Admin punya akses penuh, tidak perlu disetting 
                                ?>
                                    <a href="<?= base_url('panel/roles/access/' . $row['slug_role']) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-ui-checks"></i> Atur Akses
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted fst-italic"><small>Full Access</small></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>