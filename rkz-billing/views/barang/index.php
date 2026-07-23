<?php
// views/barang/index.php
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Master Data Barang & Tindakan</h4>
    </div>
    <div class="card-body">
        <form action="index.php?c=barang" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" name="barcode" class="form-control" placeholder="Barcode (Opsional)">
                </div>
                <div class="col-md-3">
                    <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang/Tindakan" required>
                </div>
                <div class="col-md-2">
                    <select name="kategori" class="form-control">
                        <option value="Umum">Umum</option>
                        <option value="Obat">Obat</option>
                        <option value="Alkes">Alkes</option>
                        <option value="Tindakan">Tindakan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="harga" class="form-control" placeholder="Harga (Rp)" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="stok" class="form-control" placeholder="Stok" value="0">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Tambah</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barangList)): ?>
                        <tr><td colspan="6" class="text-center">Belum ada data barang</td></tr>
                    <?php else: ?>
                        <?php foreach ($barangList as $b): ?>
                            <tr>
                                <td><?php echo $b['id_barang']; ?></td>
                                <td><?php echo htmlspecialchars($b['barcode']); ?></td>
                                <td><?php echo htmlspecialchars($b['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($b['kategori']); ?></td>
                                <td>Rp <?php echo number_format($b['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $b['stok']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
