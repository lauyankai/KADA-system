<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #fff8e8; }
        .card {
            border-radius: 15px;
            
        }
        .btn-gradient {
            background: linear-gradient(45deg, #198754, #20c997);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(45deg, #20c997, #198754);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
=======
<?php 
    $title = 'Log Masuk';
    require_once '../app/views/layouts/header.php';
?>

    <div class="login-container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
>>>>>>> 0d96497968335b81ced6c6033c0725e24d0e5e3c
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="color: #198754; font-weight: bold;">
                            <i class="bi bi-shield-lock me-2"></i>Log Masuk
                        </h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/auth/login" method="POST">
                            <div class="mb-4">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-2"></i>Username
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-key me-2"></i>Kata Laluan
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-gradient btn-lg w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk
                                </button>
                                <a href="/" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-house me-2"></i>Kembali ke Laman Utama
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>