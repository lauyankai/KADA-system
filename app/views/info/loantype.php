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
        <!-- Al Bai Loan -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3 class="card-title mb-0">Pinjaman Al Bai</h3>
                    </div>
                    <p class="card-text">Pembiayaan berdasarkan konsep jual beli yang mematuhi prinsip Syariah.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Tempoh: 1 - 5 tahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Jumlah: RM1,000 - RM100,000</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Al Innah Loan -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <h3 class="card-title mb-0">Pinjaman Al Innah</h3>
                    </div>
                    <p class="card-text">Pembiayaan peribadi yang fleksibel mengikut keperluan anda.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Tempoh: 1 - 5 tahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Jumlah: RM1,000 - RM50,000</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Skim Khas -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-star"></i>
                        </div>
                        <h3 class="card-title mb-0">Pinjaman Skim Khas</h3>
                    </div>
                    <p class="card-text">Skim pembiayaan khas untuk keperluan tertentu.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Tempoh: 1 - 3 tahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Jumlah: RM1,000 - RM30,000</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Road Tax & Insurance -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h3 class="card-title mb-0">Pinjaman Road Tax & Insuran</h3>
                    </div>
                    <p class="card-text">Pembiayaan khas untuk pembayaran road tax dan insuran kenderaan.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 4.2% setahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Tempoh: 1 - 2 tahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Jumlah: RM500 - RM10,000</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Al Qardhul Hasan -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h3 class="card-title mb-0">Pinjaman Al Qardhul Hasan</h3>
                    </div>
                    <p class="card-text">Pembiayaan kebajikan tanpa keuntungan untuk membantu ahli yang memerlukan.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Kadar keuntungan: 0%</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Tempoh: 1 - 2 tahun</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Jumlah: RM500 - RM5,000</li>
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