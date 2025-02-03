<?php 
    $title = 'Maklumat Pembiayaan';
    require_once '../app/views/layouts/header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-gradient">Jenis-jenis Pembiayaan</h1>
        <p class="lead">Pilihan pembiayaan yang sesuai dengan keperluan anda</p>
    </div>

    <div class="row g-4">
        <!-- Al-Baiubithaman Ajil -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="card-title mb-0">Skim Pembiayaan Al-Baiubithaman Ajil</h3>
                    </div>
                    <p class="card-text">Pembiayaan untuk tujuan perniagaan yang mematuhi prinsip Syariah.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Had maksimum: RM15,000.00</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bai Al-Inah -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <h3 class="card-title mb-0">Skim Pembiayaan Bai Al-Inah</h3>
                    </div>
                    <p class="card-text">Pembiayaan peribadi yang fleksibel mengikut keperluan anda.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Had maksimum: RM10,000.00</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Membaikpulih Kenderaan -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h3 class="card-title mb-0">Skim Pembiayaan Membaikpulih Kenderaan</h3>
                    </div>
                    <p class="card-text">Pembiayaan untuk membaiki dan menyelenggara kenderaan anda.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Had maksimum: RM2,000.00</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cukai Jalan dan Insuran -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <h3 class="card-title mb-0">Skim Pembiayaan Cukai Jalan & Insuran</h3>
                    </div>
                    <p class="card-text">Pembiayaan untuk pembayaran cukai jalan dan insuran kenderaan.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-info-circle text-info me-2"></i>Jumlah pembiayaan tertakluk kepada kelulusan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="/auth/login" class="btn btn-gradient btn-lg">
            <i class="bi bi-file-earmark-text me-2"></i>Mohon Sekarang
        </a>
    </div>
</div>

<div class="container mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <a href="/" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.hover-shadow {
    transition: transform 0.2s, box-shadow 0.2s;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?>