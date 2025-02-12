<?php 
    $title = 'Permohonan Berhenti';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Permohonan Berhenti Menjadi Ahli
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Member Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Nama</label>
                            <p class="mb-3"><?= htmlspecialchars($member->name) ?></p>

                            <label class="text-muted small d-block">No. K/P</label>
                            <p class="mb-3"><?= htmlspecialchars($member->ic_no) ?></p>

                            <label class="text-muted small d-block">ID Ahli</label>
                            <p class="mb-0"><?= htmlspecialchars($member->member_id) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">E-mel</label>
                            <p class="mb-3"><?= htmlspecialchars($member->email) ?></p>

                            <label class="text-muted small d-block">No. Tel</label>
                            <p class="mb-3"><?= htmlspecialchars($member->home_phone) ?></p>

                            <label class="text-muted small d-block">Tarikh Mohon</label>
                            <p class="mb-0"><?= date('d/m/Y') ?></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Resignation Form -->
                    <form action="/users/resign/submit" method="POST" id="resignForm">
                        <div class="mb-4">
                            <label class="form-label">Sebab-sebab Berhenti <span class="text-danger">*</span></label>
                            <div id="reasonsContainer">
                                <div class="reason-item mb-2">
                                    <div class="input-group">
                                        <input type="text" name="reasons[]" class="form-control" required 
                                               placeholder="Sila nyatakan sebab">
                                        <button type="button" class="btn btn-outline-secondary" onclick="removeReason(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addReason()" 
                                        id="addReasonBtn">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Sebab
                                </button>
                                <small class="text-muted ms-2">(Maksimum 5 sebab)</small>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Sila ambil perhatian bahawa permohonan berhenti akan diproses dalam tempoh 14 hari bekerja.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/users/profile" class="btn btn-light">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-danger">
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
function addReason() {
    const container = document.getElementById('reasonsContainer');
    const reasonCount = container.getElementsByClassName('reason-item').length;
    
    if (reasonCount >= 5) {
        alert('Maksimum 5 sebab sahaja dibenarkan');
        return;
    }
    
    const reasonHtml = `
        <div class="reason-item mb-2">
            <div class="input-group">
                <input type="text" name="reasons[]" class="form-control" required 
                       placeholder="Sila nyatakan sebab">
                <button type="button" class="btn btn-outline-secondary" onclick="removeReason(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', reasonHtml);
    updateAddButton();
}

function removeReason(button) {
    button.closest('.reason-item').remove();
    updateAddButton();
}

function updateAddButton() {
    const container = document.getElementById('reasonsContainer');
    const reasonCount = container.getElementsByClassName('reason-item').length;
    const addButton = document.getElementById('addReasonBtn');
    
    addButton.disabled = reasonCount >= 5;
}
</script>

<style>
.reason-item .input-group {
    max-width: 600px;
}

.form-label {
    font-weight: 500;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 