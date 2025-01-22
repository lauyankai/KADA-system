<?php 
    $title = 'Muat Naik Laporan Tahunan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <i class="bi bi-upload me-2"></i>Muat Naik Laporan Tahunan
                </h4>
                <a href="/admin/annual-reports" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <form action="/admin/annual-reports/upload" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-select" required>
                            <option value="">Pilih Tahun</option>
                            <?php 
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= 2000; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                }
                            ?>
                        </select>
                        <div class="invalid-feedback">
                            Sila pilih tahun laporan
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback">
                            Sila masukkan keterangan laporan
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Fail Laporan (PDF)</label>
                        <input type="file" 
                               name="report_file" 
                               class="form-control" 
                               accept=".pdf"
                               required>
                        <div class="invalid-feedback">
                            Sila pilih fail laporan (format PDF sahaja)
                        </div>
                        <div class="form-text">
                            Hanya fail PDF diterima. Saiz maksimum: 10MB
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload me-2"></i>Muat Naik
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'

    var forms = document.querySelectorAll('.needs-validation')

    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()

// File size validation
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes

    if (file.size > maxSize) {
        alert('Fail terlalu besar. Saiz maksimum adalah 10MB.');
        this.value = ''; // Clear the input
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 