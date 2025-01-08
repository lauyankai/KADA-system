<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="color: #198754; font-weight: bold;">
                            <i class="bi bi-person-plus me-2"></i>Admin Registration
                        </h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/auth/register" method="POST">
                            <div class="mb-4">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-2"></i>Username
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-key me-2"></i>Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-gradient btn-lg w-100 mb-3">
                                    <i class="bi bi-person-plus me-2"></i>Register
                                </button>
                                <a href="/login" class="text-muted text-decoration-none">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 