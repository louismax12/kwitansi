<?php
// views/kwitansi/view.php
?>
<?php
// views/kwitansi/view.php
// Konversi angka ke terbilang (sederhana)
function terbilang($x) {
    $x = abs((int)$x);
    $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
    if ($x < 12) return " " . $angka[$x];
    elseif ($x < 20) return terbilang($x - 10) . " belas";
    elseif ($x < 100) return terbilang($x / 10) . " puluh" . terbilang($x % 10);
    elseif ($x < 200) return " seratus" . terbilang($x - 100);
    elseif ($x < 1000) return terbilang($x / 100) . " ratus" . terbilang($x % 100);
    elseif ($x < 2000) return " seribu" . terbilang($x - 1000);
    elseif ($x < 1000000) return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
    elseif ($x < 1000000000) return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
}
$terbilang_rupiah = ucwords(trim(terbilang($kwitansi['total_bayar']))) . " Rupiah";

// Format Tanggal Indonesia
$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$ts = strtotime($kwitansi['tanggal_transaksi']);
$tanggal_indo = date('d', $ts) . ' ' . $bulan_indo[(int)date('m', $ts)] . ' ' . date('Y', $ts);
?>

<!-- Tampilan UI Normal (Web) -->
<div class="card no-print">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Nota Kwitansi: <?php echo htmlspecialchars($kwitansi['no_kwitansi']); ?></h4>
        <div>
            <button onclick="window.print();" class="btn btn-success"><i class="fas fa-print"></i> Cetak di Kertas Kuitansi</button>
            <a href="index.php?c=kwitansi&a=create" class="btn btn-primary">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-danger" style="border: 2px dashed red;">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> PENTING SEBELUM MENCETAK!</h5>
            <p>Agar teks pas jatuh di kotak kertas Kuitansi, Anda <strong>WAJIB</strong> mengubah 3 pengaturan ini di layar Print (Ctrl+P):</p>
            <ol class="mb-0 font-weight-bold">
                <li>Paper size (Ukuran Kertas): Pilih ukuran Kertas Continuous (misal 9.5 x 5.5 inch) atau Custom. Jika tidak ada, boleh pakai A4 namun pastikan setting nomor 2 & 3.</li>
                <li>Margins (Batas Tepi): Wajib pilih <span class="text-danger">"None" (Tidak Ada)</span> agar browser tidak menggeser teks ke tengah.</li>
                <li>Options: Hilangkan centang <span class="text-danger">"Headers and footers"</span>.</li>
            </ol>
        </div>
        <p><strong>Pasien:</strong> <?php echo htmlspecialchars($kwitansi['nama_pasien']); ?></p>
        <p><strong>Total:</strong> Rp <?php echo number_format($kwitansi['total_bayar'], 0, ',', '.'); ?>,-</p>
        <p><strong>Terbilang:</strong> <?php echo $terbilang_rupiah; ?></p>
        
        <h5>Detail Item:</h5>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?php echo htmlspecialchars($item['nama_barang']); ?> (<?php echo $item['jumlah']; ?>x)</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Tampilan Cetak (Print Only) -->
<div class="print-area">
    <div class="field-no-kuitansi"><?php echo htmlspecialchars($kwitansi['no_kwitansi']); ?></div>
    
    <div class="field-terima-dari"><?php echo htmlspecialchars($kwitansi['nama_pasien']); ?></div>
    <div class="field-uang-sejumlah"><?php echo $terbilang_rupiah; ?></div>
    
    <div class="field-untuk-pembayaran">
        Biaya Perawatan / Tindakan Medis<br>
        <?php 
        // Tampilkan maks 2 item pertama sebagai referensi
        $item_names = array();
        for($i = 0; $i < min(2, count($items)); $i++) {
            $item_names[] = $items[$i]['nama_barang'];
        }
        echo htmlspecialchars(implode(', ', $item_names));
        if(count($items) > 2) echo " ...dll";
        ?>
    </div>
    
    <div class="field-jumlah-rp">Rp <?php echo number_format($kwitansi['total_bayar'], 0, ',', '.'); ?>,-</div>
    <div class="field-tanggal-surabaya"><?php echo $tanggal_indo; ?></div>
    <div class="field-nama-user">(<?php echo htmlspecialchars($kwitansi['nama_user']); ?>)</div>
</div>

<style>
    /* Sembunyikan print-area di layar monitor biasa */
    .print-area {
        display: none;
    }

    @media print {
        @page {
            /* Kita hapus setingan size A4/Custom disini agar browser sepenuhnya patuh pada setting printer */
            margin: 0 !important; 
        }

        /* Sembunyikan elemen web */
        .no-print, nav, .sidebar {
            display: none !important;
        }

        body {
            background-color: white;
            margin: 0;
            padding: 0;
            font-family: Epson FX-80 Dot Matrix, monospace; /* Menggunakan font Liberation Serif sesuai permintaan */
            font-size: 24px;
            font-weight: normal; 
            color: #000;
        }

        /* Tampilkan print-area */
        .print-area {
            display: block;
            position: relative;
            width: 24cm;
            height: 13.9cm;
        }

        /* 
           KALIBRASI KOORDINAT CETAK BERDASARKAN DIAGRAM GARIS MERAH
           Asumsi: User memilih Margins = NONE di browser.
        */

        .field-no-kuitansi {
            position: absolute;
            top: 1.8cm;
            left: 15.3cm; 
            width: 6.5cm;
            font-size:23px;
        }

        .field-terima-dari {
            position: absolute;
            top: 7.2cm; 
            left: -2.0cm; 
            width: 17cm;
        }

        .field-uang-sejumlah {
            position: absolute;
            top: 8.2cm; 
            left: -2.0cm; 
            width: 25cm;
            line-height: 0.95; /* Sangat rapat agar jika 2 baris tidak menabrak baris bawahnya */
            font-size: 26px; 
        }

        .field-untuk-pembayaran {
            position: absolute;
            top: 9.2cm; 
            left: -2.0cm; 
            width: 17cm;
            line-height: 1.5; /* Dipersempit agar kalau 2 baris tidak menabrak bawahnya */
            font-size: 26px; /* Diperkecil agar nominal panjang bisa muat */
        }

        .field-jumlah-rp {
            position: absolute;
            top: 15.8cm; 
            left: -2.3cm; 
            font-size: 18pt;
        }

        .field-tanggal-surabaya {
            position: absolute;
            top: 13.3cm; 
            left: 15.3cm; 
        }

        .field-nama-user {
            position: absolute;
            top: 16.6cm; 
            left: 14.5cm; 
        }
    }
</style>
