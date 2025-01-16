<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'KADA System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shield-check me-2"></i>KADA System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/users"><i class="bi bi-people me-2"></i>Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/guest/create"><i class="bi bi-person-plus me-2"></i>Daftar Anggota</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/users/loans/details">
                    <i class="bi bi-cash-stack me-2"></i>Pinjaman

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/users/savings/page">
                    <i class="bi bi-piggy-bank me-2"></i> Simpanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users/savings/transfer">
                    <i class="bi bi-credit-card me-2"></i>Pembayaran   
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pembiayaan">
                    <i class="bi bi-receipt me-2"></i>Pembiayaan   
                       
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/pembiayaan">
                    <i class="bi bi-bar-chart me-2"></i>Pelaburan   
                       
                    </a>

                </ul>
                <div class="d-flex align-items-center">
                    <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    <a href="/auth/logout" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</body>