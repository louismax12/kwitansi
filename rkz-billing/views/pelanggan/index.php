<?php
// views/pelanggan/index.php
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Master Data Pelanggan</h4>
    </div>
    <div class="card-body">
        <form action="index.php?c=pelanggan" method="POST" class="form-inline mb-4">
            <input type="text" name="nama_pelanggan" class="form-control mr-2" placeholder="Nama Pelanggan" required>
            <input type="text" name="no_hp" class="form-control mr-2" placeholder="No HP">
            <input type="text" name="alamat" class="form-control mr-2" placeholder="Alamat">
            <button type="submit" class="btn btn-primary">Tambah Pelanggan</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pelangganList)): ?>
                        <tr><td colspan="5" class="text-center">Belum ada data pelanggan</td></tr>
                    <?php else: ?>
                        <?php foreach ($pelangganList as $p): ?>
                            <tr>
                                <td><?php echo $p['id_pelanggan']; ?></td>
                                <td><?php echo htmlspecialchars((string)$p['nama_pelanggan']); ?></td>
                                <td><?php echo htmlspecialchars((string)$p['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars((string)$p['alamat']); ?></td>
                                <td>
                                    <a href="index.php?c=pelanggan&a=edit&id=<?php echo $p['id_pelanggan']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="index.php?c=pelanggan&a=delete&id=<?php echo $p['id_pelanggan']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pelanggan ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
