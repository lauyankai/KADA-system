<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-pencil-square me-2"></i>Kemaskini Sasaran Simpanan
                    </h5>

                    <form action="/users/savings/goals/update/<?= $goal['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Sasaran</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= htmlspecialchars($goal['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Sasaran (RM)</label>
                            <input type="number" name="target_amount" class="form-control" 
                                   value="<?= $goal['target_amount'] ?>"
                                   min="10" step="0.01" required>
                            <div class="form-text">Minimum: RM10.00</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tarikh Sasaran</label>
                            <input type="date" name="target_date" class="form-control" 
                                   value="<?= $goal['target_date'] ?>"
                                   min="<?= date('Y-m-d', strtotime('+1 month')) ?>" required>
                            <div class="form-text">Tarikh mestilah sekurang-kurangnya sebulan dari sekarang</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/users/savings/page" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
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

<?php require APPROOT . '/views/includes/footer.php'; ?> 