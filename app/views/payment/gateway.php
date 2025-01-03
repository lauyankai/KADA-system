<?php 
    $title = 'Pemprosesan Pembayaran';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h4 class="mb-3">Memproses Pembayaran Anda</h4>
                    <p class="text-muted">Sila tunggu sebentar. Anda akan dibawa ke laman pembayaran.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simulate payment gateway redirect
setTimeout(() => {
    // In production, this would be the actual payment gateway URL
    window.location.href = '/payment/simulate/<?= $provider ?>';
}, 2000);
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 