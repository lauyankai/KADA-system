<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Add User</title>
    <style>
        .card {
            border-radius: 15px;
            border: none;
        }
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .btn-gradient {
            background: linear-gradient(45deg, #198754, #20c997);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(45deg, #20c997, #198754);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body style="background-color: #fff8e8;">
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-lg-8">
                <div class="card p-4 shadow-lg">               
                    <h1 class="text-center mb-4" style="color: #198754; font-weight: bold;">
                        <i class="bi bi-person-plus-fill me-2"></i>Membership Application Form
                    </h1>
                    
                    <form action="/store" method="POST" class="row g-3">
                        <!-- Personal Information -->
                        <h4 class="mt-3 mb-4 text-success"><i class="bi bi-person-badge me-2"></i>Personal Information</h4>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">IC Number</label>
                            <input type="text" name="ic_no" class="form-control" placeholder="e.g., 880101-01-1234" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Religion</label>
                            <input type="text" name="religion" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Race</label>
                            <input type="text" name="race" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marital Status</label>
                            <select name="marital_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>

                        <!-- Employment Information -->
                        <h4 class="mt-4 mb-3 text-success"><i class="bi bi-briefcase me-2"></i>Employment Details</h4>

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

                        <!-- Contact Information -->
                        <h4 class="mt-4 mb-3 text-success"><i class="bi bi-house me-2"></i>Home Address</h4>
                        
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

                        <!-- Contact Numbers -->
                        <h4 class="mt-4 mb-3 text-success"><i class="bi bi-telephone me-2"></i>Contact Information</h4>

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

                        <!-- Family Information -->
                        <h4 class="mt-4 mb-3 text-success"><i class="bi bi-people me-2"></i>Family Information</h4>
                        
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
                                        <input type="text" name="family_ic[]" class="form-control" placeholder="e.g., 880101-01-1234" required>
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

                        <!-- Fees and Contributions -->
                        <h4 class="mt-4 mb-3 text-success"><i class="bi bi-cash-coin me-2"></i>Fees and Contributions</h4>

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

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 mb-3">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                            <br>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Members List
                            </a>
                        </div>
                    </form>
                    <!-- Success Modal -->
                    <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <i id="modalIcon" class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                    <h3 class="mt-3" id="modalTitle">Success!</h3>
                                    <p class="mb-4" id="modalMessage">Your application has been submitted successfully.</p>
                                    <button type="button" class="btn btn-gradient px-4" data-bs-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Debug log
            console.log('Submitting form...');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            fetch('/store', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                const modal = new bootstrap.Modal(document.getElementById('resultModal'));
                const icon = document.getElementById('modalIcon');
                const title = document.getElementById('modalTitle');
                const message = document.getElementById('modalMessage');
                
                if (data.success) {
                    icon.className = 'bi bi-check-circle-fill text-success';
                    title.textContent = 'Success!';
                    message.textContent = data.message;
                    
                    // Add event listener for modal close
                    document.getElementById('resultModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = '/';
                    });
                } else {
                    icon.className = 'bi bi-x-circle-fill text-danger';
                    title.textContent = 'Error!';
                    message.textContent = data.message || 'An error occurred. Please try again.';
                }
                
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                const modal = new bootstrap.Modal(document.getElementById('resultModal'));
                const icon = document.getElementById('modalIcon');
                const title = document.getElementById('modalTitle');
                const message = document.getElementById('modalMessage');
                
                icon.className = 'bi bi-x-circle-fill text-danger';
                title.textContent = 'Error!';
                message.textContent = 'An error occurred. Please try again.';
                
                modal.show();
            });
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.family-member-container');
        const addButton = document.querySelector('.add-family-member');

        addButton.addEventListener('click', function() {
            const template = container.querySelector('.family-member').cloneNode(true);
            template.querySelector('[name="family_relationship[]"]').value = '';
            template.querySelector('[name="family_name[]"]').value = '';
            template.querySelector('[name="family_ic[]"]').value = '';
            template.querySelector('.remove-family').style.display = 'block';
            container.appendChild(template);

            // Add event listener to remove button
            template.querySelector('.remove-family').addEventListener('click', function() {
                template.remove();
            });
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
