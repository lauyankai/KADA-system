<?php 
    $title = 'Akaun Telah Berhenti';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3">Akaun Anda Telah Berhenti</h4>
                    <p class="text-muted mb-4">
                        Anda telah berhenti sebagai ahli koperasi pada <?= date('d/m/Y', strtotime($resignedDate)) ?>.
                        <br>Adakah anda ingin memohon semula untuk menjadi ahli?
                    </p>
                    <div class="d-grid gap-2 col-lg-8 mx-auto">
                        <a href="/users/reactivate" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Mohon Semula
                        </a>
                        <a href="/auth/logout" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-right me-2"></i>Log Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 