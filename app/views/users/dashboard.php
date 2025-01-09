<?php 
    $title = 'Akaun Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" id="successAlert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <script>
            // Auto-dismiss success alert after 3 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const successAlert = document.getElementById('successAlert');
                if (successAlert) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(successAlert);
                        bsAlert.close();
                    }, 3000); 
                }
            });
        </script>
    <?php endif; ?>

    <!-- Account Summary Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="card-title text-success mb-3">
                        <i class="bi bi-piggy-bank me-2"></i>Akaun Simpanan Saya
                    </h4>
                    <h2 class="mb-3">RM <?= number_format($totalSavings ?? 0, 2) ?></h2>
                    <p class="text-muted mb-0">No. Akaun: <?= htmlspecialchars($_SESSION['admin_id']) ?>-SAV</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="/users/accounts/accountList" class="btn btn-outline-primary me-2">
                        <i class="bi bi-wallet2 me-2"></i>Urus Akaun
                    </a>
                    <a href="/users/deposit" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-2"></i>Deposit
                    </a>
                    <a href="/users/transfer" class="btn btn-primary">
                        <i class="bi bi-arrow-left-right me-2"></i>Pindah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings Goals and Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <!-- Savings Goals -->
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-trophy me-2"></i>Sasaran Simpanan
                        </h5>
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#goalModal">
                            <i class="bi bi-plus-lg me-2"></i>Tambah
                        </button>
                    </div>
                    <?php if (!empty($savingsGoals)): ?>
                        <?php foreach ($savingsGoals as $goal): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($goal['name']) ?></h6>
                                            <small class="text-muted">
                                                Sasaran: <?= date('d/m/Y', strtotime($goal['target_date'])) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <button onclick="confirmDeleteGoal(<?= $goal['id'] ?>)" 
                                                    class="btn btn-sm btn-outline-danger me-2">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <a href="/users/goal/edit/<?= $goal['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <span class="badge bg-success">
                                                RM <?= number_format($goal['current_amount'], 2) ?> / 
                                                RM <?= number_format($goal['target_amount'], 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <?php $percentage = ($goal['current_amount'] / $goal['target_amount']) * 100; ?>
                                        <div class="progress-bar bg-success" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Anggaran: RM <?= number_format($goal['monthly_target'], 2) ?> sebulan
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-emoji-smile fs-4"></i>
                            <p class="mb-0">Tetapkan sasaran simpanan anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Recurring Payments -->
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Bayaran Berulang
                        </h5>
                        <?php if (!$recurringPayment): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='/users/recurring'">
                                <i class="bi bi-plus-lg me-2"></i>Tetapkan
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($recurringPayment): ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Potongan Bulanan</h6>
                                <p class="text-muted mb-0">Setiap <?= $recurringPayment['deduction_day'] ?> hb</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="/users/recurring/edit" class="btn btn-sm btn-outline-primary me-3">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <h5 class="mb-0 text-primary">
                                    RM <?= number_format($recurringPayment['amount'], 2) ?>
                                </h5>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <?php
                                switch($recurringPayment['payment_method']) {
                                    case 'salary':
                                        echo '<i class="bi bi-wallet2 me-1"></i>Potongan Gaji';
                                        break;
                                    case 'fpx':
                                        echo '<i class="bi bi-bank me-1"></i>FPX';
                                        break;
                                    case 'card':
                                        echo '<i class="bi bi-credit-card me-1"></i>Kad Kredit/Debit';
                                        break;
                                }
                                ?>
                            </small>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                Potongan seterusnya: <?= date('d/m/Y', strtotime($recurringPayment['next_deduction_date'])) ?>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-check fs-4"></i>
                            <p class="mb-0">Tetapkan potongan automatik</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Transaksi Terkini</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tarikh</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Baki</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentTransactions)): ?>
                            <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                                    <td>
                                        <?php
                                            switch ($transaction['type']) {
                                                case 'deposit':
                                                    echo '<span class="badge bg-success">Deposit</span>';
                                                    break;
                                                case 'transfer_in':
                                                    echo '<span class="badge bg-info">Terima</span>';
                                                    break;
                                                case 'transfer_out':
                                                    echo '<span class="badge bg-warning">Pindah</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Lain-lain</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-<?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? 'success' : 'danger' ?>">
                                        <?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? '+' : '-' ?>
                                        RM <?= number_format($transaction['amount'], 2) ?>
                                    </td>
                                    <td>RM <?= number_format($transaction['current_amount'], 2) ?></td>
                                    <td>
                                        <a href="/users/receipt/<?= $transaction['reference_no'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-receipt me-1"></i>Resit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tiada transaksi dijumpai</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/users/deposit" method="POST" id="depositForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah (RM)</label>
                        <input type="number" name="amount" class="form-control" min="1" max="1000" step="0.01" required>
                        <div class="form-text">Maksimum: RM1,000.00 setiap transaksi</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Kaedah Pembayaran</label>
                        <div class="payment-methods">
                            <!-- Online Banking -->
                            <div class="payment-option mb-3">
                                <input type="radio" class="btn-check" name="payment_method" id="onlineBank" 
                                       value="online_banking" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 text-start" for="onlineBank">
                                    <i class="bi bi-bank me-2"></i>Perbankan Dalam Talian
                                    <small class="d-block text-muted mt-1">Maybank2u, CIMB Clicks, PB engage, dll.</small>
                                </label>
                            </div>

                            <!-- Credit/Debit Card -->
                            <div class="payment-option mb-3">
                                <input type="radio" class="btn-check" name="payment_method" id="card" 
                                       value="card" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 text-start" for="card">
                                    <i class="bi bi-credit-card me-2"></i>Kad Kredit/Debit
                                    <small class="d-block text-muted mt-1">Visa, Mastercard, MyDebit</small>
                                </label>
                            </div>

                            <!-- E-Wallet -->
                            <div class="payment-option mb-3">
                                <input type="radio" class="btn-check" name="payment_method" id="ewallet" 
                                       value="ewallet" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 text-start" for="ewallet">
                                    <i class="bi bi-wallet2 me-2"></i>E-Wallet
                                    <small class="d-block text-muted mt-1">Touch 'n Go, GrabPay, Boost</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Payment Options -->
                    <div id="onlineBankingOptions" class="payment-details" style="display: none;">
                        <h6 class="mb-3">Pilih Bank</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank" id="maybank" value="maybank">
                                <label class="btn btn-outline-secondary w-100" for="maybank">
                                    <img src="/img/banks/maybank.png" alt="Maybank" class="img-fluid mb-2">
                                    <span class="d-block">Maybank2u</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank" id="cimb" value="cimb">
                                <label class="btn btn-outline-secondary w-100" for="cimb">
                                    <img src="/img/banks/cimb.png" alt="CIMB" class="img-fluid mb-2">
                                    <span class="d-block">CIMB Clicks</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank" id="publicbank" value="publicbank">
                                <label class="btn btn-outline-secondary w-100" for="publicbank">
                                    <img src="/img/banks/public.png" alt="Public Bank" class="img-fluid mb-2">
                                    <span class="d-block">PB engage</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank" id="rhb" value="rhb">
                                <label class="btn btn-outline-secondary w-100" for="rhb">
                                    <img src="/img/banks/rhb.png" alt="RHB" class="img-fluid mb-2">
                                    <span class="d-block">RHB Now</span>
                                </label>
                            </div>
                            <!-- Add other banks similarly -->
                        </div>
                    </div>

                    <div id="cardOptions" class="payment-details" style="display: none;">
                        <h6 class="mb-3">Maklumat Kad</h6>
                        <div class="mb-3">
                            <label class="form-label">Nombor Kad</label>
                            <input type="text" class="form-control" id="cardNumber" 
                                   placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tarikh Luput</label>
                                <input type="text" class="form-control" placeholder="MM/YY">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div id="ewalletOptions" class="payment-details" style="display: none;">
                        <h6 class="mb-3">Pilih E-Wallet</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="ewallet" id="tng" value="tng">
                                <label class="btn btn-outline-secondary w-100" for="tng">
                                    <img src="/img/wallets/tng.png" alt="Touch 'n Go" class="img-fluid mb-2">
                                    <span class="d-block">Touch 'n Go</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="ewallet" id="grab" value="grab">
                                <label class="btn btn-outline-secondary w-100" for="grab">
                                    <img src="/img/wallets/grab.png" alt="GrabPay" class="img-fluid mb-2">
                                    <span class="d-block">GrabPay</span>
                                </label>
                            </div>
                            <!-- Add other e-wallets similarly -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Teruskan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentDetails = document.querySelectorAll('.payment-details');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment details first
            paymentDetails.forEach(detail => detail.style.display = 'none');

            // Show the selected payment method's details
            if (this.value === 'online_banking') {
                document.getElementById('onlineBankingOptions').style.display = 'block';
            } else if (this.value === 'card') {
                document.getElementById('cardOptions').style.display = 'block';
            } else if (this.value === 'ewallet') {
                document.getElementById('ewalletOptions').style.display = 'block';
            }
        });
    });

    // Format card number input
    const cardNumber = document.getElementById('cardNumber');
    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
        });
    }
});
</script>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <!-- Similar structure to deposit modal but for transfers -->
</div>

