<?php $title = 'Sejarah KADA'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5">
                    <!-- Header with decorative line -->
                    <h2 class="card-title text-center mb-4 position-relative">
                        <i class="bi bi-book me-2 text-primary"></i>
                        <span class="fw-bold">Sejarah KADA</span>
                        <div class="position-relative mt-3">
                            <hr class="bg-primary" style="height: 2px; width: 50%; margin: 0 auto;">
                            <div class="position-absolute top-50 start-50 translate-middle bg-white px-3">
                                <i class="bi bi-star-fill text-primary"></i>
                            </div>
                        </div>
                    </h2>

                    <!-- History Content -->
                    <div class="history-content">
                        <div class="timeline">
                            <!-- First Section -->
                            <div class="timeline-item mb-4">
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-calendar-event me-2"></i>30 Mac 1972
                                        </h5>
                                        <p class="mb-0">
                                            Disempurnakan penubuhannya melalui Akta 69, Akta Lembaga Pertanian Kemubu, 1972 dan dilancarkan dengan rasminya oleh Y.A.B. Tun Hj. Abdul Razak bin Hussein, Perdana Menteri Malaysia pada 2 Mac 1973.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Section -->
                            <div class="timeline-item mb-4">
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-calendar-event me-2"></i>1 Ogos 1972
                                        </h5>
                                        <p class="mb-0">
                                            Setelah KADA diwujudkan, Kerajaan Negeri Kelantan pula menyusuli tindakan dengan meluluskan Enakmen Pihak Berkuasa Kemajuan Pertanian Kemubu, 1972 (Enakmen no.2 Tahun 1972 Kelantan) membolehkan Menteri Pertanian dan Perikanan melaksanakan Akta Lembaga Kemajuan Pertanian Kemubu, 1972.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Third Section -->
                            <div class="timeline-item">
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-geo-alt me-2"></i>Kampung Kemubu
                                        </h5>
                                        <p class="mb-0">
                                            Kampung Kemubu terletak di tebing Sungai Kelantan, 30km dari Kota Bharu telah disemadikan namanya dalam lipatan sejarah KADA. Di situlah terbina sebuah rumah pam membekalkan air ke Rancangan Pengairan Kemubu (RPK), rancangan terbesar dalam gugusan rancangan-rancangan pengairan lain yang dipersatukan di bawah kuasa pengendalian KADA.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back button -->
                    <div class="text-center mt-4">
                        <a href="/" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add custom styles -->
<style>
    .timeline-item {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid #0d6efd;
        transition: transform 0.3s ease;
    }

    .timeline-item:hover {
        transform: translateX(10px);
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
