<?php
// views/dashboard/index.php
?>
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card h-100" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0px 4px 12px rgba(0,0,0,0.03);">
            <div class="card-body d-flex flex-column flex-md-row justify-content-center align-items-center" style="min-height: 150px; gap: 24px;">
                <a href="index.php?c=kwitansi&a=create" class="btn btn-lg px-4 py-3" style="border-radius: 8px; background-color: #2a14b4; color: #ffffff; border: none; font-weight: 600; font-family: Manrope, sans-serif;"><i class="fa fa-plus mr-2"></i> Buat Kwitansi Baru</a>
                <!-- <a href="index.php?c=pelanggan" class="btn btn-lg px-4 py-3" style="border-radius: 8px; background-color: transparent; border: 2px solid #2a14b4; color: #2a14b4; font-weight: 600; font-family: Manrope, sans-serif;"><i class="fa fa-users mr-2"></i> Tambah Pasien</a> -->
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white h-100" style="border-radius: 16px; background-color: #00414d; border: none; box-shadow: 0px 4px 12px rgba(0,0,0,0.03); min-height: 150px;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <h6 class="text-uppercase mb-2" style="font-weight: 600; font-size: 13px; letter-spacing: 0.02em; color: #69d3ed; font-family: Manrope, sans-serif;">Total Kwitansi Dibuat</h6>
                <h2 class="card-title mb-1" style="font-weight: 700; font-size: 32px; font-family: Manrope, sans-serif;"><?php echo intval($totalKwitansi); ?> <span style="font-size: 16px; font-weight: 500; opacity: 0.8;">Kwitansi</span></h2>
                <p class="card-text small mb-0" style="font-weight: 400; opacity: 0.8; font-family: Manrope, sans-serif;">Kwitansi yang telah diproses</p>
            </div>
        </div>
    </div>
</div>
