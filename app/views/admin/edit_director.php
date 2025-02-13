<?php 
    $title = 'Kemaskini Pengarah';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Left Column - Profile Info -->
                        <div class="col-md-4 border-end">
                            <div class="text-center mb-4">
                                <div class="avatar-placeholder mb-3">
                                    <i class="bi bi-person-badge display-1 text-primary opacity-50"></i>
                                </div>
                                <h5 class="mb-1"><?= htmlspecialchars($director['name']) ?></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars($director['position']) ?></p>
                                <div class="badge bg-primary">Pengarah</div>
                            </div>

                            <div class="list-group list-group-flush small">
                                <div class="list-group-item px-0">
                                    <i class="bi bi-envelope me-2 text-primary"></i>
                                    <?= htmlspecialchars($director['email'] ?: 'Tiada emel') ?>
                                </div>
                                <div class="list-group-item px-0">
                                    <i class="bi bi-telephone me-2 text-success"></i>
                                    <?= htmlspecialchars($director['phone_number'] ?: 'Tiada telefon') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Edit Form -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-person-gear me-2"></i>Kemaskini Pengarah
                                    </h5>
                                    <p class="text-muted small mb-0">Kemaskini maklumat pengarah</p>
                                </div>
                                <a href="/admin" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>

                            <form action="/admin/update-director/<?= $director['id'] ?>" method="POST">
                                <div class="mb-3">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bi bi-person me-2 text-primary"></i>Nama Penuh
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($director['name']) ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bi bi-briefcase me-2 text-primary"></i>Jawatan
                                    </label>
                                    <input type="text" 
                                           name="position" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($director['position']) ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bi bi-envelope me-2 text-primary"></i>Emel
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($director['email']) ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label d-flex align-items-center">
                                        <i class="bi bi-telephone me-2 text-primary"></i>No. Telefon
                                    </label>
                                    <input type="tel" 
                                           name="phone_number" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($director['phone_number']) ?>">
                                </div>

                                <div class="d-grid gap-2">
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
    </div>
</div>

<style>
.avatar-placeholder {
    width: 120px;
    height: 120px;
    background-color: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: transform 0.2s;
}

.avatar-placeholder:hover {
    transform: scale(1.05);
}

.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 