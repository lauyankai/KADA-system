<?php 
    $title = 'Pending Member List';
    require_once '../app/views/layouts/header.php';
?>

    <div class="container mt-4">
        <!-- Main Content -->
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="card-title">
                        <i class="bi bi-people-fill me-2"></i>Senarai Ahli
                    </h2>
                </div>

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
                                <td class="text-center action-buttons">
                                    <a href="/member/view/<?= $member['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye-fill"></i> Lihat
                                    </a>
                                    <a href="/member/approve/<?= $member['id']; ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle-fill"></i> Lulus
                                    </a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Tolak</a>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php require_once '../app/views/layouts/footer.php'; ?>
