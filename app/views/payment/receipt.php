<?php 
    $title = 'Resit Pembayaran';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h4>Resit Pembayaran</h4>
                        <p class="text-muted">No. Rujukan: <?= $receipt['reference_no'] ?></p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="mb-1 text-muted">Tarikh</p>
                            <p class="fw-bold"><?= date('d/m/Y H:i', strtotime($receipt['created_at'])) ?></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1 text-muted">Status</p>
                            <span class="badge bg-success">Berjaya</span>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6>Butiran Transaksi</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted mb-1">Jenis Transaksi</p>
                                <p><?= $receipt['type'] === 'deposit' ? 'Deposit' : 'Pindahan' ?></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-1">Jumlah</p>
                                <p class="fw-bold">RM <?= number_format($receipt['amount'], 2) ?></p>
                            </div>
                        </div>

                        <?php if ($receipt['type'] === 'transfer'): ?>
                            <div class="receipt-details">
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Dari Akaun:</div>
                                    <div class="col-6 text-end"><?= htmlspecialchars($receipt['from_account']) ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Ke Akaun:</div>
                                    <div class="col-6 text-end">
                                        <?= htmlspecialchars($receipt['to_account']) ?>
                                        <?php if (isset($receipt['recipient_name'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($receipt['recipient_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($receipt['type'] === 'deposit'): ?>
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Kaedah Pembayaran</p>
                                    <p><?= ucfirst($receipt['payment_method']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($receipt['description'])): ?>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <p class="text-muted mb-1">Catatan</p>
                                    <p><?= htmlspecialchars($receipt['description']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted mb-1">Baki Sebelum</p>
                            <p>RM <?= number_format($receipt['previous_balance'], 2) ?></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-1">Baki Selepas</p>
                            <p class="fw-bold">RM <?= number_format($receipt['new_balance'], 2) ?></p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="/users" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer me-2"></i>Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 