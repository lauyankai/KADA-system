<?php 
    $title = 'Penyata Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary">
                    <i class="bi bi-file-text me-2"></i>Penyata Pembiayaan
                </h5>
                <form action="/users/loans/download" method="GET" class="d-inline">
                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                    <input type="hidden" name="start_date" value="<?= $startDate ?>">
                    <input type="hidden" name="end_date" value="<?= $endDate ?>">
                    <input type="hidden" name="period" value="<?= $period ?>">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-download me-2"></i>Muat Turun PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- Loan Selection and Period Filters -->
            <form method="GET" class="statement-form bg-light p-3 rounded mb-4">
                <div class="row g-3">
                    <!-- Loan Selection -->
                    <?php if (count($loans) > 1): ?>
                    <div class="col-md-4">
                        <label class="form-label">Pilih Pembiayaan</label>
                        <select name="loan_id" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($loans as $l): ?>
                                <option value="<?= $l['id'] ?>" 
                                        <?= $l['id'] == $loan['id'] ? 'selected' : '' ?>>
                                    <?= $l['reference_no'] ?> - <?= ucfirst($l['loan_type']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Period Selection -->
                    <div class="col-md-4">
                        <label class="form-label">Tempoh</label>
                        <select name="period" class="form-select" onchange="updateDates(this.value)">
                            <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                            <option value="current" <?= $period === 'current' ? 'selected' : '' ?>>Bulan Ini</option>
                            <option value="last" <?= $period === 'last' ? 'selected' : '' ?>>Bulan Lepas</option>
                            <option value="custom" <?= $period === 'custom' ? 'selected' : '' ?>>Pilih Tarikh</option>
                        </select>
                    </div>

                    <!-- Custom Date Range -->
                    <div id="customDateRange" class="row" 
                         style="display: <?= $period === 'custom' ? 'flex' : 'none' ?>;">
                        <div class="col-md-4">
                            <label class="form-label">Dari</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?= $startDate ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hingga</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?= $endDate ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Papar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Loan Details -->
            <div class="loan-details mb-4">
                <h6 class="text-primary mb-3">Maklumat Pembiayaan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">No. Rujukan</th>
                            <td><?= $loan['reference_no'] ?></td>
                            <th width="200">Status</th>
                            <td><?= ucfirst($loan['status']) ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Pembiayaan</th>
                            <td><?= ucfirst($loan['loan_type']) ?></td>
                            <th>Tarikh Mohon</th>
                            <td><?= date('d/m/Y', strtotime($loan['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Pembiayaan</th>
                            <td>RM <?= number_format($loan['amount'], 2) ?></td>
                            <th>Tarikh Terima</th>
                            <td><?= $loan['date_received'] ? date('d/m/Y', strtotime($loan['date_received'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Bayaran Bulanan</th>
                            <td>RM <?= number_format($loan['monthly_payment'], 2) ?></td>
                            <th>Tempoh</th>
                            <td><?= $loan['duration'] ?> bulan</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="transactions mt-4">
                <h6 class="text-primary mb-3">Rekod Transaksi</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarikh</th>
                                <th>Penerangan</th>
                                <th class="text-end">Bayaran (RM)</th>
                                <th class="text-end">Baki (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tiada rekod transaksi</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $trans): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($trans['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($trans['description']) ?></td>
                                        <td class="text-end"><?= number_format($trans['payment_amount'], 2) ?></td>
                                        <td class="text-end"><?= number_format($trans['remaining_balance'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateDates(period) {
    const customDateRange = document.getElementById('customDateRange');
    customDateRange.style.display = period === 'custom' ? 'flex' : 'none';
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 