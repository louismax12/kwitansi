<?php
// ============================================================
// KWITANSI - Rumah Sakit Katolik St. Vincentius a Paulo
// Database: MySQL (PDO)
// ============================================================
require_once __DIR__ . '/config.php';

// --- PDO Connection ---
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;color:#c0392b;background:#fde;padding:20px;margin:20px;border-radius:6px">
        <strong>Gagal konek ke MySQL:</strong><br>' . htmlspecialchars($e->getMessage()) .
        '<br><br>Periksa konfigurasi di <code>config.php</code></div>');
}

// --- Session & Auth ---
session_start();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'logout') { session_destroy(); header('Location: index.php'); exit; }

if (!isset($_SESSION['user']) && $action !== 'login') {
    header('Location: index.php?action=login'); exit;
}

// --- Helper: terbilang ---
function terbilang($n) {
    $n = abs((int)$n);
    $satuan = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan',
               'Sepuluh','Sebelas','Dua belas','Tiga belas','Empat belas','Lima belas',
               'Enam belas','Tujuh belas','Delapan belas','Sembilan belas'];
    $kata   = ['','','Dua puluh','Tiga puluh','Empat puluh','Lima puluh',
               'Enam puluh','Tujuh puluh','Delapan puluh','Sembilan puluh'];
    if ($n === 0) return 'Nol';
    if ($n < 20)  return $satuan[$n];
    if ($n < 100) return $kata[intdiv($n,10)] . ($n%10 ? ' '.terbilang($n%10) : '');
    if ($n < 200) return 'Seratus'  . ($n%100  ? ' '.terbilang($n%100)  : '');
    if ($n < 1000) return terbilang(intdiv($n,100)).' ratus'  . ($n%100  ? ' '.terbilang($n%100)  : '');
    if ($n < 2000) return 'Seribu'   . ($n%1000  ? ' '.terbilang($n%1000)  : '');
    if ($n < 1000000)    return terbilang(intdiv($n,1000)).' ribu'   . ($n%1000  ? ' '.terbilang($n%1000)  : '');
    if ($n < 1000000000) return terbilang(intdiv($n,1000000)).' juta'  . ($n%1000000 ? ' '.terbilang($n%1000000) : '');
    return terbilang(intdiv($n,1000000000)).' miliar' . ($n%1000000000 ? ' '.terbilang($n%1000000000) : '');
}

// ============================================================
// ACTIONS
// ============================================================

// Terbilang AJAX endpoint (early exit)
if ($action === 'terbilang' && isset($_GET['n'])) {
    echo terbilang((int)$_GET['n']) . '  rupiah'; exit;
}

// LOGIN
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = strtoupper(trim($_POST['username']));
    $p = trim($_POST['password']);
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$u, $p]);
    $row = $stmt->fetch();
    if ($row) {
        $_SESSION['user'] = $row['username'];
        header('Location: index.php'); exit;
    } else {
        $loginError = 'Username atau password salah.';
    }
}

