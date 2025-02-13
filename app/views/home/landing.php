<?php 
    $title = 'Welcome to KADA';
    require_once '../app/views/layouts/header.php';
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert-backdrop"></div>
    <div class="alert alert-success alert-dismissible fade show alert-floating" role="alert">
        <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']); 
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<link rel="stylesheet" href="/css/landing.css">
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
                    Platform Digital <span class="text-gradient">Koperasi</span> KADA Kelantan
                </h1>
                <p class="lead mb-5">Urus semua urusan koperasi dengan mudah dan selamat melalui platform digital kami.</p>
                <div class="d-flex gap-3">
                    <a href="/auth/login" class="btn btn-gradient btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk
                    </a>
                    <a href="/guest/create" class="btn btn-outline-primary btn-lg">Daftar Sekarang</a>
                    <a href="/guest/check-status" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-search me-2"></i>Semak Status
                </a>
                </div>
            </div>
            <div class="col-lg-6 position-relative d-none d-lg-block">
                <img src="/img/logo-kada.png" alt="KADA" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h2 class="mb-4">Laporan Tahunan</h2>
            <p class="text-muted mb-5">Akses laporan tahunan koperasi untuk mendapatkan maklumat terperinci tentang prestasi dan perkembangan kami.</p>
        </div>
    </div>

    <?php if (!empty($annualReports)): ?>
        <div class="row g-4">
            <?php foreach ($annualReports as $report): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-file-earmark-pdf text-danger fs-3 me-3"></i>
                                <div>
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($report['title']) ?></h5>
                                    <p class="text-muted small mb-0">Tahun <?= htmlspecialchars($report['year']) ?></p>
                                </div>
                            </div>
                            <?php if (!empty($report['description'])): ?>
                                <p class="card-text small text-muted"><?= htmlspecialchars($report['description']) ?></p>
                            <?php endif; ?>
                            <div class="mt-3">
                                <a href="<?= htmlspecialchars($report['file_path']) ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-download me-2"></i>Muat Turun
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <?= date('d/m/Y', strtotime($report['uploaded_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-muted">
            <i class="bi bi-info-circle fs-3 mb-3"></i>
            <p>Tiada laporan tahunan pada masa ini.</p>
        </div>
    <?php endif; ?>
</div>

<div class="quick-links">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <a href="#" class="quick-link-item" data-bs-toggle="modal" data-bs-target="#calculatorModal">
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
                <a href="/info/loantype" class="quick-link-item">
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
                        <a href="/info/loantype" class="service-link">
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

<!-- <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary py-3">
                    <h5 class="card-title mb-0 text-center text-white">
                        <i class="bi bi-search me-2"></i>
                        Semakan Status Permohonan Menjadi Anggota
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <p class="text-muted">
                            Sila masukkan nama penuh anda untuk menyemak status permohonan keahlian koperasi
                        </p>
                    </div>
                    <form id="enquiryForm" onsubmit="checkStatus(event)">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-person-badge me-2"></i>
                                Nama Penuh (seperti dalam Kad Pengenalan)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" 
                                       class="form-control form-control-lg text-uppercase" 
                                       id="name" 
                                       name="name" 
                                       style="text-transform: uppercase;" 
                                       oninput="this.value = this.value.toUpperCase()" 
                                       placeholder="Contoh: AHMAD BIN ABDULLAH"
                                       required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i>
                                Semak Status
                            </button>
                        </div>
                    </form>
                    <div id="statusResult" class="mt-4" style="display: none;">
                        <div class="alert rounded-4 shadow-sm border-0" role="alert"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Calculator Modal -->
<div class="modal fade" id="calculatorModal" tabindex="-1" aria-labelledby="calculatorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="calculatorModalLabel">
                        <i class="bi bi-calculator me-2"></i>Kalkulator Ansuran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loanCalculatorForm">
                        <div class="mb-3">
                            <label class="form-label">Amaun Dipohon (RM)</label>
                            <input type="number" id="loanAmount" class="form-control" required step="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tempoh Pembiayaan (Bulan)</label>
                            <input type="number" id="loanDuration" class="form-control" required step="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kadar Keuntungan</label>
                            <input type="text" class="form-control" value="4.2% setahun" readonly>
                        </div>
                        <div class="calculated-result p-3 rounded bg-light mb-3" style="display: none;">
                            <h6 class="text-muted mb-2">Ansuran Bulanan:</h6>
                            <h3 class="text-primary mb-0">RM <span id="monthlyPayment">0.00</span></h3>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-calculator me-2"></i>Kira Ansuran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.querySelector('.alert-floating');
        const backdrop = document.querySelector('.alert-backdrop');
        
        if (alert && backdrop) {
            // Handle manual dismissal
            alert.querySelector('.btn-close').addEventListener('click', () => {
                alert.remove();
                backdrop.remove();
            });
            
            // Click on backdrop to dismiss
            backdrop.addEventListener('click', () => {
                alert.remove();
                backdrop.remove();
            });
        }
    });

    // function checkStatus(event) {
    //     event.preventDefault();
        
    //     const name = document.getElementById('name').value.toUpperCase();
    //     const statusResult = document.getElementById('statusResult');
    //     const alertDiv = statusResult.querySelector('.alert');
        
    //     console.log('Sending request for name:', name); // Debug log
        
    //     fetch('/guest/checkStatus', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //         },
    //         body: JSON.stringify({ name: name })
    //     })
    //     .then(response => {
    //         console.log('Response status:', response.status); // Debug log
    //         return response.json();
    //     })
    //     .then(data => {
    //         console.log('Response data:', data); // Debug log
    //         statusResult.style.display = 'block';
            
    //         if (data.success) {
    //             alertDiv.className = `alert ${getAlertClass(data.status)}`;
    //             alertDiv.textContent = data.message;
    //         } else {
    //             alertDiv.className = 'alert alert-danger';
    //             alertDiv.textContent = data.error || 'An error occurred while checking the status.';
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error); // Debug log
    //         statusResult.style.display = 'block';
    //         alertDiv.className = 'alert alert-danger';
    //         alertDiv.textContent = 'An error occurred while checking the status.';
    //     });
    // }

    // function getAlertClass(status) {
    //     switch(status) {
    //         case 'Pending':
    //             return 'alert-warning bg-warning-subtle border-0';
    //         case 'Lulus':
    //         case 'Active':
    //             return 'alert-success bg-success-subtle border-0';
    //         case 'Tolak':
    //         case 'Inactive':
    //             return 'alert-danger bg-danger-subtle border-0';
    //         case 'not_found':
    //             return 'alert-info bg-info-subtle border-0';
    //         default:
    //             return 'alert-secondary bg-secondary-subtle border-0';
    //     }
    // }

    document.getElementById('loanCalculatorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(document.getElementById('loanAmount').value);
        const duration = parseInt(document.getElementById('loanDuration').value);
        const annualRate = 4.2 / 100; // 4.2%
        const monthlyRate = annualRate / 12;
        
        // Monthly Payment Formula: P * r * (1 + r)^n / ((1 + r)^n - 1)
        // Where: P = Principal, r = Monthly Interest Rate, n = Number of Payments
        const monthlyPayment = amount * monthlyRate * Math.pow(1 + monthlyRate, duration) 
                              / (Math.pow(1 + monthlyRate, duration) - 1);
        
        document.querySelector('.calculated-result').style.display = 'block';
        document.getElementById('monthlyPayment').textContent = monthlyPayment.toFixed(2);
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 