<?php 
    $title = 'Penyata Akaun Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-11">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary">Penyata Akaun Pembiayaan</h4>
                            <p class="text-muted mb-0">Lihat dan muat turun penyata akaun pembiayaan anda</p>
                        </div>
                        <a href="/users/statements" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-11">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Loan Details -->
                    <div class="bg-light p-3 rounded-3 mb-4">
                        <h5 class="mb-3">Maklumat Pembiayaan</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <?php foreach ($loans as $loan): ?>
                                <p class="mb-1"><strong>No. Rujukan:</strong> <?= htmlspecialchars($loan['reference_no']) ?></p>
                                <p class="mb-1"><strong>Jenis Pembiayaan:</strong> <?= htmlspecialchars($loan['loan_type']) ?></p>
                                <p class="mb-1"><strong>Jumlah Pembiayaan:</strong> RM<?= number_format($loan['amount'], 2) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tempoh:</strong> <?= htmlspecialchars($loan['duration']) ?> bulan</p>
                                <p class="mb-1"><strong>Baki Pembiayaan:</strong> RM<?= number_format($loan['remaining_amount'] ?? $loan['amount'] ?? 0, 2) ?></p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Aktif</span></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- After loan details section -->
                    <div class="mt-4">
                        <h5 class="mb-3">Penyata Bulanan</h5>
                        <div class="row">
                            <?php 
                            // Get last 12 months
                            $months = [];
                            for ($i = 0; $i < 12; $i++) {
                                $date = new DateTime();
                                $date->modify("-$i month");
                                $months[] = $date;
                            }
                            ?>
                            
                            <?php foreach ($months as $month): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= $month->format('F Y') ?></h6>
                                            <p class="card-text small text-muted">
                                                Penyata <?= $month->format('d/m/Y') ?> - <?= $month->format('t/m/Y') ?>
                                            </p>
                                            <a href="/users/statements/download?loan_id=<?= $loan['id'] ?>&period=<?= $month->format('Y-m') ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download me-1"></i>
                                                Muat Turun PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Styles */
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

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
}

/* Compact Styles */
.form-select, .form-control {
    padding: 0.4rem 0.75rem;
    font-size: 0.9rem;
}

.btn {
    padding: 0.4rem 1rem;
    font-size: 0.9rem;
}
</style>

<script>
    // Set default period to 'today' when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateDates('today');
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>