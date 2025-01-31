<?php
error_log('Current session data: ' . print_r($_SESSION, true));
?>

<?php 
    $title = 'Dashboard Pengarah';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard Pengarah</h2>
            <p class="text-muted mb-0">Selamat datang, <?= htmlspecialchars($_SESSION['director_name']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success">
                <i class="bi bi-printer me-2"></i>Cetak Laporan
            </button>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar3 me-2"></i>Tempoh
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="updatePeriod('today')">Hari Ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="updatePeriod('week')">Minggu Ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="updatePeriod('month')">Bulan Ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="updatePeriod('year')">Tahun Ini</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
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
                            <h6 class="card-subtitle text-muted">Jumlah Ahli</h6>
                            <h2 class="card-title mb-0"><?= number_format($metrics['total_members']) ?></h2>
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

        <!-- Total Savings -->
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
                            <h6 class="card-subtitle text-muted">Jumlah Keseluruhan Simpanan</h6>
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
                            <h6 class="card-subtitle text-muted">Pembiayaan Aktif</h6>
                            <h2 class="card-title mb-0"><?= $metrics['loan_stats']['approved_loans'] ?></h2>
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
                            <h6 class="card-subtitle text-muted">Kadar Kelulusan</h6>
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
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

        <!-- Financial Distribution -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Agihan Kewangan</h5>
                    <canvas id="financialDistChart" height="300"></canvas>
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

    <!-- Recent Activities -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Aktiviti Terkini</h5>
                <a href="#" class="btn btn-sm btn-outline-success">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tarikh</th>
                            <th>Ahli</th>
                            <th>Jenis</th>
                            <th>Amaun</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivities as $activity): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= date('d/m/Y', strtotime($activity['created_at'])) ?></span>
                                        <small class="text-muted"><?= date('H:i', strtotime($activity['created_at'])) ?></small>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($activity['member_name']) ?></td>
                                <td>
                                    <?php if ($activity['type'] === 'savings'): ?>
                                        <span class="badge bg-success rounded-pill">Simpanan</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill">Pembiayaan</span>
                                    <?php endif; ?>
                                </td>
                                <td>RM <?= "RM " . number_format($activity['amount'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="status-dot bg-<?= getStatusColor($activity['transaction_type']) ?>"></span>
                                    <?= ucfirst($activity['transaction_type']) ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-light" title="Lihat butiran">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add custom CSS -->
<style>
.stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.stats-icon i {
    font-size: 24px;
}

.legend-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.3rem;
}

.chart-legend .small {
    font-size: 0.75rem;
}

.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

@media print {
    .btn, .dropdown {
        display: none !important;
    }
}
</style>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Helper function for status colors
function getStatusColor(status) {
    switch(status.toLowerCase()) {
        case 'deposit':
        case 'approved':
            return 'success';
        case 'pending':
            return 'warning';
        case 'withdrawal':
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Membership Growth Chart
const membershipCtx = document.getElementById('membershipChart').getContext('2d');
const membershipData = <?= json_encode($membershipTrends) ?>;

new Chart(membershipCtx, {
    type: 'line',
    data: {
        labels: membershipData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('ms-MY', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Ahli Baru',
                data: membershipData.map(item => item.new_members),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 1.5,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Jumlah Ahli',
                data: membershipData.map(item => item.total_members),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 1.5,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' orang';
                    }
                },
                titleFont: { size: 11 },
                bodyFont: { size: 11 },
                padding: 8
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: { size: 10 },
                    maxRotation: 0,
                    autoSkip: true,
                    maxTicksLimit: 6
                },
                border: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2],
                    drawBorder: false
                },
                ticks: {
                    font: { size: 10 },
                    maxTicksLimit: 5,
                    padding: 5,
                    callback: function(value) {
                        return value + ' orang';
                    }
                },
                border: {
                    display: false
                }
            }
        },
        layout: {
            padding: 0
        }
    }
});

// Financial Distribution Chart
const financialCtx = document.getElementById('financialDistChart').getContext('2d');
new Chart(financialCtx, {
    type: 'doughnut',
    data: {
        labels: ['Simpanan', 'Pembiayaan', 'Yuran', 'Lain-lain'],
        datasets: [{
            data: [
                parseFloat(<?= $metrics['total_savings'] ?? 0 ?>),
                parseFloat(<?= $metrics['loan_stats']['total_amount'] ?? 0 ?>),
                parseFloat(<?= $metrics['total_fees'] ?? 0 ?>),
                parseFloat(<?= $metrics['other_amounts'] ?? 0 ?>)
            ],
            backgroundColor: [
                'rgba(13, 110, 253, 0.8)',  // Blue for Savings
                'rgba(25, 135, 84, 0.8)',   // Green for Loans
                'rgba(255, 193, 7, 0.8)',   // Yellow for Fees
                'rgba(108, 117, 125, 0.8)'  // Grey for Others
            ],
            borderColor: [
                'rgba(13, 110, 253, 1)',
                'rgba(25, 135, 84, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += 'RM ' + new Intl.NumberFormat('ms-MY').format(context.raw);
                        return label;
                    }
                }
            }
        },
        cutout: '65%'
    }
});

// Period update function
function updatePeriod(period) {
    // Add AJAX call to update dashboard data based on period
    console.log('Updating period to:', period);
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 