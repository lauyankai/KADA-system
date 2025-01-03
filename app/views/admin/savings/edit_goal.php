<?php 
    $title = 'Kemaskini Sasaran Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-trophy me-2"></i>Kemaskini Sasaran Simpanan
                        </h4>
                        <a href="/admin/savings" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/admin/savings/goal/update/<?= $goal['id'] ?>" method="POST" id="goalForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Sasaran</label>
                            <input type="text" name="goal_name" class="form-control" required
                                   value="<?= htmlspecialchars($goal['name']) ?>"
                                   placeholder="cth: Simpanan Rumah">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Sasaran (RM)</label>
                            <input type="number" name="target_amount" class="form-control" 
                                   value="<?= $goal['target_amount'] ?>"
                                   min="100" max="100000" step="100" required>
                            <div class="form-text">Minimum: RM100.00</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tarikh Sasaran</label>
                            <input type="date" name="target_date" class="form-control" required
                                   value="<?= $goal['target_date'] ?>"
                                   min="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                            <div class="form-text">Tarikh matlamat hendak dicapai</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Anggaran Simpanan Bulanan</label>
                            <div class="form-control bg-light" id="monthlyEstimate">
                                RM <?= number_format($goal['monthly_target'], 2) ?>
                            </div>
                            <div class="form-text text-warning">Anggaran berdasarkan tempoh yang dipilih</div>
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

<?php require_once '../app/views/layouts/footer.php'; ?> 