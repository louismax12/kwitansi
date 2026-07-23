<?php require_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Master Barang & Layanan</h3>
    <button class="btn btn-primary" onclick="openModal()">+ Tambah Barang</button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Barcode</th>
                    <th>Kategori</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($barang as $b): ?>
                <tr>
                    <td><?php echo $b['barcode']; ?></td>
                    <td><span class="badge bg-secondary"><?php echo $b['kategori']; ?></span></td>
                    <td><?php echo $b['nama_barang']; ?></td>
                    <td>Rp <?php echo number_format($b['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if($b['stok'] <= 5): ?>
                            <span class="text-danger fw-bold"><?php echo $b['stok']; ?></span>
                        <?php else: ?>
                            <?php echo $b['stok']; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick='editData(<?php echo json_encode($b); ?>)'>✏️ Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CRUD -->
<div id="modalBarang" class="modal" tabindex="-1" style="display: none; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="index.php?c=barang&a=save">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Barang</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_barang" id="f_id">
                    <div class="mb-3">
                        <label>Barcode</label>
                        <input type="text" name="barcode" id="f_barcode" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="kategori" id="f_kategori" class="form-select">
                            <option value="Obat">Obat</option>
                            <option value="Alkes">Alat Kesehatan</option>
                            <option value="Layanan">Layanan Medis</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nama Barang / Layanan</label>
                        <input type="text" name="nama_barang" id="f_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" id="f_harga" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stok" id="f_stok" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Barang';
    document.getElementById('f_id').value = '';
    document.getElementById('f_barcode').value = '';
    document.getElementById('f_nama').value = '';
    document.getElementById('f_harga').value = '';
    document.getElementById('f_stok').value = '0';
    document.getElementById('modalBarang').style.display = 'block';
}
function closeModal() {
    document.getElementById('modalBarang').style.display = 'none';
}
function editData(data) {
    document.getElementById('modalTitle').innerText = 'Edit Barang';
    document.getElementById('f_id').value = data.id_barang;
    document.getElementById('f_barcode').value = data.barcode || '';
    document.getElementById('f_kategori').value = data.kategori;
    document.getElementById('f_nama').value = data.nama_barang;
    document.getElementById('f_harga').value = data.harga;
    document.getElementById('f_stok').value = data.stok;
    document.getElementById('modalBarang').style.display = 'block';
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
