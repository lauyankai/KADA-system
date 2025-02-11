<?php 
    $title = 'Akaun Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <!-- Add Back Button -->
    <div class="mb-3">
        <a href="/users" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Account Summary Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="card-title text-success mb-3">
                        <i class="bi bi-piggy-bank me-2"></i>Akaun Simpanan Saya
                    </h4>
                    <p class="text-muted small mb-2">No. Akaun: <?= htmlspecialchars($account['account_number']) ?></p>
                    <h2 class="text-success mb-3">RM <?= number_format($account['current_amount'], 2) ?></h2>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="/users/savings/deposit" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Deposit
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
                        <a href="/users/savings/goals/create" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-plus-lg me-2"></i>Tambah
                        </a>
                    </div>

                    <?php if (empty($goals)): ?>
                        <p class="text-muted mb-0">Tiada sasaran simpanan yang aktif</p>
                    <?php else: ?>
                        <?php foreach ($goals as $goal): ?>
                            <div class="goal-item mb-3" id="goal-<?= $goal['id'] ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="goal-name"><?= htmlspecialchars($goal['name']) ?></span>
                                        </h6>
                                        <small class="text-muted">
                                            Sasaran: RM <?= number_format($goal['target_amount'], 2) ?>
                                            <br>
                                            Tarikh: <?= date('d/m/Y', strtotime($goal['target_date'])) ?>
                                        </small>
                                    </div>
                                    <div class="actions">
                                        <a href="/users/savings/goals/edit/<?= $goal['id'] ?>" 
                                           class="btn btn-sm btn-link text-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="confirmDeleteGoal(<?= $goal['id'] ?>)" 
                                                class="btn btn-sm btn-link text-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= min(100, $goal['progress']) ?>%"
                                         aria-valuenow="<?= $goal['progress'] ?>" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">
                                        RM <?= number_format($goal['current_amount'] ?? 0, 2) ?>
                                    </small>
                                    <small class="text-muted">
                                        <?= number_format($goal['progress'], 1) ?>%
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Recurring Payment -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-arrow-repeat me-2"></i>Pembayaran Berulang
                            </h5>
                            <p class="text-muted small mb-0">Pembayaran automatik untuk pembiayaan</p>
                        </div>
                        <a href="/users/savings/recurring" class="btn btn-primary btn-sm">
                            <i class="bi bi-gear-fill me-2"></i>Urus
                        </a>
                    </div>

                    <?php if ($totalMonthlyPayments > 0): ?>
                        <div class="alert alert-info mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Jumlah Bayaran Bulanan</h6>
                                    <small class="text-muted">Potongan automatik dari akaun simpanan</small>
                                </div>
                                <h5 class="mb-0">RM <?= number_format($totalMonthlyPayments, 2) ?></h5>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Tiada pembayaran berulang aktif
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-clock-history me-2"></i>Transaksi Terkini
            </h5>
            <?php if (empty($transactions)): ?>
                <p class="text-muted">Tiada transaksi</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarikh</th>
                                <th>Penerangan</th>
                                <th>Jenis</th>
                                <th>Kaedah</th>
                                <th class="text-end">Jumlah (RM)</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?= htmlspecialchars($transaction['formatted_date'] ?? date('d/m/Y H:i', strtotime($transaction['created_at']))) ?></td>
                                    <td><?= htmlspecialchars($transaction['description']) ?></td>
                                    <td>
                                        <?php if ($transaction['type'] === 'deposit'): ?>
                                            <span class="badge bg-success">Deposit</span>
                                        <?php elseif ($transaction['type'] === 'withdrawal'): ?>
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        <?php elseif ($transaction['type'] === 'transfer_in'): ?>
                                            <span class="badge bg-info">Pindah Masuk</span>
                                        <?php elseif ($transaction['type'] === 'transfer_out'): ?>
                                            <span class="badge bg-warning">Pindah Keluar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(ucfirst($transaction['payment_method'] ?? '-')) ?></td>
                                    <td class="text-end fw-bold <?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                        <?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? '+' : '-' ?>
                                        <?= number_format($transaction['amount'], 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($transaction['reference_no'])): ?>
                                            <a href="/users/savings/receipt/<?= $transaction['reference_no'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt me-1"></i>Resit
                                            </a>
                                        <?php endif; ?>
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

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/users/savings/deposit" method="POST" id="depositForm">
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
<div class="modal fade" id="newGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sasaran Simpanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/users/savings/goals/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sasaran</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Sasaran (RM)</label>
                        <input type="number" name="target_amount" class="form-control" 
                               min="10" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarikh Sasaran</label>
                        <input type="date" name="target_date" class="form-control" required
                               min="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
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
<div class="modal fade" id="newRecurringModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tetapan Bayaran Berulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/users/savings/recurring/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bulanan (RM)</label>
                        <input type="number" name="amount" class="form-control" 
                               min="10" step="0.01" required>
                        <div class="form-text">Minimum: RM10.00 sebulan</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hari Potongan</label>
                        <select name="deduction_day" class="form-select" required>
                            <?php for($i = 1; $i <= 28; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> hb</option>
                            <?php endfor; ?>
                        </select>
                        <div class="form-text">Hari dalam bulan untuk membuat potongan</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kaedah Pembayaran</label>
                        <div class="payment-methods">
                            <div class="form-check mb-2">
                                <input type="radio" class="form-check-input" name="payment_method" 
                                       id="fpx" value="fpx" required>
                                <label class="form-check-label" for="fpx">
                                    <i class="bi bi-bank me-2"></i>FPX
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="payment_method" 
                                       id="card" value="card" required>
                                <label class="form-check-label" for="card">
                                    <i class="bi bi-credit-card me-2"></i>Kad Kredit/Debit
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDeleteGoal(goalId) {
    if (confirm('Adakah anda pasti untuk memadam sasaran ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/users/savings/goals/delete/${goalId}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 