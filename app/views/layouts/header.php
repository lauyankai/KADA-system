<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Koperasi KADA' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shield-check me-2"></i>KADA System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Tentang Kami -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="tentangKamiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-info-circle me-2"></i>Tentang Kami
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="tentangKamiDropdown">
                            <li><a class="dropdown-item" href="/about/vision"><i class="bi bi-eye"></i>Visi & Misi</a></li>
                            <li><a class="dropdown-item" href="/about/history"><i class="bi bi-book"></i>Sejarah</a></li>
                            <li><a class="dropdown-item" href="/about/facts"><i class="bi bi-bar-chart"></i>Fakta & Angka</a></li>
                        </ul>
                    </li>

                    <!-- Perkhidmatan -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perkhidmatanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-wrench me-2"></i>Perkhidmatan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="perkhidmatanDropdown">
                            <li><a class="dropdown-item" href="info/loantype"><i class="bi bi-cash"></i>Pinjaman</a></li>
                            <li><a class="dropdown-item" href="/users/"><i class="bi bi-graph-up"></i>Pelaburan</a></li>
                            <li><a class="dropdown-item" href="/users/savings"><i class="bi bi-piggy-bank"></i>Tabung</a></li>
                            <li><a class="dropdown-item" href="/users"><i class="bi bi-credit-card"></i>Pembiayaan</a></li>
                        </ul>
                    </li>

                    <!-- Hubungi Kami -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="hubungiKamiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-envelope"></i>Hubungi Kami
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="hubungiKamiDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-geo-alt me-2"></i>127, Jalan Dato'Lundang 15710 Kota Bharu, Kelantan.
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="tel:+6097455388">
                                    <i class="bi bi-telephone me-2"></i>+609-7455388
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="mailto:prokada@kada.gov.my">
                                    <i class="bi bi-envelope me-2"></i>prokada@kada.gov.my
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-clock me-2"></i>Waktu Operasi
                                </a>
                            </li>
                        </ul>
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