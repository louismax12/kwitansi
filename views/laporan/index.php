<?php require_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Laporan Pendapatan</h3>
    <form class="d-flex" method="GET" action="index.php">
        <input type="hidden" name="c" value="laporan">
        <input type="hidden" name="a" value="index">
        <input type="date" name="start" class="form-control me-2" value="<?php echo htmlspecialchars($start); ?>">
        <span class="me-2 mt-2">s/d</span>
        <input type="date" name="end" class="form-control me-2" value="<?php echo htmlspecialchars($end); ?>">
        <button class="btn btn-primary me-2" type="submit">Filter</button>
        <button class="btn btn-success" type="submit" name="export" value="csv">📥 CSV</button>
    </form>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No. Kwitansi</th>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Kasir</th>
                    <th class="text-end">Total Bayar (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                if(empty($data)): 
                ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td></tr>
                <?php else: ?>
                    <?php foreach($data as $r): $grand_total += $r['total_bayar']; ?>
                    <tr>
                        <td><?php echo $r['no_kwitansi']; ?></td>
                        <td><?php echo $r['tanggal_transaksi']; ?></td>
                        <td><?php echo $r['nama_pasien']; ?></td>
                        <td><?php echo $r['nama_petugas']; ?></td>
                        <td class="text-end"><?php echo number_format($r['total_bayar'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="4" class="text-end">GRAND TOTAL PENDAPATAN</td>
                    <td class="text-end text-success">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3 text-end">
    <button class="btn btn-outline-dark" onclick="window.print()">🖨 Cetak Laporan</button>
</div>

<?php require_once 'views/layout/footer.php'; ?>
