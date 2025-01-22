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
                    <form class="p-3 rounded-3 mb-4">
                        <div class="row g-3">
                            <!-- Account Selection -->
                            <div class="col-md-12">
                                <h5 class="mb-3">Senarai Akaun Pembiayaan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Jenis Akaun</th>
                                                    <th>Nombor Akaun</th>
                                                    <th>Jumlah Baki</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <option value="<?= $account['id'] ?>">
                                                    <tr>
                                                        <td><?= $loop_index = isset($loop_index) ? $loop_index + 1 : 1; ?></td>
                                                        <td>Savings</td>
                                                        <td><?= htmlspecialchars($account['account_number']) ?></td>
                                                        <td>RM<?= number_format($account['current_amount'] ?? 0, 2) ?></td>
                                                        <td><span class="badge bg-success">Aktif</span></td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                        <!-- Loan Accounts Selection -->
                    <?php if (!empty($loans)): ?>
                        <div class="mt-4">
                            <h5 class="mb-3">Senarai Akaun Pembiayaan</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Jenis Pembiayaan</th>
                                            <th>No. Rujukan</th>
                                            <th>Jumlah Pembiayaan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($loans as $loan): ?>
                                            <tr>
                                                <td><?= $loop_index = isset($loop_index) ? $loop_index + 1 : 1; ?></td>
                                                <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                                <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                                <td>RM<?= number_format($loan['amount'] ?? 0, 2) ?></td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    </form>
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