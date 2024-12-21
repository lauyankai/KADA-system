<?php 
    $title = 'Add User';
    require_once '../app/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-lg-8">
            <div class="card p-4 shadow-lg">               
                <h1 class="text-center mb-4 page-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Membership Application Form
                </h1>

                <!-- Step Indicators -->
                <div class="form-wizard">
                    <div class="step-indicator mb-5">
                        <div class="step active" data-step="1">
                            <i class="bi bi-person-badge"></i>
                            <div>Personal Information</div>
                        </div>
                        <div class="step" data-step="2">
                            <i class="bi bi-briefcase"></i>
                            <div>Employment Details</div>
                        </div>
                        <div class="step" data-step="3">
                            <i class="bi bi-house"></i>
                            <div>Contact Information</div>
                        </div>
                        <div class="step" data-step="4">
                            <i class="bi bi-people"></i>
                            <div>Family Information</div>
                        </div>
                        <div class="step" data-step="5">
                            <i class="bi bi-cash-coin"></i>
                            <div>Fees & Contributions</div>
                        </div>
                    </div>
                    
                    <form id="membershipForm" action="/store" method="POST" class="row g-3">
                        <!-- Step 1: Personal Information -->
                        <div class="step-content active" data-step="1">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-person-badge me-2"></i>Personal Information</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">IC Number</label>
                                    <input type="text" name="ic_no" class="form-control" placeholder="e.g., 880101-01-1234" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Religion</label>
                                    <input type="text" name="religion" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Race</label>
                                    <input type="text" name="race" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Marital Status</label>
                                    <select name="marital_status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Employment Details -->
                        <div class="step-content" data-step="2">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-briefcase me-2"></i>Employment Details</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Member Number</label>
                                    <input type="text" name="member_no" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">PF Number</label>
                                    <input type="text" name="pf_no" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Monthly Salary (RM)</label>
                                    <input type="number" name="monthly_salary" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Position</label>
                                    <input type="text" name="position" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Grade</label>
                                    <input type="text" name="grade" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Contact Information -->
                        <div class="step-content" data-step="3">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-house me-2"></i>Contact Information</h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Home Address</label>
                                    <textarea name="home_address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Postcode</label>
                                    <input type="text" name="home_postcode" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">State</label>
                                    <select name="home_state" class="form-select" required>
                                        <option value="">Select State</option>
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
                                <h4 class="mt-4 mb-3 text-success"><i class="bi bi-building me-2"></i>Office Address</h4>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Office Address</label>
                                    <textarea name="office_address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Office Postcode</label>
                                    <input type="text" name="office_postcode" class="form-control" required>
                                </div>
                                <h4 class="mt-4 mb-3 text-success"><i class="bi bi-telephone me-2"></i>Contact Numbers</h4>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Office Telephone</label>
                                    <input type="tel" name="office_phone" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Home Telephone</label>
                                    <input type="tel" name="home_phone" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fax Number</label>
                                    <input type="tel" name="fax" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Family Information -->
                        <div class="step-content" data-step="4">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-people me-2"></i>Family Information</h4>
                            <div class="row g-3">
                                <div class="col-12 mb-3">
                                    <div class="family-member-container">
                                        <div class="row family-member mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Relationship</label>
                                                <select name="family_relationship[]" class="form-select" required>
                                                    <option value="">Select</option>
                                                    <option value="Spouse">Spouse</option>
                                                    <option value="Child">Child</option>
                                                    <option value="Parent">Parent</option>
                                                    <option value="Sibling">Sibling</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Name</label>
                                                <input type="text" name="family_name[]" class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">IC Number</label>
                                                <input type="text" name="family_ic[]" class="form-control" 
                                                       placeholder="e.g., 880101-01-1234" required>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-family mb-3" style="display: none;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success add-family-member">
                                        <i class="bi bi-plus-circle me-2"></i>Add Family Member
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Fees & Contributions -->
                        <div class="step-content" data-step="5">
                            <h4 class="mt-3 mb-4 text-success"><i class="bi bi-cash-coin me-2"></i>Fees and Contributions</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Registration Fee (RM)</label>
                                    <input type="number" name="registration_fee" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Share Capital (RM)</label>
                                    <input type="number" name="share_capital" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fee Capital (RM)</label>
                                    <input type="number" name="fee_capital" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Member Deposit Funds (RM)</label>
                                    <input type="number" name="deposit_funds" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">AL-ABRAR Welfare Fund (RM)</label>
                                    <input type="number" name="welfare_fund" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fixed Deposit (RM)</label>
                                    <input type="number" name="fixed_deposit" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Other Contributions</label>
                                    <textarea name="other_contributions" class="form-control" rows="3" 
                                            placeholder="Please specify any other contributions..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="step-buttons mt-4">
                            <button type="button" class="btn btn-secondary prev-step" style="display: none;">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-gradient next-step">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-gradient submit-form" style="display: none;">
                                Submit Application<i class="bi bi-check-circle ms-2"></i>
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
