<?php 
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Stats Cards -->
        <div class="col-12 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 me-3">
                                <i class="bi bi-clock text-warning"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Menunggu Kelulusan</h6>
                                <h3 class="card-title mb-0"><?= $metrics['loan_stats']['pending_count'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 me-3">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Diluluskan</h6>
                                <h3 class="card-title mb-0"><?= $metrics['loan_stats']['approved_loans'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon bg-danger bg-opacity-10 me-3">
                                <i class="bi bi-x-circle text-danger"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Ditolak</h6>
                                <h3 class="card-title mb-0"><?= $metrics['loan_stats']['rejected_count'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-1">
                                <i class="bi bi-file-text me-2"></i>Senarai Permohonan Pembiayaan
                            </h4>
                            <p class="text-muted mb-0">Urus permohonan pembiayaan ahli koperasi</p>
                        </div>
                        <div>
                            <select class="form-select" id="statusFilter" onchange="filterLoans(this.value)">
                                <option value="pending" <?= ($_GET['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Diluluskan</option>
                                <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Rujukan</th>
                                    <th>Nama Pemohon</th>
                                    <th>No. K/P</th>
                                    <th>Jenis</th>
                                    <th>Jumlah (RM)</th>
                                    <th>Tempoh</th>
                                    <th>Tarikh Mohon</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($loans)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <p class="text-muted mb-0">Tiada permohonan pembiayaan baharu</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($loans as $loan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($loan['reference_no']) ?></td>
                                            <td><?= htmlspecialchars($loan['member_name']) ?></td>
                                            <td><?= htmlspecialchars($loan['ic_no']) ?></td>
                                            <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                            <td><?= number_format($loan['amount'], 2) ?></td>
                                            <td><?= htmlspecialchars($loan['duration']) ?> bulan</td>
                                            <td><?= date('d/m/Y', strtotime($loan['date_received'])) ?></td>
                                            <td>
                                                <?php
                                                $statusBadge = match($loan['status']) {
                                                    'pending' => '<span class="badge bg-warning">Menunggu</span>',
                                                    'approved' => '<span class="badge bg-success">Diluluskan</span>',
                                                    'rejected' => '<span class="badge bg-danger">Ditolak</span>',
                                                    default => '<span class="badge bg-secondary">-</span>'
                                                };
                                                echo $statusBadge;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($loan['status'] === 'pending'): ?>
                                                    <button onclick="showReviewModal(<?= $loan['id'] ?>)" 
                                                            class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/director/loans/update-status" method="POST">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Semak Permohonan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="loan_id" id="loanId">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="approved">Lulus</option>
                            <option value="rejected">Tolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                    </div>
                    <?php if (isset($_SESSION['csrf_token'])): ?>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2 me-1"></i>Hantar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.badge {
    font-weight: 500;
}

.modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.stats-icon i {
    font-size: 24px;
}
</style>

<script>
function showReviewModal(loanId) {
    document.getElementById('loanId').value = loanId;
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}

function filterLoans(status) {
    window.location.href = `/director/loans?status=${status}`;
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>