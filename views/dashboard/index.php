<?php require_once 'views/layout/header.php'; ?>

<h3 class="mb-4">Dashboard</h3>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h5 class="card-title">Transaksi Hari Ini</h5>
                <h2 class="display-4 fw-bold"><?php echo $total_transaksi; ?></h2>
                <p class="card-text">Total nota yang diterbitkan hari ini.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h5 class="card-title">Penerimaan Kas (Hari Ini)</h5>
                <h2 class="display-4 fw-bold">Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h2>
                <p class="card-text">Akumulasi uang masuk hari ini.</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-2">
    <div class="card-body text-center py-5">
        <h4 class="text-muted">Gunakan menu di samping untuk mulai bekerja.</h4>
        <a href="index.php?c=sales&a=index" class="btn btn-lg btn-dark mt-3">Buka Register Kasir</a>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
