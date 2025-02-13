<?php 
    $title = 'Senarai Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="bi bi-credit-card me-2"></i>Senarai Pembiayaan
            </h5>
        </div>

        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($loans)): ?>
                <div class="alert alert-info">
                    Tiada rekod pembiayaan ditemui.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Rujukan</th>
                                <th>Jenis</th>
                                <th>Jumlah (RM)</th>
                                <th>Bayaran Bulanan (RM)</th>
                                <th>Status</th>
                                <th>Tarikh Mohon</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                    <td><?= ucfirst($loan['loan_type']) ?></td>
                                    <td><?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $loan['status'] === 'approved' ? 'success' : 
                                            ($loan['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($loan['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($loan['created_at'])) ?></td>
                                    <td>
                                        <a href="/users/loans/report/<?= $loan['id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-file-text me-1"></i>Penyata
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 