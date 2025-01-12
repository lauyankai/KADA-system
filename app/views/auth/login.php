
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