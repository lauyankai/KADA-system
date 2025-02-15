<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Koperasi KADA' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/img/logo-kada.png" alt="Koperasi KADA" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/about/history">
                            <i class="bi bi-book me-2"></i>Sejarah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about/facts">
                            <i class="bi bi-bar-chart me-2"></i>Fakta & Angka
                        </a>
                    </li>
                </ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="d-flex align-items-center">
                    <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    <a href="/auth/logout" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

</body>
</html>