<?php 
    $title = 'Profil Saya';
    require_once '../app/views/layouts/header.php';
?>

<div class="container-fluid mt-4 mb-4">
    <div class="row g-4">
        <!-- Profile Overview Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <div class="profile-image mb-3">
                            <i class="bi bi-person-circle display-1 text-primary"></i>
                        </div>
                        <h4 class="mb-1"><?= htmlspecialchars($member->name) ?></h4>
                        <p class="text-muted mb-2">ID Ahli: <?= htmlspecialchars($member->member_id) ?></p>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>Ahli Aktif
                        </span>
                    </div>

                    <div class="border-top pt-4">
                        <div class="row text-start g-4">
                            <div class="col-6">
                                <label class="text-muted small d-block">No. K/P</label>
                                <p class="mb-0"><?= htmlspecialchars($member->ic_no) ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">E-mel</label>
                                <p class="mb-0"><?= htmlspecialchars($member->email) ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">No. Tel (HP)</label>
                                <p class="mb-0"><?= htmlspecialchars($member->home_phone) ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">No. Tel (Pejabat)</label>
                                <p class="mb-0"><?= htmlspecialchars($member->office_phone) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personal">
                                <i class="bi bi-person me-2"></i>Maklumat Peribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#employment">
                                <i class="bi bi-briefcase me-2"></i>Maklumat Pekerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#family">
                                <i class="bi bi-people me-2"></i>Maklumat Waris
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="personal">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Jantina</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->gender) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Agama</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->religion) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Bangsa</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->race) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Status Perkahwinan</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->marital_status) ?></p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small d-block">Alamat Rumah</label>
                                    <p class="mb-1"><?= htmlspecialchars($member->home_address) ?></p>
                                    <p class="mb-0">
                                        <?= htmlspecialchars($member->home_postcode) ?>, 
                                        <?= htmlspecialchars($member->home_state) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Info Tab -->
                        <div class="tab-pane fade" id="employment">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Jawatan</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->position) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Gred</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->grade) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Gaji Bulanan</label>
                                    <p class="mb-0">RM <?= number_format($member->monthly_salary, 2) ?></p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small d-block">Alamat Pejabat</label>
                                    <p class="mb-1"><?= htmlspecialchars($member->office_address) ?></p>
                                    <p class="mb-0"><?= htmlspecialchars($member->office_postcode) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Family Info Tab -->
                        <div class="tab-pane fade" id="family">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Nama Waris</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->family_name) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">No. K/P Waris</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->family_ic) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block">Hubungan</label>
                                    <p class="mb-0"><?= htmlspecialchars($member->family_relationship) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-image {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 1rem 1.5rem;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
}

.nav-tabs .nav-link:hover:not(.active) {
    border-bottom: 2px solid #e9ecef;
}
</style>

<?php require_once '../app/views/layouts/footer.php'; ?> 