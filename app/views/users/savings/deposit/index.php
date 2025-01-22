<?php 
    $title = 'Deposit Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" id="successAlert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>Deposit Simpanan
                        </h4>
                        <a href="/users/savings" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/users/savings/deposit" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label">Jumlah (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" name="amount" class="form-control" 
                                       min="1" max="1000" step="0.01" required>
                            </div>
                            <div class="form-text">Maksimum: RM1,000.00 setiap transaksi</div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label">Kaedah Pembayaran</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" 
                                           id="fpx" value="fpx" required>
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" 
                                           for="fpx">
                                        <i class="bi bi-bank fs-3 mb-2"></i>
                                        <span>FPX</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" 
                                           id="card" value="card" required>
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" 
                                           for="card">
                                        <i class="bi bi-credit-card fs-3 mb-2"></i>
                                        <span>Kad Kredit/Debit</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" 
                                           id="ewallet" value="ewallet" required>
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" 
                                           for="ewallet">
                                        <i class="bi bi-wallet2 fs-3 mb-2"></i>
                                        <span>E-Wallet</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Add bank selection for FPX -->
                        <div id="fpxOptions" class="mb-4" style="display: none;">
                            <label class="form-label">Pilih Bank</label>
                            <select name="bank" class="form-select" id="bankSelect">
                                <option value="">Pilih bank anda</option>
                                <option value="maybank">Maybank2u</option>
                                <option value="cimb">CIMB Clicks</option>
                                <option value="rhb">RHB Now</option>
                                <option value="bsn">BSN</option>
                                <option value="bank_islam">Bank Islam</option>
                                <option value="public_bank">Public Bank</option>
                                <option value="hong_leong">Hong Leong Bank</option>
                                <option value="ambank">AmBank</option>
                            </select>
                        </div>

                        <!-- Add card details section -->
                        <div id="cardOptions" class="mb-4" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Nombor Kad</label>
                                <input type="text" class="form-control" name="card_number" 
                                       placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tarikh Luput</label>
                                    <input type="text" class="form-control" name="card_expiry" 
                                           placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" name="card_cvv" 
                                           placeholder="123" maxlength="3">
                                </div>
                            </div>
                        </div>

                        <!-- Add e-wallet options -->
                        <div id="ewalletOptions" class="mb-4" style="display: none;">
                            <label class="form-label">Pilih E-Wallet</label>
                            <select name="ewallet_type" class="form-select">
                                <option value="">Pilih e-wallet anda</option>
                                <option value="touch_n_go">Touch 'n Go eWallet</option>
                                <option value="boost">Boost</option>
                                <option value="grabpay">GrabPay</option>
                                <option value="shopeepay">ShopeePay</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Teruskan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide payment options based on selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const fpxOptions = document.getElementById('fpxOptions');
    const cardOptions = document.getElementById('cardOptions');
    const ewalletOptions = document.getElementById('ewalletOptions');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all options first
            fpxOptions.style.display = 'none';
            cardOptions.style.display = 'none';
            ewalletOptions.style.display = 'none';

            // Show selected option
            if (this.value === 'fpx') {
                fpxOptions.style.display = 'block';
            } else if (this.value === 'card') {
                cardOptions.style.display = 'block';
            } else if (this.value === 'ewallet') {
                ewalletOptions.style.display = 'block';
            }
        });
    });

    // Format card number input
    const cardNumber = document.querySelector('input[name="card_number"]');
    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
        });
    }

    // Format card expiry input
    const cardExpiry = document.querySelector('input[name="card_expiry"]');
    if (cardExpiry) {
        cardExpiry.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 