<?php 
    $title = 'Permohonan Pembiayaan Anggota';
?>

<div class="container">
    <div class="form-wizard">
        <div class="row justify-content-center my-5">
            <div class="col-lg-8">
                <div class="card p-4 shadow-lg">
                    <h1 class="pageTitle text-center mb-4">
                        <i class="bi bi-file-earmark-text me-2"></i>Borang Permohonan Pembiayaan Anggota
                    </h1>

                    <!-- Step Indicators -->
                    <div class="step-indicator mb-5">
                        <div class="step active" data-step="1">
                            <i class="bi bi-cash-coin"></i>
                            <div>Butir-butir Pembiayaan</div>
                        </div>
                        <div class="step" data-step="2">
                            <i class="bi bi-person-badge"></i>
                            <div>Butir-Butir Peribadi</div>
                        </div>
                        <div class="step" data-step="3">
                            <i class="bi bi-file-text"></i>
                            <div>Pengakuan</div>
                        </div>
                        <div class="step" data-step="4">
                            <i class="bi bi-people"></i>
                            <div>Butir-butir Penjamin</div>
                        </div>
                        
                    </div>
                    <script src="/js/form-validation.js"></script>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/loans/request/submit" method="POST" class="needs-validation" novalidate>
                        <!-- Keep existing form sections but wrap them in step-content divs -->
                        <div class="step-content active" data-step="1">
                            <!-- Section 1: Butir-butir Pembiayaan content -->
                            <div class="mb-4">
                                <h4 class="mt-3 mb-4 text-success">
                                    <i class="bi bi-cash-coin me-2"></i>Butir-butir Pembiayaan
                                </h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jenis Pembiayaan</label>
                                        <select name="loan_type" class="form-select" required>
                                            <option value="" disabled selected>Pilih jenis</option>
                                            <option value="al_bai">Pinjaman Al Bai</option>
                                            <option value="al_innah">Pinjaman Al Innah</option>
                                            <option value="skim_khas">Pinjaman Skim Khas</option>
                                            <option value="road_tax">Pinjaman Road Tax & Insuran</option>
                                            <option value="al_qardhul">Pinjaman Al Qardhul Hasan</option>
                                            <option value="other">Lain-lain</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="otherLoanType" style="display: none;">
                                        <label class="form-label fw-bold">Nyatakan Jenis Lain</label>
                                        <input type="text" name="other_loan_type" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Amaun Dipohon (RM)</label>
                                        <input type="number" name="amount" class="form-control" required
                                               min="1" max="100000.00" step="0.01" onkeyup="validateAmount(this)"
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tempoh Pembiayaan (Bulan)</label>
                                        <input type="number" name="duration" class="form-control" required
                                               min="10" max="60" onkeyup="validateDuration(this)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ansuran Bulanan (RM)</label>
                                        <input type="text" name="monthly_payment" class="form-control" readonly>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content" data-step="2">
                            <!-- Section 2: Butir-Butir Peribadi content -->
                            <div class="mb-4">
                                <h4 class="mt-3 mb-4 text-success">
                                    <i class="bi bi-person-badge me-2"></i>Butir-Butir Peribadi
                                </h4>
                                <div class="row g-3">
                                    <!-- Personal Info Box -->
                                    <div class="col-12">
                                        <div class="card bg-white p-4 shadow-sm mb-4">
                                            <h5 class="card-title mb-4">Maklumat Asas</h5>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Nama: </label>
                                                    <span class="fw-bold"><?= htmlspecialchars($member->name ?? '') ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">No. Kad Pengenalan: </label>
                                                    <span class="fw-bold"><?= htmlspecialchars($member->ic_no ?? '') ?></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Tarikh Lahir</label>
                                                    <input type="date" name="birth_date" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Umur (Tahun)</label>
                                                    <input type="number" name="age" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Jantina</label>
                                                    <select name="gender" class="form-select" required>
                                                        <option value="">Pilih jantina</option>
                                                        <option value="Lelaki">Lelaki</option>
                                                        <option value="Perempuan">Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Agama</label>
                                                    <select name="religion" class="form-select" required>
                                                        <option value="">Pilih agama</option>
                                                        <option value="Islam">Islam</option>
                                                        <option value="Buddha">Buddha</option>
                                                        <option value="Hindu">Hindu</option>
                                                        <option value="Kristian">Kristian</option>
                                                        <option value="Lain-lain">Lain-lain</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Bangsa</label>
                                                    <select name="race" class="form-select" required>
                                                        <option value="">Pilih bangsa</option>
                                                        <option value="Melayu">Melayu</option>
                                                        <option value="Cina">Cina</option>
                                                        <option value="India">India</option>
                                                        <option value="Lain-lain">Lain-lain</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Employment Info Box -->
                                        <div class="card bg-white p-4 shadow-sm mb-4">
                                            <h5 class="card-title mb-4">Maklumat Pekerjaan</h5>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">No. Anggota</label>
                                                    <input type="text" name="member_no" class="form-control" required
                                                           pattern="\d{5}" placeholder="00000">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">No. PF</label>
                                                    <input type="text" name="pf_no" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Jawatan</label>
                                                    <input type="text" name="position" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address Info Box -->
                                        <div class="card bg-white p-4 shadow-sm mb-4">
                                            <h5 class="card-title mb-4">Maklumat Alamat</h5>
                                            <div class="row g-3">
                                                <!-- Residential Address -->
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Alamat Kediaman</label>
                                                    <input type="text" name="address_line1" class="form-control mb-2" 
                                                           placeholder="Alamat baris 1" required>
                                                    <input type="text" name="address_line2" class="form-control mb-2" 
                                                           placeholder="Alamat baris 2">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Poskod</label>
                                                    <input type="text" name="postcode" class="form-control" required
                                                           pattern="\d{5}" placeholder="00000">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Bandar</label>
                                                    <input type="text" name="city" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Negeri</label>
                                                    <select name="state" class="form-select" required>
                                                        <option value="">Pilih negeri</option>
                                                        <option value="Kelantan">Kelantan</option>
                                                        <option value="Terengganu">Terengganu</option>
                                                        <option value="Pahang">Pahang</option>
                                                        <option value="Perak">Perak</option>
                                                        <option value="Kedah">Kedah</option>
                                                        <option value="Perlis">Perlis</option>
                                                        <option value="Pulau Pinang">Pulau Pinang</option>
                                                        <option value="Selangor">Selangor</option>
                                                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                                                        <option value="Melaka">Melaka</option>
                                                        <option value="Johor">Johor</option>
                                                        <option value="Sabah">Sabah</option>
                                                        <option value="Sarawak">Sarawak</option>
                                                        <option value="W.P. Kuala Lumpur">W.P. Kuala Lumpur</option>
                                                        <option value="W.P. Putrajaya">W.P. Putrajaya</option>
                                                        <option value="W.P. Labuan">W.P. Labuan</option>
                                                    </select>
                                                </div>

                                                <!-- Office Address -->
                                                <div class="col-12 mt-4">
                                                    <label class="form-label fw-bold">Alamat Pejabat</label>
                                                    <input type="text" name="office_address" class="form-control mb-2" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">No. Telefon/No. Faks Pejabat</label>
                                                    <input type="tel" name="office_phone" class="form-control" required
                                                           placeholder="09-xxxxxxxx">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">No. Telefon Bimbit</label>
                                                    <input type="tel" name="phone" class="form-control"
                                                           placeholder="09-xxxxxxxx">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Info Box -->
                                        <div class="card bg-white p-4 shadow-sm">
                                            <h5 class="card-title mb-4">Maklumat Bank</h5>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Nama Bank</label>
                                                    <select name="bank_name" class="form-select" required>
                                                        <option value="">Pilih bank</option>
                                                        <option value="Maybank">Maybank</option>
                                                        <option value="CIMB Bank">CIMB Bank</option>
                                                        <option value="Bank Islam">Bank Islam</option>
                                                        <option value="RHB Bank">RHB Bank</option>
                                                        <option value="Public Bank">Public Bank</option>
                                                        <option value="AmBank">AmBank</option>
                                                        <option value="Hong Leong Bank">Hong Leong Bank</option>
                                                        <option value="Bank Rakyat">Bank Rakyat</option>
                                                        <option value="Bank Muamalat">Bank Muamalat</option>
                                                        <option value="Affin Bank">Affin Bank</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">No. Akaun Bank</label>
                                                    <input type="text" name="bank_account" class="form-control" required
                                                           placeholder="xxxxxxxxxx">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content" data-step="3">
                            <!-- Section 3: Pengakuan content -->
                            <div class="mb-4">
                                <h4 class="mt-3 mb-4 text-success">
                                    <i class="bi bi-file-text me-2"></i>Pengakuan
                                </h4>
                                <div class="card bg-white p-4 shadow-sm">
                                    <h5 class="card-title mb-4">Pengakuan Pemohon</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="declaration-text p-3 bg-light rounded">
                                                Saya <span class="fw-bold"><?= htmlspecialchars($member->name ?? '') ?></span> 
                                                No.K/P: <span class="fw-bold"><?= htmlspecialchars($member->ic_no ?? '') ?></span> 
                                                dengan ini memberi kuasa kepada KOPERASI KAKITANGAN KADA KELANTAN BHD atau wakilnya yang sah 
                                                untuk mendapat apa-apa maklumat yang diperlukan dan juga mendapatakan bayaran balik dari 
                                                potongan gaji dan emolumen saya sebagaimana amaun yang dipinjamkan. Saya juga bersetuju 
                                                menerima sebarang keputusan dari KOPERASI ini untuk menolak pemohonan tanpa memberi sebarang alasan.
                                            </div>
                                            <div class="form-check mt-3">
                                                <input type="checkbox" class="form-check-input" id="confirmationCheckbox" name="declaration_confirmed" value="1" required>
                                                <label class="form-check-label" for="confirmationCheckbox">
                                                    Saya mengesah pengakuan di atas
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content" data-step="4">
                            <!-- Section 4: Butir-butir Penjamin content -->
                            <div class="mb-4">
                                <h4 class="mt-3 mb-4 text-success">
                                    <i class="bi bi-people me-2"></i>Butir-butir Penjamin
                                </h4>
                                
                                <!-- Penjamin 1 -->
                                <div class="card bg-white p-4 shadow-sm mb-4">
                                    <h5 class="card-title mb-4">Penjamin 1</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Nama</label>
                                            <input type="text" name="guarantor1_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">No. K/P</label>
                                            <input type="text" name="guarantor1_ic" class="form-control" required
                                                   pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">No. Anggota</label>
                                            <input type="text" name="guarantor1_member_no" class="form-control" required
                                                   pattern="\d{5}" placeholder="00000">
                                        </div>
                                    </div>
                                </div>

                                <!-- Penjamin 2 -->
                                <div class="card bg-white p-4 shadow-sm">
                                    <h5 class="card-title mb-4">Penjamin 2</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Nama</label>
                                            <input type="text" name="guarantor2_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">No. K/P</label>
                                            <input type="text" name="guarantor2_ic" class="form-control" required
                                                   pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">No. Anggota</label>
                                            <input type="text" name="guarantor2_member_no" class="form-control" required
                                                   pattern="\d{5}" placeholder="00000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <!-- Navigation Buttons -->
                        <div class="step-buttons mt-4">
                            <button type="button" class="btn btn-secondary prev-step" style="display: none;">
                                <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                            </button>
                            <button type="button" class="btn btn-gradient next-step">
                                Seterusnya<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-gradient submit-form" style="display: none;">
                                Hantar<i class="bi bi-check-circle ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/loanform.js"></script>
<link rel="stylesheet" href="/css/admin.css">

<?php require_once '../app/views/layouts/footer.php'; ?>

