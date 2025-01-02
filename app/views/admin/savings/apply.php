<?php 
    $title = 'Permohonan Akaun Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <!-- Add error/success messages here -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
        <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengesahan Permohonan</h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>Permohonan akaun simpanan berjaya dihantar
                        </div>
                        <p class="text-muted mb-0"><?= $message ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="window.location.href='/admin/dashboard'">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            });
        </script>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-piggy-bank me-2"></i>Permohonan Simpanan
                    </h4>

                    <form id="savingsForm" action="/admin/savings/store" method="POST" class="needs-validation" novalidate>
                        <!-- Hidden field for CSRF protection if needed -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        
                        <div class="mb-4">
                            <label class="form-label">Jumlah Sasaran (RM)</label>
                            <input type="number" 
                                   name="target_amount" 
                                   class="form-control" 
                                   min="100"
                                   max="10000"
                                   step="100"
                                   required>
                            <div class="form-text">Julat: RM100 - RM10,000</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tempoh (Bulan)</label>
                            <select name="duration_months" class="form-select" required>
                                <option value="">Pilih tempoh</option>
                                <?php for($i = 6; $i <= 60; $i += 6): ?>
                                    <option value="<?= $i ?>"><?= $i ?> bulan</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Average Monthly Savings -->
                        <div class="mb-4">
                            <label class="form-label">Anggaran Simpanan Bulanan</label>
                            <div class="form-control bg-light" id="monthlyAverage">RM 0.00</div>
                        </div>

                        <div class="text-end">
                            <a href="/admin/dashboard" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="button" class="btn btn-success" onclick="showConfirmation()">
                                <i class="bi bi-check-circle me-2"></i>Hantar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengesahan Permohonan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Jumlah Sasaran:</strong> <span id="confirmAmount">RM 0.00</span><br>
                    <strong>Tempoh:</strong> <span id="confirmDuration">0 bulan</span><br>
                    <strong>Simpanan Bulanan:</strong> <span id="confirmMonthly">RM 0.00</span>
                </div>
                <p class="text-muted mb-0">Sila pastikan maklumat di atas adalah tepat sebelum menghantar permohonan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">Sahkan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('savingsForm');
    const targetInput = form.querySelector('[name="target_amount"]');
    const durationSelect = form.querySelector('[name="duration_months"]');
    const monthlyAverage = document.getElementById('monthlyAverage');

    function updateMonthlyAverage() {
        const target = parseFloat(targetInput.value) || 0;
        const duration = parseInt(durationSelect.value) || 1;
        const monthly = target / duration;
        monthlyAverage.textContent = `RM ${monthly.toFixed(2)}`;
    }

    targetInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value < 100) {
            this.setCustomValidity('Jumlah minimum adalah RM100');
        } else if (value > 10000) {
            this.setCustomValidity('Jumlah maksimum adalah RM10,000');
        } else {
            this.setCustomValidity('');
        }
        updateMonthlyAverage();
    });
    
    durationSelect.addEventListener('change', updateMonthlyAverage);
});

function showConfirmation() {
    const form = document.getElementById('savingsForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const target = parseFloat(form.querySelector('[name="target_amount"]').value);
    const duration = parseInt(form.querySelector('[name="duration_months"]').value);
    const monthly = target / duration;

    document.getElementById('confirmAmount').textContent = `RM ${target.toFixed(2)}`;
    document.getElementById('confirmDuration').textContent = `${duration} bulan`;
    document.getElementById('confirmMonthly').textContent = `RM ${monthly.toFixed(2)}`;

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function submitForm() {
    const form = document.getElementById('savingsForm');
    if (form.checkValidity()) {
        console.log('Form is valid, submitting...');
        form.submit();
    } else {
        console.log('Form validation failed');
        form.reportValidity();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 