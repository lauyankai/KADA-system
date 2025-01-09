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

// Include core files
require_once '../app/Core/Database.php';
require_once '../app/Core/Model.php';
require_once '../app/Core/BaseController.php';
require_once '../app/Core/Router.php';

// Include controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Controllers/PaymentController.php';

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
$router->addRoute('POST', '/store', 'UserController', 'store');
$router->addRoute('POST', '/users/store', 'UserController', 'store');
$router->addRoute('GET', '/users/edit/{id}', 'UserController', 'edit');
$router->addRoute('GET', '/users/details/{id}', 'UserController', 'details');
$router->addRoute('GET', '/users/approve/{id}', 'UserController', 'approve');
$router->addRoute('GET', '/users/reject/{id}', 'UserController', 'reject');
$router->addRoute('GET', '/users/view/{id}', 'UserController', 'viewMember');

// Savings routes
$router->addRoute('GET', '/admin/savings', 'UserController', 'showSavingsDashboard');
$router->addRoute('GET', '/admin/savings/apply', 'UserController', 'showSavingsApplication');
$router->addRoute('POST', '/admin/savings/store', 'UserController', 'storeSavingsAccount');
$router->addRoute('GET', '/admin/savings/deposit/{id}', 'UserController', 'showDepositForm');
$router->addRoute('POST', '/admin/savings/deposit/{id}', 'UserController', 'makeDeposit');
$router->addRoute('POST', '/admin/savings/goal/store', 'UserController', 'storeSavingsGoal');
$router->addRoute('GET', '/admin/savings/recurring', 'UserController', 'showRecurringSettings');
$router->addRoute('POST', '/admin/savings/recurring/store', 'UserController', 'storeRecurringPayment');
$router->addRoute('GET', '/admin/savings/deposit', 'UserController', 'showDepositPage');
$router->addRoute('GET', '/admin/savings/transfer', 'UserController', 'showTransferPage');

// Payment routes
$router->addRoute('POST', '/payment/process', 'PaymentController', 'processPayment');
$router->addRoute('GET', '/payment/simulate/{provider}', 'PaymentController', 'showSimulation');
$router->addRoute('POST', '/payment/callback', 'PaymentController', 'handleCallback');

// Add these routes
$router->addRoute('POST', '/admin/savings/deposit', 'UserController', 'makeDeposit');
$router->addRoute('POST', '/admin/savings/transfer', 'UserController', 'makeTransfer');

// Add these routes for editing
$router->addRoute('GET', '/admin/savings/goal/edit/{id}', 'UserController', 'editSavingsGoal');
$router->addRoute('POST', '/admin/savings/goal/update/{id}', 'UserController', 'updateSavingsGoal');
$router->addRoute('GET', '/admin/savings/recurring/edit', 'UserController', 'editRecurringPayment');
$router->addRoute('POST', '/admin/savings/recurring/update', 'UserController', 'updateRecurringPayment');

// Add this route
$router->addRoute('GET', '/admin/savings/verify-account/{account}', 'UserController', 'verifyAccount');

// Add these routes for account management
$router->addRoute('GET', '/admin/savings/accounts', 'UserController', 'showAccounts');
$router->addRoute('GET', '/admin/savings/accounts/add', 'UserController', 'showAddAccount');
$router->addRoute('POST', '/admin/savings/accounts/store', 'UserController', 'storeAccount');
$router->addRoute('POST', '/admin/savings/accounts/delete/{id}', 'UserController', 'deleteAccount');
$router->addRoute('POST', '/admin/savings/accounts/set-main/{id}', 'UserController', 'setMainAccount');

// Add this route
$router->addRoute('GET', '/admin/savings/receipt/{reference}', 'UserController', 'showReceipt');
$router->addRoute('GET', '/payment/receipt/{referenceNo}', 'UserController', 'showReceipt');

// Add this route
$router->addRoute('POST', '/admin/savings/goal/delete/{id}', 'UserController', 'deleteGoal');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the route
$router->dispatch();    