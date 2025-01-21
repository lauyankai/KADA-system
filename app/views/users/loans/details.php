<?php 
    $title = 'Butiran Pinjaman';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-file-text me-2"></i>Butiran Pinjaman
                    </h4>

                    <!-- Loan Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">No. Rujukan</h6>
                            <p class="h5"><?= $loan['reference_no'] ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted">Status</h6>
                            <span class="badge bg-success">Diluluskan</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Jenis Pinjaman</h6>
                            <p><?= $loan['loan_type'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Tempoh</h6>
                            <p><?= $loan['duration'] ?> bulan</p>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted">Jumlah Pinjaman</h6>
                                    <p class="h5 text-primary">RM <?= number_format($loan['amount'], 2) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-muted">Kadar Keuntungan</h6>
                                    <p class="h5">4.2% setahun</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-muted">Ansuran Bulanan</h6>
                                    <p class="h5 text-success">RM <?= number_format($loan['monthly_payment'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- Payment Schedule -->
<h5 class="mb-3">Jadual Pembayaran</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Tarikh</th>
                                    <th>Ansuran (RM)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['month'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($payment['due_date'])) ?></td>
                                        <td>RM <?= number_format($payment['amount'], 2) ?></td>
                                        <td>
                                            <?php if ($payment['status'] === 'paid'): ?>
                                                <span class="badge bg-success">Dibayar</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Dibayar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>