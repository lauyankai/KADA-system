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

        <!-- Annual Reports Section -->
        <div class="col-lg-8 mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>Laporan Tahunan
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReportModal">
                            <i class="bi bi-upload me-2"></i>Muat Naik
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tahun</th>
                                    <th>Tajuk</th>
                                    <th>Tarikh Muat Naik</th>
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
                                            <td><?= date('d/m/Y', strtotime($report['uploaded_at'])) ?></td>
                                            <td><?= formatFileSize($report['file_size']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= URLROOT ?>/uploads/reports/<?= $report['file_name'] ?>" 
                                                       class="btn btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger"
                                                            onclick="deleteReport(<?= $report['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
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

<div class="modal fade" id="uploadReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= URLROOT ?>/admin/upload_report" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Muat Naik Laporan Tahunan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reportYear" class="form-label">Tahun</label>
                        <select class="form-select" id="reportYear" name="year" required>
                            <?php 
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reportTitle" class="form-label">Tajuk Laporan</label>
                        <input type="text" class="form-control" id="reportTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportFile" class="form-label">Fail PDF</label>
                        <input type="file" class="form-control" id="reportFile" name="report_file" 
                               accept=".pdf" required>
                        <div class="form-text">Maksimum saiz fail: 10MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Muat Naik
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

<!-- Add this JavaScript at the bottom of your file -->
<script>
function deleteReport(reportId) {
    if (confirm('Adakah anda pasti untuk memadamkan laporan ini?')) {
        fetch(`${URLROOT}/admin/delete_report/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal memadamkan laporan. Sila cuba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat telah berlaku. Sila cuba lagi.');
        });
    }
}

// File size validation
document.getElementById('reportFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    
    if (file && file.size > maxSize) {
        alert('Saiz fail terlalu besar. Sila pilih fail yang kurang daripada 10MB.');
        this.value = '';
    }
});
</script>

<?php
// Helper function to format file size
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

<?php require_once '../app/views/layouts/footer.php'; ?>
