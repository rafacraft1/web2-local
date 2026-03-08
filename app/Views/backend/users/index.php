<?= $this->extend('backend/layout/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-people me-2"></i> <?= esc($title) ?></h4>
    <a href="<?= base_url('panel/users/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold text-secondary">Daftar Akun Sistem</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Info Pengguna</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($users as $user) : ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" alt="Avatar" width="40" height="40" class="rounded-circle me-3 object-fit-cover shadow-sm">
                                    <?php else: ?>
                                        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person fs-5"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0 fw-semibold"><?= esc($user['nama_lengkap']) ?></h6>
                                        <small class="text-muted"><?= esc($user['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= esc($user['username']) ?></span></td>
                            <td>
                                <span class="badge bg-info text-dark text-capitalize">
                                    <?= esc($user['nama_role'] ?? $user['role']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php if (isset($user['is_active']) && $user['is_active'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php
                                // Panggil library Encrypter CI4 dan ubah ke format HEX
                                $encrypter = \Config\Services::encrypter();
                                $safeId = bin2hex($encrypter->encrypt($user['id']));

                                // Cek apakah baris ini adalah user yang sedang login ATAU memiliki role admin
                                $isSelf = session()->get('user_id') == $user['id'];
                                $isAdmin = $user['role'] === 'admin';
                                ?>

                                <a href="<?= base_url('panel/users/edit/' . $safeId) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <?php if ($isSelf || $isAdmin): ?>
                                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Akun ini dilindungi sistem">
                                        <i class="bi bi-shield-lock"></i>
                                    </button>
                                <?php else: ?>

                                    <form action="<?= base_url('panel/users/toggle/' . $safeId) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <?php if ($user['is_active'] == 1): ?>
                                            <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Yakin ingin menonaktifkan akun ini? Akun ini tidak akan bisa login.')" title="Nonaktifkan Akun">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin mengaktifkan kembali akun ini?')" title="Aktifkan Akun">
                                                <i class="bi bi-check2-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <form action="<?= base_url('panel/users/delete/' . $safeId) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen? Data yang dihapus tidak dapat dikembalikan.')" title="Hapus Permanen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>