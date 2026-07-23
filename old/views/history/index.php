<?php require_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Riwayat Kwitansi</h3>
    <form class="d-flex" method="GET" action="index.php">
        <input type="hidden" name="c" value="riwayat">
        <input type="hidden" name="a" value="index">
        <input class="form-control me-2" type="search" name="q" placeholder="Cari No / Pasien..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button class="btn btn-outline-dark" type="submit">Cari</button>
    </form>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No. Kwitansi</th>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($riwayat)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach($riwayat as $r): ?>
                    <tr>
                        <td><strong><?php echo $r['no_kwitansi']; ?></strong></td>
                        <td><?php echo $r['tanggal_transaksi']; ?></td>
                        <td><?php echo $r['nama_pasien']; ?></td>
                        <td><?php echo $r['kasir']; ?></td>
                        <td>Rp <?php echo number_format($r['total_bayar'], 0, ',', '.'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="reprint('<?php echo $r['no_kwitansi']; ?>')">🖨 Cetak</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function reprint(no_kwitansi) {
    fetch('index.php?c=riwayat&a=detail&id=' + encodeURIComponent(no_kwitansi))
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            let data = res.data;
            document.getElementById('print_no_kwitansi').innerText = data.no_kwitansi + ' (REPRINT)';
            document.getElementById('print_tanggal').innerText = data.tanggal_transaksi;
            document.getElementById('print_nama_pasien').innerText = data.nama_pasien;
            
            let ph = '';
            data.items.forEach(item => {
                ph += `<tr><td>${item.nama_barang}</td><td>${item.jumlah}</td><td class="text-end">${parseInt(item.subtotal).toLocaleString('id-ID')}</td></tr>`;
            });
            document.getElementById('print_tbody').innerHTML = ph;
            document.getElementById('print_grand_total').innerText = parseInt(data.total_bayar).toLocaleString('id-ID');
            
            window.print();
        } else {
            alert('Gagal memuat detail kwitansi!');
        }
    });
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
