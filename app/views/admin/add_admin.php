<?php 
    $title = 'Tambah Admin';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-shield-plus me-2"></i>Tambah Admin Baru
                            </h5>
                            <p class="text-muted small mb-0">Isi maklumat admin baru</p>
                        </div>
                        <a href="/admin" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/store-admin" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Emel</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kata Laluan</label>
                            <input type="password" name="password" class="form-control" required>
                            <div class="form-text">Minimum 8 aksara</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sahkan Kata Laluan</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 