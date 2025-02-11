<?php 
    $title = 'Dashboard Admin';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
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
                            <h5 class="modal-title text-success">Success</h5>
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
        
        <!-- Annual Report Section -->
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
    
    window.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
