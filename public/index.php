<?php
session_start();

// Load Composer autoloader and environment variables
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'])->notEmpty();
} catch (Exception $e) {
    die('Error loading environment variables: ' . $e->getMessage());
}

// Core
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';

// Controllers
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/AuthController.php';

// Models
require_once '../app/models/User.php';
require_once '../app/models/AuthUser.php';

// Autoloader
require_once '../app/core/Autoload.php';

// Get the URI and method
$uri = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];

// Initialize controllers
$userController = new App\Controllers\UserController();
$authController = new App\Controllers\AuthController();

// Define public routes
$publicRoutes = ['login', 'register', 'auth/login', 'auth/register'];

// Router
if ($uri === '') {
    // Show landing page
    require_once '../app/views/home/landing.php';
}
// Handle public routes
else if (in_array($uri, $publicRoutes)) {
    switch ($uri) {
        case 'login':
            if (isset($_SESSION['admin_id'])) {
                header('Location: /');
                exit;
            }
            $authController->showLogin();
            break;
        case 'register':
            if (isset($_SESSION['admin_id'])) {
                header('Location: /');
                exit;
            }
            $authController->showRegister();
            break;
        case 'auth/login':
            $authController->login();
            break;
        case 'auth/register':
            $authController->register();
            break;
    }
}
// Handle protected routes
else {
    // Check authentication for protected routes
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /login');
        exit;
    }

    switch ($uri) {
        case 'logout':
            $authController->logout();
            break;
        case 'create':
            $userController->create();
            break;
        case 'store':
            if ($method === 'POST') {
                $userController->store();
            }
            break;
        default:
            if (preg_match('/edit\/(\d+)/', $uri, $matches)) {
                $userController->edit($matches[1]);
            }
            else if (preg_match('/update\/(\d+)/', $uri, $matches) && $method === 'POST') {
                $userController->update($matches[1]);
            }
            else if (preg_match('/delete\/(\d+)/', $uri, $matches) && $method === 'POST') {
                $userController->delete($matches[1]);
            }
            else {
                http_response_code(404);
                echo "Page not found.";
            }
    }
}