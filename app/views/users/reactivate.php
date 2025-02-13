<?php 
    $title = 'Permohonan Semula';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Permohonan Semula Keahlian
                    </h4>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/users/reactivate/submit" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Sebab Memohon Semula</label>
                            <div class="reason-inputs">
                                <div class="mb-3">
                                    <textarea name="reasons[]" class="form-control" rows="3" 
                                              placeholder="Sila nyatakan sebab anda ingin memohon semula"></textarea>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="addReason">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Sebab
                            </button>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">
                                Saya mengesahkan bahawa semua maklumat yang diberikan adalah benar
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/auth/logout" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Hantar Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('addReason').addEventListener('click', function() {
    const container = document.querySelector('.reason-inputs');
    const count = container.children.length;
    
    if (count >= 5) {
        alert('Maksimum 5 sebab sahaja dibenarkan');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'mb-3 d-flex';
    div.innerHTML = `
        <textarea name="reasons[]" class="form-control" rows="3" 
                  placeholder="Sebab tambahan"></textarea>
        <button type="button" class="btn btn-outline-danger ms-2" onclick="removeReason(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    
    container.appendChild(div);
});

function removeReason(button) {
    button.closest('.mb-3').remove();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 