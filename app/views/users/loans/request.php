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

                    <form action="/loans/submit" method="POST" class="needs-validation" novalidate>
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
                            <h5 class="border-bottom pb-2">Pengakuan Pemohon</h5>
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
                                        <input type="checkbox" class="form-check-input" id="declaration" required>
                                            <label class="form-check-label" for="declaration" required>
                                            Saya mengesahkan pengakuan di atas
                                        </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content" data-step="4">
                            <!-- Section 4: Butir-butir Penjamin content -->
<div class="mb-4">
                            <h5 class="border-bottom pb-2">Butir-butir Penjamin</h5>
                            
                            <!-- Penjamin 1 -->
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <h6>Penjamin 1</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="guarantor1_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. K/P</label>
                                    <input type="text" name="guarantor1_ic" class="form-control" required
                                           pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Anggota</label>
                                    <input type="text" name="guarantor1_member_no" class="form-control" required
                                           pattern="\d{5}" placeholder="00000">
                                </div>
                            </div>

                            <!-- Penjamin 2 -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6>Penjamin 2</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="guarantor2_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. K/P</label>
                                    <input type="text" name="guarantor2_ic" class="form-control" required
                                           pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Anggota</label>
                                    <input type="text" name="guarantor2_member_no" class="form-control" required
                                           pattern="\d{5}" placeholder="00000">
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="step-content" data-step="5">
                            <!-- Section 5: Pengesahan Majikan content -->
<div class="mb-4">
                            <h5 class="border-bottom pb-2">Pengesahan Majikan</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="confirmation-text p-3 bg-light rounded">
                                        Kami mengesahkan bahawa: 
                                        <span class="fw-bold"><?= htmlspecialchars($member->name ?? '') ?></span><br>
                                        No.K/P: <span class="fw-bold"><?= htmlspecialchars($member->ic_no ?? '') ?></span> 
                                        telah memberikan butir-butir peribadi dan maklumat pendapatan selaras dengan rekod 
                                        pekerjaan kakitangan tersebut.<br>
                                        Kami juga mengesahkan bahawa kakitangan adalah berjawatan tetap.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gaji Pokok Sebulan (RM)</label>
                                    <input type="number" name="basic_salary" class="form-control" required
                                           min="0" max="99999.99" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gaji Bersih Sebulan (RM)</label>
                                    <input type="number" name="net_salary" class="form-control" required
                                           min="0" max="99999.99" step="0.01" placeholder="0.00">
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

<script>
// Validate amount to 5 digits and 2 decimals
function validateAmount(input) {
    let value = parseFloat(input.value);
    
    // Validate amount range
    if (!value || value > 100000 || value < 1000) {
        input.classList.add('is-invalid');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = value > 100000 ? 'Amaun tidak boleh melebihi RM100,000' : 'Amaun minimum ialah RM1,000';
            input.parentNode.appendChild(feedback);
        }
    } else {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
    
    // Force value within range
    if (value > 100000) input.value = 100000;
    if (value < 1000 && value !== 0) input.value = 1000;
    
    calculateMonthlyPayment();
}

// Validate duration
function validateDuration(input) {
    let value = parseInt(input.value);
    
    // Validate duration range
    if (!value || value < 10 || value > 60) {
        input.classList.add('is-invalid');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = 'Tempoh pembiayaan mestilah antara 10 hingga 60 bulan';
            input.parentNode.appendChild(feedback);
        }
    } else {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
    
    // Force value within range
    if (value > 60) input.value = 60;
    if (value < 10 && value !== 0) input.value = 10;
    
    calculateMonthlyPayment();
}

// Show/hide other loan type field
document.querySelector('[name="loan_type"]').addEventListener('change', function() {
    const otherField = document.getElementById('otherLoanType');
    otherField.style.display = this.value === 'other' ? 'block' : 'none';
});

