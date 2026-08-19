<?php
// views/kwitansi/index.php
?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Riwayat Kwitansi</h4>
        <a href="index.php?c=kwitansi&a=create" class="btn btn-primary btn-sm">Buat Kwitansi Baru</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>No Kwitansi</th>
                        <th>Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Total Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr><td colspan="5" class="text-center">Belum ada kwitansi</td></tr>
                    <?php else: ?>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($h['no_kwitansi']); ?></strong></td>
                                <td><?php echo date('d M Y H:i', strtotime($h['tanggal_transaksi'])); ?></td>
                                <td><?php echo htmlspecialchars($h['nama_pasien']); ?></td>
                                <td>Rp <?php echo number_format($h['total_bayar'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="index.php?c=kwitansi&a=view&id=<?php echo urlencode($h['no_kwitansi']); ?>" class="btn btn-info btn-sm">Lihat Nota</a>
                                    <a href="index.php?c=kwitansi&a=edit&id=<?php echo urlencode($h['no_kwitansi']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
