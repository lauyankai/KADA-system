<?php 
    $title = 'Penyata Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary">
                    <i class="bi bi-file-text me-2"></i>Penyata Akaun
                </h5>
                <form action="/users/statements/download" method="GET" class="d-inline">
                    <input type="hidden" name="account_type" value="<?= $accountType ?>">
                    <input type="hidden" name="start_date" value="<?= $startDate ?>">
                    <input type="hidden" name="end_date" value="<?= $endDate ?>">
                    <?php if ($accountType === 'loans' && isset($account['id'])): ?>
                        <input type="hidden" name="loan_id" value="<?= $account['id'] ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-download me-2"></i>Muat Turun PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statement Filters -->
            <form method="GET" class="statement-form">
                <div class="row g-3">
                    <!-- Account Selection -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-2">
                            <label class="form-label text-secondary">Nombor Akaun</label>
                        </div>
                        <div class="col-md-6">
                            <select name="account_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php if ($accountType === 'savings'): ?>
                                    <option value="<?= $account['id'] ?>">
                                        <?= htmlspecialchars($account['account_number']) ?> / Akaun Simpanan
                                    </option>
                                <?php else: ?>
                                    <?php foreach ($accounts as $loan): ?>
                                        <option value="<?= $loan['id'] ?>" 
                                                <?= ($loan['id'] == $account['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loan['reference_no']) ?> / Akaun Pembiayaan
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Statement Period -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-2">
                            <label class="form-label text-secondary">Tarikh Penyata</label>
                        </div>
                        <div class="col-md-6">
                            <select name="period" class="form-select form-select-sm" onchange="updateDates(this.value)">
                                <option value="today" <?= ($period === 'today') ? 'selected' : '' ?>>Hari Ini</option>
                                <option value="current" <?= ($period === 'current') ? 'selected' : '' ?>>Bulan Ini</option>
                                <option value="last" <?= ($period === 'last') ? 'selected' : '' ?>>Bulan Sebelumnya</option>
                                <option value="custom" <?= ($period === 'custom') ? 'selected' : '' ?>>Tarikh</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom Date Range -->
                    <div id="customDateRange" class="row mb-3" style="display: <?= $period === 'custom' ? 'block' : 'none' ?>;">
                        <div class="offset-md-2 col-md-6">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">Tarikh Mula</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" 
                                           value="<?= $startDate ?>" max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">Tarikh Akhir</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" 
                                           value="<?= $endDate ?>" max="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="offset-md-2 col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search me-1"></i>Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Transactions Table -->
            <div class="table-responsive mt-4">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr class="table-light">
                            <th>Tarikh</th>
                            <th>Penerangan</th>
                            <?php if ($accountType === 'savings'): ?>
                                <th class="text-end">Debit (RM)</th>
                                <th class="text-end">Kredit (RM)</th>
                            <?php else: ?>
                                <th class="text-end">Bayaran (RM)</th>
                                <th class="text-end">Baki Pinjaman (RM)</th>
                            <?php endif; ?>
                            <th class="text-end">Baki (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Get the current balance from savings_accounts
                        $balance = $account['current_amount'] ?? 0;
                        
                        // Sort transactions by date in ascending order
                        usort($transactions, function($a, $b) {
                            return strtotime($a['created_at']) - strtotime($b['created_at']);
                        });
                        
                        foreach ($transactions as $trans): 
                            // Determine transaction type and amount
                            $isDebit = in_array($trans['type'], ['transfer_out', 'withdrawal']);
                            $isCredit = in_array($trans['type'], ['deposit', 'transfer_in']);
                            
                            // Calculate running balance
                            if ($accountType === 'savings') {
                                $balance += ($isCredit ? $trans['amount'] : -$trans['amount']);
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
                                <td class="text-end fw-bold"><?= number_format($balance, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.3rem;
}

.form-select, .form-control {
    border: 1px solid #e0e0e0;
    box-shadow: none;
}

.form-select:focus, .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.table {
    font-size: 0.9rem;
}

.table thead th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,.03);
}

.btn-success {
    background-color: #198754;
    border: none;
}

.btn-success:hover {
    background-color: #157347;
}

.statement-form {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.text-primary {
    color: #0d6efd !important;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>

<script>
function updateDates(period) {
    const customDateRange = document.getElementById('customDateRange');
    if (period === 'custom') {
        customDateRange.style.display = 'block';
    } else {
        customDateRange.style.display = 'none';
        
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