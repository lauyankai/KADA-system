<?php 
    $title = 'Kemaskini Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Kemaskini Akaun
                        </h4>
                        <a href="/admin/savings/accounts" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/admin/savings/accounts/update/<?= $account['id'] ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Nama Akaun</label>
                            <input type="text" name="account_name" class="form-control" required
                                   value="<?= htmlspecialchars($account['account_name'] ?? 'Akaun Simpanan') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $account['status'] === 'active' ? 'selected' : '' ?>>
                                    Aktif
                                </option>
                                <option value="inactive" <?= $account['status'] === 'inactive' ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Baki Semasa</label>
                            <div class="form-control bg-light">
                                RM <?= number_format($account['current_amount'], 2) ?>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 