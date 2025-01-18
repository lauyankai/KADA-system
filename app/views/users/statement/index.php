<?php 
    $title = 'Penyata Akaun';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>Penyata Akaun
                </h4>
                <a href="/users/statements/generate" class="btn btn-success">
                    <i class="bi bi-plus-lg me-2"></i>Jana Penyata Baru
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($statements)): ?>
                <div class="alert alert-info">
                    Tiada penyata dijumpai. Sila jana penyata baru.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Rujukan</th>
                                <th>Tarikh</th>
                                <th>Status</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statements as $statement): ?>
                                <tr>
                                    <td><?= htmlspecialchars($statement['reference_no']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($statement['created_at'])) ?></td>
                                    <td>
                                        <?php if ($statement['status'] === 'generated'): ?>
                                            <span class="badge bg-success">Baru</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Dimuat turun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/users/statements/download/<?= $statement['id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-download me-1"></i>Muat turun
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 