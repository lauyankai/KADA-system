<?php 
    $title = 'Tambah Simpanan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Simpanan
                    </h4>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form id="depositForm" action="/admin/savings/deposit/<?= $account['id'] ?>" method="POST">
                        <!-- Account Details -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>No. Akaun:</strong></p>
                                        <p class="text-success"><?= $account['id'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Baki Semasa:</strong></p>
                                        <p class="text-success">RM <?= number_format($account['current_amount'], 2) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deposit Amount -->
                        <div class="mb-4">
                            <label class="form-label">Jumlah Simpanan (RM)</label>
                            <input type="number" 
                                   name="amount" 
                                   class="form-control form-control-lg" 
                                   min="1" 
                                   max="50" 
                                   step="0.01" 
                                   required>
                            <div class="form-text">Maksimum: RM50.00 setiap transaksi</div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label class="form-label">Kaedah Bayaran</label>
                            <select name="payment_method" class="form-select form-select-lg" required>
                                <option value="">Pilih kaedah bayaran</option>
                                <option value="cash">Tunai</option>
                                <option value="bank_transfer">Pindahan Bank</option>
                                <option value="salary_deduction">Potongan Gaji</option>
                            </select>
                        </div>

                        <!-- Reference Number (for bank transfer) -->
                        <div id="refNoField" class="mb-4" style="display: none;">
                            <label class="form-label">No. Rujukan</label>
                            <input type="text" 
                                   name="reference_no" 
                                   class="form-control"
                                   placeholder="cth: ABC123456">
                            <div class="form-text">Masukkan nombor rujukan untuk pindahan bank</div>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end mt-4">
                            <a href="/admin/savings" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="button" class="btn btn-success" onclick="showConfirmation()">
                                <i class="bi bi-check-circle me-2"></i>Simpan
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
                <h5 class="modal-title">Pengesahan Simpanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Jumlah:</strong> <span id="confirmAmount">RM 0.00</span><br>
                    <strong>Kaedah:</strong> <span id="confirmMethod">-</span><br>
                    <strong>No. Rujukan:</strong> <span id="confirmRef">-</span>
                </div>
                <p class="text-muted mb-0">Sila pastikan maklumat di atas adalah tepat sebelum meneruskan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">Sahkan</button>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide reference number field based on payment method
document.querySelector('[name="payment_method"]').addEventListener('change', function() {
    const refNoField = document.getElementById('refNoField');
    refNoField.style.display = this.value === 'bank_transfer' ? 'block' : 'none';
    if (this.value !== 'bank_transfer') {
        document.querySelector('[name="reference_no"]').value = '';
    }
});

function showConfirmation() {
    const form = document.getElementById('depositForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const amount = parseFloat(form.querySelector('[name="amount"]').value);
    const method = form.querySelector('[name="payment_method"]');
    const refNo = form.querySelector('[name="reference_no"]').value;

    const methodText = method.options[method.selectedIndex].text;

    document.getElementById('confirmAmount').textContent = `RM ${amount.toFixed(2)}`;
    document.getElementById('confirmMethod').textContent = methodText;
    document.getElementById('confirmRef').textContent = refNo || '-';

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function submitForm() {
    document.getElementById('depositForm').submit();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 