// SAVE KWITANSI
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $editKwt = trim($_POST['no_kwitansi']);
    $noFkt   = strtoupper(trim($_POST['no_faktur']));
    if ($noFkt === '') {
        die('<div style="font-family:sans-serif;color:#c0392b;background:#fde;padding:20px;margin:20px;border-radius:6px">
            <strong>Error:</strong> No. Faktur wajib diisi. <a href="index.php" style="color:#1a3d6e">← Kembali</a></div>');
    }
    if (!preg_match('/^[A-Z0-9]+$/', $noFkt)) {
        die('<div style="font-family:sans-serif;color:#c0392b;background:#fde;padding:20px;margin:20px;border-radius:6px">
            <strong>Error:</strong> No. Faktur hanya boleh huruf BESAR dan angka. <a href="index.php" style="color:#1a3d6e">← Kembali</a></div>');
    }
    $dari   = strtoupper(trim($_POST['terima_dari']));
    $untuk  = strtoupper(trim($_POST['untuk_pembayaran']));
    $ket    = trim($_POST['keterangan']);
    $jumlah = (float)str_replace(['.', ','], ['', '.'], $_POST['jumlah']);
    $uang   = terbilang($jumlah) . '  rupiah';
    $tgl    = date('Y-m-d');
    $kasir  = $_SESSION['user'];

    // Generate no_kwitansi atomically inside transaction
    $db->beginTransaction();
    try {
        if ($editKwt) {
            // Edit existing — cek apakah benar-benar ada
            $cek = $db->prepare("SELECT no_kwitansi FROM kwitansi WHERE no_kwitansi = ?");
            $cek->execute([$editKwt]);
            $noKwt = $cek->fetchColumn();
            if (!$noKwt) $editKwt = ''; // fallback ke generate baru
        }
        if (!$editKwt) {
            // Generate nomor baru dengan lock
            $yy = date('y');
            $stmt = $db->prepare("SELECT no_kwitansi FROM kwitansi WHERE no_kwitansi LIKE ? ORDER BY id DESC LIMIT 1 FOR UPDATE");
            $stmt->execute(["M{$yy}%"]);
            $row = $stmt->fetch();
            $num = $row ? (int)substr($row['no_kwitansi'], 3) + 1 : 1;
            $noKwt = 'M' . $yy . sprintf('%06d', $num);
        }

        // Insert (bukan upsert — nomor unik tidak boleh tertindih)
        $stmt = $db->prepare("INSERT INTO kwitansi
            (no_kwitansi, no_faktur, terima_dari, uang_sejumlah, untuk_pembayaran, keterangan, jumlah, tgl, kasir)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            no_faktur=VALUES(no_faktur), terima_dari=VALUES(terima_dari),
            uang_sejumlah=VALUES(uang_sejumlah), untuk_pembayaran=VALUES(untuk_pembayaran),
            keterangan=VALUES(keterangan), jumlah=VALUES(jumlah), tgl=VALUES(tgl), kasir=VALUES(kasir)");
        $stmt->execute([$noKwt, $noFkt, $dari, $uang, $untuk, $ket, $jumlah, $tgl, $kasir]);

        // Save detail
        $del = $db->prepare("DELETE FROM detail_kwitansi WHERE no_kwitansi = ?");
        $del->execute([$noKwt]);

        if (!empty($_POST['det_nama'])) {
            $ins = $db->prepare("INSERT INTO detail_kwitansi (no_kwitansi, no_faktur, kd_brg, nama, jumlah)
                                 VALUES (?, ?, ?, ?, ?)");
            foreach ($_POST['det_nama'] as $i => $nm) {
                if (trim($nm) === '') continue;
                $kdB = (int)$_POST['det_kd'][$i];
                $jml = (float)str_replace(['.', ','], ['', '.'], $_POST['det_jml'][$i]);
                $ins->execute([$noKwt, $noFkt, $kdB, strtoupper(trim($nm)), $jml]);
            }
        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    header('Location: index.php?saved=1'); exit;
}

// DELETE
if ($action === 'delete') {
    $id = $_GET['id'];
    $db->prepare("DELETE FROM kwitansi WHERE no_kwitansi = ?")->execute([$id]);
    $db->prepare("DELETE FROM detail_kwitansi WHERE no_kwitansi = ?")->execute([$id]);
    header('Location: index.php'); exit;
}

// ============================================================
// FETCH DATA with PAGINATION
// ============================================================
$kwitansiList = array();
$search = trim(isset($_GET['q']) ? $_GET['q'] : '');
$page   = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) FROM kwitansi";
$countBind = [];
if ($search) {
    $like = "%$search%";
    $countSql .= " WHERE no_kwitansi LIKE ? OR terima_dari LIKE ? OR no_faktur LIKE ?";
    $countBind = [$like, $like, $like];
}
$stmt = $db->prepare($countSql);
$stmt->execute($countBind);
$total = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $limit));

$dataSql = "SELECT * FROM kwitansi";
$dataBind = [];
if ($search) {
    $dataSql .= " WHERE no_kwitansi LIKE ? OR terima_dari LIKE ? OR no_faktur LIKE ?";
    $dataBind = [$like, $like, $like];
}
$dataSql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($dataSql);
$stmt->execute($dataBind);
$kwitansiList = $stmt->fetchAll();

$brgs = $db->query("SELECT * FROM brg ORDER BY kd_brg")->fetchAll();

// Edit mode
$editData    = null;
$editDetails = array();
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM kwitansi WHERE no_kwitansi = ?");
    $stmt->execute([$_GET['id']]);
    $editData = $stmt->fetch();

    $stmt2 = $db->prepare("SELECT d.*, b.nm_brg FROM detail_kwitansi d LEFT JOIN brg b ON d.kd_brg = b.kd_brg WHERE d.no_kwitansi = ?");
    $stmt2->execute([$_GET['id']]);
    $editDetails = $stmt2->fetchAll();
}

// Print mode
if ($action === 'print' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM kwitansi WHERE no_kwitansi = ?");
    $stmt->execute([$_GET['id']]);
    $kw = $stmt->fetch();

    $stmt2 = $db->prepare("SELECT d.*, b.nm_brg FROM detail_kwitansi d LEFT JOIN brg b ON d.kd_brg = b.kd_brg WHERE d.no_kwitansi = ?");
    $stmt2->execute([$_GET['id']]);
    $details = $stmt2->fetchAll();

    $blnIndo = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    $tgl_print = $kw ? date('j', strtotime($kw['tgl'])) . '-' . $blnIndo[(int)date('n', strtotime($kw['tgl']))] . '-' . date('Y', strtotime($kw['tgl'])) : '';
    $kasir_time = $kw ? (htmlspecialchars($kw['kasir']) . ' - ' . date('H:i:s', strtotime($kw['tgl']))) : '';
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kwitansi <?= $kw ? htmlspecialchars($kw['no_kwitansi']) : '' ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',Courier,monospace;font-size:13px;background:#fff;color:#000;padding:30px 40px}
.top-right{text-align:right;font-weight:700;font-size:14px;line-height:2;margin-bottom:30px}
.section{margin-bottom:10px}
.amount-words{margin:8px 0;font-weight:600}
.detail-table{width:auto;margin:4px 0;margin-left:20px}
.detail-table td{padding:1px 20px 1px 0;font-size:13px}
.detail-table td:last-child{text-align:right;padding-right:0}
.date-right{text-align:right;margin:6px 0}
.total-amount{font-weight:700;font-size:14px;margin-top:16px}
.kasir-line{text-align:right;margin-top:20px;font-size:12px}
.separator{border-top:1px dashed #999;margin:18px 0}
.btn-print-bar{margin-bottom:18px;display:flex;gap:10px}
.btn-p{padding:7px 18px;border:1px solid #2d5a8e;background:#2d5a8e;color:#fff;font-size:13px;cursor:pointer;border-radius:3px}
.btn-p.back{background:#888;border-color:#888}
@media print{.btn-print-bar{display:none}body{margin:1cm}}
@page{margin:0}
</style>
</head>
<body>
<div class="btn-print-bar">
  <button class="btn-p" onclick="window.print()">🖨 Cetak</button>
  <button class="btn-p back" onclick="window.close()">✕ Tutup</button>
</div>

<?php if (!$kw): ?>
  <p style="color:red">Data kwitansi tidak ditemukan.</p>
<?php else: ?>

<div class="top-right">
  <?= htmlspecialchars($kw['no_kwitansi']) ?><br>
  <?= htmlspecialchars($kw['no_faktur']) ?>
</div>

<div class="section">
  <?= htmlspecialchars($kw['terima_dari']) ?>
</div>

<div class="section amount-words">
  = <?= htmlspecialchars(ucfirst($kw['uang_sejumlah'])) ?> =
</div>

<div class="section">
  <?= htmlspecialchars($kw['untuk_pembayaran']) ?>
</div>

<?php if (!empty($details)): ?>
<table class="detail-table">
  <?php foreach ($details as $d): ?>
  <tr>
    <td><?= htmlspecialchars($d['nama']) ?></td>
    <td><?= number_format($d['jumlah'],0,',','.') ?>,-</td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<div class="date-right"><?= $tgl_print ?></div>
<div class="separator"></div>
<div class="total-amount"><?= number_format($kw['jumlah'],0,',','.') ?>,-</div>
<div class="kasir-line"><?= $kasir_time ?></div>

<?php endif; ?>
</body>
</html>
    <?php
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kwitansi - RSK St. Vincentius a Paulo</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:13px;background:#e8e8e0;color:#222}

/* TOP BAR */
.topbar{background:#2d5a8e;color:#fff;padding:6px 16px;display:flex;align-items:center;gap:20px}
.topbar .logo{font-weight:700;font-size:15px;letter-spacing:.5px}
.topbar nav a{color:#cce3ff;text-decoration:none;font-size:13px;margin-right:14px;padding:3px 8px;border-radius:3px}
.topbar nav a:hover{background:rgba(255,255,255,.2);color:#fff}
.topbar .user{margin-left:auto;font-size:12px;color:#cce3ff}

/* HEADER */
.header-bar{background:#f0f0e8;border-bottom:2px solid #2d5a8e;padding:10px 16px;display:flex;align-items:flex-start;gap:40px}
.hospital-info{font-size:12px;line-height:1.7}
.hospital-info strong{font-size:14px;color:#1a3d6e}
.kwitansi-title{font-size:22px;font-weight:700;letter-spacing:2px;color:#1a3d6e;text-align:center;flex:1;align-self:center}
.no-box{font-size:13px;color:#333}
.no-box input{border:1px solid #aaa;padding:2px 6px;font-size:13px;width:160px;background:#fff}

/* FORM */
.form-wrap{background:#f8f8f0;border:1px solid #ccc;margin:10px;padding:14px;border-radius:4px}
.form-row{display:flex;align-items:center;margin-bottom:8px;gap:8px}
.form-row label{width:130px;text-align:right;color:#333;flex-shrink:0}
.form-row input[type=text],.form-row textarea{flex:1;border:1px solid #aaa;padding:4px 8px;font-size:13px;font-family:inherit;background:#fff;border-radius:2px}
.form-row input:focus{outline:2px solid #2d5a8e;border-color:#2d5a8e}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;font-size:13px;font-family:inherit;border:1px solid;border-radius:3px;cursor:pointer;font-weight:600;text-decoration:none}
.btn-new   {background:#e8f0ff;border-color:#4a7cc7;color:#1a3d6e}
.btn-save  {background:#d4edda;border-color:#28a745;color:#155724}
.btn-print {background:#fff3cd;border-color:#ffc107;color:#856404}
.btn-cancel{background:#f8d7da;border-color:#dc3545;color:#721c24}
.btn-batal {background:#f0e6ff;border-color:#7a4fc7;color:#3a1a6e}
.btn-delete{background:#f8d7da;border-color:#dc3545;color:#721c24;padding:2px 8px;font-size:12px}
.btn-edit  {background:#e8f0ff;border-color:#4a7cc7;color:#1a3d6e;padding:2px 8px;font-size:12px}
.btn:hover{filter:brightness(.92)}
.pagination a:hover{filter:brightness(.85)}
.btn-bar{display:flex;gap:6px;flex-wrap:wrap;margin:10px 10px 6px}

/* TABLE */
.tbl-wrap{margin:6px 10px;overflow-x:auto}
table.grid{width:100%;border-collapse:collapse;font-size:12.5px;background:#fff}
table.grid th{background:#2d5a8e;color:#fff;padding:5px 8px;text-align:left;white-space:nowrap}
table.grid td{padding:4px 8px;border-bottom:1px solid #ddd;white-space:nowrap}
table.grid tr:hover td{background:#dceeff}
table.grid tr.selected td{background:#b8d4f8}

/* DETAIL */
.detail-section{margin:4px 10px;border:1px solid #aaa;background:#fff;padding:10px;border-radius:3px;display:none}
.detail-section.open{display:block}
.det-tbl{width:100%;border-collapse:collapse;font-size:12.5px}
.det-tbl th{background:#4a7cc7;color:#fff;padding:4px 8px}
.det-tbl td{border:1px solid #ccc;padding:3px 6px}
.det-tbl select,.det-tbl input{font-size:12.5px;border:none;outline:none;width:100%;background:transparent}
.det-tbl tr:hover td{background:#f0f7ff}

/* SEARCH */
.search-bar{margin:6px 10px;display:flex;gap:8px;align-items:center}
.search-bar input{border:1px solid #aaa;padding:4px 10px;font-size:13px;border-radius:2px;width:280px}
.search-bar button{padding:4px 12px;font-size:13px;border:1px solid #2d5a8e;background:#2d5a8e;color:#fff;border-radius:2px;cursor:pointer}

/* ALERTS */
.alert{padding:8px 14px;margin:6px 10px;border-radius:3px;font-size:13px}
.alert-success{background:#d4edda;border:1px solid #28a745;color:#155724}
.alert-error  {background:#f8d7da;border:1px solid #dc3545;color:#721c24}

/* LOGIN */
.login-wrap{display:flex;justify-content:center;align-items:center;min-height:100vh;background:#e8e8e0}
.login-box{background:#f8f8f0;border:2px solid #2d5a8e;padding:30px 36px;border-radius:6px;width:340px;box-shadow:0 4px 20px rgba(0,0,0,.15)}
.login-box h2{color:#1a3d6e;font-size:18px;margin-bottom:4px;text-align:center}
.login-box p{font-size:11px;color:#666;text-align:center;margin-bottom:20px}
.login-box label{display:block;font-size:13px;margin-bottom:3px;color:#333;font-weight:600}
.login-box input{width:100%;border:1px solid #aaa;padding:7px 10px;font-size:14px;margin-bottom:12px;border-radius:3px}
.login-box button{width:100%;padding:9px;background:#2d5a8e;color:#fff;border:none;font-size:14px;font-weight:700;border-radius:3px;cursor:pointer}
.login-box button:hover{background:#1a3d6e}
.login-error{color:#c0392b;font-size:12px;margin-bottom:10px;background:#fde;padding:6px 10px;border-radius:3px}
.amt{text-align:right}
</style>
</head>
<body>

<?php if ($action === 'login'): ?>
<!-- LOGIN PAGE -->
<div class="login-wrap">
  <div class="login-box">
    <h2>🏥 KWITANSI</h2>
    <p>Rumah Sakit Katolik St. Vincentius a Paulo</p>
    <?php if (!empty($loginError)): ?><div class="login-error"><?= $loginError ?></div><?php endif; ?>
    <form method="POST" action="index.php?action=login">
      <label>Username</label>
      <input type="text" name="username" autofocus autocomplete="off">
      <label>Password</label>
      <input type="password" name="password">
      <button type="submit">LOGIN</button>
    </form>
  </div>
</div>

<?php else: ?>

<!-- TOPBAR -->
<div class="topbar">
  <span class="logo">🏥 KWITANSI</span>
  <nav>
    <a href="index.php">Beranda</a>
    <a href="index.php?action=edit">Baru</a>
    <a href="index.php?action=logout" onclick="return confirm('Logout?')">Logout</a>
  </nav>
  <span class="user">👤 <?= htmlspecialchars($_SESSION['user']) ?> &nbsp;|&nbsp; SUPERVISOR</span>
</div>

<!-- HEADER -->
<div class="header-bar">
  <div class="hospital-info">
    <strong>RUMAH SAKIT KATOLIK</strong><br>
    St. Vincentius a Paulo<br>
    Jl. Diponegoro 51
  </div>
  <div class="kwitansi-title">KWITANSI</div>
  <div class="no-box">
    <div>No.Kwitansi : <strong style="color:#2d5a8e"><?= $editData ? htmlspecialchars($editData['no_kwitansi']) : 'Auto' ?></strong></div>
    <div style="margin-top:4px">No.Faktur :&nbsp;
      <input type="text" id="hdr_faktur" value="<?= $editData ? htmlspecialchars($editData['no_faktur']) : '' ?>" readonly>
    </div>
  </div>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success">✅ Kwitansi berhasil disimpan.</div>
<?php endif; ?>

<!-- FORM -->
<form method="POST" action="index.php?action=save" id="frmKwt">
<input type="hidden" name="no_kwitansi" id="f_no_kwt" value="<?= $editData ? htmlspecialchars($editData['no_kwitansi']) : '' ?>">
<input type="hidden" name="jumlah" id="f_jumlah" value="<?= $editData ? $editData['jumlah'] : 0 ?>">

<div class="form-wrap">
  <div class="form-row">
    <label>Telah Terima Dari :</label>
    <input type="text" name="terima_dari" id="f_dari" value="<?= $editData ? htmlspecialchars($editData['terima_dari']) : '' ?>" required autofocus>
  </div>
  <div class="form-row">
    <label>Uang Sejumlah :</label>
    <input type="text" id="f_uang" value="<?= $editData ? htmlspecialchars($editData['uang_sejumlah']) : '' ?>" readonly style="background:#f4f4ec;color:#555">
  </div>
  <div class="form-row">
    <label>Untuk Pembayaran :</label>
    <input type="text" name="untuk_pembayaran" id="f_untuk" required value="<?= $editData ? htmlspecialchars($editData['untuk_pembayaran']) : '' ?>">
  </div>
  <div class="form-row">
    <label>Keterangan :</label>
    <input type="text" name="keterangan" id="f_ket" required value="<?= $editData ? htmlspecialchars($editData['keterangan']) : '' ?>">
  </div>
  <div class="form-row">
    <label></label>
    <input type="text" name="no_faktur" id="f_faktur" placeholder="No. Faktur" value="<?= $editData ? htmlspecialchars($editData['no_faktur']) : '' ?>" required oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')">
    <label style="width:auto;margin-left:20px">Jumlah : Rp.</label>
    <input type="text" id="disp_jumlah"
           value="<?= $editData ? number_format($editData['jumlah'],0,',','.') : '' ?>"
           style="width:160px;font-weight:700;color:#1a3d6e;background:#fff;border:1px solid #aaa;padding:4px 6px;border-radius:2px"
           placeholder="0"
           oninput="this.value=this.value.replace(/[^0-9.,]/g,'')">
    &nbsp;
    <label><input type="checkbox" id="chkDetail" <?= !empty($editDetails) ? 'checked' : '' ?>> Detail</label>
  </div>
</div>

<!-- DETAIL SECTION -->
<div class="detail-section <?= ($editData || !empty($editDetails)) ? 'open' : '' ?>" id="detailBox">
  <strong style="color:#1a3d6e">Detail Pembayaran</strong>
  <table class="det-tbl" style="margin-top:8px">
    <thead>
      <tr>
        <th style="width:40px">#</th>
        <th style="width:130px">Kategori</th>
        <th>Nama</th>
        <th style="width:130px">Jumlah (Rp)</th>
        <th style="width:50px"></th>
      </tr>
    </thead>
    <tbody id="detBody">
    <?php foreach ($editDetails as $i => $d): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td>
        <input type="hidden" name="det_kd[]" value="<?= $d['kd_brg'] ?>">
        <select onchange="updateKdBrg(this)" style="width:100%">
          <?php foreach ($brgs as $b): ?>
          <option value="<?= $b['kd_brg'] ?>" <?= $b['kd_brg']==$d['kd_brg']?'selected':'' ?>><?= htmlspecialchars($b['nm_brg']) ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="text" name="det_nama[]" class="det-nama" placeholder="Nama detail (wajib)" value="<?= htmlspecialchars($d['nama']) ?>" oninput="clearNamaError(this)"></td>
      <td class="amt"><input type="text" name="det_jml[]" class="det-jml"
          value="<?= number_format($d['jumlah'],0,',','.') ?>" style="text-align:right" onchange="recalc()"></td>
      <td><button type="button" class="btn btn-delete" onclick="delRow(this)">✕</button></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
    <button type="button" class="btn btn-new" onclick="addRow()">+ Tambah Baris</button>
    <span style="font-size:12px;color:#666">Pilih KdBrg → nama terisi otomatis</span>
  </div>
</div>

<!-- BUTTON BAR -->
<div class="btn-bar">
  <button type="button" class="btn btn-new"    onclick="newForm()">📄 Baru</button>
  <button type="button" class="btn btn-save" id="btnSimpan" onclick="simpan()" disabled>💾 Simpan</button>
  <button type="button" class="btn btn-print"  onclick="printKwt()">🖨 Cetak</button>
  <button type="button" class="btn btn-cancel" onclick="newForm()">✕ Cancel</button>
  <button type="button" class="btn btn-batal">🔄 Batal</button>
</div>
</form>

<!-- SEARCH -->
<div class="search-bar">
  <form method="GET" style="display:flex;gap:8px">
    <input type="text" name="q" placeholder="Cari no. kwitansi / nama / faktur..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">🔍 Cari</button>
    <?php if ($search): ?>
    <a href="index.php" style="padding:4px 10px;font-size:13px;border:1px solid #aaa;border-radius:2px;background:#fff;text-decoration:none;color:#333">✕ Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- KWITANSI LIST TABLE -->
<div class="tbl-wrap">
  <table class="grid">
    <thead>
      <tr>
        <th style="width:28px"></th>
        <th>St</th>
        <th>No. Kwitansi</th>
        <th>No. Faktur</th>
        <th>Terima Dari</th>
        <th>Jumlah (Rp)</th>
        <th>Untuk Pembayaran</th>
        <th>Uang Sejumlah</th>
        <th>Tgl</th>
        <th>Kasir</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($kwitansiList)): ?>
    <tr><td colspan="11" style="text-align:center;padding:20px;color:#888">Belum ada data kwitansi.</td></tr>
    <?php else: foreach ($kwitansiList as $k): ?>
    <tr class="<?= ($editData && $editData['no_kwitansi']===$k['no_kwitansi']) ? 'selected' : '' ?>">
      <td>▶</td>
      <td><?= htmlspecialchars($k['stat']) ?></td>
      <td>
        <a href="index.php?action=edit&id=<?= urlencode($k['no_kwitansi']) ?>"
           style="color:#1a3d6e;font-weight:600"><?= htmlspecialchars($k['no_kwitansi']) ?></a>
      </td>
      <td><?= htmlspecialchars($k['no_faktur']) ?></td>
      <td><?= htmlspecialchars($k['terima_dari']) ?></td>
      <td class="amt"><?= number_format($k['jumlah'],0,',','.') ?></td>
      <td><?= htmlspecialchars(substr($k['untuk_pembayaran'],0,40)) ?><?= strlen($k['untuk_pembayaran'])>40?'…':'' ?></td>
      <td><?= htmlspecialchars(substr($k['uang_sejumlah'],0,44)) ?>…</td>
      <td><?= htmlspecialchars($k['tgl']) ?></td>
      <td><?= htmlspecialchars($k['kasir']) ?></td>
      <td style="white-space:nowrap">
        <a href="index.php?action=edit&id=<?= urlencode($k['no_kwitansi']) ?>"   class="btn btn-edit">✏️</a>
        <a href="index.php?action=print&id=<?= urlencode($k['no_kwitansi']) ?>"  class="btn btn-print" target="_blank">🖨</a>
        <a href="index.php?action=delete&id=<?= urlencode($k['no_kwitansi']) ?>" class="btn btn-delete"
           onclick="return confirm('Hapus kwitansi <?= htmlspecialchars($k['no_kwitansi']) ?>?')">✕</a>
      </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<div class="pagination" style="margin:10px;display:flex;gap:6px;align-items:center;flex-wrap:wrap">
  <?php
  $qs = $search ? 'q=' . urlencode($search) . '&' : '';
  for ($p = 1; $p <= $totalPages; $p++):
  ?>
    <a href="index.php?<?= $qs ?>page=<?= $p ?>"
       style="padding:4px 10px;border:1px solid #aaa;border-radius:3px;background:<?= $p===$page?'#2d5a8e':'#fff' ?>;color:<?= $p===$page?'#fff':'#333' ?>;text-decoration:none;font-size:13px"><?= $p ?></a>
  <?php endfor; ?>
  <span style="font-size:12px;color:#888">(<?= $total ?> data)</span>
</div>
<?php endif; ?>

<?php endif; // end non-login ?>

<script>
const brgs = <?= json_encode($brgs) ?>;
const brgsMap = {};
brgs.forEach(b => brgsMap[b.kd_brg] = b.nm_brg);

function addRow() {
    const tbody = document.getElementById('detBody');
    const idx   = tbody.rows.length + 1;
    const opts  = brgs.map(b => `<option value="${b.kd_brg}">${b.nm_brg}</option>`).join('');
    const tr    = document.createElement('tr');
    tr.innerHTML = `
      <td>${idx}</td>
      <td>
        <input type="hidden" name="det_kd[]" value="${brgs[0].kd_brg}">
        <select onchange="updateKdBrg(this)" style="width:100%">${opts}</select>
      </td>
      <td><input type="text" name="det_nama[]" class="det-nama" placeholder="Nama detail (wajib)" value="" oninput="clearNamaError(this)"></td>
      <td class="amt"><input type="text" name="det_jml[]" class="det-jml" value="0" style="text-align:right" onchange="recalc()"></td>
      <td><button type="button" class="btn btn-delete" onclick="delRow(this)">✕</button></td>`;
    tbody.appendChild(tr);
    // Focus nama field of new row
    tr.querySelector('.det-nama').focus();
}

function updateKdBrg(sel) {
    const row = sel.closest('tr');
    const kd  = parseInt(sel.value);
    row.querySelector('input[name="det_kd[]"]').value = kd;
    // Nama TIDAK auto-fill — user isi bebas
}

function delRow(btn) {
    btn.closest('tr').remove();
    recalc();
}

function recalc() {
    // Hanya jalan jika detail dicentang
    if (!document.getElementById('chkDetail').checked) return;
    let total = 0;
    document.querySelectorAll('.det-jml').forEach(inp => {
        total += parseFloat(inp.value.replace(/\./g,'').replace(',','.')) || 0;
    });
    setJumlah(total);
}

function setJumlah(total) {
    document.getElementById('f_jumlah').value    = total;
    document.getElementById('disp_jumlah').value = total.toLocaleString('id-ID');
    toggleSimpan();
    fetch('index.php?action=terbilang&n=' + Math.round(total))
        .then(r => r.text())
        .then(t => { document.getElementById('f_uang').value = t; });
}

function toggleDetail(checked) {
    document.getElementById('detailBox').classList.toggle('open', checked);
    const inp = document.getElementById('disp_jumlah');
    if (checked) {
        inp.readOnly = true;
        inp.style.background = '#eef4ff';
        inp.style.cursor = 'default';
        recalc();
    } else {
        inp.readOnly = false;
        inp.style.background = '#fff';
        inp.style.cursor = 'text';
        inp.focus();
    }
    toggleSimpan();
}

document.getElementById('chkDetail')?.addEventListener('change', function() {
    toggleDetail(this.checked);
});

// Saat jumlah diisi manual (detail tidak dicentang)
document.getElementById('disp_jumlah')?.addEventListener('change', function() {
    if (document.getElementById('chkDetail').checked) return;
    const raw = parseFloat(this.value.replace(/\./g,'').replace(',','.')) || 0;
    this.value = raw.toLocaleString('id-ID');
    document.getElementById('f_jumlah').value = raw;
    fetch('index.php?action=terbilang&n=' + Math.round(raw))
        .then(r => r.text())
        .then(t => { document.getElementById('f_uang').value = t; });
});

function toggleSimpan() {
    const dari   = document.getElementById('f_dari').value.trim();
    const untuk  = document.getElementById('f_untuk').value.trim();
    const ket    = document.getElementById('f_ket').value.trim();
    const faktur = document.getElementById('f_faktur').value.trim();
    const raw    = document.getElementById('disp_jumlah').value.replace(/\./g,'').replace(',','.');
    const jml    = parseFloat(raw) || 0;
    const btn    = document.getElementById('btnSimpan');
    btn.disabled = !(dari && untuk && ket && faktur && jml > 0);
}

['f_dari','f_untuk','f_ket','f_faktur','disp_jumlah'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', toggleSimpan);
});

// Init state on page load
(function() {
    const chk = document.getElementById('chkDetail');
    const inp = document.getElementById('disp_jumlah');
    if (!chk || !inp) return;
    if (chk.checked) {
        inp.readOnly = true;
        inp.style.background = '#eef4ff';
        inp.style.cursor = 'default';
    } else {
        inp.readOnly = false;
        inp.style.background = '#fff';
        inp.style.cursor = 'text';
    }
    toggleSimpan();
})();

function simpan() {
    const dari  = document.getElementById('f_dari').value.trim();
    const untuk = document.getElementById('f_untuk').value.trim();
    const ket   = document.getElementById('f_ket').value.trim();
    const rawJml = document.getElementById('disp_jumlah').value.replace(/\./g,'').replace(',','.');
    const jml    = parseFloat(rawJml) || 0;

    const faktur = document.getElementById('f_faktur').value.trim();
    let errors = [];
    if (!dari)   errors.push('• Telah Terima Dari wajib diisi');
    if (!untuk)  errors.push('• Untuk Pembayaran wajib diisi');
    if (!ket)    errors.push('• Keterangan wajib diisi');
    if (!faktur) errors.push('• No. Faktur wajib diisi');
    else if (!/^[A-Z0-9]+$/.test(faktur)) errors.push('• No. Faktur hanya boleh huruf BESAR dan angka');
    if (jml <= 0) errors.push('• Jumlah harus lebih dari 0');

    highlight('f_dari',   !dari);
    highlight('f_untuk',  !untuk);
    highlight('f_ket',    !ket);
    highlight('f_faktur', !faktur);
    highlightJml(jml <= 0);

    // Validasi nama detail jika detail dicentang
    if (document.getElementById('chkDetail').checked) {
        let namaErrors = false;
        document.querySelectorAll('.det-nama').forEach(inp => {
            if (inp.closest('tr') && inp.value.trim() === '') {
                inp.style.borderColor = '#dc3545';
                inp.style.background  = '#fff5f5';
                namaErrors = true;
            }
        });
        if (namaErrors) errors.push('• Nama detail tidak boleh ada yang kosong');
    }

    if (errors.length > 0) {
        alert('Data belum lengkap:\n\n' + errors.join('\n'));
        return;
    }
    document.getElementById('f_jumlah').value = jml;
    document.getElementById('frmKwt').submit();
}

function highlight(id, isError) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.borderColor = isError ? '#dc3545' : '#aaa';
    el.style.background  = isError ? '#fff5f5' : '#fff';
}

function highlightJml(isError) {
    const el = document.getElementById('disp_jumlah');
    if (!el) return;
    el.style.borderColor = isError ? '#dc3545' : '#aaa';
    el.style.background  = isError ? '#fff5f5' : (document.getElementById('chkDetail').checked ? '#eef4ff' : '#fff');
}

// Reset highlight on input
['f_dari','f_untuk','f_ket','f_faktur'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => highlight(id, false));
});
document.getElementById('disp_jumlah')?.addEventListener('input', () => highlightJml(false));

function clearNamaError(inp) {
    inp.style.borderColor = '';
    inp.style.background  = '';
}

// Enter key navigates to next field (urutan tab order)
const enterFields = ['f_dari', 'f_uang', 'f_untuk', 'f_ket', 'f_faktur', 'disp_jumlah'];
enterFields.forEach((id, i) => {
    document.getElementById(id)?.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        // Cari field berikutnya yang tidak readonly dan visible
        for (let j = i + 1; j < enterFields.length; j++) {
            const next = document.getElementById(enterFields[j]);
            if (next && !next.readOnly && next.offsetParent !== null) {
                next.focus();
                next.select();
                return;
            }
        }
        // Sudah field terakhir, langsung simpan
        simpan();
    });
});

function newForm()  { window.location.href = 'index.php?action=edit'; }
function printKwt() {
    const no = document.getElementById('f_no_kwt').value;
    if (no) window.open('index.php?action=print&id=' + encodeURIComponent(no), '_blank');
}

document.getElementById('f_faktur')?.addEventListener('input', function() {
    document.getElementById('hdr_faktur').value = this.value;
});
</script>
</body>
</html>
