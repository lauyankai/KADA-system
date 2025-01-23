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
                    <div class="mt-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Penyata Bulanan
                            </h5>
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Penyata dijana setiap 28 haribulan
                            </div>
                        </div>

                        <div class="row g-4">
                            <?php 
                            // Get last 12 months
                            $months = [];
                            for ($i = 0; $i < 12; $i++) {
                                $date = new DateTime();
                                $date->modify("-$i month");
                                $months[] = $date;
                            }

                            // Array of Malay month names
                            $malayMonths = [
                                'January' => 'Januari',
                                'February' => 'Februari', 
                                'March' => 'Mac',
                                'April' => 'April',
                                'May' => 'Mei',
                                'June' => 'Jun',
                                'July' => 'Julai',
                                'August' => 'Ogos',
                                'September' => 'September',
                                'October' => 'Oktober',
                                'November' => 'November',
                                'December' => 'Disember'
                            ];
                            ?>
                            
                            <?php foreach ($months as $month): ?>
                                <div class="col-md-3">
                                    <div class="card statement-card h-100 border-0">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="statement-icon me-3">
                                                    <i class="bi bi-file-pdf text-danger"></i>
                                                </div>
                                                <div>
                                                    <h6 class="card-title mb-1">
                                                        <?= $malayMonths[$month->format('F')] ?> <?= $month->format('Y') ?>
                                                    </h6>
                                                    <p class="card-subtitle text-muted small mb-0">
                                                        <?= $month->format('d/m/Y') ?> - <?= $month->format('t/m/Y') ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="/users/statements/download?loan_id=<?= $loan['id'] ?>&period=<?= $month->format('Y-m') ?>" 
                                               class="btn btn-light btn-sm w-100 statement-download-btn">
                                                <i class="bi bi-download me-2"></i>
                                                Muat Turun
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

/* New styles for statement cards */
.statement-card {
    background: #f8f9fa;
    transition: all 0.3s ease;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.statement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    background: white;
}

.statement-icon {
    font-size: 1.5rem;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 12px;
}

.statement-download-btn {
    border: 1px solid #dee2e6;
    background: white;
    transition: all 0.2s ease;
}

.statement-download-btn:hover {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-3 {
        padding: 0 8px;
    }
    
    .statement-card {
        margin-bottom: 16px;
    }
}

/* Animation for hover effects */
@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0px);
    }
}

.statement-card:hover .statement-icon {
    animation: float 2s ease-in-out infinite;
}
</style>

<script>
    // Set default period to 'today' when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateDates('today');
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>