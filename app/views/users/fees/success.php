<?php 
    $title = 'Pembayaran Berjaya';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body text-center">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Pembayaran Berjaya!</h3>
            <p class="lead">Terima kasih. Keahlian anda telah diaktifkan sepenuhnya.</p>
            <a href="/users/dashboard" class="btn btn-primary mt-3">Ke Dashboard</a>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 