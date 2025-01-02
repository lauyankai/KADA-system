<?php 
    $title = 'Senarai Ahli';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-lg mb-4">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">
                <i class="bi bi-people-fill me-2"></i>Senarai Ahli
            </h2>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead>
                        <tr class="text-center">
                            <th><i class="bi bi-person me-2"></i>Nama</th>
                            <th><i class="bi bi-credit-card me-2"></i>No. KP</th>
                            <th><i class="bi bi-gender-ambiguous me-2"></i>Jantina</th>
                            <th><i class="bi bi-briefcase me-2"></i>Jawatan</th>
                            <th><i class="bi bi-cash me-2"></i>Gaji Bulanan</th>
                            <th><i class="bi bi-gear me-2"></i>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingregistermembers as $member): ?>
                        <tr>
                            <td><?= htmlspecialchars($member['name']); ?></td>
                            <td><?= htmlspecialchars($member['ic_no']); ?></td>
                            <td><?= htmlspecialchars($member['gender']); ?></td>
                            <td><?= htmlspecialchars($member['position']); ?></td>
                            <td class="text-end">RM <?= number_format($member['monthly_salary'], 2); ?></td>
                            <td class="text-center">
                                <a href="/member-dashboard/<?= $member['id'] ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-speedometer2 me-1"></i>Papan Pemuka
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Admin Dashboard Access -->
            <div class="text-center mt-4">
                <a href="/admin/dashboard" class="btn btn-lg btn-primary">
                    <i class="bi bi-speedometer2 me-2"></i>Papan Pemuka Admin
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
