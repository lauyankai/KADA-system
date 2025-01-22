<?php 
    $title = 'Status Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <i class="bi bi-list-check me-2"></i>Status Pembiayaan
                </h4>
                <div>
                    <a href="/users/dashboard" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="/users/loans/request" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Permohonan Baru
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Rujukan</th>
                            <th>Jenis</th>
                            <th>Jumlah (RM)</th>
                            <th>Tempoh</th>
                            <th>Bayaran Bulanan</th>
                            <th>Tarikh Mohon</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingLoans) && empty($activeLoans) && empty($rejectedLoans)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-3">
                                    Tiada rekod pembiayaan
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingLoans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                    <td><?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($loan['duration']) ?> bulan</td>
                                    <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($loan['date_received'])) ?></td>
                                    <td><span class="badge bg-warning">Dalam Proses</span></td>
                                    <td>-</td>
                                </tr>
                            <?php endforeach; ?>

                            <?php foreach ($activeLoans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                    <td><?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($loan['duration']) ?> bulan</td>
                                    <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($loan['approved_date'])) ?></td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                    <td>
                                        <a href="/users/loans/details/<?= $loan['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php foreach ($rejectedLoans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                    <td><?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($loan['duration']) ?> bulan</td>
                                    <td><?= number_format($loan['monthly_payment'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($loan['date_received'])) ?></td>
                                    <td>
                                        <span class="badge bg-danger" 
                                              title="<?= htmlspecialchars($loan['remarks']) ?>">
                                            Ditolak
                                        </span>
                                    </td>
                                    <td>-</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>