<?php 
    $title = 'Dashboard Pengarah';
    require_once '../app/views/layouts/header.php';
?>
<link rel="stylesheet" href="/css/director.css">
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard Pengarah</h2>
            <p class="text-muted mb-0">Selamat datang, <?= htmlspecialchars($_SESSION['director_name']) ?></p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Members -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Jumlah Ahli</h6>
                            <div class="d-flex align-items-baseline">
                                <h2 class="card-title mb-0"><?= number_format($metrics['total_members']) ?></h2>
                                <small class="text-muted ms-2">orang</small>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">
                            <i class="bi bi-graph-up me-1"></i>+<?= $metrics['new_members'] ?>
                        </span>
                        <small class="text-muted">Ahli baru bulan ini</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-piggy-bank"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Jumlah Keseluruhan Simpanan</h6>
                            <h2 class="card-title mb-0"><?= "RM " . number_format($metrics['total_savings'] ?? 0, 2) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Pembiayaan Aktif</h6>
                            <div class="d-flex align-items-baseline">
                                <h2 class="card-title mb-0"><?= $metrics['loan_stats']['approved_loans'] ?></h2>
                                <small class="text-muted ms-2">permohonan</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">

                        <h6 class="mb-0 me-2"><?= "RM " . number_format($metrics['loan_stats']['total_amount'] ?? 0, 2) ?></h6>
                        <small class="text-muted">Jumlah pembiayaan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Rate -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="stats-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Kadar Kelulusan</h6>
                            <?php
                                $approvedLoans = $metrics['loan_stats']['approved_loans'] ?? 0;
                                $totalLoans = ($metrics['loan_stats']['total_loans'] ?? 0) + 
                                            ($metrics['loan_stats']['rejected_count'] ?? 0) + 
                                            ($metrics['loan_stats']['pending_count'] ?? 0);
                                $approvalRate = $totalLoans > 0 ? ($approvedLoans / $totalLoans) * 100 : 0;
                            ?>
                            <h2 class="card-title mb-0"><?= number_format($approvalRate, 1) ?>%</h2>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: <?= $approvalRate ?>%"></div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <?= $approvedLoans ?> diluluskan daripada <?= $totalLoans ?> permohonan tahun ini
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Membership Growth -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-4">Trend Keahlian</h5>
                        <div class="chart-legend d-flex gap-2">
                            <div class="d-flex align-items-center">
                                <span class="legend-indicator bg-primary"></span>
                                <span class="small">Ahli Baru</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="legend-indicator bg-success"></span>
                                <span class="small">Jumlah Ahli</span>
                            </div>
                        </div>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="membershipChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Trends Chart -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Trend Kewangan</h5>
                    </div>
                    <canvas id="financialTrendChart" height="280"></canvas>
                    
                    <!-- Add this insights section below the chart -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="stats-trend-indicator rounded-circle bg-primary bg-opacity-10 p-2">
                                            <i class="bi bi-graph-up text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="small text-muted">Pembiayaan Bulan Ini</div>
                                        <div class="fw-medium">
                                            RM <?= number_format(end($financialTrends['loans']), 2) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="stats-trend-indicator rounded-circle bg-success bg-opacity-10 p-2">
                                            <i class="bi bi-piggy-bank text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="small text-muted">Simpanan Bulan Ini</div>
                                        <div class="fw-medium">
                                            RM <?= number_format(end($financialTrends['savings']), 2) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Loan Approvals -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Kelulusan Pembiayaan</h5>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                            <div>
                                <span>Menunggu Kelulusan</span>
                                <?php if ($metrics['loan_stats']['pending_count'] == 0): ?>
                                    <small class="d-block text-muted">Tiada permohonan baru</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-warning rounded-pill">
                                <?= $metrics['loan_stats']['pending_count'] ?? 0 ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                            <div>
                                <span>Diluluskan</span>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                <?= $metrics['loan_stats']['approved_loans'] ?? 0 ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                            <div>
                                <span>Ditolak</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">
                                <?= $metrics['loan_stats']['rejected_count'] ?? 0 ?>
                            </span>
                        </div>
                        <div class="mt-2">
                            <a href="director/loans" class="btn btn-primary w-100">
                                <i class="bi bi-check2-square me-2"></i>Proses Kelulusan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/director.js"></script>
<script>
    initDashboardCharts(
        <?= json_encode($membershipTrends) ?>,
        {
            labels: <?= json_encode($financialTrends['labels'] ?? []) ?>,
            loans: <?= json_encode($financialTrends['loans'] ?? []) ?>,
            savings: <?= json_encode($financialTrends['savings'] ?? []) ?>
        }
    );
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 