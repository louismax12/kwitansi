<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>RKZ POS Dashboard</title>
    <!-- SB Admin CSS -->
    <link href="assets/sbadmin/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }
        .autocomplete-suggestion:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark no-print">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php?c=dashboard&a=index">RKZ POS</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 ms-auto" style="margin-left: auto !important;">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i> <?php echo isset($_SESSION['nama_petugas']) ? $_SESSION['nama_petugas'] : 'User'; ?></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="index.php?c=auth&a=logout">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav" class="no-print">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu Kasir</div>
                        <a class="nav-link" href="index.php?c=dashboard&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link" href="index.php?c=sales&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Penjualan Baru
                        </a>
                        <a class="nav-link" href="index.php?c=riwayat&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            Riwayat Kwitansi
                        </a>

                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="sb-sidenav-menu-heading">Administrator</div>
                        <a class="nav-link" href="index.php?c=barang&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Master Barang
                        </a>
                        <a class="nav-link" href="index.php?c=user&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Master Pengguna
                        </a>
                        <a class="nav-link" href="index.php?c=laporan&a=index">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                            Laporan Pendapatan
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo isset($_SESSION['role']) ? strtoupper($_SESSION['role']) : 'GUEST'; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 pt-4">
