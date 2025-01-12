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
require_once '../app/Core/Autoload.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/BaseModel.php';
require_once '../app/Core/BaseController.php';
require_once '../app/Core/Router.php';

// Include controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Controllers/GuestController.php';
require_once '../app/Controllers/PaymentController.php';
require_once '../app/Controllers/AdminController.php';
require_once '../app/Controllers/LoanController.php';

// Include models
require_once '../app/Models/User.php';
require_once '../app/Models/Guest.php';
require_once '../app/Models/AuthUser.php';
require_once '../app/Models/Admin.php';

// Instantiate the Router
$router = new App\Core\Router();

// Home route
$router->addRoute('GET', '/', 'HomeController', 'index');
$router->addRoute('GET', '/home', 'HomeController', 'index');
$router->addRoute('GET', '/home/index', 'HomeController', 'index');

// Guest routes
$router->addRoute('GET', '/guest/create', 'GuestController', 'create'); // For new member registration
$router->addRoute('POST', '/guest/store', 'GuestController', 'store'); // To store new pending member

// Auth routes
$router->addRoute('GET', '/auth/login', 'AuthController', 'showLogin');
$router->addRoute('POST', '/auth/login', 'AuthController', 'login');
$router->addRoute('GET', '/auth/register', 'AuthController', 'showRegister');
$router->addRoute('POST', '/auth/register', 'AuthController', 'register');
$router->addRoute('GET', '/auth/logout', 'AuthController', 'logout');

// Admin basic routes --login, register, logout
$router->addRoute('GET', '/admin', 'AdminController', 'index');
$router->addRoute('GET', '/admin/login', 'AdminController', 'login');

// Admin routes --approve, reject, edit, viewMember
$router->addRoute('GET', '/admin/approve/{id}', 'AdminController', 'approve');
$router->addRoute('GET', '/admin/reject/{id}', 'AdminController', 'reject');
$router->addRoute('GET', '/admin/edit/{id}', 'AdminController', 'edit');
$router->addRoute('GET', '/admin/view/{id}', 'AdminController', 'viewMember');

// User routes
$router->addRoute('GET', '/users/dashboard', 'UserController', 'dashboard');
// $router->addRoute('GET', '/users/create', 'UserController', 'create');
// $router->addRoute('POST', '/store', 'UserController', 'store');
// $router->addRoute('POST', '/users/store', 'UserController', 'store');

    // User routes --Savings
    $router->addRoute('GET', '/users/savings/dashboard', 'UserController', 'savingsDashboard');
    $router->addRoute('GET', '/users/accounts/accountList', 'UserController', 'accountPage');
    $router->addRoute('GET', '/users/accounts/addAccount', 'UserController', 'addAccount');
    $router->addRoute('POST', '/users/accounts/storeAccount', 'UserController', 'storeAccount');
    $router->addRoute('POST', '/users/accounts/set-main/{id}', 'UserController', 'setMainAccount');
    $router->addRoute('GET', '/users/accounts/delete/{id}', 'UserController', 'deleteAccount');

    // User routes --Deposit, Transfer
    $router->addRoute('GET', '/users/savings/deposit/index', 'UserController', 'depositPage');
    $router->addRoute('POST', '/users/savings/deposit/deposit/{id}', 'UserController', 'makeDeposit');
    $router->addRoute('GET', '/users/savings/transfer/index', 'UserController', 'transferPage');
    $router->addRoute('POST', '/users/savings/transfer/index/{id}', 'UserController', 'makeTransfer');

    // User routes --Receipts
    $router->addRoute('GET', '/users/receipt/{reference}', 'UserController', 'showReceipt');
    // $router->addRoute('GET', '/payment/receipt/{referenceNo}', 'UserController', 'showReceipt');

// $router->addRoute('GET', '/users/savings/apply', 'UserController', 'showSavingsApplication');

// $router->addRoute('GET', '/users/savings/deposit/{id}', 'UserController', 'showDepositForm');

// $router->addRoute('POST', '/users/savings/goal/store', 'UserController', 'storeSavingsGoal');
// $router->addRoute('GET', '/users/savings/recurring', 'UserController', 'showRecurringSettings');
// $router->addRoute('POST', '/users/savings/recurring/store', 'UserController', 'storeRecurringPayment');
// $router->addRoute('GET', '/users/savings/deposit', 'UserController', 'showDepositPage');
// $router->addRoute('GET', '/users/savings/transfer', 'UserController', 'showTransferPage');
// $router->addRoute('POST', '/users/savings/deposit', 'UserController', 'makeDeposit');
// $router->addRoute('POST', '/users/savings/transfer', 'UserController', 'makeTransfer');



// Payment routes
$router->addRoute('POST', '/payment/process', 'PaymentController', 'processPayment');
$router->addRoute('GET', '/payment/simulate/{provider}', 'PaymentController', 'showSimulation');
$router->addRoute('POST', '/payment/callback', 'PaymentController', 'handleCallback');

// Add these routes for editing
$router->addRoute('GET', '/users/savings/goal/edit/{id}', 'UserController', 'editSavingsGoal');
$router->addRoute('POST', '/users/savings/goal/update/{id}', 'UserController', 'updateSavingsGoal');
$router->addRoute('GET', '/users/savings/recurring/edit', 'UserController', 'editRecurringPayment');
$router->addRoute('POST', '/users/savings/recurring/update', 'UserController', 'updateRecurringPayment');

// Add this route
// $router->addRoute('GET', '/admin/savings/verify-account/{account}', 'UserController', 'verifyAccount');

// Add these routes for account management
// $router->addRoute('GET', '/admin/savings/accounts', 'UserController', 'showAccounts');
// $router->addRoute('GET', '/admin/savings/accounts/add', 'UserController', 'showAddAccount');
// $router->addRoute('POST', '/admin/savings/accounts/store', 'UserController', 'storeAccount');
// $router->addRoute('POST', '/admin/savings/accounts/delete/{id}', 'UserController', 'deleteAccount');

// User routes --Loans
$router->addRoute('GET', '/users/loans/request', 'LoanController', 'showRequest');
$router->addRoute('POST', '/users/loans/submit', 'LoanController', 'submitRequest');
$router->addRoute('GET', '/users/loans/status', 'LoanController', 'showStatus');
$router->addRoute('GET', '/users/loans/details/{id}', 'LoanController', 'showDetails');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the route
$router->dispatch();    