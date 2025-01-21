<?php 
    $title = 'Laporan Tahunan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
                    <i class="bi bi-file-earmark-text me-2"></i>Laporan Tahunan
                </h4>
                <div>
                    <a href="/admin" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="/admin/annual-reports/upload" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>Muat Naik
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Judul</th>
                            <th>Keterangan</th>
                            <th>Dimuat Naik Oleh</th>
                            <th>Tarikh</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= htmlspecialchars($report['year']) ?></td>
                                <td><?= htmlspecialchars($report['title']) ?></td>
                                <td><?= htmlspecialchars($report['description']) ?></td>
                                <td><?= htmlspecialchars($report['uploader_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($report['uploaded_at'])) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($report['file_path']) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       target="_blank">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button onclick="deleteReport(<?= $report['id'] ?>)" 
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteReport(id) {
    if (confirm('Adakah anda pasti untuk memadam laporan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/annual-reports/delete/${id}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 