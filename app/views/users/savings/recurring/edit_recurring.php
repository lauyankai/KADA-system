<?php 
    $title = 'Kemaskini Bayaran Berulang';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Kemaskini Bayaran Berulang
                        </h4>
                        <a href="/admin/savings" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/admin/savings/recurring/update" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Jumlah Bulanan (RM)</label>
                            <input type="number" name="amount" class="form-control" 
                                   value="<?= $payment['amount'] ?>"
                                   min="10" step="0.01" required>
                            <div class="form-text">Minimum: RM10.00 sebulan</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Hari Potongan</label>
                            <select name="deduction_day" class="form-select" required>
                                <?php for ($i = 1; $i <= 28; $i++): ?>
                                    <option value="<?= $i ?>" <?= $payment['deduction_day'] == $i ? 'selected' : '' ?>>
                                        <?= $i ?> hb
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <div class="form-text">Hari dalam bulan untuk membuat potongan</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Kaedah Pembayaran</label>
                            <div class="payment-methods">
                                <div class="form-check mb-3">
                                    <input type="radio" class="form-check-input" name="payment_method" 
                                           id="salary" value="salary" 
                                           <?= $payment['payment_method'] == 'salary' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="salary">
                                        <i class="bi bi-wallet2 me-2"></i>Potongan Gaji
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="radio" class="form-check-input" name="payment_method" 
                                           id="fpx" value="fpx"
                                           <?= $payment['payment_method'] == 'fpx' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="fpx">
                                        <i class="bi bi-bank me-2"></i>FPX
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="payment_method" 
                                           id="card" value="card"
                                           <?= $payment['payment_method'] == 'card' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="card">
                                        <i class="bi bi-credit-card me-2"></i>Kad Kredit/Debit
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 