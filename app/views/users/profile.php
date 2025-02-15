<?php 
    $title = 'Profil Saya';
    require_once '../app/views/layouts/header.php';
?>

<!-- Add this temporarily for debugging -->
<?php error_log('Member data: ' . print_r($member, true)); ?>

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
                        <h4 class="mb-1"><?= htmlspecialchars($member->name ?? '') ?></h4>
                        <p class="text-muted mb-2">ID Ahli: <?= htmlspecialchars($member->member_id ?? '') ?></p>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>Ahli Aktif
                        </span>
                        <div class="mt-4">
                            <?php if ($hasResignationRecord): ?>
                                <button class="btn btn-secondary btn-sm" disabled title="Permohonan berhenti telah dihantar">
                                    <i class="bi bi-box-arrow-right me-2"></i>Permohonan Berhenti telah dihantar pada 
                                    <?= date('d/m/Y', strtotime($resignationDate)) ?>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resignModal">
                                    <i class="bi bi-box-arrow-right me-2"></i>Berhenti Menjadi Ahli
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="border-top pt-4">
                        <div class="row text-start g-4">
                            <div class="col-6">
                                <label class="text-muted small d-block">No. K/P</label>
                                <p class="mb-0"><?= htmlspecialchars($member->ic_no ?? '') ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">E-mel</label>
                                <p class="mb-0"><?= htmlspecialchars($member->email ?? '') ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">No. Tel (Bimbit)</label>
                                <p class="mb-0">
                                    <?php 
                                    error_log('Mobile phone: ' . ($member->mobile_phone ?? 'null'));
                                    echo htmlspecialchars($member->mobile_phone ?? ''); 
                                    ?>
                                </p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">No. Tel (Rumah)</label>
                                <p class="mb-0"><?= htmlspecialchars($member->home_phone ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
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
                    <button class="btn btn-primary btn-sm" onclick="toggleEdit()">
                        <i class="bi bi-pencil me-2"></i>Kemaskini
                    </button>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="personal">
                            <!-- View Mode -->
                            <div class="view-mode">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Jantina</label>
                                        <p class="mb-0">
                                            <?php
                                            $gender = [
                                                'Male' => 'Lelaki',
                                                'Female' => 'Perempuan'
                                            ];
                                            echo htmlspecialchars($gender[$member->gender] ?? $member->gender);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Agama</label>
                                        <p class="mb-0">
                                            <?php
                                            $religion = [
                                                'Islam' => 'Islam',
                                                'Buddha' => 'Buddha',
                                                'Hindu' => 'Hindu',
                                                'Christian' => 'Kristian',
                                                'Others-Religion' => 'Lain-lain'
                                            ];
                                            echo htmlspecialchars($religion[$member->religion] ?? $member->religion);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Bangsa</label>
                                        <p class="mb-0">
                                            <?php
                                            $race = [
                                                'Malay' => 'Melayu',
                                                'Chinese' => 'Cina',
                                                'Indian' => 'India',
                                                'Others-Race' => 'Lain-lain'
                                            ];
                                            echo htmlspecialchars($race[$member->race] ?? $member->race);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Status Perkahwinan</label>
                                        <p class="mb-0">
                                            <?php
                                            $maritalStatus = [
                                                'Single' => 'Bujang',
                                                'Married' => 'Berkahwin'
                                            ];
                                            echo htmlspecialchars($maritalStatus[$member->marital_status] ?? $member->marital_status);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small d-block">Alamat Rumah</label>
                                        <p class="mb-1"><?= htmlspecialchars($member->home_address ?? '') ?></p>
                                        <p class="mb-0">
                                            <?= htmlspecialchars($member->home_postcode ?? '') ?>, 
                                            <?= htmlspecialchars($member->home_state ?? '') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Mode (Initially Hidden) -->
                            <form class="edit-mode d-none" action="/users/profile/update" method="POST">
                                <input type="hidden" name="section" value="personal">
                                <div class="row g-4">
                                    <!-- Read-only fields -->
                                    <div class="col-md-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($member->name ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. K/P</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($member->ic_no ?? '') ?>" readonly>
                                    </div>
                                    
                                    <!-- Editable fields -->
                                    <div class="col-md-6">
                                        <label class="form-label">E-mel</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($member->email ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Tel (Bimbit)</label>
                                        <input type="text" name="mobile_phone" class="form-control" 
                                               value="<?= htmlspecialchars($member->mobile_phone ?? '') ?>" 
                                               pattern="^01[0-9]-[0-9]{7,8}$" 
                                               placeholder="01x-xxxxxxx">
                                        <div class="form-text">Format: 01x-xxxxxxx</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Tel (Pejabat)</label>
                                        <input type="tel" name="office_phone" class="form-control" value="<?= htmlspecialchars($member->office_phone ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Perkahwinan</label>
                                        <select name="marital_status" class="form-select">
                                            <option value="Bujang" <?= $member->marital_status == 'Bujang' ? 'selected' : '' ?>>Bujang</option>
                                            <option value="Berkahwin" <?= $member->marital_status == 'Berkahwin' ? 'selected' : '' ?>>Berkahwin</option>
                                            <option value="Bercerai" <?= $member->marital_status == 'Bercerai' ? 'selected' : '' ?>>Bercerai</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat Rumah</label>
                                        <input type="text" name="home_address" class="form-control mb-2" value="<?= htmlspecialchars($member->home_address ?? '') ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="home_postcode" class="form-control" placeholder="Poskod" value="<?= htmlspecialchars($member->home_postcode ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <select name="home_state" class="form-select">
                                                    <?php
                                                    $states = ['Kelantan', 'Terengganu', 'Pahang', 'Kedah', 'Perlis', 'Perak', 'Selangor', 'Negeri Sembilan', 'Melaka', 'Johor', 'Sabah', 'Sarawak', 'Pulau Pinang', 'Wilayah Persekutuan'];
                                                    foreach ($states as $state) {
                                                        $selected = ($member->home_state == $state) ? 'selected' : '';
                                                        echo "<option value=\"$state\" $selected>$state</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan
                                    </button>
                                    <button type="button" class="btn btn-light ms-2" onclick="toggleEdit()">
                                        <i class="bi bi-x me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Employment Info Tab -->
                        <div class="tab-pane fade" id="employment">
                            <!-- View Mode -->
                            <div class="view-mode">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Jawatan</label>
                                        <p class="mb-0"><?= htmlspecialchars($member->position ?? '') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Gred</label>
                                        <p class="mb-0">
                                            <?php
                                            $grade = [
                                                'N1' => 'N1 - Pembantu Tadbir',
                                                'N19' => 'N19 - Pembantu Tadbir',
                                                'N22' => 'N22 - Pembantu Tadbir Kanan',
                                                'N26' => 'N26 - Pembantu Tadbir Tingkatan Kanan',
                                                'N28' => 'N28 - Pembantu Tadbir Tingkatan Tertinggi',
                                                'N29' => 'N29 - Pembantu Tadbir Khas',
                                                'N32' => 'N32 - Pembantu Tadbir Utama',
                                                'N36' => 'N36 - Pembantu Tadbir Tertinggi',
                                                'N40' => 'N40 - Pembantu Tadbir Atasan',
                                                'N44' => 'N44 - Pembantu Tadbir Kanan Atasan',
                                                'N48' => 'N48 - Pembantu Tadbir Tertinggi Atasan',
                                                'N52' => 'N52 - Pembantu Tadbir Utama Atasan',
                                                'N54' => 'N54 - Pembantu Tadbir Khas Atasan',
                                                'F29' => 'F29 - Pegawai Kewangan',
                                                'F32' => 'F32 - Pegawai Kewangan Kanan',
                                                'F36' => 'F36 - Pegawai Kewangan Tingkatan Kanan',
                                                'F40' => 'F40 - Pegawai Kewangan Tingkatan Tertinggi',
                                                'F44' => 'F44 - Pegawai Kewangan Khas',
                                                'F48' => 'F48 - Pegawai Kewangan Utama',
                                                'F52' => 'F52 - Pegawai Kewangan Tertinggi',
                                                'F54' => 'F54 - Pegawai Kewangan Atasan'
                                            ];
                                            echo htmlspecialchars($grade[$member->grade] ?? $member->grade);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Status Pekerjaan</label>
                                        <p class="mb-0">
                                            <?php
                                            $employmentStatus = [
                                                'Permanent' => 'Tetap',
                                                'Contract' => 'Kontrak',
                                                'Temporary' => 'Sementara'
                                            ];
                                            $status = $member->employment_status ?? 'Permanent'; // Default to Permanent if null
                                            echo htmlspecialchars($employmentStatus[$status] ?? $status);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Gaji Bulanan (RM)</label>
                                        <p class="mb-0"><?= number_format($member->monthly_salary ?? 0, 2) ?></p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small d-block">Alamat Pejabat</label>
                                        <p class="mb-1"><?= htmlspecialchars($member->office_address ?? '') ?></p>
                                        <p class="mb-0">
                                            <?= htmlspecialchars($member->office_postcode ?? '') ?>
                                            <?= htmlspecialchars($member->office_state ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">No. Tel Pejabat</label>
                                        <p class="mb-0"><?= htmlspecialchars($member->office_phone ?? '') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">No. Faks</label>
                                        <p class="mb-0"><?= htmlspecialchars($member->fax ?? '') ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <form class="edit-mode d-none" action="/users/profile/update" method="POST">
                                <input type="hidden" name="section" value="employment">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Jawatan</label>
                                        <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($member->position) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gred</label>
                                        <select name="grade" class="form-select" required>
                                            <option value="">Pilih Gred</option>
                                            <?php foreach ($grade as $key => $value): ?>
                                                <option value="<?= $key ?>" <?= $member->grade == $key ? 'selected' : '' ?>><?= $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Pekerjaan</label>
                                        <select name="employment_status" class="form-select" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Permanent" <?= ($member->employment_status ?? 'Permanent') == 'Permanent' ? 'selected' : '' ?>>Tetap</option>
                                            <option value="Contract" <?= ($member->employment_status ?? 'Permanent') == 'Contract' ? 'selected' : '' ?>>Kontrak</option>
                                            <option value="Temporary" <?= ($member->employment_status ?? 'Permanent') == 'Temporary' ? 'selected' : '' ?>>Sementara</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gaji Bulanan (RM)</label>
                                        <input type="number" name="monthly_salary" class="form-control" value="<?= $member->monthly_salary ?>" step="0.01" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat Pejabat</label>
                                        <input type="text" name="office_address" class="form-control mb-2" value="<?= htmlspecialchars($member->office_address ?? '') ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Poskod</label>
                                                <input type="text" name="office_postcode" class="form-control" placeholder="Poskod" value="<?= htmlspecialchars($member->office_postcode ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Negeri</label>
                                                <select name="office_state" class="form-select">
                                                    <?php
                                                    $states = [
                                                        'Johor',
                                                        'Kedah', 
                                                        'Kelantan',
                                                        'Melaka',
                                                        'Negeri Sembilan',
                                                        'Pahang',
                                                        'Perak',
                                                        'Perlis',
                                                        'Pulau Pinang',
                                                        'Sabah',
                                                        'Sarawak',
                                                        'Selangor',
                                                        'Terengganu',
                                                        'WP Kuala Lumpur',
                                                        'WP Labuan',
                                                        'WP Putrajaya'
                                                    ];
                                                    foreach ($states as $state) {
                                                        $selected = ($member->office_state == $state) ? 'selected' : '';
                                                        echo "<option value=\"$state\" $selected>$state</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan
                                    </button>
                                    <button type="button" class="btn btn-light ms-2" onclick="toggleEdit()">
                                        <i class="bi bi-x me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Family Info Tab -->
                        <div class="tab-pane fade" id="family">
                            <!-- View Mode -->
                            <div class="view-mode">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Nama Waris</label>
                                        <p class="mb-0"><?= htmlspecialchars($member->family_name ?? '') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">No. K/P Waris</label>
                                        <p class="mb-0"><?= htmlspecialchars($member->family_ic ?? '') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small d-block">Hubungan</label>
                                        <p class="mb-0">
                                            <?php
                                            $relationships = [
                                                'Spouse' => 'Pasangan',
                                                'Husband' => 'Suami',
                                                'Wife' => 'Isteri',
                                                'Child' => 'Anak',
                                                'Father' => 'Bapa',
                                                'Mother' => 'Ibu',
                                                'Sibling' => 'Adik-beradik',
                                                'Brother' => 'Abang/Adik Lelaki',
                                                'Sister' => 'Kakak/Adik Perempuan'
                                            ];
                                            echo htmlspecialchars($relationships[$member->family_relationship] ?? $member->family_relationship);
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode (Initially Hidden) -->
                            <form class="edit-mode d-none" action="/users/profile/update" method="POST">
                                <input type="hidden" name="section" value="family">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Waris</label>
                                        <input type="text" name="family_name" class="form-control" value="<?= htmlspecialchars($member->family_name) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. K/P Waris</label>
                                        <input type="text" name="family_ic" class="form-control" value="<?= htmlspecialchars($member->family_ic) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hubungan</label>
                                        <select name="family_relationship" class="form-select" required>
                                            <option value="">Pilih Hubungan</option>
                                            <option value="Spouse" <?= $member->family_relationship == 'Spouse' ? 'selected' : '' ?>>Pasangan</option>
                                            <option value="Husband" <?= $member->family_relationship == 'Husband' ? 'selected' : '' ?>>Suami</option>
                                            <option value="Wife" <?= $member->family_relationship == 'Wife' ? 'selected' : '' ?>>Isteri</option>
                                            <option value="Child" <?= $member->family_relationship == 'Child' ? 'selected' : '' ?>>Anak</option>
                                            <option value="Father" <?= $member->family_relationship == 'Father' ? 'selected' : '' ?>>Bapa</option>
                                            <option value="Mother" <?= $member->family_relationship == 'Mother' ? 'selected' : '' ?>>Ibu</option>
                                            <option value="Sibling" <?= $member->family_relationship == 'Sibling' ? 'selected' : '' ?>>Adik-beradik</option>
                                            <option value="Brother" <?= $member->family_relationship == 'Brother' ? 'selected' : '' ?>>Abang/Adik Lelaki</option>
                                            <option value="Sister" <?= $member->family_relationship == 'Sister' ? 'selected' : '' ?>>Kakak/Adik Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan
                                    </button>
                                    <button type="button" class="btn btn-light ms-2" onclick="toggleEdit()">
                                        <i class="bi bi-x me-2"></i>Batal
                                    </button>
                                </div>
                            </form>
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

.form-control:read-only {
    background-color: #f8f9fa;
}

.edit-mode .row {
    margin-bottom: 1rem;
}

.form-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.btn-secondary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>

<!-- Resign Confirmation Modal -->
<div class="modal fade" id="resignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    Pengesahan Berhenti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <?php if ($hasResignationRecord): ?>
                    <p class="text-center mb-0">Anda telah menghantar permohonan berhenti. Sila tunggu untuk pengesahan.</p>
                <?php else: ?>
                    <p class="text-center mb-0">Adakah anda pasti untuk berhenti menjadi ahli koperasi?</p>
                    <p class="text-center text-muted small mb-0">Tindakan ini tidak boleh dibatalkan.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <?php if (!$hasResignationRecord): ?>
                    <button type="button" class="btn btn-danger" onclick="proceedToResign()">
                        <i class="bi bi-check-circle me-2"></i>Ya, Teruskan
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function proceedToResign() {
    window.location.href = '/users/resign';
}

function toggleEdit() {
    const viewModes = document.querySelectorAll('.view-mode');
    const editModes = document.querySelectorAll('.edit-mode');
    
    viewModes.forEach(view => {
        view.classList.toggle('d-none');
    });
    
    editModes.forEach(edit => {
        edit.classList.toggle('d-none');
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 