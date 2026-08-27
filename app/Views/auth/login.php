<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pemesanan Kendaraan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a252f 0%, #2c3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #fff;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        
        .login-header h2 {
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .login-header p {
            margin-bottom: 0;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 15px;
        }
        
        .form-floating .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #2980b9 0%, #1f6dad 100%);
        }
        
        .credentials-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .credentials-info h6 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .credentials-info table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-truck"></i>
            <h2>Fleet Management</h2>
            <p>Sistem Pemesanan Kendaraan</p>
        </div>
        
        <div class="login-body">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="/auth/login" method="POST">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Username" required autofocus
                           value="<?= old('username') ?>">
                    <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                </div>
                
                <button type="submit" class="btn btn-login btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
            
            <div class="credentials-info">
                <h6><i class="bi bi-info-circle me-2"></i>Demo Credentials:</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Admin:</strong></td>
                        <td>admin / password</td>
                    </tr>
                    <tr>
                        <td><strong>Approver L1:</strong></td>
                        <td>approver1 / password</td>
                    </tr>
                    <tr>
                        <td><strong>Approver L2:</strong></td>
                        <td>approver2 / password</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
