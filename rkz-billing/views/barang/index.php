<?php
// views/barang/index.php
?>
<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-header bg-white pb-0 pt-4 border-0">
        <h4 class="mb-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i> Master Data Kategori</h4>
    </div>
    <div class="card-body">
        <form action="index.php?c=barang" method="POST" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <input type="text" name="nama_barang" class="form-control" placeholder="Nama Kategori/Tindakan Baru" required style="background-color: #fafafa; border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-plus mr-1"></i> Tambah Kategori
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">ID Kategori</th>
                        <th>Nama Kategori / Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barangList)): ?>
                        <tr><td colspan="2" class="text-center text-muted">Belum ada data kategori</td></tr>
                    <?php else: ?>
                        <?php foreach ($barangList as $b): ?>
                            <tr>
                                <td><span class="badge badge-light border text-dark p-2"><?= htmlspecialchars($b['id_barang']) ?></span></td>
                                <td class="align-middle font-weight-bold text-secondary"><?= htmlspecialchars($b['nama_barang']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
