<?php 
    $title = 'Penyata';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Penyata Akaun</h4>
                            <p class="text-muted mb-0">Lihat dan muat turun penyata akaun anda</p>
                        </div>
                        <a href="/users" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="/users/statements" method="GET" class="statement-form">
                        <div class="row g-4">
                            <!-- Account Type -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label text-muted">Jenis Akaun</label>
                                    <select name="account_id" class="form-select form-select-lg">
                                        <option value="savings" <?= ($accountType === 'savings') ? 'selected' : '' ?>>
                                            <?php if ($accountType === 'savings'): ?>    
                                                <?= htmlspecialchars($account['account_number']) ?> / Akaun Simpanan
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

                            <!-- Period Selection -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label text-muted">Tempoh</label>
                                    <select name="period" class="form-select form-select-lg" onchange="updateDateInputs(this.value)">
                                        <option value="today" <?= ($period === 'today') ? 'selected' : '' ?>>Hari Ini</option>
                                        <option value="current" <?= ($period === 'current') ? 'selected' : '' ?>>Bulan Semasa</option>
                                        <option value="last" <?= ($period === 'last') ? 'selected' : '' ?>>Bulan Lepas</option>
                                        <option value="yearly" <?= ($period === 'yearly') ? 'selected' : '' ?>>Tahunan</option>
                                        <option value="custom" <?= ($period === 'custom') ? 'selected' : '' ?>>Pilih Tarikh</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Year Selection (for yearly period) -->
                            <div id="yearSelection" class="col-md-4" style="display: <?= $period === 'yearly' ? 'block' : 'none' ?>;">
                                <div class="form-group">
                                    <label class="form-label text-muted">Tahun</label>
                                    <select name="year" class="form-select form-select-lg">
                                        <?php 
                                        $currentYear = date('Y');
                                        for ($i = $currentYear; $i >= 2020; $i--): ?>
                                            <option value="<?= $i ?>" <?= ($year == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Custom Date Range -->
                            <div id="customDateRange" class="row g-3" style="display: <?= $period === 'custom' ? 'block' : 'none' ?>;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label text-muted">Dari Tarikh</label>
                                        <input type="date" name="start_date" class="form-control form-control-lg" 
                                               value="<?= $startDate ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label text-muted">Hingga Tarikh</label>
                                        <input type="date" name="end_date" class="form-control form-control-lg" 
                                               value="<?= $endDate ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-search me-2"></i>Cari
                                    </button>
                                    <button type="submit" name="format" value="pdf" class="btn btn-outline-primary btn-lg">
                                        <i class="bi bi-file-pdf me-2"></i>Muat Turun PDF
                                    </button>
                                </div>
                            </div>

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
                        // Calculate opening balance
                        $runningBalance = $account['current_amount'] ?? 0;
                        foreach ($transactions as $t) {
                            if ($accountType === 'savings') {
                                $isCredit = in_array($t['type'], ['deposit', 'transfer_in']);
                                $runningBalance -= ($isCredit ? $t['amount'] : -$t['amount']);
                            }
                        }
                        $openingBalance = $runningBalance;
                        
                        // Add opening balance row
                        ?>
                        <tr class="table-light">
                            <td class="small">-</td>
                            <td><strong>Baki Awal</strong></td>
                            <?php if ($accountType === 'savings'): ?>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                            <?php else: ?>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                            <?php endif; ?>
                            <td class="text-end fw-bold"><?= number_format($openingBalance, 2) ?></td>
                        </tr>

                        <?php
                        // Sort transactions by date in ascending order (oldest first)
                        usort($transactions, function($a, $b) {
                            return strtotime($a['created_at']) - strtotime($b['created_at']);
                        });
                        
                        foreach ($transactions as $trans): 
                            // Determine transaction type and amount
                            $isDebit = in_array($trans['type'], ['transfer_out', 'withdrawal']);
                            $isCredit = in_array($trans['type'], ['deposit', 'transfer_in']);
                            
                            // Calculate running balance going forward
                            if ($accountType === 'savings') {
                                $runningBalance += ($isCredit ? $trans['amount'] : -$trans['amount']);
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
                        <?php endforeach; 
                        
                        // Add closing balance row
                        ?>
                        <tr class="table-light">
                            <td class="small">-</td>
                            <td><strong>Baki Akhir</strong></td>
                            <?php if ($accountType === 'savings'): ?>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                            <?php else: ?>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                            <?php endif; ?>
                            <td class="text-end fw-bold"><?= number_format($runningBalance, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.form-select-lg, .form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border-radius: 0.5rem;
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 0.5rem;
}

.statement-form {
    max-width: 100%;
}

/* Custom Select Styling */
.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

/* Custom Date Input Styling */
input[type="date"] {
    position: relative;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}
</style>

<script>
function updateDateInputs(period) {
    const yearSelection = document.getElementById('yearSelection');
    const customDateRange = document.getElementById('customDateRange');
    const today = new Date();
    
    yearSelection.style.display = period === 'yearly' ? 'block' : 'none';
    customDateRange.style.display = period === 'custom' ? 'block' : 'none';

    if (period === 'custom') {
        return;
    }

    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'current':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = today;
            break;
        case 'last':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'yearly':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
    }

    if (startDate && endDate) {
        document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
        document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
    }
}

// Initialize date inputs on page load
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.querySelector('select[name="period"]');
    updateDateInputs(periodSelect.value);
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>