<?php 
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
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

    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>Senarai Permohonan Pembiayaan
                </h4>
                <a href="/director/dashboard" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Rujukan</th>
                            <th>Nama Pemohon</th>
                            <th>No. K/P</th>
                            <th>Jenis</th>
                            <th>Jumlah (RM)</th>
                            <th>Tarikh Mohon</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($loans)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Tiada permohonan pembiayaan baharu
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
                                    <td><?= date('d/m/Y', strtotime($loan['date_received'])) ?></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                onclick="showReviewModal('<?= $loan['id'] ?>')">
                                            <i class="bi bi-check-circle me-1"></i>Semak
                                        </button>
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

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/director/loans/update-status" method="POST">
                <div class="modal-header">
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
                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Hantar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showReviewModal(loanId) {
    document.getElementById('loanId').value = loanId;
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>