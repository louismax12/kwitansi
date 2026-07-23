<?php
// views/dashboard/index.php
?>
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Total Pendapatan (Bulan Ini)</div>
            <div class="card-body">
                <h4 class="card-title">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></h4>
                <p class="card-text">Dari tagihan bulan <?php echo date('F Y'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Total Pasien Terdaftar</div>
            <div class="card-body">
                <h4 class="card-title"><?php echo intval($totalPelanggan); ?> Pasien</h4>
                <p class="card-text">Data master pelanggan</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Total Kwitansi Dibuat</div>
            <div class="card-body">
                <h4 class="card-title"><?php echo intval($totalKwitansi); ?> Kwitansi</h4>
                <p class="card-text">Invoice yang telah diproses</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        Aksi Cepat
    </div>
    <div class="card-body text-center">
        <a href="index.php?c=kwitansi&a=create" class="btn btn-primary btn-lg mx-2"><i class="fa fa-plus"></i> Buat Tagihan Baru</a>
        <a href="index.php?c=pelanggan" class="btn btn-secondary btn-lg mx-2"><i class="fa fa-users"></i> Tambah Pasien</a>
    </div>
</div>
