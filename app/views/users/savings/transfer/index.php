<?php 
    $title = 'Pindah Wang';
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
                            <i class="bi bi-arrow-left-right me-2"></i>Pindah Wang
                        </h4>
                        <a href="/users/dashboard" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>

                    <form action="/admin/savings/transfer" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label">Dari Akaun</label>
                            <select name="from_account" class="form-select" required>
                                <option value="">Pilih akaun</option>
                                <?php foreach ($accounts as $account): ?>
                                    <option value="<?= $account['id'] ?>">
                                        Akaun Simpanan (RM <?= number_format($account['current_amount'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Ke Akaun</label>
                            <div class="input-group">
                                <input type="text" name="to_account" class="form-control" 
                                       placeholder="Masukkan nombor akaun penerima"
                                       pattern="[0-9]+-SAV" required>
                                <button class="btn btn-outline-secondary" type="button" id="verifyAccount">
                                    <i class="bi bi-search"></i> Semak
                                </button>
                            </div>
                            <div class="form-text">Format: ID-SAV (cth: 1234-SAV)</div>
                            <div id="accountDetails" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <small class="d-block"><strong>Nama:</strong> <span id="recipientName">-</span></small>
                                    <small class="d-block"><strong>Status:</strong> <span id="accountStatus">-</span></small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Jumlah (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" name="amount" class="form-control" 
                                       min="1" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan (Pilihan)</label>
                            <input type="text" name="reference" class="form-control" 
                                   placeholder="cth: Bayaran sewa">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitTransfer" disabled>
                                <i class="bi bi-check-circle me-2"></i>Hantar
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
    const verifyBtn = document.getElementById('verifyAccount');
    const accountInput = document.querySelector('input[name="to_account"]');
    const accountDetails = document.getElementById('accountDetails');
    const recipientName = document.getElementById('recipientName');
    const accountStatus = document.getElementById('accountStatus');
    const submitBtn = document.getElementById('submitTransfer');

    verifyBtn.addEventListener('click', async function() {
        const accountNumber = accountInput.value.trim();
        if (!accountNumber) return;

        try {
            const response = await fetch(`/admin/savings/verify-account/${accountNumber}`);
            const data = await response.json();

            if (data.success) {
                recipientName.textContent = data.name;
                accountStatus.textContent = data.status;
                accountDetails.style.display = 'block';
                submitBtn.disabled = false;
                accountInput.classList.add('is-valid');
                accountInput.classList.remove('is-invalid');
            } else {
                accountDetails.style.display = 'none';
                submitBtn.disabled = true;
                accountInput.classList.add('is-invalid');
                accountInput.classList.remove('is-valid');
                alert('Akaun tidak dijumpai atau tidak aktif');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat semasa menyemak akaun');
        }
    });

    // Disable submit button when account number changes
    accountInput.addEventListener('input', function() {
        submitBtn.disabled = true;
        accountDetails.style.display = 'none';
        this.classList.remove('is-valid', 'is-invalid');
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 