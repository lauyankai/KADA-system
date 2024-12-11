<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .card {
            border-radius: 15px;
            border: none;
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
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .action-buttons form {
            display: inline-block;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.05);
        }
    </style>
</head>

<body style="background-color: #fff8e8;">
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

        <!-- Action Buttons -->
        <div class="text-center">
            <a href="/create" class="btn btn-gradient btn-lg mb-3">
                <i class="bi bi-plus-circle me-2"></i>Add New User
            </a>
            <br>
            <a href="/logout" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