function calculateMonthlyPayment() {
    const amount = parseFloat(document.querySelector('[name="amount"]').value) || 0;
    const duration = parseInt(document.querySelector('[name="duration"]').value) || 1;
    
    if (amount >= 1000 && amount <= 100000 && duration >= 10 && duration <= 60) {
        // Formula: (Principal Amount / Duration) + (Principal Amount * 4.2% / Duration)
        const principal = amount / duration;
        const interest = (amount * 0.042) / duration;
        const monthly = principal + interest;
    document.querySelector('[name="monthly_payment"]').value = monthly.toFixed(2);
    } else {
        document.querySelector('[name="monthly_payment"]').value = '0.00';
    }
}

// Initialize calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for real-time calculation
    const amountInput = document.querySelector('[name="amount"]');
    const durationInput = document.querySelector('[name="duration"]');
    
    amountInput.addEventListener('keyup', calculateMonthlyPayment);
    amountInput.addEventListener('change', calculateMonthlyPayment);
    durationInput.addEventListener('keyup', calculateMonthlyPayment);
    durationInput.addEventListener('change', calculateMonthlyPayment);
    
    // Initial calculation
    calculateMonthlyPayment();
});

// Validate required fields in current step
function validateStep(step) {
    const currentStepContent = document.querySelector(`.step-content[data-step="${step}"]`);
    const requiredFields = currentStepContent.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }

        // Special validation for loan type
        if (field.name === 'loan_type' && field.value === 'other') {
            const otherField = document.querySelector('[name="other_loan_type"]');
            if (!otherField.value) {
                isValid = false;
                otherField.classList.add('is-invalid');
            } else {
                otherField.classList.remove('is-invalid');
            }
        }

        // Special validation for amount
        if (field.name === 'amount') {
            const amount = parseFloat(field.value);
            if (!amount || amount < 1000 || amount > 100000) {
                isValid = false;
                field.classList.add('is-invalid');
            }
        }
    });

    return isValid;
}

// Add invalid feedback styling
.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const stepContents = document.querySelectorAll('.step-content');
    const stepIndicators = document.querySelectorAll('.step-indicator .step');
    const prevButton = document.querySelector('.prev-step');
    const nextButton = document.querySelector('.next-step');
    const submitButton = document.querySelector('.submit-form');
    let currentStep = 1;

    function updateSteps() {
        // Hide all step contents
        stepContents.forEach(content => content.classList.remove('active'));
        // Show current step content
        document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            if (index + 1 < currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (index + 1 === currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('completed', 'active');
            }
        });

        // Show/hide navigation buttons
        prevButton.style.display = currentStep === 1 ? 'none' : 'inline-block';
        nextButton.style.display = currentStep === 5 ? 'none' : 'inline-block';
        submitButton.style.display = currentStep === 5 ? 'inline-block' : 'none';
    }

    nextButton.addEventListener('click', () => {
        if (currentStep < 5 && validateStep(currentStep)) {
            currentStep++;
            updateSteps();
        } else if (!validateStep(currentStep)) {
            // Show alert if validation fails
            alert('Sila lengkapkan semua maklumat yang diperlukan sebelum meneruskan.');
            return false;
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });
});
</script>

<style>
.btn-gradient {
    background: linear-gradient(to right, #198754, #157347);
    color: white;
    border: none;
}

.btn-gradient:hover {
    background: linear-gradient(to right, #157347, #115c39);
    color: white;
}

.form-label.fw-bold {
    color: #444;
}

.text-success {
    color: #198754 !important;
}

/* Step navigation styles */
.step-content {
    display: none;
}

.step-content.active {
    display: block;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin: 30px 0;
    padding: 0;
}

.step {
    text-align: center;
    flex: 1;
    position: relative;
    padding: 12px;
}

.step i {
    font-size: 1.5rem;
    margin-bottom: 8px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step div {
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    .step i, .step div {
        color: #198754;
    }
    .step i {
        transform: scale(1.2);
    }
}

.step.completed {
    color: #28a745;
}

.step.completed i {
    color: #28a745;
}

.invalid-feedback {
    display: none;
    color: #dc3545;
}

/* Add any additional styles from create.php */
</style>

<?php require_once '../app/views/layouts/footer.php'; ?>

