<?php 
    $title = 'Bayaran Keahlian';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">
                        <i class="bi bi-credit-card me-2"></i>Bayaran Keahlian
                    </h4>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Sila selesaikan bayaran keahlian untuk mengakses sistem sepenuhnya.
                    </div>

                    <!-- Fee Details -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Perkara</th>
                                    <th class="text-end">Jumlah (RM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Yuran Pendaftaran</td>
                                    <td class="text-end"><?= number_format($fees['registration_fee'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Modal Syer</td>
                                    <td class="text-end"><?= number_format($fees['share_capital'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Yuran Modal</td>
                                    <td class="text-end"><?= number_format($fees['fee_capital'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Wang Simpanan</td>
                                    <td class="text-end"><?= number_format($fees['deposit_funds'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Tabung Kebajikan</td>
                                    <td class="text-end"><?= number_format($fees['welfare_fund'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Simpanan Tetap</td>
                                    <td class="text-end"><?= number_format($fees['fixed_deposit'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Sumbangan Lain</td>
                                    <td class="text-end"><?= number_format($fees['other_contributions'], 2) ?></td>
                                </tr>
                                <tr class="table-active fw-bold">
                                    <td>Jumlah Keseluruhan</td>
                                    <td class="text-end">
                                        <?= number_format($fees['total_amount'], 2) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Form -->
                    <form action="/users/fees/process" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label">Kaedah Pembayaran</label>
                            <div class="row g-3">
                                <!-- FPX -->
                                <div class="col-md-4">
                                    <div class="form-check custom-radio">
                                        <input type="radio" name="payment_method" value="fpx" 
                                               class="form-check-input" required>
                                        <label class="form-check-label">
                                            <i class="bi bi-bank me-2"></i>FPX Online Banking
                                        </label>
                                    </div>
                                </div>

                                <!-- Card -->
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="payment_method" value="card" 
                                               class="form-check-input" required>
                                        <label class="form-check-label">
                                            <i class="bi bi-credit-card me-2"></i>Kad Kredit/Debit
                                        </label>
                                    </div>
                                </div>

                                <!-- E-Wallet -->
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="payment_method" value="ewallet" 
                                               class="form-check-input" required>
                                        <label class="form-check-label">
                                            <i class="bi bi-wallet2 me-2"></i>E-Wallet
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-credit-card me-2"></i>
                                Bayar RM<?= number_format($fees['total_amount'], 2) ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 