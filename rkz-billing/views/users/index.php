<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-weight-bold text-dark">Manajemen User</h2>
    <a href="index.php?c=users&a=create" class="btn btn-primary shadow-sm"><i class="fas fa-plus"></i> Tambah User</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-top-0 pl-4">Username</th>
                        <th class="border-top-0">Hak Akses M1 (Admin)</th>
                        <th class="border-top-0">Hak Akses M2</th>
                        <th class="border-top-0 text-center pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="pl-4 align-middle font-weight-bold"><?php echo htmlspecialchars($u['username']); ?></td>
                        <td class="align-middle">
                            <?php if ($u['m1']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="align-middle">
                            <?php if ($u['m2']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center pr-4 align-middle">
                            <a href="index.php?c=users&a=edit&id=<?php echo urlencode($u['username']); ?>" class="btn btn-sm btn-outline-info mr-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if ($u['username'] !== $_SESSION['username']): ?>
                            <a href="index.php?c=users&a=delete&id=<?php echo urlencode($u['username']); ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data user.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
