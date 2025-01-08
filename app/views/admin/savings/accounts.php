<?php 
    $title = 'Senarai Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
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

    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <i class="bi bi-wallet2 me-2"></i>Senarai Akaun
                </h4>
                <div>
                    <a href="/admin/savings" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="/admin/savings/accounts/add" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Akaun
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Akaun</th>
                            <th>Baki</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($account['account_name'] ?? 'Akaun Simpanan') ?>
                                    <?php if ($account['target_amount'] === null): ?>
                                        <span class="badge bg-primary ms-2">Utama</span>
                                    <?php endif; ?>
                                </td>
                                <td>RM <?= number_format($account['current_amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $account['status'] === 'active' ? 'success' : 'warning' ?>">
                                        <?= $account['status'] === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="setMainAccount(<?= $account['id'] ?>)" 
                                            class="btn btn-sm btn-outline-primary me-2"
                                            <?= $account['display_main'] ? 'disabled' : '' ?>>
                                        <i class="bi bi-star<?= $account['display_main'] ? '-fill' : '' ?>"></i>
                                    </button>
                                    <?php if ($account['target_amount'] !== null): ?>
                                        <button onclick="confirmDelete(<?= $account['id'] ?>)" 
                                                class="btn btn-sm btn-outline-danger"
                                                <?= $account['current_amount'] > 0 ? 'disabled' : '' ?>>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(accountId) {
    if (confirm('Adakah anda pasti untuk memadam akaun ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/savings/accounts/delete/${accountId}`;
        document.body.appendChild(form);
        form.submit();
    }
}

function setMainAccount(accountId) {
    if (confirm('Jadikan akaun ini sebagai paparan utama?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/savings/accounts/set-main/${accountId}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 