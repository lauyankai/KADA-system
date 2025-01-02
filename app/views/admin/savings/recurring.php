<?php 
    $title = 'Tetapan Bayaran Berulang';
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

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-arrow-repeat me-2"></i>Tetapan Bayaran Berulang
                    </h4>

                    <?php if (isset($success) && $success): ?>
                        <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Pengesahan Bayaran Berulang</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-success">
                                            <i class="bi bi-check-circle me-2"></i>Bayaran berulang berjaya didaftarkan
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

                    <form id="recurringForm" action="/admin/savings/recurring/store" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label">Jumlah Bayaran (RM)</label>
                            <input type="number" 
                                   name="amount" 
                                   class="form-control" 
                                   min="1"
                                   step="0.01"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Kekerapan</label>
                            <select name="frequency" class="form-select" required>
                                <option value="">Pilih kekerapan</option>
                                <option value="weekly">Mingguan</option>
                                <option value="biweekly">Dua Minggu</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Kaedah Bayaran</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Pilih kaedah</option>
                                <option value="bank_transfer">Pindahan Bank</option>
                                <option value="salary_deduction">Potongan Gaji</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tarikh Mula</label>
                            <input type="date" 
                                   name="start_date" 
                                   class="form-control" 
                                   min="<?= date('Y-m-d') ?>"
                                   required>
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
                <h5 class="modal-title">Pengesahan Bayaran Berulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Jumlah Bayaran:</strong> <span id="confirmAmount">RM 0.00</span><br>
                    <strong>Kekerapan:</strong> <span id="confirmFrequency">-</span><br>
                    <strong>Kaedah:</strong> <span id="confirmMethod">-</span><br>
                    <strong>Tarikh Mula (Hari/Bulan/Tahun):</strong> <span id="confirmDate">-</span>
                </div>
                <p class="text-muted mb-0">Sila pastikan maklumat di atas adalah tepat sebelum menghantar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">Sahkan</button>
            </div>
        </div>
    </div>
</div>

<script>
function showConfirmation() {
    const form = document.getElementById('recurringForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const amount = parseFloat(form.querySelector('[name="amount"]').value);
    const frequency = form.querySelector('[name="frequency"]');
    const method = form.querySelector('[name="payment_method"]');
    const date = form.querySelector('[name="start_date"]').value;

    const frequencyText = frequency.options[frequency.selectedIndex].text;
    const methodText = method.options[method.selectedIndex].text;

    document.getElementById('confirmAmount').textContent = `RM ${amount.toFixed(2)}`;
    document.getElementById('confirmFrequency').textContent = frequencyText;
    document.getElementById('confirmMethod').textContent = methodText;
    document.getElementById('confirmDate').textContent = new Date(date).toLocaleDateString('ms-MY');

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function submitForm() {
    document.getElementById('recurringForm').submit();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 