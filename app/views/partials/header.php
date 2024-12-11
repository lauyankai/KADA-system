<!-- /app/views/partials/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'yk MVC Application' ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Optional: Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">yk's MVC Application</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- If logged in, show Logout button -->
            <li class="nav-item">
                <a class="nav-link" href="/user/logout">Logout</a>
            </li>
        <?php else: ?>
            <!-- If not logged in, show Login button -->
            <li class="nav-item">
                <a class="nav-link" href="/user">Login</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

    <!-- Container for page content -->
    <div class="container mt-4">