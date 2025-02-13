<?php 
    $title = 'Kemaskini Profil';
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
                                <i class="bi bi-person-gear me-2"></i>Kemaskini Profil Admin
                            </h5>
                            <p class="text-muted small mb-0">Kemaskini maklumat profil anda</p>
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

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/update-profile" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($admin['username']) ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Emel</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($admin['email']) ?>" 
                                   required>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label">Kata Laluan Semasa</label>
                            <input type="password" name="current_password" class="form-control">
                            <div class="form-text">Hanya isi jika ingin menukar kata laluan</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kata Laluan Baru</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sahkan Kata Laluan Baru</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 