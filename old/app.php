<?php
require_once __DIR__ . '/app_common.php';

session_start();

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

if ($action === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user']) && $action !== 'login') {
    header('Location: index.php?action=login');
    exit;
}

$db = connectDb();
$loginError = '';
$saveError = '';
$flashMessage = '';

if (isset($_SESSION['flash'])) {
    $flashMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtoupper(trim(isset($_POST['username']) ? $_POST['username'] : ''));
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    $stmt = $db->prepare('SELECT username, nama_petugas, role FROM users WHERE username = ? AND password = ?');
    $stmt->execute(array($username, hashPassword($password)));
    $row = $stmt->fetch();

    if ($row) {
        $_SESSION['user'] = $row['username'];
        $_SESSION['nama_petugas'] = $row['nama_petugas'];
        $_SESSION['role'] = $row['role'];
        header('Location: index.php');
        exit;
    }

    $loginError = 'Username atau password salah.';
}

if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = saveKwitansi($db, $_POST);
    if ($result['status'] === 'success') {
        $_SESSION['flash'] = $result['message'];
        header('Location: index.php');
        exit;
    }
    $saveError = $result['message'];
}

if ($action === 'print' && isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT k.no_kwitansi, k.tanggal_transaksi, k.nama_pasien, k.total_bayar, u.nama_petugas FROM kwitansi k LEFT JOIN users u ON u.id_user = k.id_user WHERE k.no_kwitansi = ?');
    $stmt->execute(array($_GET['id']));
    $kwitansi = $stmt->fetch();

    $detailStmt = $db->prepare('SELECT d.jumlah, d.subtotal, b.nama_barang, b.harga FROM detail_kwitansi d LEFT JOIN barang b ON b.id_barang = d.id_barang WHERE d.no_kwitansi = ?');
    $detailStmt->execute(array($_GET['id']));
    $details = $detailStmt->fetchAll();

    if (!$kwitansi) {
        echo '<h3>Data kwitansi tidak ditemukan.</h3>';
        exit;
    }

    $tanggal = date('d-m-Y H:i', strtotime($kwitansi['tanggal_transaksi']));
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Receipt</title><style>body{font-family:Arial,sans-serif;padding:24px;max-width:640px;margin:auto}table{width:100%;border-collapse:collapse;margin-top:12px}th,td{border-bottom:1px solid #ddd;padding:6px 0;text-align:left} .right{text-align:right} .btn{margin-top:16px}</style></head><body>';
    echo '<h2>RKZ SURABAYA HOSPITAL</h2>';
    echo '<p><strong>No. Kwitansi:</strong> ' . escapeHtml($kwitansi['no_kwitansi']) . '</p>';
    echo '<p><strong>Tanggal:</strong> ' . escapeHtml($tanggal) . '</p>';
    echo '<p><strong>Nama Pasien:</strong> ' . escapeHtml($kwitansi['nama_pasien']) . '</p>';
    echo '<table><thead><tr><th>Barang</th><th class="right">Qty</th><th class="right">Subtotal</th></tr></thead><tbody>';
    foreach ($details as $detail) {
        echo '<tr><td>' . escapeHtml($detail['nama_barang']) . '</td><td class="right">' . escapeHtml($detail['jumlah']) . '</td><td class="right">' . number_format($detail['subtotal'], 0, ',', '.') . '</td></tr>';
    }
    echo '</tbody></table>';
    echo '<p class="right"><strong>Total: Rp ' . number_format($kwitansi['total_bayar'], 0, ',', '.') . '</strong></p>';
    echo '<p>Hormat kami,<br>' . escapeHtml($kwitansi['nama_petugas']) . '</p>';
    echo '<div class="btn"><button onclick="window.print()">Cetak</button></div>';
    echo '</body></html>';
    exit;
}

$barangList = getBarangList($db);

if ($action === 'login') {
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Login Kwitansi</title><style>body{font-family:Segoe UI,sans-serif;background:#f4f7fb;margin:0;padding:0} .card{max-width:360px;margin:80px auto;padding:24px;border-radius:12px;background:#fff;box-shadow:0 8px 25px rgba(0,0,0,.12)} input{width:100%;padding:10px;margin:8px 0;border:1px solid #cbd5e1;border-radius:8px} button{width:100%;padding:10px;border:0;border-radius:8px;background:#1d4ed8;color:#fff;cursor:pointer} .error{color:#b91c1c;margin-bottom:10px}</style></head><body><div class="card"><h2>Login Kwitansi</h2><p>Masuk sebagai kasir/admin</p>';
    if ($loginError !== '') {
        echo '<div class="error">' . escapeHtml($loginError) . '</div>';
    }
    echo '<form method="post" action="index.php?action=login"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button type="submit">Masuk</button></form></div></body></html>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Sistem Kwitansi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f5f7fb}.card{border-radius:12px}.table td,.table th{vertical-align:middle}</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-1">Sistem Kwitansi RKZ</h2>
      <p class="text-muted mb-0">Catat transaksi dan simpan ke database</p>
    </div>
    <a href="index.php?action=logout" class="btn btn-outline-secondary">Logout</a>
  </div>

  <?php if ($flashMessage !== ''): ?>
  <div class="alert alert-success"><?php echo escapeHtml($flashMessage); ?></div>
  <?php endif; ?>

  <?php if ($saveError !== ''): ?>
  <div class="alert alert-danger"><?php echo escapeHtml($saveError); ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form id="kwitansiForm">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Nama Pasien</label>
            <input type="text" name="nama_pasien" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input type="text" name="tanggal_transaksi" class="form-control" value="<?php echo escapeHtml(date('Y-m-d H:i:s')); ?>" readonly>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Cari barang</label>
          <input type="text" id="barangSearch" class="form-control" placeholder="Ketik nama barang...">
        </div>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th style="width:60%">Barang</th>
              <th style="width:20%">Jumlah</th>
              <th style="width:20%">Subtotal</th>
              <th style="width:5%"></th>
            </tr>
          </thead>
          <tbody id="itemRows"></tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
          <button type="button" class="btn btn-outline-primary" id="addRowBtn">Tambah Item</button>
          <div class="fw-bold fs-5">Total: Rp <span id="totalBayar">0</span></div>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" class="btn btn-success">Simpan & Cetak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
var barangData = <?php echo json_encode($barangList); ?>;

function addRow() {
    var tbody = document.getElementById('itemRows');
    var row = document.createElement('tr');
    var options = '';
    barangData.forEach(function(item) {
        options += '<option value="' + item.id_barang + '" data-harga="' + item.harga + '">' + item.nama_barang + ' (Stok: ' + item.stok + ')</option>';
    });
    row.innerHTML = '<td><select class="form-select" name="item_id[]">' + options + '</select></td><td><input type="number" class="form-control" name="qty[]" min="1" value="1" onchange="recalculate()"></td><td><input type="text" class="form-control subtotal" value="0" readonly></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">×</button></td>';
    tbody.appendChild(row);
    recalculate();
}

function removeRow(button) {
    var row = button.closest('tr');
    row.remove();
    recalculate();
}

function recalculate() {
    var rows = document.querySelectorAll('#itemRows tr');
    var total = 0;
    rows.forEach(function(row) {
        var select = row.querySelector('select');
        var qty = parseInt(row.querySelector('input[name="qty[]"]').value || 0, 10);
        var price = parseInt(select.options[select.selectedIndex].getAttribute('data-harga') || 0, 10);
        var subtotal = qty * price;
        row.querySelector('.subtotal').value = subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
    document.getElementById('totalBayar').textContent = total.toLocaleString('id-ID');
}

function populateBarangOptionsFromSearch(keyword) {
    if (!keyword) {
        return;
    }
    fetch('api_cari_barang.php?keyword=' + encodeURIComponent(keyword))
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                barangData = data.data;
                var rows = document.querySelectorAll('#itemRows tr');
                rows.forEach(function(row) {
                    var select = row.querySelector('select');
                    if (!select) {
                        return;
                    }
                    var currentValue = select.value;
                    var options = '';
                    data.data.forEach(function(item) {
                        options += '<option value="' + item.id_barang + '" data-harga="' + item.harga + '">' + item.nama_barang + ' (Stok: ' + item.stok + ')</option>';
                    });
                    select.innerHTML = options;
                    if (currentValue) {
                        select.value = currentValue;
                    }
                });
                recalculate();
            }
        });
}

document.getElementById('addRowBtn').addEventListener('click', addRow);
document.getElementById('barangSearch').addEventListener('input', function() {
    populateBarangOptionsFromSearch(this.value);
});
document.getElementById('kwitansiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('api_simpan_kwitansi.php', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                window.open('index.php?action=print&id=' + encodeURIComponent(data.no_kwitansi), '_blank');
                window.location.href = 'index.php';
            } else {
                alert(data.message || 'Gagal menyimpan kwitansi.');
            }
        });
});

addRow();
</script>
</body>
</html>
