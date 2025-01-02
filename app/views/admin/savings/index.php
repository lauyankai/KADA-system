<?php 
    $title = 'Savings Management';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Savings Actions -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-gear me-2"></i>Savings Actions
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="/admin/savings/apply" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>New Savings Account
                        </a>
                        <a href="/admin/savings/recurring" class="btn btn-outline-success">
                            <i class="bi bi-arrow-repeat me-2"></i>Setup Recurring Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Savings Accounts -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-piggy-bank me-2"></i>Active Savings Accounts
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($savingsAccounts)): ?>
                        <p class="text-muted text-center py-4">No active savings accounts</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Account #</th>
                                        <th>Target</th>
                                        <th>Progress</th>
                                        <th>Monthly</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($savingsAccounts as $account): ?>
                                    <tr>
                                        <td><?= $account['id'] ?></td>
                                        <td>RM <?= number_format($account['target_amount'], 2) ?></td>
                                        <td>
                                            <?php $progress = ($account['current_amount'] / $account['target_amount']) * 100; ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width: <?= $progress ?>%">
                                                    <?= number_format($progress, 1) ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>RM <?= number_format($account['monthly_deposit'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $account['status'] === 'active' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($account['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/admin/savings/view/<?= $account['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>View
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
    </div>

    <!-- Recurring Payments -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-arrow-repeat me-2"></i>Recurring Payments
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($recurringPayments)): ?>
                <p class="text-muted text-center py-4">No recurring payments set up</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Account #</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                                <th>Method</th>
                                <th>Next Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recurringPayments as $payment): ?>
                            <tr>
                                <td><?= $payment['savings_account_id'] ?></td>
                                <td>RM <?= number_format($payment['amount'], 2) ?></td>
                                <td><?= ucfirst($payment['frequency']) ?></td>
                                <td><?= str_replace('_', ' ', ucfirst($payment['payment_method'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($payment['next_payment_date'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $payment['status'] === 'active' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($payment['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="togglePaymentStatus(<?= $payment['id'] ?>)">
                                        <?= $payment['status'] === 'active' ? 'Pause' : 'Resume' ?>
                                    </button>
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

<script>
function togglePaymentStatus(paymentId) {
    if (confirm('Are you sure you want to change this payment\'s status?')) {
        fetch(`/admin/savings/toggle-payment/${paymentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update payment status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 