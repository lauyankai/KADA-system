<?php 
    $title = 'Senarai Ahli';
    require_once '../app/views/layouts/header.php';

function getBadgeClass($status) {
    switch ($status) {
        case 'Pending':
            return 'bg-warning';
        case 'Active':
            return 'bg-success';
        case 'Resigned':
            return 'bg-secondary';
        case 'Rejected':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'Pending':
            return 'Pending';
        case 'Active':
            return 'Ahli Aktif';
        case 'Resigned':
            return 'Berhenti';
        case 'Rejected':
            return 'Tolak';
        default:
            return $status;
    }
}
?>
<link rel="stylesheet" href="/css/admin.css">
<div class="admin-dashboard">
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h2 class="mb-1">Senarai Ahli</h2>
            </div>
            <div class="header-actions d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form id="exportForm" method="POST" style="display: none;">
                                <input type="hidden" name="export_type" value="">
                            </form>
                            <a href="/admin/export-pdf" class="dropdown-item">
                                <i class="bi bi-file-pdf me-2"></i>PDF
                            </a>
                        </li>
                        <li>
                            <a href="/admin/export-excel" class="dropdown-item">
                                <i class="bi bi-file-excel me-2"></i>Excel
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="/admin" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $stats['total'] ?></h3>
                    <p>Jumlah Ahli</p>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $stats['pending'] ?></h3>
                    <p>Pending</p>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $stats['active'] ?></h3>
                    <p>Ahli Aktif</p>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $stats['rejected'] ?></h3>
                    <p>Ditolak</p>
                </div>
            </div>

            <div class="stat-card resigned">
                <div class="stat-icon">
                    <i class="bi bi-person-x"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $stats['resigned'] ?></h3>
                    <p>Berhenti</p>
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
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Active">Ahli Aktif</option>
                        <option value="Resigned">Berhenti</option>
                        <option value="Rejected">Tolak</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>No. K/P</th>
                            <th>Jantina</th>
                            <th>Jawatan</th>
                            <th>Gaji</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($members as $member): 
                        ?>
                        <tr class="member-row" data-status="<?= $member['status'] ?>">
                            <td class="row-number"><?= $counter++ ?></td>
                            <td class="member-name"><?= htmlspecialchars($member['name']) ?></td>
                            <td><?= htmlspecialchars($member['ic_no']) ?></td>
                            <td><?= htmlspecialchars($member['gender']) ?></td>
                            <td><?= htmlspecialchars($member['position']) ?></td>
                            <td>RM <?= number_format($member['monthly_salary'], 2) ?></td>
                            <td>
                                <span class="status-badge badge <?= getBadgeClass($member['status']) ?>">
                                    <?= getStatusLabel($member['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="action-btn view" 
                                            onclick="window.location.href='/admin/view/<?= $member['id']; ?>'"
                                            title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($member['status'] === 'Pending'): ?>
                                        <button onclick="confirmAction('approve', <?= $member['id'] ?>)" 
                                                class="action-btn approve" 
                                                data-bs-toggle="tooltip" 
                                                title="Lulus">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button onclick="confirmAction('reject', <?= $member['id'] ?>)" 
                                                class="action-btn reject" 
                                                data-bs-toggle="tooltip" 
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php elseif ($member['status'] === 'Rejected'): ?>
                                        <button onclick="confirmAction('approve', <?= $member['id'] ?>)" 
                                                class="action-btn approve" 
                                                data-bs-toggle="tooltip" 
                                                title="Lulus">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    <?php endif; ?>
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
                    Menunjukkan 1-<?= count($members) ?> daripada <?= count($members) ?> rekod
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
    const rows = document.querySelectorAll('.member-row');
    let visibleCounter = 1;
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const numberCell = row.querySelector('.row-number');
        
        if (!status || rowStatus === status) {
            row.style.display = '';
            numberCell.textContent = visibleCounter++;
        } else {
            row.style.display = 'none';
        }
    });
}

function searchTable(query) {
    query = query.toLowerCase();
    const rows = document.querySelectorAll('.member-row');
    let visibleCounter = 1;
    
    rows.forEach(row => {
        const name = row.querySelector('.member-name').textContent.toLowerCase();
        const icNo = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const numberCell = row.querySelector('.row-number');
        
        if (name.includes(query) || icNo.includes(query)) {
            row.style.display = '';
            numberCell.textContent = visibleCounter++;
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>