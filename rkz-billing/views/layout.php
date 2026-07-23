<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKZ Billing System (Akaunting Clone)</title>
    <!-- Bootstrap 4 murni -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #fff;
            color: #333;
            transition: all 0.3s;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.1em;
            display: block;
            color: #555;
            text-decoration: none;
        }
        
        #sidebar ul li a:hover {
            color: #007bff;
            background: #f4f6f9;
        }
        
        #sidebar ul li.active > a {
            color: #007bff;
            background: #eef2f7;
            border-left: 4px solid #007bff;
            font-weight: bold;
        }

        /* Page Content Styles */
        #content {
            width: calc(100% - 250px);
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
        
        /* Top Navbar Styles */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 10px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .content-wrapper { padding: 30px; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); border: none; margin-bottom: 1.5rem; }
        
        /* FontAwesome inclusion (using CDN for icons) */
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="no-print">
            <div class="sidebar-header">
                <h4 class="text-primary font-weight-bold mb-0">RKZ Billing</h4>
            </div>
            
            <?php $current_page = isset($_GET['c']) ? $_GET['c'] : 'dashboard'; ?>
            
            <ul class="list-unstyled components">
                <li class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php?c=dashboard"><i class="fas fa-home mr-2"></i> Dashboard</a>
                </li>
                <li class="<?php echo $current_page == 'pelanggan' ? 'active' : ''; ?>">
                    <a href="index.php?c=pelanggan"><i class="fas fa-users mr-2"></i> Pelanggan</a>
                </li>
                <li class="<?php echo $current_page == 'barang' ? 'active' : ''; ?>">
                    <a href="index.php?c=barang"><i class="fas fa-box mr-2"></i> Barang/Obat</a>
                </li>
                <li class="<?php echo $current_page == 'kwitansi' ? 'active' : ''; ?>">
                    <a href="index.php?c=kwitansi&a=history"><i class="fas fa-file-invoice-dollar mr-2"></i> Invoices</a>
                </li>
                <li>
                    <a href="index.php?c=kwitansi&a=create" class="text-success"><i class="fas fa-plus-circle mr-2"></i> Buat Baru</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light top-navbar no-print">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-lg-none">
                        <i class="fas fa-align-left"></i>
                    </button>
                    
                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-bell"></i></a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg mr-1 text-primary"></i> <strong>Admin Kasir</strong>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Profile</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="content-wrapper">
                <?php
                if (isset($content) && file_exists($content)) {
                    require_once $content;
                } else {
                    echo "<p>Content not found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
