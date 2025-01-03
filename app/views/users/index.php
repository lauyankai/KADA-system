<?php 
    $title = 'List of Users';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <h2>Senarai Pengguna</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
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
