<?php 
    $title = 'Dashboard Ahli';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <!-- Welcome Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-1">
                        Selamat Datang, <?= htmlspecialchars($member->name ?? 'Ahli') ?>
                    </h3>
                    <p class="text-muted mb-0">
                        No. Ahli: <?= htmlspecialchars($member->member_id ?? '-') ?>
                    </p>
                </div>
                <!-- Last Login Info -->
                <div class="text-end text-muted">
                    <small>Login Terakhir: <?= date('d/m/Y H:i') ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Savings Section -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-piggy-bank me-2"></i>Simpanan
                        </h5>
                    <!-- Total Savings Amount -->
                    <h3 class="text-success mb-3">RM <?= number_format($totalSavings ?? 0, 2) ?></h3>
                    <div class="d-grid">
                        <a href="/users/savings/page" class="btn btn-outline-success">
                            <i class="bi bi-piggy-bank me-2"></i>
                            Akaun Simpanan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loans Section -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-cash-stack me-2"></i>Pinjaman
                        </h5>
                    <!-- Loan Actions -->
                    <div class="d-grid gap-2">
                        <a href="/users/loans/status" class="btn btn-outline-primary">
                            <i class="bi bi-list-check me-2"></i>Status Pinjaman
                        </a>
                        <a href="/users/loans/request" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>Mohon Pinjaman
                        </a>
                    </div>
                            </div>
                            </div>
                        </div>

        <!-- Payments Section -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-credit-card me-2"></i>Pembayaran
                    </h5>
                    <!-- Payment Actions -->
                    <div class="d-grid gap-2">
                        <a href="/users" class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history me-2"></i>Sejarah Pembayaran
                        </a>
                        <a href="/users/savings/transfer" class="btn btn-secondary">
                            <i class="bi bi-cash me-2"></i>Buat Pembayaran
                        </a>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <!-- Member Status -->
        <div class="col-md-6 mb-4">
    <div class="card">
        <div class="card-body">
                    <h5 class="card-title">Status Keahlian</h5>
            <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Nama Penuh</td>
                                <td width="5%">:</td>
                                <td><?= htmlspecialchars($member->name ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td>No. Ahli</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($member->member_id ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td>No. Akaun</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($member->account_number ?? '-') ?></td>
                        </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                </tr>
                            <tr>
                                <td>Tarikh Daftar</td>
                                <td>:</td>
                                <td><?= date('d/m/Y', strtotime($member->created_at ?? 'now')) ?></td>
                            </tr>
                </table>
            </div>
        </div>
    </div>
</div>

        <!-- Recent Activities -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Aktiviti Terkini</h5>
                    <?php if (!empty($recentActivities)): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($recentActivities as $activity): ?>
                                <li class="mb-2">
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($activity->created_at)) ?></small>
                                    <br>
                                    <?= htmlspecialchars($activity->description) ?>
                                    <?php if ($activity->amount > 0): ?>
                                        <span class="text-<?= $activity->type === 'savings' ? 'success' : 'primary' ?>">
                                            RM <?= number_format($activity->amount, 2) ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Tiada aktiviti terkini</p>
                    <?php endif; ?>
                </div>
                </div>
        </div>
    </div>
</div>

<!-- Notifications Toast -->
<?php if (isset($_SESSION['success'])): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle me-2"></i>
            <strong class="me-auto">Berjaya</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
</div>
        <div class="toast-body">
            <?= $_SESSION['success']; ?>
        </div>
    </div>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php require_once '../app/views/layouts/footer.php'; ?> 