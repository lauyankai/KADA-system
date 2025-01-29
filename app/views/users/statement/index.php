<?php 
    $title = 'Penyata Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary">Penyata Akaun</h4>
                            <p class="text-muted mb-0">Lihat dan muat turun penyata akaun anda</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <i class="bi bi-bell<?= isset($notifications['email_enabled']) && $notifications['email_enabled'] ? '-fill' : '' ?>"></i>
                            </button>
                            <a href="/users" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Statement Form -->
                    <form class="p-3 rounded-3">
                        <div class="row g-3">
                            <!-- Account Selection -->
                            <div class="col-md-12">
                                <h5 class="mb-3">Senarai Akaun Pembiayaan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Jenis Akaun</th>
                                                    <th>Nombor Akaun</th>
                                                    <th>Jumlah Baki</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <option value="<?= $account['id'] ?>">
                                                    <tr>
                                                        <td><?= $loop_index = isset($loop_index) ? $loop_index + 1 : 1; ?></td>
                                                        <td>Savings</td>
                                                        <td>
                                                            <a href="/users/statements/savings" 
                                                               class="text-primary text-decoration-none">
                                                                <?= htmlspecialchars($account['account_number']) ?>
                                                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                            </a>
                                                        </td>
                                                        <td>RM<?= number_format($account['current_amount'] ?? 0, 2) ?></td>
                                                        <td><span class="badge bg-success">Aktif</span></td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                        
                        <!-- Loan Accounts Selection -->
                        <?php if (!empty($loans)): ?>
                            <div class="mt-4">
                                <h5 class="mb-3">Senarai Akaun Pembiayaan</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Jenis Pembiayaan</th>
                                                <th>No. Rujukan</th>
                                                <th>Jumlah Pembiayaan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($loans as $loan): ?>
                                                <tr>
                                                    <td><?= $index = ($index ?? 0) + 1; ?></td>
                                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                                    <td>
                                                        <a href="/users/statements/loans?id=<?= $loan['id'] ?>" 
                                                           class="text-primary text-decoration-none">
                                                            <?= htmlspecialchars($loan['reference_no']) ?>
                                                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                        </a>
                                                    </td>
                                                    <td>RM<?= number_format($loan['amount'] ?? 0, 2) ?></td>
                                                    <td><span class="badge bg-success">Aktif</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-4">
                            <p class="text-muted fst-italic">
                                <i class="bi bi-info-circle me-1"></i>
                                Jika tidak dapat melihat akaun anda, sila hubungi kakitangan kami untuk bantuan.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove the old notification card and add this modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="bi bi-bell me-2"></i>
                        Tetapan Notifikasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/users/statements/notifications" method="POST" class="notification-form">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotification" 
                                   name="email_notification" 
                                   <?= isset($notifications['email_enabled']) && $notifications['email_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="emailNotification">
                                Terima penyata secara automatik
                            </label>
                        </div>
                        
                        <div class="notification-settings <?= isset($notifications['email_enabled']) && $notifications['email_enabled'] ? '' : 'd-none' ?>">
                            <!-- Delivery Method -->
                            <div class="mb-3">
                                <label class="form-label">Kaedah Penghantaran</label>
                                <select class="form-select" name="delivery_method" id="deliveryMethod" required>
                                    <option value="email" <?= isset($notifications['delivery_method']) && $notifications['delivery_method'] == 'email' ? 'selected' : '' ?>>Emel</option>
                                    <option value="sms" <?= isset($notifications['delivery_method']) && $notifications['delivery_method'] == 'sms' ? 'selected' : '' ?>>SMS</option>
                                </select>
                            </div>

                            <!-- Frequency -->
                            <div class="mb-3">
                                <label class="form-label">Kekerapan</label>
                                <select class="form-select" name="frequency" id="frequency" required>
                                    <option value="daily" <?= isset($notifications['frequency']) && $notifications['frequency'] == 'daily' ? 'selected' : '' ?>>Harian</option>
                                    <option value="weekly" <?= isset($notifications['frequency']) && $notifications['frequency'] == 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                                    <option value="monthly" <?= isset($notifications['frequency']) && $notifications['frequency'] == 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                                    <option value="yearly" <?= isset($notifications['frequency']) && $notifications['frequency'] == 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                                </select>
                            </div>

                            <!-- Email Field -->
                            <div class="email-field <?= isset($notifications['delivery_method']) && $notifications['delivery_method'] == 'sms' ? 'd-none' : '' ?>">
                                <div class="mb-4">
                                    <label class="form-label">Emel</label>
                                    <input type="email" class="form-control bg-light" name="email" id="emailInput"
                                           value="kada.ecopioneer@gmail.com" 
                                           placeholder="Masukkan alamat emel anda">
                                </div>
                            </div>

                            <!-- Phone Field -->
                            <div class="phone-field <?= isset($notifications['delivery_method']) && $notifications['delivery_method'] == 'email' ? 'd-none' : '' ?>">
                                <div class="mb-4">
                                    <label class="form-label">Nombor Telefon</label>
                                    <input type="tel" class="form-control bg-light" name="phone" id="phoneInput"
                                           value="0123928471" 
                                           placeholder="Masukkan nombor telefon anda">
                    
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>
                                Simpan Tetapan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.75rem;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn:hover {
    transform: translateY(-1px);
}

.table {
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem;
    background-color: #f8f9fa;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.text-success {
    color: #198754 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
}

.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    padding: 1.5rem 1.5rem 1rem;
}

.modal-body {
    padding: 1rem 1.5rem 1.5rem;
}

.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}

.btn-light:hover {
    background-color: #e9ecef;
    border-color: #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailNotification = document.getElementById('emailNotification');
    const notificationSettings = document.querySelector('.notification-settings');
    const emailField = document.querySelector('.email-field');
    const phoneField = document.querySelector('.phone-field');
    const deliveryMethod = document.getElementById('deliveryMethod');
    const emailInput = document.getElementById('emailInput');
    const phoneInput = document.getElementById('phoneInput');
    const form = document.querySelector('.notification-form');

    // Handle checkbox change
    emailNotification.addEventListener('change', function() {
        notificationSettings.classList.toggle('d-none', !this.checked);
        updateRequiredFields();
    });

    // Handle delivery method change
    deliveryMethod.addEventListener('change', function() {
        emailField.classList.toggle('d-none', this.value === 'sms');
        phoneField.classList.toggle('d-none', this.value === 'email');
        updateRequiredFields();
    });

    function updateRequiredFields() {
        const isEnabled = emailNotification.checked;
        const isSMS = deliveryMethod.value === 'sms';

        emailInput.required = isEnabled && !isSMS;
        phoneInput.required = isEnabled && isSMS;
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        if (!emailNotification.checked) return;

        if (deliveryMethod.value === 'email' && !emailInput.value) {
            e.preventDefault();
            alert('Sila masukkan alamat emel anda');
            return;
        }

        if (deliveryMethod.value === 'sms' && !phoneInput.value) {
            e.preventDefault();
            alert('Sila masukkan nombor telefon anda');
            return;
        }
    });

    // Initial setup
    updateRequiredFields();
});

// Keep existing date update function
document.addEventListener('DOMContentLoaded', function() {
    updateDates('today');
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>