<?php 
    $title = 'Penyata Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-11">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary">Penyata Akaun</h4>
                            <p class="text-muted mb-0">Lihat dan muat turun penyata akaun anda</p>
                        </div>
                        <a href="/users" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-11">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Statement Form -->
                    <form method="GET" class="statement-form bg-light p-3 rounded-3 mb-4">
                        <div class="row g-3">
                            <!-- Account Selection -->
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0">Akaun Simpanan</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select name="account_id" class="form-select" onchange="this.form.submit()">
                                            <option value="<?= $account['id'] ?>">
                                                <?= htmlspecialchars($account['account_number']) ?> / 
                                                RM<?= number_format($account['current_amount'], 2) ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Statement Period -->
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0">Tarikh Penyata</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select name="period" class="form-select" onchange="updateDates(this.value)">
                                            <option value="today" <?= ($period === 'today') ? 'selected' : '' ?>>Hari Ini</option>
                                            <option value="current" <?= ($period === 'current') ? 'selected' : '' ?>>Bulan Ini</option>
                                            <option value="last" <?= ($period === 'last') ? 'selected' : '' ?>>Bulan Sebelumnya</option>
                                            <option value="custom" <?= ($period === 'custom') ? 'selected' : '' ?>>Tarikh</option>
                                            <option value="yearly" <?= ($period === 'yearly') ? 'selected' : '' ?>>Tahun</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Date Range -->
                            <div id="customDateRange" class="col-md-12" style="display: <?= $period === 'custom' ? 'block' : 'none' ?>;">
                                <div class="row">
                                    <div class="offset-md-3 col-md-9">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small mb-1">Tarikh Mula</label>
                                                <input type="date" name="start_date" class="form-control" 
                                                       value="<?= $startDate ?>" max="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small mb-1">Tarikh Akhir</label>
                                                <input type="date" name="end_date" class="form-control" 
                                                       value="<?= $endDate ?>" max="<?= date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Year Selection -->
                            <div id="yearSelection" class="col-md-12" style="display: <?= $period === 'yearly' ? 'block' : 'none' ?>;">
                                <div class="row">
                                    <div class="offset-md-3 col-md-9">
                                        <select name="year" class="form-select">
                                            <?php 
                                            $currentYear = date('Y');
                                            for ($i = $currentYear; $i >= ($currentYear - 4); $i--): ?>
                                                <option value="<?= $i ?>" <?= ($year == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="offset-md-3 col-md-9">
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="" method="GET">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-search me-1"></i>Cari
                                                </button>
                                            </form>
                                            
                                            <form action="/users/statements/download" method="GET">
                                                <input type="hidden" name="account_type" value="<?= htmlspecialchars($accountType) ?>">
                                                <input type="hidden" name="period" value="<?= htmlspecialchars($period) ?>">
                                                <input type="hidden" name="year" value="<?= htmlspecialchars($year) ?>">
                                                <?php if ($period === 'custom'): ?>
                                                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                                                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                                                <?php endif; ?>
                                                <?php if ($accountType === 'loans' && isset($account['id'])): ?>
                                                    <input type="hidden" name="loan_id" value="<?= htmlspecialchars($account['id']) ?>">
                                                <?php endif; ?>
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="bi bi-file-pdf me-1"></i>Muat Turun PDF
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Loan Accounts Table -->
                    <?php if (!empty($loans)): ?>
                        <div class="mt-4">
                            <h5 class="mb-3">Senarai Akaun Pembiayaan</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Rujukan</th>
                                            <th>Jenis Pembiayaan</th>
                                            <th>Jumlah Pembiayaan</th>
                                            <th>Baki Pembiayaan</th>
                                            <th>Tempoh</th>
                                            <th>Status</th>
                                            <th>Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($loans as $loan): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                                <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                                <td>RM<?= number_format($loan['amount'] ?? 0, 2) ?></td>
                                                <td>RM<?= number_format($loan['remaining_amount'] ?? $loan['amount'] ?? 0, 2) ?></td>
                                                <td><?= htmlspecialchars($loan['duration']) ?> bulan</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <form action="/users/statements/download" method="GET" class="d-inline">
                                                        <input type="hidden" name="account_type" value="loans">
                                                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                                        <input type="hidden" name="period" value="<?= htmlspecialchars($period) ?>">
                                                        <input type="hidden" name="year" value="<?= htmlspecialchars($year) ?>">
                                                        <?php if ($period === 'custom'): ?>
                                                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                                                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                                                        <?php endif; ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Transactions Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0">Tarikh</th>
                                    <th class="border-0">Penerangan</th>
                                    <?php if ($accountType === 'savings'): ?>
                                        <th class="border-0 text-end">Debit (RM)</th>
                                        <th class="border-0 text-end">Kredit (RM)</th>
                                    <?php else: ?>
                                        <th class="border-0 text-end">Bayaran (RM)</th>
                                        <th class="border-0 text-end">Baki Pinjaman (RM)</th>
                                    <?php endif; ?>
                                    <th class="border-0 text-end">Baki (RM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr class="table-light fw-bold">
                                    <td>-</td>
                                    <td>Baki Awal</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end"><?= number_format($openingBalance, 2) ?></td>
                                </tr>

                                <!-- Transaction Rows -->
                                <?php 
                                    $runningBalance = $openingBalance;
                                    foreach ($transactions as $trans): 
                                        if ($accountType === 'savings') {
                                            $isDebit = in_array($trans['type'], ['transfer_out', 'withdrawal']);
                                            $isCredit = in_array($trans['type'], ['deposit', 'transfer_in']);
                                            $runningBalance += ($isCredit ? $trans['amount'] : -$trans['amount']);
                                        } else {
                                            $runningBalance = $trans['remaining_balance'];
                                        }
                                ?>
                                    <tr>
                                        <td class="small"><?= date('d/m/Y', strtotime($trans['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($trans['description']) ?></td>
                                        <?php if ($accountType === 'savings'): ?>
                                            <td class="text-end <?= $isDebit ? 'text-danger' : '' ?>">
                                                <?= $isDebit ? number_format($trans['amount'], 2) : '-' ?>
                                            </td>
                                            <td class="text-end <?= $isCredit ? 'text-success' : '' ?>">
                                                <?= $isCredit ? number_format($trans['amount'], 2) : '-' ?>
                                            </td>
                                        <?php else: ?>
                                            <td class="text-end text-danger"><?= number_format($trans['payment_amount'], 2) ?></td>
                                            <td class="text-end"><?= number_format($trans['remaining_balance'], 2) ?></td>
                                        <?php endif; ?>
                                        <td class="text-end fw-bold"><?= number_format($runningBalance, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- Closing Balance Row -->
                                <tr class="table-light fw-bold">
                                    <td>-</td>
                                    <td>Baki Akhir</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end"><?= number_format($runningBalance, 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Styles */
.card {
    border-radius: 0.75rem;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-select, .form-control {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus, .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
}

.btn {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn:hover {
    transform: translateY(-1px);
}

.table {
    font-size: 0.95rem;
    margin-bottom: 2rem;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem;
    background-color: #f8f9fa;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.table-light {
    background-color: rgba(248,249,250,0.5) !important;
}

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.statement-form {
    background-color: #f8f9fa;
    border-radius: 0.75rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
}

/* Compact Styles */
.form-select, .form-control {
    padding: 0.4rem 0.75rem;
    font-size: 0.9rem;
}

.btn {
    padding: 0.4rem 1rem;
    font-size: 0.9rem;
}

.form-label {
    font-size: 0.9rem;
    color: #495057;
}

.statement-form {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
}

/* Reduce spacing */
.row {
    --bs-gutter-y: 0.5rem;
}
</style>

<script>
function updateDates(period) {
    const customDateRange = document.getElementById('customDateRange');
    if (period === 'custom') {
        customDateRange.style.display = 'block';
        yearSelection.style.display = 'none';

    } else if (period === 'yearly') {
        customDateRange.style.display = 'none';
        yearSelection.style.display = 'block';
    } else {
        customDateRange.style.display = 'none';
        yearSelection.style.display = 'none';

        
        const today = new Date();
        let startDate, endDate;
        
        switch(period) {
            case 'today':
                startDate = today;
                endDate = today;
                break;
            case 'current':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = today;
                break;
            case 'last':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
        }
        
        document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
        document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
    }
}

// Set default period to 'today' when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateDates('today');
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>