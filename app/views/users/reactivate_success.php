<?php 
    $title = 'Permohonan Semula Berjaya';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3">Permohonan Semula Telah Dihantar</h4>
                    <p class="text-muted mb-4">
                        Permohonan semula anda akan diproses dalam tempoh 14 hari bekerja.<br>
                        Sila semak emel anda untuk maklumat lanjut.
                    </p>
                    <a href="/auth/logout" class="btn btn-primary">
                        <i class="bi bi-box-arrow-right me-2"></i>Log Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 