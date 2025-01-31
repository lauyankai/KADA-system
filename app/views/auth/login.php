<?php 
    $title = 'Log Masuk';
    require_once '../app/views/layouts/header.php';
?>

<div class="login-container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="color: #198754; font-weight: bold;">
                            <i class="bi bi-shield-lock me-2"></i>Log Masuk
                        </h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                <hr>
                                <p class="mb-0">Belum menjadi ahli? 
                                    <a href="/guest/create" class="alert-link">
                                        Daftar sebagai ahli sekarang
                                    </a>
                                </p>
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
                            <label class="form-label">ID Pengguna</label>
                            <input type="text" 
                                   name="username" 
                                   class="form-control form-control-lg" 
                                   required
                                   oninput="formatInput(this);"
                                   placeholder="Masukkan ID Admin atau No. K/P">
                        </div>
                        <div class="mb-4" id="passwordField">
                            <label class="form-label">Kata Laluan</label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control form-control-lg"
                                   required
                                   placeholder="Masukkan kata laluan">
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

                    <!-- Registration Link -->
                    <div class="text-center mt-4">
                        <p class="mb-0">Belum mendaftar? 
                            <a href="/guest/create" class="text-success fw-bold">
                                Daftar sebagai ahli
                            </a>
                        </p>
                    </div>
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
    value = value.substring(0, 14);
    input.value = value;
}

function togglePassword(input) {
    const passwordField = document.getElementById('passwordField');
    const passwordInput = passwordField.querySelector('input');
    
    // Always show password field for all types of users
    passwordField.style.display = 'block';
    passwordInput.setAttribute('required', 'required');
}
</script>