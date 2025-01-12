<?php 
    $title = 'Senarai Ahli';
    require_once '../app/views/layouts/header.php';
?>
<link rel="stylesheet" href="/css/admin.css">
<div class="admin-dashboard">
    <div class="main-content">
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
                    <input type="text" class="search-input" placeholder="Cari ahli..." onkeyup="searchTable(this.value)">
                </div>
                <div class="filters-wrapper">
                    <select class="filter-select" onchange="filterTable(this.value)">
                        <option value="" disabled selected>Status</option>
                        <option value="">Semua</option>
                        <option value="Pending">Pending</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Tolak">Tolak</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>No</th>
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
                            <td><?= $member['id'] ?></td>
                            <td>
                                <div class="member-info">
                                    <div class="member-details">
                                        <div class="member-name"><?= htmlspecialchars($member['name']); ?></div>
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

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

function filterTable(status) {
    const rows = document.querySelectorAll('.modern-table tbody tr');
    rows.forEach(row => {
        const statusCell = row.querySelector('.status-badge');
        if (!status || statusCell.textContent.trim() === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchTable(query) {
    query = query.toLowerCase();
    const rows = document.querySelectorAll('.modern-table tbody tr');
                    
    rows.forEach(row => {
        const name = row.querySelector('.member-name').textContent.toLowerCase();
        const icNo = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (name.includes(query) || icNo.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
