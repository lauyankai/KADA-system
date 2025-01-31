<?php 
    $title = 'Urus Pembayaran Berulang';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Urus Pembayaran Berulang
                        </h4>
                        <a href="/users/savings" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($loans)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Tiada pembiayaan aktif ditemui
                        </div>
                    <?php else: ?>
                        <?php foreach ($loans as $loan): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">
                                        <?= $loan['loan_type'] ?> - <?= $loan['reference_no'] ?>
                                    </h6>

                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Jumlah Bulanan</label>
                                            <div class="input-group">
                                                <span class="input-group-text">RM</span>
                                                <input type="text" class="form-control" 
                                                       value="<?= number_format($loan['monthly_payment'], 2) ?>" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Kekerapan</label>
                                            <select class="form-select" 
                                                    onchange="updateFrequency(<?= $loan['id'] ?>, this.value)">
                                                <option value="monthly" <?= $loan['frequency'] === 'monthly' ? 'selected' : '' ?>>
                                                    Bulanan
                                                </option>
                                                <option value="biweekly" <?= $loan['frequency'] === 'biweekly' ? 'selected' : '' ?>>
                                                    Dua Minggu
                                                </option>
                                                <option value="weekly" <?= $loan['frequency'] === 'weekly' ? 'selected' : '' ?>>
                                                    Mingguan
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Tarikh Bayaran</label>
                                            <input type="date" class="form-control"
                                                   value="<?= $loan['payment_date'] ?>"
                                                   onchange="updatePaymentDate(<?= $loan['id'] ?>, this.value)">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       <?= $loan['status'] === 'active' ? 'checked' : '' ?>
                                                       onchange="updateStatus(<?= $loan['id'] ?>, this.checked)">
                                                <label class="form-check-label">
                                                    <?= $loan['status'] === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateFrequency(loanId, frequency) {
    updateRecurringPayment(loanId, { frequency: frequency });
}

function updatePaymentDate(loanId, date) {
    updateRecurringPayment(loanId, { payment_date: date });
}

function updateStatus(loanId, isActive) {
    updateRecurringPayment(loanId, { status: isActive ? 'active' : 'paused' });
}

function updateRecurringPayment(loanId, data) {
    fetch('/users/savings/recurring/update/' + loanId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Pembayaran berulang berjaya dikemaskini');
        } else {
            // Show error message
            alert('Ralat: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ralat semasa mengemaskini pembayaran berulang');
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 