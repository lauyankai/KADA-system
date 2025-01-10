<?php 
    $title = 'Senarai Ahli';
    require_once '../app/views/layouts/header.php';
?>

<div class="admin-dashboard">
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h2 class="mb-1">Senarai Ahli</h2>
            </div>
            <div class="header-actions">
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-file-pdf me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel me-2"></i>Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <h3><?= count($pendingmember) ?></h3>
                    <p>Jumlah Ahli</p>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-details">
                    <h3><?= count(array_filter($pendingmember, fn($m) => $m['status'] === 'Pending' || !$m['status'])) ?></h3>
                    <p>Pending</p>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3><?= count(array_filter($pendingmember, fn($m) => $m['status'] === 'Lulus')) ?></h3>
                    <p>Diluluskan</p>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-details">
                    <h3><?= count(array_filter($pendingmember, fn($m) => $m['status'] === 'Tolak')) ?></h3>
                    <p>Ditolak</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
            <div class="alerts-wrapper">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-modern alert-danger">
                        <i class="bi bi-x-octagon"></i>
                        <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-modern alert-success">
                        <i class="bi bi-check-circle"></i>
                        <span><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Data Card -->
        <div class="data-card">
            <!-- Search and Filters -->
            <div class="data-card-header">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="search-input" placeholder="Cari ahli...">
                </div>
                <div class="filters-wrapper">
                    <select class="filter-select">
                        <option value="">Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Tolak">Tolak</option>
                    </select>
                    <select class="filter-select">
                        <option value="">Jantina</option>
                        <option value="Lelaki">Lelaki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No. KP</th>
                            <th>Jantina</th>
                            <th>Jawatan</th>
                            <th>Gaji (RM)</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingmember as $member): ?>
                        <tr>
                            <td>
                                <div class="member-info">
                                    <div class="member-avatar">
                                        <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                    </div>
                                    <div class="member-details">
                                        <div class="member-name"><?= htmlspecialchars($member['name']); ?></div>
                                        <div class="member-email"><?= htmlspecialchars($member['email'] ?? ''); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($member['ic_no']); ?></td>
                            <td>
                                <span class="gender-badge <?= strtolower($member['gender']) ?>">
                                    <?= htmlspecialchars($member['gender']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($member['position']); ?></td>
                            <td><?= number_format($member['monthly_salary'], 2); ?></td>
                            <td>
                                <span class="status-badge <?= strtolower($member['status'] ?? 'pending') ?>">
                                    <?= $member['status'] ?? 'Pending' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn view" 
                                            onclick="window.location.href='/admin/view/<?= $member['id']; ?>'"
                                            title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="action-btn approve" 
                                            onclick="confirmAction('approve', <?= $member['id']; ?>)"
                                            title="Lulus">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="action-btn reject" 
                                            onclick="confirmAction('reject', <?= $member['id']; ?>)"
                                            title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <span class="pagination-info">
                    Menunjukkan 1-<?= count($pendingmember) ?> daripada <?= count($pendingmember) ?> rekod
                </span>
                <div class="pagination">
                    <button class="page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.total .stat-icon { background: #e8f0fe; color: #1a73e8; }
.pending .stat-icon { background: #fff4e5; color: #f59e0b; }
.approved .stat-icon { background: #e6f6f0; color: #10b981; }
.rejected .stat-icon { background: #fee2e2; color: #ef4444; }

.stat-details h3 {
    font-size: 24px;
    margin: 0;
    font-weight: 600;
}

.stat-details p {
    margin: 0;
    color: #6b7280;
}

.data-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

.data-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-wrapper {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-wrapper i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    outline: none;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26,115,232,0.1);
}

.filters-wrapper {
    display: flex;
    gap: 0.75rem;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    outline: none;
    background: white;
    min-width: 120px;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table th {
    background: #f9fafb;
    padding: 1rem;
    font-weight: 600;
    text-align: left;
    color: #374151;
}

.modern-table td {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
}

.member-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.member-avatar {
    width: 40px;
    height: 40px;
    background: #e8f0fe;
    color: #1a73e8;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.member-name {
    font-weight: 500;
    color: #111827;
}

.member-email {
    font-size: 0.875rem;
    color: #6b7280;
}

.gender-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.gender-badge.lelaki { background: #e8f0fe; color: #1a73e8; }
.gender-badge.perempuan { background: #fce7f3; color: #db2777; }

.status-badge.pending { background: #fff4e5; color: #f59e0b; }
.status-badge.lulus { background: #e6f6f0; color: #10b981; }
.status-badge.tolak { background: #fee2e2; color: #ef4444; }

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn.view { background: #f3f4f6; color: #374151; }
.action-btn.approve { background: #e6f6f0; color: #10b981; }
.action-btn.reject { background: #fee2e2; color: #ef4444; }

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.pagination {
    display: flex;
    gap: 0.5rem;
}

.page-btn {
    width: 32px;
    height: 32px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.page-btn.active {
    background: #1a73e8;
    color: white;
    border-color: #1a73e8;
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.alert-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    border: none;
}

.alert-modern i {
    font-size: 1.25rem;
}

@media (max-width: 768px) {
    .admin-dashboard {
        padding: 1rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .data-card-header {
        flex-direction: column;
    }

    .filters-wrapper {
        width: 100%;
    }

    .filter-select {
        flex: 1;
    }
}
</style>

<script>
function confirmAction(action, id) {
    const messages = {
        approve: 'Adakah anda pasti untuk meluluskan permohonan ini?',
        reject: 'Adakah anda pasti untuk menolak permohonan ini?'
    };
    
    if (confirm(messages[action])) {
        window.location.href = `/admin/${action}/${id}`;
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
