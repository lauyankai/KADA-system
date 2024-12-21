<?php 
    $title = 'Edit User';
    require_once '../app/views/layouts/header.php';
?>

    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-lg-6">
                <div class="card p-4 shadow-lg">               
                    <h1 class="text-center mb-4 page-title">
                        <i class="bi bi-pencil-square me-2"></i>Edit User
                    </h1>
                    <form action="/update/<?= $user['id']; ?>" method="POST">
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
                                       placeholder="e.g., John Doe" 
                                       value="<?= htmlspecialchars($user['name']); ?>" 
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
                                       placeholder="e.g., user@example.com" 
                                       value="<?= htmlspecialchars($user['email']); ?>" 
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
                                <i class="bi bi-check-circle me-2"></i>Update User
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
<?php require_once '../app/views/layouts/footer.php'; ?>