<!-- Savings Goal Modal -->
<div class="modal fade" id="goalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tetapkan Sasaran Simpanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/users/goal/store" method="POST" id="goalForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sasaran</label>
                        <input type="text" name="goal_name" class="form-control" required
                               placeholder="cth: Simpanan Rumah">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Sasaran (RM)</label>
                        <input type="number" name="target_amount" class="form-control" 
                               min="100" max="100000" step="100" required>
                        <div class="form-text">Minimum: RM100.00</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tarikh Sasaran</label>
                        <input type="date" name="target_date" class="form-control" required
                               min="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                        <div class="form-text">Tarikh matlamat hendak dicapai</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anggaran Simpanan Bulanan</label>
                        <div class="form-control bg-light" id="monthlyEstimate">RM 0.00</div>
                        <div class="form-text text-warning">Anggaran berdasarkan tempoh yang dipilih</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Sasaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetAmount = document.querySelector('[name="target_amount"]');
    const targetDate = document.querySelector('[name="target_date"]');
    const monthlyEstimate = document.getElementById('monthlyEstimate');

    function updateMonthlyEstimate() {
        if (targetAmount.value && targetDate.value) {
            const amount = parseFloat(targetAmount.value);
            const months = Math.ceil(
                (new Date(targetDate.value) - new Date()) / (1000 * 60 * 60 * 24 * 30)
            );
            const monthly = amount / months;
            monthlyEstimate.textContent = `RM ${monthly.toFixed(2)}`;
        }
    }

    targetAmount.addEventListener('input', updateMonthlyEstimate);
    targetDate.addEventListener('input', updateMonthlyEstimate);
});
</script>

<!-- Recurring Payment Modal -->
<div class="modal fade" id="recurringModal" tabindex="-1">
    <!-- Modal for setting up recurring payments -->
</div>

<script>
function confirmDeleteGoal(goalId) {
    if (confirm('Adakah anda pasti untuk memadam sasaran ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/users/goal/delete/${goalId}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 