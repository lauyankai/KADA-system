<?php 
    $title = 'Welcome to KADA';
    require_once '../app/views/layouts/header.php';
?>

<!-- Hero Section -->
<div class="animated-hero min-vh-100 d-flex align-items-center">
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="hero-badge">Koperasi Kakitangan KADA</span>
                <h1 class="display-4 fw-bold mb-4">
                    Platform Digital <span class="text-gradient">Koperasi</span> Untuk Masa Depan
                </h1>
                <p class="lead mb-5">Urus semua urusan koperasi anda dengan mudah dan selamat melalui platform digital kami.</p>
                <div class="d-flex gap-3">
                    <a href="/login" class="btn btn-gradient btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk
                    </a>
                    <a href="/users/create" class="btn btn-outline-primary btn-lg">Daftar Sekarang</a>
                </div>
            </div>
            <div class="col-lg-6 position-relative d-none d-lg-block">
                <img src="/img/hero-illustration.svg" alt="KADA" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Quick Links Section -->
<div class="quick-links">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <a href="#" class="quick-link-item">
                    <i class="bi bi-calculator"></i>
                    <span>Kalkulator</span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#" class="quick-link-item">
                    <i class="bi bi-credit-card"></i>
                    <span>Pembayaran</span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#" class="quick-link-item">
                    <i class="bi bi-person-badge"></i>
                    <span>Pinjaman</span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#" class="quick-link-item">
                    <i class="bi bi-file-text"></i>
                    <span>Borang</span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#" class="quick-link-item">
                    <i class="bi bi-percent"></i>
                    <span>Kadar Yuran</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="services-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Perkhidmatan Kami</h2>
            <div class="header-line"></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="service-card-3d">
                    <div class="card-content">
                        <div class="service-icon gradient-1">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3>Keahlian Atas Talian</h3>
                        <p>Daftar dan urus keahlian anda secara dalam talian dengan mudah dan pantas.</p>
                        <a href="#" class="service-link">
                            Ketahui Lebih Lanjut <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="service-card-3d">
                    <div class="card-content">
                        <div class="service-icon gradient-2">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3>Pinjaman</h3>
                        <p>Mohon pinjaman dengan kadar faedah yang kompetitif dan proses kelulusan yang cepat.</p>
                        <a href="#" class="service-link">
                            Ketahui Lebih Lanjut <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="service-card-3d">
                    <div class="card-content">
                        <div class="service-icon gradient-3">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Pelaburan</h3>
                        <p>Tingkatkan nilai pelaburan anda dengan portfolio pelaburan yang pelbagai.</p>
                        <a href="#" class="service-link">
                            Ketahui Lebih Lanjut <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<!-- <div class="features-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-6 fw-bold mb-4">Kenapa Pilih Kami?</h2>
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <h4>Keselamatan Terjamin</h4>
                        <p>Sistem keselamatan yang diiktiraf dengan encryption data terkini.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-lightning-charge"></i>
                    <div>
                        <h4>Proses Pantas</h4>
                        <p>Proses permohonan dan kelulusan yang cepat dan efisien.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-headset"></i>
                    <div>
                        <h4>Sokongan 24/7</h4>
                        <p>Khidmat sokongan pelanggan yang sedia membantu bila-bila masa.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="/img/features-illustration.svg" alt="Features" class="img-fluid">
            </div>
        </div>
    </div>
</div> -->

<?php require_once '../app/views/layouts/footer.php'; ?> 