<?php 
    $title = 'Permohonan Pembiayaan Anggota';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-file-earmark-text me-2"></i>Borang Permohonan Pembiayaan Anggota
                    </h4>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/loans/submit" method="POST" class="needs-validation" novalidate>
                        <!-- Section 1: Butir-butir Pembiayaan -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Butir-butir Pembiayaan</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Pembiayaan</label>
                                    <select name="loan_type" class="form-select" required>
                                        <option value="">Pilih jenis</option>
                                        <option value="al_bai">Pinjaman Al Bai</option>
                                        <option value="al_innah">Pinjaman Al Innah</option>
                                        <option value="skim_khas">Pinjaman Skim Khas</option>
                                        <option value="road_tax">Pinjaman Road Tax & Insuran</option>
                                        <option value="al_qardhul">Pinjaman Al Qardhul Hasan</option>
                                        <option value="other">Lain-lain</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="otherLoanType" style="display: none;">
                                    <label class="form-label">Nyatakan Jenis Lain</label>
                                    <input type="text" name="other_loan_type" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Amaun Dipohon (RM)</label>
                                    <input type="number" name="amount" class="form-control" required
                                           min="0" max="99999.99" step="0.01"
                                           placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tempoh Pembiayaan (Bulan)</label>
                                    <input type="number" name="duration" class="form-control" required
                                           min="1" max="120">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ansuran Bulanan (RM)</label>
                                    <input type="number" name="monthly_payment" class="form-control" required
                                           min="0" max="99999.99" step="0.01"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
<!-- Section 2: Butir-Butir Peribadi Pemohon -->
<div class="mb-4">
                            <h5 class="border-bottom pb-2">Butir-Butir Peribadi Pemohon</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. Kad Pengenalan</label>
                                    <input type="text" name="ic_number" class="form-control" required
                                           pattern="\d{6}-\d{2}-\d{4}"
                                           placeholder="000000-00-0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tarikh Lahir</label>
                                    <input type="date" name="birth_date" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Umur (Tahun)</label>
                                    <input type="number" name="age" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jantina</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Pilih jantina</option>
                                        <option value="Lelaki">Lelaki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <!-- Continue with religion, race, addresses etc. -->
                            </div>
                        </div>

                        <!-- Section 3: Pengakuan Pemohon -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Pengakuan Pemohon</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="declaration-text p-3 bg-light rounded">
                                        Saya <span class="fw-bold"><?= htmlspecialchars($member->full_name ?? '') ?></span> 
                                        No.K/P: <span class="fw-bold"><?= htmlspecialchars($member->ic_number ?? '') ?></span> 
                                        dengan ini memberi kuasa kepada KOPERASI KAKITANGAN KADA KELANTAN BHD atau wakilnya yang sah 
                                        untuk mendapat apa-apa maklumat yang diperlukan dan juga mendapatakan bayaran balik dari 
                                        potongan gaji dan emolumen saya sebagaimana amaun yang dipinjamkan. Saya juga bersetuju 
                                        menerima sebarang keputusan dari KOPERASI ini untuk menolak pemohonan tanpa memberi sebarang alasan.
                                    </div>
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" id="declaration" required>
                                        <label class="form-check-label" for="declaration">
                                            Saya mengesahkan pengakuan di atas
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
<!-- Section 4: Butir-butir Penjamin -->
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
<!-- Section 5: Pengesahan Majikan -->
<div class="mb-4">
                            <h5 class="border-bottom pb-2">Pengesahan Majikan</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="confirmation-text p-3 bg-light rounded">
                                        Kami mengesahkan bahawa: 
                                        <span class="fw-bold"><?= htmlspecialchars($member->full_name ?? '') ?></span><br>
                                        No.K/P: <span class="fw-bold"><?= htmlspecialchars($member->ic_number ?? '') ?></span> 
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Hantar Permohonan
                            </button>
                            <a href="/users/dashboard" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('[name="loan_type"]').addEventListener('change', function() {
    const otherField = document.getElementById('otherLoanType');
    otherField.style.display = this.value === 'other' ? 'block' : 'none';
});

// Auto-calculate monthly payment
document.querySelectorAll('[name="amount"], [name="duration"]').forEach(input => {
    input.addEventListener('input', calculateMonthlyPayment);
});

function calculateMonthlyPayment() {
    const amount = parseFloat(document.querySelector('[name="amount"]').value) ⠞⠟⠟⠞⠟⠵⠵⠺⠺⠵⠟⠟⠟⠵⠞⠟⠺⠞⠵⠺⠵⠞⠞⠞⠵⠵⠺⠟⠞⠟⠞⠵⠵⠟⠺⠞⠟⠵⠞⠞⠺⠵⠟⠞⠵⠟⠵⠺⠟⠟⠟⠺⠟⠟⠟⠵⠵⠟⠟⠺⠵⠵⠞⠟⠺⠞⠞⠟⠞⠺⠵⠟⠺⠵⠺⠞⠺⠵⠺⠟⠟⠵⠟⠞⠵ 1;
    const monthly = amount / duration;
    document.querySelector('[name="monthly_payment"]').value = monthly.toFixed(2);
}
</script>

