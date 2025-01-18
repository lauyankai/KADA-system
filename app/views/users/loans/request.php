<?php $title = 'Permohonan Pembiayaan Anggota'; ?>

<div class="container">
    <div class="form-wizard">
        <div class="row justify-content-center my-5">
            <div class="col-lg-8">
                <div class="card p-4 shadow-lg">
                    <!-- Header -->
                    <h1 class="pageTitle text-center mb-4">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Borang Permohonan Pembiayaan Anggota
                    </h1>
                    
                    <!-- Step Indicator -->
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

                    <!-- Form -->
                    <form action="/users/loans/submitRequest" method="POST" class="needs-validation" novalidate>
                        <!-- Step 1: Loan Details -->
                        <div class="step-content active" data-step="1">
                            <h4 class="mt-3 mb-4 text-success">
                                <i class="bi bi-cash-coin me-2"></i>Butir-butir Pembiayaan
                            </h4>
                            
                            <div class="row g-3">
                                <!-- Loan Type -->
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

                                <!-- Loan Amount -->
                                <div class="col-md-4">
                                    <label class="form-label">Amaun Dipohon (RM)</label>
                                    <input type="number" name="amount" class="form-control" required
                                           min="1" max="100000.00" step="0.01" 
                                           onkeyup="validateAmount(this)"
                                           placeholder="0.00">
                                </div>

                                <!-- Loan Duration -->
                                <div class="col-md-4">
                                    <label class="form-label">Tempoh Pembiayaan (Bulan)</label>
                                    <input type="number" name="duration" class="form-control" required
                                           min="10" max="60" onkeyup="validateDuration(this)">
                                </div>

                                <!-- Monthly Payment -->
                                <div class="col-md-4">
                                    <label class="form-label">Ansuran Bulanan (RM)</label>
                                    <input type="text" name="monthly_payment" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Personal Details -->
                        <div class="step-content" data-step="2">
                            <h4 class="mt-3 mb-4 text-success">
                                <i class="bi bi-person-badge me-2"></i>Butir-Butir Peribadi
                            </h4>

                            <!-- Display Member Info -->
                            <div class="card bg-light p-4 mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama:</label>
                                        <span><?= htmlspecialchars($member->name ?? '') ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">No. K/P:</label>
                                        <span><?= htmlspecialchars($member->ic_no ?? '') ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Alamat:</label>
                                        <span><?= htmlspecialchars($member->home_address ?? '') ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Details -->
                            <div class="card bg-light p-4">
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

                        <!-- Step 3: Declaration -->
                        <div class="step-content" data-step="3">
                            <h4 class="mt-3 mb-4 text-success">
                                <i class="bi bi-file-text me-2"></i>Pengakuan
                            </h4>
                            
                            <div class="card bg-light p-4">
                                <h5 class="card-title mb-4">Pengakuan Pemohon</h5>
                                <div class="declaration-text p-3 bg-white rounded">
                                    Saya <strong><?= htmlspecialchars($member->name ?? '') ?></strong>
                                    No.K/P: <strong><?= htmlspecialchars($member->ic_no ?? '') ?></strong>
                                    dengan ini memberi kuasa kepada KOPERASI KAKITANGAN KADA KELANTAN BHD atau wakilnya yang sah
                                    untuk mendapat apa-apa maklumat yang diperlukan dan juga mendapatakan bayaran balik dari
                                    potongan gaji dan emolumen saya sebagaimana amaun yang dipinjamkan. Saya juga bersetuju
                                    menerima sebarang keputusan dari KOPERASI ini untuk menolak pemohonan tanpa memberi sebarang alasan.
                                </div>
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="confirmationCheckbox" 
                                           name="declaration_confirmed" value="1" required>
                                    <label class="form-check-label" for="confirmationCheckbox">
                                        Saya mengesah pengakuan di atas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Guarantor Details -->
                        <div class="step-content" data-step="4">
                            <h4 class="mt-3 mb-4 text-success">
                                <i class="bi bi-people me-2"></i>Butir-butir Penjamin
                            </h4>

                            <!-- Guarantor 1 -->
                            <div class="card bg-light p-4 mb-4">
                                <h5 class="card-title mb-4">Penjamin 1</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama</label>
                                        <input type="text" name="guarantor1_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">No. K/P</label>
                                        <input type="text" name="guarantor1_ic" class="form-control" required
                                               pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Alamat</label>
                                        <input type="text" name="guarantor1_address" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Guarantor 2 -->
                            <div class="card bg-light p-4">
                                <h5 class="card-title mb-4">Penjamin 2</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama</label>
                                        <input type="text" name="guarantor2_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">No. K/P</label>
                                        <input type="text" name="guarantor2_ic" class="form-control" required
                                               pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Alamat</label>
                                        <input type="text" name="guarantor2_address" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="step-buttons mt-4">
                            <a href="/users/dashboard" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Kembali
                            </a>
                            <div class="float-end">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/form-validation.js"></script>
<script src="/js/loanform.js"></script>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>