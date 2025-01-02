<?php 
    $title = 'Papan Pemuka Admin';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <!-- Welcome Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">
                <i class="bi bi-person-circle me-2"></i>Selamat Datang, 
                <?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username']) ?>
            </h3>
            <p class="text-muted mb-0">Sistem Pengurusan Koperasi</p>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="card bg-success text-white mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-piggy-bank me-2"></i>Jumlah Simpanan
            </h5>
            <h3 class="mb-0">RM <?= number_format($totalSavings, 2) ?></h3>
        </div>
    </div>

    <!-- Savings Management Section -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-gear me-2"></i>Pengurusan Simpanan
                </h5>
                <div>
                    <a href="/admin/savings/apply" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Akaun Simpanan Baru
                    </a>
                    <a href="/admin/savings/recurring" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-repeat me-2"></i>Tetapan Bayaran Berulang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Savings Accounts -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-piggy-bank me-2"></i>Akaun Simpanan Terkini
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ahli</th>
                            <th>Jumlah Sasaran</th>
                            <th>Jumlah Semasa</th>
                            <th>Kemajuan</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSavings as $saving): ?>
                        <tr>
                            <td><?= htmlspecialchars($saving['member_number']) ?></td>
                            <td>RM <?= number_format($saving['target_amount'], 2) ?></td>
                            <td>RM <?= number_format($saving['current_amount'], 2) ?></td>
                            <td>
                                <?php $progress = ($saving['current_amount'] / $saving['target_amount']) * 100; ?>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: <?= $progress ?>%">
                                        <?= number_format($progress, 1) ?>%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $saving['status'] === 'active' ? 'success' : 'warning' ?>">
                                    <?= $saving['status'] === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/savings/view/<?= $saving['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye me-1"></i>Lihat
                                    </a>
                                    <a href="/admin/savings/deposit/<?= $saving['id'] ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah
                                    </a>
                                    <?php if ($saving['status'] === 'active'): ?>
                                        <a href="/admin/savings/deactivate/<?= $saving['id'] ?>" 
                                           class="btn btn-sm btn-warning"
                                           onclick="return confirm('Adakah anda pasti untuk nyahaktifkan akaun ini?')">
                                            <i class="bi bi-pause-circle me-1"></i>Nyahaktif
                                        </a>
                                    <?php else: ?>
                                        <a href="/admin/savings/activate/<?= $saving['id'] ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Adakah anda pasti untuk aktifkan akaun ini?')">
                                            <i class="bi bi-play-circle me-1"></i>Aktif
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Recurring Payments -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-arrow-repeat me-2"></i>Bayaran Berulang Terkini
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ahli</th>
                            <th>Jumlah</th>
                            <th>Kekerapan</th>
                            <th>Kaedah</th>
                            <th>Bayaran Seterusnya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRecurring as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['member_number']) ?></td>
                            <td>RM <?= number_format($payment['amount'], 2) ?></td>
                            <td><?= $payment['frequency'] === 'monthly' ? 'Bulanan' : 
                                   ($payment['frequency'] === 'biweekly' ? 'Dua Minggu' : 'Mingguan') ?></td>
                            <td><?= $payment['payment_method'] === 'bank_transfer' ? 'Pindahan Bank' : 'Potongan Gaji' ?></td>
                            <td><?= date('d/m/Y', strtotime($payment['next_payment_date'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $payment['status'] === 'active' ? 'success' : 'warning' ?>">
                                    <?= $payment['status'] === 'active' ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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

<?php if (isset($_SESSION['success']) && strpos($_SESSION['success'], 'simpanan') !== false): ?>
<!-- Recurring Payment Modal -->
<div class="modal fade" id="recurringPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tetapan Bayaran Berulang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/savings/recurring/setup" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayaran (RM)</label>
                        <input type="number" name="amount" class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kekerapan</label>
                        <select name="frequency" class="form-select" required>
                            <option value="weekly">Mingguan</option>
                            <option value="biweekly">Dua Minggu</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kaedah Bayaran</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Pindahan Bank</option>
                            <option value="salary_deduction">Potongan Gaji</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarikh Mula</label>
                        <input type="date" name="start_date" class="form-control" required 
                               min="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tetapkan Bayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('recurringPaymentModal'));
    modal.show();
});
</script>
<?php endif; ?>

<?php require_once '../app/views/layouts/footer.php'; ?> 