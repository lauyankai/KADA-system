<<<<<<< HEAD
login.php
*app\views\auth\login.php

=======
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
>>>>>>> d74ee6e0718d134dcb0fe7fe083249c4ee043e9f
<?php 
    $title = 'Log Masuk';
    require_once '../app/views/layouts/header.php';
?>

<div class="login-container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
<<<<<<< HEAD
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
=======
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
>>>>>>> d74ee6e0718d134dcb0fe7fe083249c4ee043e9f

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/auth/login" method="POST">
                        <div class="mb-4">
                            <label class="form-label">ID Pengguna / No. Kad Pengenalan</label>
                            <input type="text" 
                                   name="username" 
                                   class="form-control form-control-lg" 
                                   required
                                   oninput="formatInput(this); togglePassword(this);"
                                   placeholder="Masukkan ID Admin atau No. K/P">
                        </div>
                        <div class="mb-4" id="passwordField" style="display: none;">
                            <label class="form-label">Kata Laluan</label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control form-control-lg">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk
                            </button>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="bi bi-house me-2"></i>Kembali ke Laman Utama
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
}
.btn-success {
    background-color: #198754;
    border-color: #198754;
}
.btn-success:hover {
    background-color: #157347;
    border-color: #157347;
}
</style>

<script>
function formatInput(input) {
    let value = input.value;
    
    // If it looks like an IC number (has numbers and is long enough)
    if (value.length > 6 && /^\d[-\d]*$/.test(value)) {
        // Remove any non-digits
        value = value.replace(/\D/g, '');
        
        // Format as IC: XXXXXX-XX-XXXX
        if (value.length >= 6) {
            value = value.substring(0, 6) + '-' + value.substring(6);
        }
        if (value.length >= 9) {
            value = value.substring(0, 9) + '-' + value.substring(9);
        }
        // Limit to 14 characters (12 digits + 2 hyphens)
        value = value.substring(0, 14);
    }
    
    input.value = value;
}

function togglePassword(input) {
    const passwordField = document.getElementById('passwordField');
    const passwordInput = passwordField.querySelector('input');
    
    // Check if input contains only numbers and hyphens (IC number)
    const isIC = /^\d[-\d]*$/.test(input.value);
    
    if (isIC) {
        passwordField.style.display = 'none';
        passwordInput.removeAttribute('required');
    } else {
        passwordField.style.display = 'block';
        passwordInput.setAttribute('required', 'required');
    }
}
</script>