<?php
// views/kwitansi/view.php
?>
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h3 class="text-primary font-weight-bold">RKZ Hospital</h3>
                <div>Sistem Penagihan (Billing)</div>
            </div>
            <div class="col-sm-6 text-right">
                <h4 class="text-secondary">KWITANSI</h4>
                <div class="font-weight-bold text-dark" style="font-size: 1.2rem;"><?php echo htmlspecialchars($kwitansi['no_kwitansi']); ?></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="mb-2">Ditagihkan Kepada:</h6>
                <div><strong><?php echo htmlspecialchars($kwitansi['nama_pasien']); ?></strong></div>
            </div>
            <div class="col-sm-6 text-right">
                <div>Tanggal: <?php echo date('d M Y H:i', strtotime($kwitansi['tanggal_transaksi'])); ?></div>
                <div>Kasir ID: <?php echo intval($kwitansi['id_user']); ?></div>
            </div>
        </div>

        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Item (Tindakan/Obat)</th>
                        <th class="right">Harga</th>
                        <th class="center">Qty</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($items as $item): 
                    ?>
                    <tr>
                        <td class="center"><?php echo $no++; ?></td>
                        <td class="left strong"><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                        <td class="right">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td class="center"><?php echo $item['jumlah']; ?></td>
                        <td class="right">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-5 ml-auto">
                <table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left"><strong>Grand Total</strong></td>
                            <td class="right"><strong>Rp <?php echo number_format($kwitansi['total_bayar'], 0, ',', '.'); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 no-print">
            <button onclick="window.print();" class="btn btn-success"><i class="fa fa-print"></i> Cetak Kwitansi</button>
            <a href="index.php?c=kwitansi&a=create" class="btn btn-primary">Buat Kwitansi Baru</a>
        </div>
        <style>
            @media print {
                .no-print, .navbar { display: none !important; }
                body { background-color: white; }
                .card { box-shadow: none; border: none; }
            }
        </style>
    </div>
</div>
