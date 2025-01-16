<?php 
    $title = 'Permohonan Berjaya';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="mb-4">Permohonan Berjaya Dihantar!</h3>
                    
                    <div class="alert alert-light border bg-light-subtle mb-4">
                        <p class="mb-2">Nombor rujukan anda ialah:</p>
                        <h4 class="text-success mb-0"><strong><?= $reference_no ?></strong></h4>
                    </div>
                    
                    <p class="text-muted mb-4">
                        Sila simpan nombor rujukan ini untuk semakan status permohonan anda pada masa hadapan.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-success">
                            <i class="bi bi-house-fill me-2"></i>Kembali ke Laman Utama
                        </a>
                        <button onclick="copyReferenceNo()" class="btn btn-outline-secondary">
                            <i class="bi bi-clipboard me-2"></i>Salin Nombor Rujukan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferenceNo() {
    const refNo = '<?= $reference_no ?>';
    navigator.clipboard.writeText(refNo).then(() => {
        // Change button text temporarily
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-2"></i>Disalin!';
        btn.classList.replace('btn-outline-secondary', 'btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    });
}
</script>

<style>
.card {
    border-radius: 15px;
}

.alert {
    border-radius: 10px;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
}

.text-success {
    color: #198754 !important;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 