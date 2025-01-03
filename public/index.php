<?php
session_start();

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Include core files in correct order
require_once '../app/Core/Database.php';
require_once '../app/Core/Model.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Router.php';

// Include controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/UserController.php';

// Include models
require_once '../app/Models/User.php';
require_once '../app/Models/AuthUser.php';

// Instantiate the Router
$router = new App\Core\Router();

// Define the routes
$router->addRoute('GET', '/', 'HomeController', 'index');
$router->addRoute('GET', '/home', 'HomeController', 'index');
$router->addRoute('GET', '/home/index', 'HomeController', 'index');

// Auth routes
$router->addRoute('GET', '/auth/login', 'AuthController', 'showLogin');
$router->addRoute('POST', '/auth/login', 'AuthController', 'authenticate');
$router->addRoute('GET', '/auth/register', 'AuthController', 'showRegister');
$router->addRoute('POST', '/auth/register', 'AuthController', 'register');
$router->addRoute('GET', '/auth/logout', 'AuthController', 'logout');

// User routes
$router->addRoute('GET', '/users', 'UserController', 'index');
$router->addRoute('GET', '/users/create', 'UserController', 'create');
$router->addRoute('POST', '/users', 'UserController', 'store');
$router->addRoute('GET', '/users/edit/{id}', 'UserController', 'edit');
$router->addRoute('GET', '/users/details/{id}', 'UserController', 'details');
$router->addRoute('GET', '/users/approve/{id}', 'UserController', 'approve');
$router->addRoute('GET', '/users/reject/{id}', 'UserController', 'reject');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the route
$router->dispatch();