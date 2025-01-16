<?php 
    $title = 'Pemindahan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i>Pemindahan Dana
                        </h4>
                        <a href="/KADA-system/users/savings/page" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-warning">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($currentAccount) && $currentAccount): ?>
                        <form action="/KADA-system/users/savings/transfer" method="POST" class="needs-validation" novalidate>
                            <!-- From Account Display -->
                            <div class="mb-3">
                                <label class="form-label">Dari Akaun</label>
                                <div class="form-control bg-light">
                                    <?= htmlspecialchars($currentAccount['account_number']) ?>
                                    (RM <?= number_format($currentAccount['current_amount'], 2) ?>)
                                </div>
                                <input type="hidden" name="from_account_id" value="<?= $currentAccount['id'] ?>">
                            </div>

                            <?php if (!empty($otherAccounts)): ?>
                            <div class="mb-3">
                                <label class="form-label">Pilih Akaun Lain</label>
                                <select name="from_account_id" class="form-select">
                                    <option value="<?= $currentAccount['id'] ?>" selected>
                                        <?= htmlspecialchars($currentAccount['account_number']) ?> - 
                                        <?= htmlspecialchars($currentAccount['account_name'] ?? '') ?>
                                        (RM <?= number_format($currentAccount['current_amount'], 2) ?>)
                                    </option>
                                    <?php foreach ($otherAccounts as $account): ?>
                                    <option value="<?= $account['id'] ?>">
                                        <?= htmlspecialchars($account['account_number']) ?> - 
                                        <?= htmlspecialchars($account['account_name'] ?? '') ?>
                                        (RM <?= number_format($account['current_amount'], 2) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <!-- Transfer Type Selection -->
                            <div class="mb-3">
                                <label class="form-label">Jenis Pemindahan</label>
                                <select name="transfer_type" id="transferType" class="form-select" required>
                                    <option value="">Pilih jenis pemindahan</option>
                                    <option value="member">Akaun Ahli</option>
                                    <option value="other">Akaun Lain</option>
                                </select>
                            </div>

                            <!-- Member Account Transfer Fields -->
                            <div id="memberFields" class="mb-3" style="display: none;">
                                <label class="form-label">Nombor Akaun Ahli Penerima</label>
                                <input type="text" 
                                       name="recipient_account_number" 
                                       class="form-control" 
                                       placeholder="Contoh: SAV-000001-1234"
                                       pattern="SAV-\d{6}-\d{4}"
                                       title="Format: SAV-XXXXXX-XXXX">
                                <div class="form-text">Masukkan nombor akaun ahli penerima</div>
                            </div>

                            <!-- Other Account Transfer Fields -->
                            <div id="otherFields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Nama Bank</label>
                                    <select name="bank_name" class="form-select">
                                        <option value="">Pilih bank</option>
                                        <option value="maybank">Maybank</option>
                                        <option value="cimb">CIMB Bank</option>
                                        <option value="rhb">RHB Bank</option>
                                        <option value="bankislam">Bank Islam</option>
                                        <option value="bsn">Bank Simpanan Nasional</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombor Akaun</label>
                                    <input type="text" name="bank_account_number" class="form-control" 
                                           placeholder="Contoh: 1234567890">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" name="recipient_name" class="form-control" 
                                           placeholder="Nama penuh penerima">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah (RM)</label>
                                <input type="number" name="amount" class="form-control" 
                                       min="10" step="0.01" required>
                                <div class="form-text">Minimum: RM10.00</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan (Pilihan)</label>
                                <input type="text" name="description" class="form-control" 
                                       placeholder="cth: Pemindahan ke ahli">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Pindah
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Tiada akaun aktif ditemui. Sila hubungi admin untuk bantuan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferType = document.getElementById('transferType');
    const memberFields = document.getElementById('memberFields');
    const otherFields = document.getElementById('otherFields');

    transferType.addEventListener('change', function() {
        memberFields.style.display = 'none';
        otherFields.style.display = 'none';

        if (this.value === 'member') {
            memberFields.style.display = 'block';
        } else if (this.value === 'other') {
            otherFields.style.display = 'block';
        }
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 