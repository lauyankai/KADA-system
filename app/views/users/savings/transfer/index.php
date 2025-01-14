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
                        <a href="/users/savings/page" class="btn btn-outline-secondary">
                        <a href="/users/dashboard" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/users/savings/transfer" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Akaun Penerima</label>
                            <select name="to_account_id" class="form-select" required>
                                <option value="">Pilih akaun penerima</option>
                                <?php foreach ($accounts as $account): ?>
                                    <?php if ($account['id'] != $_SESSION['member_id']): ?>
                                        <option value="<?= $account['id'] ?>">
                                            <?= htmlspecialchars($account['account_number'] ?? 'Akaun Simpanan') ?> - 
                                            <?= htmlspecialchars($account['member_name'] ?? 'Ahli') ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
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

                        <div class="d-flex justify-content-between">
                            <a href="/users/savings/page" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Pindah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 