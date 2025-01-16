<?php 
    $title = 'Resit Pembayaran';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-5" id="receipt">
                    <!-- Receipt Header -->
                    <div class="text-center mb-4">
                        <h4 class="mb-0">Platform Digital Koperasi KADA Kelantan</h4>
                        <p class="text-muted mb-0">D/A Lembaga Kemajuan Pertanian Kemubu,</p>
                        <p class="text-muted mb-0">P/S 127, 15710 Kota Bharu, Kelantan</p>
                    </div>

                    <hr>

                    <!-- Transaction Details -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="mb-1">No. Rujukan:</p>
                            <p class="fw-bold"><?= htmlspecialchars($transaction['reference_no']) ?></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">Tarikh:</p>
                            <p class="fw-bold"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></p>
                        </div>
                    </div>

                    <!-- Member Details -->
                    <div class="mb-4">
                        <p class="mb-1">Ahli:</p>
                        <p class="fw-bold mb-1"><?= htmlspecialchars($member['name']) ?></p>
                        <p class="text-muted mb-0">No. Ahli: <?= htmlspecialchars($member['member_number']) ?></p>
                    </div>

                    <!-- Transaction Info -->
                    <div class="bg-light p-3 rounded mb-4">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1">Jenis Transaksi:</p>
                                <p class="fw-bold">
                                    <?php
                                    switch($transaction['type']) {
                                        case 'deposit':
                                            echo 'Deposit';
                                            break;
                                        case 'transfer_in':
                                            echo 'Pindahan Masuk';
                                            break;
                                        case 'transfer_out':
                                            echo 'Pindahan Keluar';
                                            break;
                                        default:
                                            echo ucfirst($transaction['type']);
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1">Jumlah:</p>
                                <h4 class="mb-0 <?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                    <?= in_array($transaction['type'], ['deposit', 'transfer_in']) ? '+' : '-' ?>
                                    RM <?= number_format($transaction['amount'], 2) ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="mb-1">Kaedah Pembayaran:</p>
                            <p class="fw-bold"><?= htmlspecialchars(ucfirst($transaction['payment_method'] ?? '-')) ?></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">Status:</p>
                            <span class="badge bg-success">Selesai</span>
                        </div>
                    </div>

                    <?php if (!empty($transaction['description'])): ?>
                        <div class="mb-4">
                            <p class="mb-1">Penerangan:</p>
                            <p class="fw-bold"><?= htmlspecialchars($transaction['description']) ?></p>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="text-center text-muted small">
                        <p class="mb-0">Ini adalah resit yang dijana oleh komputer.</p>
                        <p class="mb-0">Tiada tandatangan diperlukan.</p>
                    </div>
                </div>

                <!-- Print/Download Buttons -->
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex justify-content-center gap-2">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer me-2"></i>Cetak
                        </button>
                        <a href="/users/savings/page" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt, #receipt * {
        visibility: visible;
    }
    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .card-footer {
        display: none;
    }
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 