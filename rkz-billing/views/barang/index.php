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
                        <!-- <th>Barcode</th> -->
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barangList)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada data barang</td></tr>
                    <?php else: ?>
                        <?php foreach ($barangList as $b): ?>
                            <tr>
                                <td><?php echo $b['id_barang']; ?></td>
                                <!-- <td><?php echo htmlspecialchars($b['barcode']); ?></td> -->
                                <td><?php echo htmlspecialchars($b['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($b['kategori']); ?></td>
                                <td>Rp <?php echo number_format($b['harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($b['stok'] < 0): ?>
                                        <span class="badge badge-danger" style="font-size: 14px;"><?php echo $b['stok']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success" style="font-size: 14px;"><?php echo $b['stok']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalStok<?php echo $b['id_barang']; ?>">
                                        <i class="fas fa-plus"></i> Stok
                                    </button>
                                    
                                    <!-- Modal Stok Masuk -->
                                    <div class="modal fade" id="modalStok<?php echo $b['id_barang']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="index.php?c=barang&a=addStock" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Stok Masuk: <?php echo htmlspecialchars($b['nama_barang']); ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_barang" value="<?php echo $b['id_barang']; ?>">
                                                        <div class="form-group text-left">
                                                            <label>Jumlah Barang Masuk / Top-up</label>
                                                            <input type="number" name="stok_masuk" class="form-control" required min="1" placeholder="Misal: 50">
                                                            <small class="form-text text-muted">Stok saat ini: <strong><?php echo $b['stok']; ?></strong>. Angka yang Anda masukkan akan <strong>ditambahkan</strong> ke stok saat ini.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Stok</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
