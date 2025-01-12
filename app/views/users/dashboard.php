<?php 
    $title = 'Dashboard Ahli';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <!-- Welcome Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">Selamat Datang, <?= htmlspecialchars($_SESSION['member_name']) ?></h3>
            <p class="text-muted mb-0">Dashboard Ahli KADA</p>
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
                    <h3 class="text-success mb-3">RM <?= number_format($totalSavings ?? 0, 2) ?></h3>
                    <div class="d-grid gap-2">
                        <a href="/users/savings" class="btn btn-outline-success">
                            <i class="bi bi-wallet me-2"></i>Urus Simpanan
                        </a>
                        <a href="/users/savings/deposit" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Simpanan
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
                        <i class="bi bi-cash-stack me-2"></i>Pembiayaan
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="/users/loans/status" class="btn btn-outline-primary">
                            <i class="bi bi-list-check me-2"></i>Status Pembiayaan
                        </a>
                        <a href="/users/loans/request" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>Mohon Pembiayaan
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
                    <div class="d-grid gap-2">
                        <a href="/users/payments/history" class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history me-2"></i>Sejarah Pembayaran
                        </a>
                        <a href="/users/payments/make" class="btn btn-secondary">
                            <i class="bi bi-cash me-2"></i>Buat Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>