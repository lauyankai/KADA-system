<?php $title = 'Muat Turun Penyata'; ?>

<div class="container mt-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">
                <i class="bi bi-download me-2"></i>Muat Turun Penyata
            </h3>
            <p class="text-muted mb-0">Muat turun penyata bulanan/tahunan anda dalam format PDF</p>
        </div>
    </div>

    <!-- Statement Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="/users/statements/generate-pdf" method="GET" class="row g-3">
                <!-- Account Type -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Jenis Akaun</label>
                    <select name="account_type" class="form-select" required>
                        <option value="savings">Akaun Simpanan</option>
                        <option value="loan">Akaun Pinjaman</option>
                    </select>
                </div>

                <!-- Statement Type -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Jenis Penyata</label>
                    <select name="statement_type" class="form-select" required>
                        <option value="monthly">Penyata Bulanan</option>
                        <option value="annual">Penyata Tahunan</option>
                    </select>
                </div>

                <!-- Year Selection -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tahun</label>
                    <select name="year" class="form-select" required>
                        <?php 
                        $currentYear = date('Y');
                        for($year = $currentYear; $year >= 2020; $year--) {
                            echo "<option value='$year'>$year</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Month Selection -->
                <div class="col-md-4" id="monthSelect">
                    <label class="form-label fw-bold">Bulan</label>
                    <select name="month" class="form-select">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Mac</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Jun</option>
                        <option value="7">Julai</option>
                        <option value="8">Ogos</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Disember</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-pdf me-2"></i>Muat Turun PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Statements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Penyata Yang Tersedia</h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Akaun</th>
                            <th>Tempoh</th>
                            <th>Tarikh Jana</th>
                            <th>Saiz Fail</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($statements) && !empty($statements)): ?>
                            <?php foreach ($statements as $statement): ?>
                                <tr>
                                    <td>
                                        <?php if ($statement->account_type === 'savings'): ?>
                                            <span class="badge bg-success">Simpanan</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Pinjaman</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($statement->period) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($statement->generated_at)) ?></td>
                                    <td><?= formatFileSize($statement->file_size) ?></td>
                                    <td>
                                        <a href="/users/statements/download/<?= $statement->id ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-download me-1"></i>Muat Turun
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Tiada penyata tersedia untuk dimuat turun
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle month selection based on statement type
document.querySelector('select[name="statement_type"]').addEventListener('change', function() {
    const monthSelect = document.getElementById('monthSelect');
    if (this.value === 'annual') {
        monthSelect.style.display = 'none';
    } else {
        monthSelect.style.display = 'block';
    }
});

// Helper function for file size formatting
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['error']); endif; ?>