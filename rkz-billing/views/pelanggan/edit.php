<?php
// views/pelanggan/edit.php
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Edit Data Pelanggan</h4>
    </div>
    <div class="card-body">
        <form action="index.php?c=pelanggan&a=edit&id=<?php echo $p['id_pelanggan']; ?>" method="POST">
            <div class="form-group">
                <label>Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" class="form-control" value="<?php echo htmlspecialchars((string)$p['nama_pelanggan']); ?>" required>
            </div>
            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?php echo htmlspecialchars((string)$p['no_hp']); ?>">
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"><?php echo htmlspecialchars((string)$p['alamat']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php?c=pelanggan" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
