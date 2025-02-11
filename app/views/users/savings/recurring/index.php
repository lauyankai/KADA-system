<?php 
    $title = 'Urus Pembayaran Berulang Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Urus Pembayaran Pembiayaan
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
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-subtitle text-muted">
                                            <?= ucfirst($loan['loan_type']) ?> - <?= $loan['reference_no'] ?>
                                        </h5>
                                        <span class="badge bg-<?= ($loan['payment_status'] ?? 'active') === 'active' ? 'success' : 'warning' ?>">
                                            <?= ($loan['payment_status'] ?? 'active') === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Jumlah Bayaran Bulanan</label>
                                            <div class="input-group">
                                                <span class="input-group-text">RM</span>
                                                <input type="number" class="form-control" 
                                                       value="<?= $loan['monthly_payment'] ?>" 
                                                       onchange="updateMonthlyPayment(<?= $loan['id'] ?>, this.value)"
                                                       min="<?= $loan['monthly_payment'] * 0.8 ?>"
                                                       max="<?= $loan['monthly_payment'] * 2 ?>"
                                                       step="0.01">
                                            </div>
                                            <small class="text-muted">
                                                Minimum: RM <?= number_format($loan['monthly_payment'] * 0.8, 2) ?>
                                            </small>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Hari Potongan</label>
                                            <select class="form-select" 
                                                    onchange="updateDeductionDay(<?= $loan['id'] ?>, this.value)">
                                                <?php for($i = 1; $i <= 28; $i++): ?>
                                                    <option value="<?= $i ?>" 
                                                        <?= ($loan['deduction_day'] ?? 1) == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> hb
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <small class="text-muted">
                                                Hari potongan dalam setiap bulan
                                            </small>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Status Potongan Automatik</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       <?= ($loan['payment_status'] ?? 'active') === 'active' ? 'checked' : '' ?>
                                                       onchange="updateStatus(<?= $loan['id'] ?>, this.checked)">
                                                <label class="form-check-label">
                                                    <?= ($loan['payment_status'] ?? 'active') === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Potongan seterusnya: 
                                                <?= $loan['next_deduction_date'] ? 
                                                    date('d/m/Y', strtotime($loan['next_deduction_date'])) : 
                                                    'Belum ditetapkan' ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Jumlah akan dipotong secara automatik dari akaun simpanan anda pada hari yang ditetapkan
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
function updateMonthlyPayment(loanId, amount) {
    updateRecurringPayment(loanId, {
        monthly_payment: parseFloat(amount)
    });
}

function updateDeductionDay(loanId, day) {
    updateRecurringPayment(loanId, {
        deduction_day: parseInt(day),
        next_deduction_date: calculateNextDeductionDate(day)
    });
}

function updateStatus(loanId, isActive) {
    updateRecurringPayment(loanId, {
        status: isActive ? 'active' : 'inactive'
    });
}

function calculateNextDeductionDate(day) {
    let date = new Date();
    date.setDate(day);
    if (date < new Date()) {
        date.setMonth(date.getMonth() + 1);
    }
    return date.toISOString().split('T')[0];
}

function updateRecurringPayment(loanId, data) {
    fetch(`/users/savings/recurring/update/${loanId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
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