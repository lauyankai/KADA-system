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

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= $success_message ?>
                        </div>
                    <?php endif; ?>
                    
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
                        <button onclick="copyReferenceNo(this)" class="btn btn-outline-secondary">
                            <i class="bi bi-clipboard me-2"></i>Salin Nombor Rujukan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup Notification -->
<div class="popup-notification" id="copyPopup">
    <i class="bi bi-check-circle-fill"></i> Nombor rujukan berjaya disalin!
</div>

<script>
function copyReferenceNo(button) {
    const refNo = '<?= $reference_no ?>';
    navigator.clipboard.writeText(refNo).then(() => {
        // Change button text and style
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check2 me-2"></i>Berjaya disalin!';
        button.classList.replace('btn-outline-secondary', 'btn-success');
        
        // Show popup notification
        const popup = document.getElementById('copyPopup');
        popup.classList.add('show');
        
        // Hide popup after 2 seconds
        setTimeout(() => {
            popup.classList.remove('show');
        }, 2000);
        
        // Reset button after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalHtml;
            button.classList.replace('btn-success', 'btn-outline-secondary');
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

/* Popup Notification Styles */
.popup-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #198754;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1000;
}

.popup-notification.show {
    transform: translateY(0);
    opacity: 1;
}

.popup-notification i {
    font-size: 1.2rem;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 