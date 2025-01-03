<?php 
    $title = 'Tambah Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Akaun Baru
                        </h4>
                        <a href="/admin/savings/accounts" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/admin/savings/accounts/store" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Nama Akaun</label>
                            <input type="text" name="account_name" class="form-control" required
                                   placeholder="cth: Akaun Simpanan Kedua">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Baki Permulaan (RM)</label>
                            <input type="number" name="initial_amount" class="form-control" 
                                   min="0" step="0.01" value="0">
                            <div class="form-text">Biarkan 0 jika tiada baki permulaan</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 