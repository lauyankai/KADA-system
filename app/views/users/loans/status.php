*status.php
app\views\loans\status.php

<?php 
    $title = 'Status Pinjaman';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-clock-history me-2"></i>Status Permohonan Pinjaman
                    </h4>

                    <?php if (empty($loans)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="mt-3">Tiada permohonan pinjaman dijumpai</p>
                            <a href="/loans/request" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Buat Permohonan Baru
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Rujukan</th>
                                        <th>Jenis Pinjaman</th>
                                        <th>Jumlah (RM)</th>
                                        <th>Tarikh Mohon</th>
                                        <th>Status</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($loans as $loan): ?>
                                        <tr>
                                            <td><?= $loan['reference_no'] ?></td>
                                            <td><?= $loan['loan_type'] ?></td>
                                            <td>RM <?= number_format($loan['amount'], 2) ?></td>
                                            <td><?= date('d/m/Y', strtotime($loan['created_at'])) ?></td>
                                            <td>
                                                <?php
                                                    $statusClass = '';
                                                    switch($loan['status']) {
                                                        case 'pending':
                                                            $statusClass = 'warning';
                                                            $statusText = 'Dalam Proses';
                                                            break;
                                                        case 'approved':
                                                            $statusClass = 'success';
                                                            $statusText = 'Diluluskan';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'danger';
                                                            $statusText = 'Ditolak';
                                                            break;
                                                    }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($loan['status'] === 'approved'): ?>
                                                    <a href="/loans/details/<?= $loan['id'] ?>" 
                                                       class="btn btn-sm btn-primary">
                                                       <i class="bi bi-file-text"></i> Butiran
                                                    </a>
                                                <?php elseif ($loan['status'] === 'rejected'): ?>
                                                    <a href="/loans/request" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-arrow-repeat"></i> Mohon Semula
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>