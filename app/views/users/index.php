<?php 
    $title = 'Users List';
    require_once '../app/views/templates/header.php';
?>

    <div class="container mt-4">
        <!-- Main Content -->
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="card-title" style="color: #198754; font-weight: bold; font-style: italic;">
                        <i class="bi bi-people-fill me-2"></i>List of Users
                    </h2>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead>
                            <tr class="text-center">
                                <th><i class="bi bi-person me-2"></i>Name</th>
                                <th><i class="bi bi-envelope me-2"></i>Email</th>
                                <th><i class="bi bi-gear me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td class="text-center action-buttons">
                                    <a href="/edit/<?= $user['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <form action="/delete/<?= $user['id']; ?>" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="bi bi-trash-fill"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
