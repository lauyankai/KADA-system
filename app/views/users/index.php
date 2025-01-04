<?php 
    $title = 'List of Users';
    require_once '../app/views/layouts/header.php';
?>


    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
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
                                <th>Status</th>
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
                                    <a href="/users/approve/<?= $member['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Adakah anda pasti untuk meluluskan permohonan ini?')">
                                        <i class="bi bi-check-circle-fill"></i> Lulus
                                    </a>
                                    <a href="/users/reject/<?= $member['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Adakah anda pasti untuk menolak permohonan ini?')">
                                        <i class="bi bi-x-circle-fill"></i> Tolak
                                    </a>
                                </td>
                                <td><?= $member['status'] ?? 'Pending' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>                       
    </div>

    <!-- Savings Dashboard Redirect Button -->
    <div class="text-center mt-4">
        <a href="/admin/savings" class="btn btn-success btn-lg">
            <i class="bi bi-piggy-bank me-2"></i>Ke Papan Pemuka Simpanan
        </a>
    </div>


<?php require_once '../app/views/layouts/footer.php'; ?>
