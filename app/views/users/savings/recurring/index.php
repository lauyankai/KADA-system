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

                    <?php if (!empty($loans)): ?>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Jumlah akan dipotong secara automatik dari akaun simpanan anda pada tarikh yang ditetapkan
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Rujukan</th>
                                        <th>Jenis Pembiayaan</th>
                                        <th>Jumlah Bayaran (RM)</th>
                                        <th>Tarikh Potongan Seterusnya</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($loans as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                        <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                        <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                                        <td><?= $loan['next_deduction_date'] ? date('d/m/Y', strtotime($loan['next_deduction_date'])) : '-' ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="editRecurring('<?= $loan['id'] ?>', <?= $loan['monthly_payment'] ?>, '<?= $loan['next_deduction_date'] ?>')">
                                                <i class="bi bi-pencil"></i> Kemaskini
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Tiada pembiayaan aktif ditemui
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editRecurringModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kemaskini Bayaran Berulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editRecurringForm">
                    <input type="hidden" id="loanId">
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayaran (RM)</label>
                        <input type="number" class="form-control" id="monthlyPayment" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tarikh Potongan Seterusnya</label>
                        <input type="date" class="form-control" id="nextDeductionDate" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="updateRecurring()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
function editRecurring(loanId, monthlyPayment, nextDeductionDate) {
    document.getElementById('loanId').value = loanId;
    document.getElementById('monthlyPayment').value = monthlyPayment;
    document.getElementById('nextDeductionDate').value = nextDeductionDate;
    
    new bootstrap.Modal(document.getElementById('editRecurringModal')).show();
}

function updateRecurring() {
    const loanId = document.getElementById('loanId').value;
    const data = {
        monthly_payment: document.getElementById('monthlyPayment').value,
        next_deduction_date: document.getElementById('nextDeductionDate').value,
        status: 'active'
    };

    fetch(`/users/savings/recurring/update/${loanId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating recurring payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update recurring payment');
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 