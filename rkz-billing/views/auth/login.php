<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RKZ Billing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            background-color: #f3f5f8; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-wrapper {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 550px;
            margin: 20px;
        }
        .login-left {
            flex: 1.2;
            background: url('assets/images/login_bg.png') center center no-repeat;
            background-size: cover;
            position: relative;
            padding: 40px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 20px 0 0 20px;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(74,144,226,0.1) 0%, rgba(30,80,180,0.8) 100%);
            border-radius: 20px 0 0 20px;
        }
        .login-left-content {
            position: relative;
            z-index: 1;
        }
        .logo-box {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        .login-right {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            border-radius: 0 20px 20px 0;
        }
        .login-right h3 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .login-right p.text-muted {
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px 12px 40px;
            border: 1px solid #e0e0e0;
            height: auto;
            font-size: 14px;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #3b66d6;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group i {
            position: absolute;
            top: 37px;
            left: 15px;
            color: #a0a0a0;
        }
        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
            display: block;
        }
        .btn-login {
            background-color: #3b66d6;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background-color: #2a4db3;
            transform: translateY(-1px);
        }
        .custom-control-label::before {
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { border-radius: 20px; padding: 40px 30px; }
            .login-wrapper { max-width: 450px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Left Side -->
    <div class="login-left d-none d-md-flex">
        <div class="login-left-content">
            <div class="logo-box">
                <img src="img/logo.svg" alt="Logo" style="height: 40px; margin-right: 12px;">
                RKZ Kwitansi
            </div>
        </div>
        <div class="login-left-content">
            <!-- <h3 class="font-weight-bold">Empowering You to Plan, Track, and Deliver with Clarity</h3> -->
            <!-- <p class="mb-0 text-light" style="opacity: 0.8;">Sistem informasi tagihan terintegrasi untuk efisiensi transaksi Rumah Sakit.</p> -->
        </div>
    </div>
    
    <!-- Right Side -->
    <div class="login-right">
        <h3>Log In 👋</h3>
        <p class="text-muted">Log In to Your RKZ Kwitansi</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="font-size: 14px; border-radius: 8px; padding: 10px 15px;">
                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?c=auth&a=login">
            <div class="form-group">
                <label>Username</label>
                <i class="fas fa-envelope"></i>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="rememberMe">
                    <label class="custom-control-label text-muted" for="rememberMe" style="font-size: 13px; padding-top: 2px;">Remember Me</label>
                </div> -->
                <!-- <a href="#" class="text-dark font-weight-bold" style="font-size: 13px;">Forgot Password?</a> -->
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-login">Log in</button>
            
            <!-- <div class="text-center mt-4">
                <p class="text-muted" style="font-size: 13px;">Don't have an account yet? <a href="#" class="text-primary font-weight-bold">Registration</a></p>
            </div> -->
        </form>
    </div>
</div>

</body>
</html>
