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
                            <h4 class="mb-4 text-success">
                                <i class="bi bi-person-badge me-2"></i>Personal Information
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">IC Number</label>
                                    <input type="text" name="ic_no" class="form-control" required>
                                </div>

                                <!-- Other personal info fields -->
                            </div>
                        </div>

                        <!-- Step 2: Employment Details -->
                        <div class="step-content" data-step="2">
                            <h4 class="mb-4 text-success">
                                <i class="bi bi-briefcase me-2"></i>Employment Details
                            </h4>
                            
                            <div class="row g-3">
                                <!-- Employment fields -->
                            </div>
                        </div>

                        <!-- Step 3: Contact Information -->
                        <div class="step-content" data-step="3">
                            <!-- Contact fields -->
                        </div>

                        <!-- Step 4: Family Information -->
                        <div class="step-content" data-step="4">
                            <!-- Family fields -->
                        </div>

                        <!-- Step 5: Fees & Contributions -->
                        <div class="step-content" data-step="5">
                            <!-- Fees fields -->
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
