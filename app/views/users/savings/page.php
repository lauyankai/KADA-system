<?php 
    $title = 'Akaun Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Akaun Simpanan</h4>
                <span class="badge bg-success">Aktif</span>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="text-muted mb-1">No. Akaun</p>
                    <h5><?= htmlspecialchars($savingsAccount['account_number'] ?? '-') ?></h5>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Baki Semasa</p>
                    <h5 class="text-success">RM <?= number_format($savingsAccount['current_amount'] ?? 0, 2) ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="/users/savings/deposit/index" class="btn btn-success w-100 mb-2">
                <i class="bi bi-plus-circle me-2"></i>Deposit
            </a>
        </div>
        <div class="col-md-6">
            <a href="/users/savings/transfer/index" class="btn btn-primary w-100 mb-2">
                <i class="bi bi-arrow-left-right me-2"></i>Pindahan
            </a>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Transaksi Terkini</h5>
            <?php if (!empty($recentTransactions)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarikh</th>
                                <th>Jenis</th>
                                <th>Amaun (RM)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        switch($transaction['transaction_type']) {
                                            case 'deposit':
                                                echo 'Deposit';
                                                break;
                                            case 'withdrawal':
                                                echo 'Pengeluaran';
                                                break;
                                            case 'transfer':
                                                echo 'Pindahan';
                                                break;
                                            default:
                                                echo ucfirst($transaction['transaction_type']);
                                        }
                                        ?>
                                    </td>
                                    <td class="<?= $transaction['transaction_type'] === 'deposit' ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($transaction['amount'], 2) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Selesai</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Tiada transaksi terkini</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 