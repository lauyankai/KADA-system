<?php 
    $title = 'Butiran Pinjaman';
    require_once '../app/views/layouts/header.php';

// Add debug output temporarily
error_log('Loan data in view: ' . print_r($loan, true));
?>

<div class="container mt-4">
    <?php if (isset($loan) && $loan): ?>
        <!-- Display loan details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Maklumat Pembiayaan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>No Rujukan:</strong> <?= htmlspecialchars($loan['reference_no']) ?></p>
                        <p><strong>Jenis Pembiayaan:</strong> <?= htmlspecialchars($loan['loan_type']) ?></p>
                        <p><strong>Jumlah:</strong> RM<?= number_format($loan['amount'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <?= htmlspecialchars($loan['status']) ?></p>
                        <p><strong>Tempoh:</strong> <?= htmlspecialchars($loan['duration']) ?> bulan</p>
                        <p><strong>Bayaran Bulanan:</strong> RM<?= number_format($loan['monthly_payment'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Maklumat pembiayaan tidak dijumpai
        </div>
    <?php endif; ?>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>