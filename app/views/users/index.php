<?php 
    $title = 'List of Users';
    require_once '../app/views/layouts/header.php';
?>

<<<<<<< HEAD
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
                                    <a href="/users/delete/<?php echo $member['id']; ?>" class="btn btn-danger" onclick="return confirm('Adakah anda pasti untuk menolak permohonan ini?')">Tolak</a>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
=======
<div class="container mt-4">
    <h2>Senarai Pengguna</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
>>>>>>> 1dfbf68df6007cc594c5aa7728e7bc9ceb2b7a24
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. KP</th>
                    <th>Jantina</th>
                    <th>Bangsa</th>
                    <th>Jawatan</th>
                    <th>Gred</th>
                    <th>Gaji Bulanan</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingregistermembers as $member): ?>
                    <tr>
                        <td><?= htmlspecialchars($member['name'] ?? 'error') ?></td>
                        <td><?= htmlspecialchars($member['ic_no'] ?? 'error') ?></td>
                        <td><?= htmlspecialchars($member['gender'] ?? 'error') ?></td>
                        <td><?= htmlspecialchars($member['race'] ?? 'error') ?></td>
                        <td><?= htmlspecialchars($member['position'] ?? 'error') ?></td>
                        <td><?= htmlspecialchars($member['grade'] ?? 'error') ?></td>
                        <td><?= isset($member['monthly_salary']) ? 'RM ' . number_format($member['monthly_salary'], 2) : '-' ?></td>
                        <td>
                            <a href="/users/details/<?= $member['id'] ?>" class="btn btn-sm btn-info text-white me-1">
                                <i class="bi bi-info-circle me-1"></i>Butiran
                            </a>
                            <a href="/users/approve/<?= $member['id'] ?>" class="btn btn-sm btn-success me-1" 
                               onclick="return confirm('Adakah anda pasti untuk meluluskan permohonan ini?')">
                                <i class="bi bi-check-circle me-1"></i>Lulus
                            </a>
                            <a href="/users/reject/<?= $member['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Adakah anda pasti untuk menolak permohonan ini?')">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Savings Dashboard Redirect Button -->
    <div class="text-center mt-4">
        <a href="/admin/savings" class="btn btn-success btn-lg">
            <i class="bi bi-piggy-bank me-2"></i>Ke Papan Pemuka Simpanan
        </a>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
