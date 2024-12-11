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
            <div class="col-lg-6">
                <div class="card p-4 shadow-lg">               
                    <h1 class="text-center mb-4" style="color: #198754; font-weight: bold; font-style: italic;">
                        <i class="bi bi-person-plus-fill me-2"></i>Add New User
                    </h1>
                    <form action="/store" method="POST">
                        <!-- User Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-person me-2"></i>Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0">
                                    <i class="bi bi-person-fill text-success"></i>
                                </span>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control border-start-0" 
                                       placeholder="e.g., Rick Sanchez" 
                                       required>
                                <span class="input-group-text">
                                    <span class="tt" data-bs-toggle="tooltip" title="Enter user's full name">
                                        <i class="bi bi-question-circle text-muted"></i>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0">
                                    <i class="bi bi-envelope-fill text-success"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control border-start-0" 
                                       placeholder="e.g., rick@example.com" 
                                       required>
                                <span class="input-group-text">
                                    <span class="tt" data-bs-toggle="tooltip" title="Enter a valid email address">
                                        <i class="bi bi-question-circle text-muted"></i>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-gradient btn-lg px-5 mb-3">
                                <i class="bi bi-plus-circle me-2"></i>Add User
                            </button>
                            <br>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to User List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltips = document.querySelectorAll('.tt')
            tooltips.forEach(t => {
                new bootstrap.Tooltip(t)
            })
        })
    </script>
</body>
</html>
