<?php 
    $title = 'Tetapkan Kata Laluan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Tetapkan Kata Laluan</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/auth/setup-password" method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Kata Laluan Baharu</label>
                            <input type="password" name="password" class="form-control" required 
                                   minlength="8">
                            <div class="form-text">Minimum 8 aksara</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Sahkan Kata Laluan</label>
                            <input type="password" name="confirm_password" class="form-control" 
                                   required minlength="8">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                Tetapkan Kata Laluan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 