<?php 
    $title = 'Dashboard Admin';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4 mb-4">
    <?php if (isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
        <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
        <?php if (isset($_SESSION['error'])): ?>
                        <div class="modal-header border-0 bg-danger bg-opacity-10">
                            <h5 class="modal-title text-danger">Error</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
                        <div class="modal-header border-0 bg-success bg-opacity-10">
                            <h5 class="modal-title text-success">Berjaya!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
            </div>
        <?php endif; ?>
        
    <!-- Main Content Row -->
    <div class="row g-3 mb-4">
        <!-- Status Keahlian Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
            <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-people me-2"></i>Status Keahlian
                            </h5>
                            <p class="text-muted small mb-0">Statistik keahlian koperasi</p>
                        </div>
                        <a href="/admin/member_list" class="btn btn-primary btn-sm">
                            <i class="bi bi-people me-2"></i>Senarai Ahli
                        </a>
                    </div>

                    <div class="row g-3">
                        <!-- Pending Members -->
                        <div class="col-md-6 col-lg-3">
                            <div class="status-card bg-primary bg-opacity-10 rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-primary bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-people text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0 text-primary"><?= $stats['total'] ?></h3>
                                        <p class="text-muted small mb-0">Jumlah Ahli</p>
                                    </div>
                </div>
                </div>
            </div>

                        <!-- Active Members -->
                        <div class="col-md-6 col-lg-3">
                            <div class="status-card bg-success bg-opacity-10 rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-success bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-person-check-fill text-success"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0 text-success"><?= $stats['active'] ?></h3>
                                        <p class="text-muted small mb-0">Ahli Aktif</p>
                                    </div>
                </div>
                </div>
            </div>

                        <!-- Rejected Members -->
                        <div class="col-md-6 col-lg-3">
                            <div class="status-card bg-danger bg-opacity-10 rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-danger bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-person-x-fill text-danger"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0 text-danger"><?= $stats['rejected'] ?></h3>
                                        <p class="text-muted small mb-0">Ditolak</p>
                                    </div>
                </div>
                </div>
            </div>

                        <!-- Pending Members -->
                        <div class="col-md-6 col-lg-3">
                            <div class="status-card bg-warning bg-opacity-10 rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-warning bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-hourglass-split text-warning"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0 text-warning"><?= $stats['pending'] ?></h3>
                                        <p class="text-muted small mb-0">Dalam Proses</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Interest Rates Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-percent me-2"></i>Kadar Faedah
                            </h5>
                        </div>
                        <button class="btn btn-light btn-sm" onclick="editInterestRates()">
                            <i class="bi bi-pencil me-2"></i>Kemaskini
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <!-- Savings Interest -->
                        <div class="col-6">
                            <div class="interest-rate-card bg-success bg-opacity-10 rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="interest-icon bg-success bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-piggy-bank text-success"></i>
                                    </div>
                                    <h6 class="mb-0">Simpanan</h6>
                                </div>
                                <div class="interest-value">
                                    <span class="h3 mb-0 text-success"><?= number_format($interestRates['savings_rate'] ?? 0, 2) ?>%</span>
                                    <span class="text-muted ms-2">setahun</span>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Interest -->
                        <div class="col-6">
                            <div class="interest-rate-card bg-primary bg-opacity-10 rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="interest-icon bg-primary bg-opacity-25 rounded p-2 me-3">
                                        <i class="bi bi-cash-stack text-primary"></i>
                                    </div>
                                    <h6 class="mb-0">Pembiayaan</h6>
                                </div>
                                <div class="interest-value">
                                    <span class="h3 mb-0 text-primary"><?= number_format($interestRates['loan_rate'] ?? 0, 2) ?>%</span>
                                    <span class="text-muted ms-2">setahun</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                </div>

    <!-- Second Row -->
    <div class="row g-3">
        <!-- Annual Reports Card -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>Laporan Tahunan
                        </h4>
                        <div>
                            <button onclick="showUploadModal()" class="btn btn-success">
                                <i class="bi bi-upload me-2"></i>Muat Naik
                            </button>
                </div>
                </div>

                <div class="table-responsive">
                        <table class="table table-hover">
                        <thead>
                        <tr>
                                    <th>Tahun</th>
                                    <th>Tajuk</th>
                                    <th>Tarikh</th>
                                    <th>Saiz Fail</th>
                            <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php if (isset($annual_reports) && !empty($annual_reports)): ?>
                                    <?php foreach ($annual_reports as $report): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($report['year']) ?></td>
                                            <td><?= htmlspecialchars($report['title']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($report['uploaded_at'])) ?></td>
                                            <td><?= formatFileSize($report['file_size']) ?></td>
                                            <td>
                                                <a href="<?= htmlspecialchars($report['file_path']) ?>" 

                                                class="btn btn-sm btn-outline-primary"
                                                target="_blank">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button onclick="deleteReport(<?= $report['id'] ?>)" 
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                        </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-file-earmark-text display-6 d-block mb-3"></i>
                                            Tiada laporan tahunan dimuat naik
                                        </td>
                                    </tr>
                                <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>                       
    </div>

    <div class="col-lg-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-person-dash me-2"></i>Permohonan Berhenti
                        </h5>
                        <p class="text-muted small mb-0">Senarai permohonan berhenti</p>
                    </div>
                    <a href="/admin/resignations" class="btn btn-primary btn-sm">
                        <i class="bi bi-list me-2"></i>Lihat Semua
                    </a>
                </div>

                <?php if (empty($resignations)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox display-6 d-block mb-3"></i>
                        Tiada permohonan berhenti
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($resignations, 0, 5) as $resignation): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($resignation['name']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($resignation['resignation_date'])) ?>
                                        </small>
                                    </div>
                                    <form action="/admin/resignations/approve" method="POST" class="d-inline">
                                        <input type="hidden" name="member_id" value="<?= $resignation['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Adakah anda pasti untuk meluluskan permohonan ini?')">
                                            <i class="bi bi-check-circle me-2"></i>Lulus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Director Management Card -->
    <div class="col-lg-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-person-badge me-2"></i>Pengurusan Pengarah
                        </h5>
                        <p class="text-muted small mb-0">Senarai pengarah koperasi</p>
                    </div>
                    <a href="/director/add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-2"></i>Tambah
                    </a>
                </div>

                <?php if (empty($directors)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people display-6 d-block mb-3"></i>
                        Tiada pengarah berdaftar
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($directors as $director): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($director['name']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($director['position']) ?></small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="/director/edit/<?= $director['id'] ?>" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="deleteDirector(<?= $director['id'] ?>)" 
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-shield-lock me-2"></i>Pengurusan Admin
                        </h5>
                        <p class="text-muted small mb-0">Senarai pentadbir sistem</p>
                    </div>
                    <a href="/admin/add-admin" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-2"></i>Tambah
                    </a>
                </div>

                <?php if (empty($admins)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people display-6 d-block mb-3"></i>
                        Tiada admin berdaftar
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($admins as $admin): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($admin['username']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($admin['email']) ?></small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="/admin/edit-admin/<?= $admin['id'] ?>" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($admin['id'] !== $_SESSION['admin_id']): ?>
                                            <button onclick="deleteAdmin(<?= $admin['id'] ?>)" 
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
                </div>

    <!-- Upload Report Modal -->
    <div class="modal fade" id="uploadReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/admin/uploadReport" method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">Muat Naik Laporan Tahunan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-select" required>
                                <option value="">Pilih Tahun</option>
                                <?php 
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= ($currentYear - 5); $year--) {
                                        echo "<option value=\"$year\">$year</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tajuk</label> 
                            <input type="text" name="title" class="form-control" required>
                        </div>  
                        <div class="mb-3">
                            <label class="form-label">Fail PDF</label>
                            <input type="file" name="report_file" class="form-control" accept=".pdf" required>
                            <div class="form-text">Maksimum 100MB</div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-1"></i>Muat Naik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editInterestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="bi bi-percent me-2"></i>Kemaskini Kadar Faedah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/update-interest-rates" method="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kadar Faedah Simpanan (%)</label>
                                    <input type="number" 
                                           name="savings_rate" 
                                           class="form-control" 
                                           value="<?= number_format($interestRates['savings_rate'] ?? 0, 2) ?>" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kadar Faedah Pembiayaan (%)</label>
                                    <input type="number" 
                                           name="loan_rate" 
                                           class="form-control" 
                                           value="<?= number_format($interestRates['loan_rate'] ?? 0, 2) ?>" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-5"></div>

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

    .interest-rate-card {
        transition: transform 0.2s;
    }
    .interest-rate-card:hover {
        transform: translateY(-2px);
    }
    .interest-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .interest-icon i {
        font-size: 1.25rem;
    }
    </style>

<?php
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<script>
    function showUploadModal() {
        new bootstrap.Modal(document.getElementById('uploadReportModal')).show();
    }

    function deleteReport(id) {
        if (confirm('Adakah anda pasti untuk memadam laporan ini?')) {
            window.location.href = `/admin/deleteReport/${id}`;
        }
    }
    
    function editInterestRates() {
        new bootstrap.Modal(document.getElementById('editInterestModal')).show();
    }
    
    function deleteAdmin(id) {
        if (confirm('Adakah anda pasti untuk memadam admin ini?')) {
            window.location.href = `/admin/delete-admin/${id}`;
        }
    }
    
    window.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
