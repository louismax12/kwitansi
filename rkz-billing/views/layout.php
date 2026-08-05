<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKZ Billing System</title>
    <!-- Bootstrap 4 murni -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        body { 
            background-color: #f4f7fe; 
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            color: #2b3674;
        }
        
        /* Sidebar Styles */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            min-height: 100vh;
            background: #100325ff;
            color: #a3aed1;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        #sidebar .sidebar-header {
            padding: 30px 20px;
            background: #100325ff;
            display: flex;
            align-items: center;
        }
        
        .sidebar-logo-icon {
            width: 35px;
            height: 35px;
            background: #4318FF;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 12px;
        }

        #sidebar h4 {
            font-weight: 700;
            font-size: 22px;
            color: #e4e5ebff;
            margin: 0;
        }
        
        #sidebar ul.components {
            padding: 10px 0;
        }
        
        #sidebar ul li {
            margin-bottom: 5px;
        }
        
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            display: block;
            color: #a3aed1;
            text-decoration: none;
            margin: 0 15px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        
        #sidebar ul li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
        
        #sidebar ul li a:hover {
            color: #4318FF;
            background: #f4f7fe;
        }
        
        #sidebar ul li.active > a {
            color: #4318FF;
            background: #f4f7fe;
            border: 1px solid #4318FF;
            font-weight: 700;
        }

        /* Page Content Styles */
        #content {
            width: calc(100% - 260px);
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
        
        /* Top Navbar Styles */
        .top-navbar {
            background: #f4f7fe;
            padding: 20px 30px;
            border-bottom: none;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #2b3674;
            margin: 0;
        }

        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }

        .nav-icon-btn {
            color: #a3aed1;
            font-size: 20px;
            background: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            text-decoration: none;
            position: relative;
        }

        .nav-icon-btn:hover {
            color: #4318FF;
            text-decoration: none;
        }

        .nav-icon-btn .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ff5b5b;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .user-profile {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 5px 15px 5px 5px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            color: #2b3674;
            font-weight: 600;
            text-decoration: none;
        }
        .user-profile:hover { text-decoration: none; color: #4318FF; }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eef2ff;
            color: #4318FF;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .content-wrapper { padding: 10px 30px 30px 30px; }
        
        /* Card Styles */
        .card { 
            background: #ffffff;
            border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            border: none; 
            margin-bottom: 1.5rem; 
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f4f7fe;
            padding: 20px;
        }

        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 16px;
        }
        .btn-primary {
            background-color: #4318FF;
            border-color: #4318FF;
        }
        .btn-primary:hover {
            background-color: #3311cc;
            border-color: #3311cc;
        }
        
        /* Table Styles */
        .table {
            color: #2b3674;
            margin-bottom: 0;
        }
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #f4f7fe;
            color: #a3aed1;
            font-weight: 600;
            font-size: 14px;
            background-color: #fafbfc;
            text-transform: capitalize;
            padding: 15px;
        }
        .table tbody td {
            border-bottom: 1px solid #f4f7fe;
            border-top: none;
            padding: 15px;
            vertical-align: middle;
            font-weight: 500;
        }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-success { background-color: #e5f8ed; color: #05cd99; }
        .badge-danger { background-color: #feeceb; color: #ee5d50; }
        .badge-warning { background-color: #fff8e5; color: #ffce20; }
        .badge-info { background-color: #eef2ff; color: #4318FF; }

        /* Actions */
        .table-action-icon {
            color: #a3aed1;
            margin: 0 5px;
            font-size: 16px;
            transition: color 0.2s;
        }
        .table-action-icon:hover { color: #4318FF; text-decoration: none; }
        .table-action-icon.text-danger:hover { color: #ee5d50; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; }
            #sidebar.active { margin-left: 0; }
            #content { width: 100%; }
            #content.active { width: calc(100% - 260px); margin-left: 260px; }
        }
    </style>
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="no-print">
            <div class="sidebar-header">
                <div class="brand-logo-wrap mr-3">
                    <img src="img/logo.svg" alt="Logo RKZ" class="brand-logo-img" style="height: 40px; width: auto; max-width: 100%;">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-0" style="color: #e4e5eb; font-weight: 700; line-height: 1;">RKZ Billing</h4>
                    <h6 class="mb-0 mt-1" style="color: rgba(228, 229, 235, 0.7); font-size: 11px; letter-spacing: 0.5px;">Sistem Informasi Kwitansi</h6>
                </div>
            </div>
            
            <?php 
                $current_page = isset($_GET['c']) ? $_GET['c'] : 'dashboard'; 
                $current_action = isset($_GET['a']) ? $_GET['a'] : 'index';

                // Map page to title
                $page_titles = [
                    'dashboard' => 'Dashboard',
                    'kwitansi' => ($current_action == 'create') ? 'Buat Tagihan Baru' : 'Invoices',
                    'pelanggan' => 'Client Management',
                    'barang' => 'Master Barang',
                    'users' => 'Settings'
                ];
                $page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'RKZ Billing';
            ?>
            
            <ul class="list-unstyled components">
                <li class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php?c=dashboard"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="<?php echo ($current_page == 'kwitansi' && $current_action == 'create') ? 'active' : ''; ?>">
                    <a href="index.php?c=kwitansi&a=create"><i class="fas fa-file-invoice"></i> Buat Invoice</a>
                </li>
                <li class="<?php echo $current_page == 'pelanggan' ? 'active' : ''; ?>">
                    <a href="index.php?c=pelanggan"><i class="fas fa-users"></i> Client Management</a>
                </li>
                <li class="<?php echo $current_page == 'barang' ? 'active' : ''; ?>">
                    <a href="index.php?c=barang"><i class="fas fa-box"></i> Barang/Obat</a>
                </li>
                <li class="<?php echo ($current_page == 'kwitansi' && $current_action != 'create') ? 'active' : ''; ?>">
                    <a href="index.php?c=kwitansi&a=history"><i class="fas fa-file-invoice-dollar"></i> Invoices</a>
                </li>
                <!-- <?php if (isset($_SESSION['m1']) && $_SESSION['m1'] == 1): ?> -->
                <li class="<?php echo $current_page == 'users' ? 'active' : ''; ?>">
                    <a href="index.php?c=users"><i class="fas fa-cog"></i> Settings</a>
                </li>
                <li class="<?php echo $current_page == 'users' ? 'active' : ''; ?>">
                    <a class="text-danger" href="index.php?c=auth&a=logout"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light top-navbar no-print">
                <div class="container-fluid px-0">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-lg-none mr-3">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <h2 class="page-title d-none d-md-block"><?php echo $page_title; ?></h2>
                    
                    <ul class="nav navbar-nav ml-auto flex-row align-items-center">
                        <li class="nav-item">
                            <a class="nav-icon-btn" href="#">
                                <i class="far fa-bell"></i>
                                <span class="badge-dot"></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown ml-3">
                            <a class="user-profile dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 mt-2">
                                <a class="dropdown-item" href="#"><i class="fas fa-user-circle mr-2 text-muted"></i> Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="index.php?c=auth&a=logout"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
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
                    echo "<div class='card'><div class='card-body text-center py-5 text-muted'>Content not found.</div></div>";
                }
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
    });
    </script>
    
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
