<?php 
    $title = 'Tambah Anggota';
    require_once '../app/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-lg-8">
            <div class="card p-4 shadow-lg">               
                <h1 class="text-center mb-4 page-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Pendaftaran Anggota
                </h1>

                <!-- Step Indicators -->
                <div class="form-wizard">
                    <div class="step-indicator mb-5">
                        <div class="step active" data-step="1">
                            <i class="bi bi-person-badge"></i>
                            <div>Maklumat Pemohon</div>
                        </div>
                        <div class="step" data-step="2">
                            <i class="bi bi-briefcase"></i>
                            <div>Maklumat Pekerjaan</div>
                        </div>
                        <div class="step" data-step="3">
                            <i class="bi bi-house"></i>
                            <div>Maklumat Kediaman</div>
                        </div>
                        <div class="step" data-step="4">
                            <i class="bi bi-people"></i>
                            <div>Maklumat Keluarga</div>
                        </div>
                        <div class="step" data-step="5">
                            <i class="bi bi-cash-coin"></i>
                            <div>Yuran dan Sumbangan</div>
                        </div>
                    </div>
                    
                    <form id="membershipForm" action="/store" method="POST" class="row g-3">
                        <!-- Step 1: Personal Information -->
                        <div class="step-content active" data-step="1">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-person-badge me-2"></i>Maklumat Pemohon</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Penuh</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. K/P</label>
                                    <input type="text" name="ic_no" class="form-control" maxlength="14" oninput="formatIC(this)" placeholder="e.g., 880101-01-1234" required>
                                    <script>
                                        function formatIC(input) {
                                            let value = input.value.replace(/\D/g, '');
                                            value = value.substring(0, 14);
                                            if (value.length >= 6) {
                                                value = value.substring(0, 6) + '-' + value.substring(6);
                                            }
                                            if (value.length >= 9) {
                                                value = value.substring(0, 9) + '-' + value.substring(9);
                                            }
                                            input.value = value;
                                        }
                                    </script>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Jantina</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="" disabled selected>Pilih</option>
                                        <option value="Male">Lelaki</option>
                                        <option value="Female">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Agama</label>
                                    <select name="religion" class="form-select" required>
                                        <option value="" disabled selected>Pilih</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Kristian">Kristian</option>
                                        <option value="Others-Religion">Lain-lain</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Bangsa</label>
                                    <select name="race" class="form-select" required>
                                        <option value="" disabled selected>Pilih</option>
                                        <option value="Malay">Melayu</option>
                                        <option value="Chinese">Cina</option>
                                        <option value="Indian">India</option>
                                        <option value="Others-Race">Lain-lain</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Status Perkahwinan</label>
                                    <select name="marital_status" class="form-select" required>
                                        <option value="" disabled selected>Pilih</option>
                                        <option value="Single">Bujang</option>
                                        <option value="Married">Kahwin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Employment Details -->
                        <div class="step-content" data-step="2">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-briefcase me-2"></i>Maklumat Pekerjaan Pemohon</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">No. Anggota</label>
                                    <input type="text" name="member_no" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">No. PF</label>
                                    <input type="text" name="pf_no" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Gaji Bulanan (RM)</label>
                                    <input type="text" name="monthly_salary" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jawatan</label>
                                    <input type="text" name="position" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Gred</label>
                                    <input type="text" name="grade" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Contact Information -->
                        <div class="step-content" data-step="3">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-house me-2"></i>Maklumat Kediaman</h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Alamat Rumah</label>
                                    <textarea name="home_address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Poskod</label>
                                    <input type="text" name="home_postcode" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Negeri/Wilayah</label>
                                    <select name="home_state" class="form-select" required>
                                        <option value="" disabled selected>Pilih</option>
                                        <option value="Johor">Johor</option>
                                        <option value="Kedah">Kedah</option>
                                        <option value="Kelantan">Kelantan</option>
                                        <option value="Melaka">Melaka</option>
                                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                                        <option value="Pahang">Pahang</option>
                                        <option value="Perak">Perak</option>
                                        <option value="Perlis">Perlis</option>
                                        <option value="Pulau Pinang">Pulau Pinang</option>
                                        <option value="Sabah">Sabah</option>
                                        <option value="Sarawak">Sarawak</option>
                                        <option value="Selangor">Selangor</option>
                                        <option value="Terengganu">Terengganu</option>
                                        <option value="WP Kuala Lumpur">WP Kuala Lumpur</option>
                                        <option value="WP Labuan">WP Labuan</option>
                                        <option value="WP Putrajaya">WP Putrajaya</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">No. Telefon Rumah</label>
                                    <input type="tel" name="home_phone" class="form-control" required>
                                </div>
                                <h4 class="mt-4 mb-3 text-success"><i class="bi bi-building me-2"></i>Alamat</h4>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Alamat Pejabat</label>
                                    <textarea name="office_address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Poskod</label>
                                    <input type="text" name="office_postcode" class="form-control" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                </div>
                                <h4 class="mt-4 mb-3 text-success"><i class="bi bi-telephone me-2"></i>Contact Numbers</h4>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">No. Telefon Pejabat</label>
                                    <input type="tel" name="office_phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')"class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">No. Fax</label>
                                    <input type="tel" name="fax" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Family Information -->
                        <div class="step-content" data-step="4">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-people me-2"></i>Maklumat Keluarga dan Pewaris</h4>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <div class="family-member-container">
                                        <div class="row family-member mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Hubungan Keluarga</label>
                                                <select name="family_relationship[]" class="form-select" required>
                                                    <option value="" disabled selected>Pilih</option>
                                                    <option value="Spouse">Suami</option>
                                                    <option value="Child">Anak</option>
                                                    <option value="Parent">Bapa</option>
                                                    <option value="Sibling">Adik-beradik</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Nama</label>
                                                <input type="text" name="family_name[]" class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                            <label class="form-label fw-bold">No. K/P atau No. Surat Beranak</label>
                                            <input type="text" name="family_ic[]" class="form-control" maxlength="14" oninput="formatIC(this)" placeholder="e.g., 880101-01-1234" required>
                                                <script>
                                                    function formatIC(input) {
                                                        let value = input.value.replace(/\D/g, '');
                                                        value = value.substring(0, 14);
                                                        if (value.length >= 6) {
                                                            value = value.substring(0, 6) + '-' + value.substring(6);
                                                        }
                                                        if (value.length >= 9) {
                                                            value = value.substring(0, 9) + '-' + value.substring(9);
                                                        }
                                                        input.value = value;
                                                    }
                                                </script>                  
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-family mb-3" style="display: none;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success add-family-member">
                                        <i class="bi bi-plus-circle me-2"></i>Tambah Ahli
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Fees & Contributions -->
                        <div class="step-content" data-step="5">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-cash-coin me-2"></i>Yuran dan Sumbangan</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fee Masuk (RM)</label>
                                    <input type="number" name="registration_fee" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Modal Syer (RM)</label>
                                    <input type="number" name="share_capital" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Modal Yuran (RM)</label>
                                    <input type="number" name="fee_capital" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Wang Deposit Anggota (RM)</label>
                                    <input type="number" name="deposit_funds" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sumbangan Tabung Kebajikan (Al-Abrar)  (RM)</label>
                                    <input type="number" name="welfare_fund" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Simpanan Tetap (RM)</label>
                                    <input type="number" name="fixed_deposit" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Lain-lain Sumbangan</label>
                                    <textarea name="other_contributions" class="form-control" rows="3"
                                            placeholder="Please specify any other contributions..." required></textarea>
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

<script src="/js/form-wizard.js"></script>

<?php require_once '../app/views/layouts/footer.php'; ?>
