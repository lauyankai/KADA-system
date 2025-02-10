<?php 
    $title = 'Dashboard Admin';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Member Approval Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-plus-fill me-2"></i>Status Keahlian
                        </h5>
                        <a href="/admin/member_list" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Pending Members -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning bg-opacity-10 me-3">
                                    <i class="bi bi-hourglass-split text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Dalam Proses</h6>
                                    <h4 class="mb-0 text-warning">
                                        <?= count(array_filter($members, fn($m) => $m['member_type'] === 'Pending')) ?>
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <!-- Active Members -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success bg-opacity-10 me-3">
                                    <i class="bi bi-person-check-fill text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Ahli Aktif</h6>
                                    <h4 class="mb-0 text-success">
                                        <?= count(array_filter($members, fn($m) => $m['member_type'] === 'Ahli')) ?>
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected Members -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-danger bg-opacity-10 me-3">
                                    <i class="bi bi-person-x-fill text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Ditolak</h6>
                                    <h4 class="mb-0 text-danger">
                                        <?= count(array_filter($members, fn($m) => $m['member_type'] === 'Rejected')) ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Quick Actions Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Tindakan Pantas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/admin/member_list" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Senarai Ahli
                        </a>
                        <a href="/admin/loans" class="btn btn-outline-success">
                            <i class="bi bi-cash-coin me-2"></i>Pembiayaan
                        </a>
                        <a href="/admin/annual-reports" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark-text me-2"></i>Laporan Tahunan
                        </a>
                        <a href="/admin/settings" class="btn btn-outline-secondary">
                            <i class="bi bi-gear me-2"></i>Tetapan Sistem
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
<?php require_once '../app/views/layouts/footer.php'; ?>
