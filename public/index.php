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

// Include core files first
require_once '../app/Core/Autoload.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/BaseModel.php';
require_once '../app/Core/BaseController.php';
require_once '../app/Core/Router.php';

// Then include models
require_once '../app/Models/User.php';
require_once '../app/Models/Guest.php';
require_once '../app/Models/AuthUser.php';
require_once '../app/Models/Admin.php';
require_once '../app/Models/Saving.php';
require_once '../app/Models/Loan.php';
require_once '../app/Models/Director.php';
require_once '../app/Models/Statement.php';

// Include controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Controllers/GuestController.php';
require_once '../app/Controllers/PaymentController.php';
require_once '../app/Controllers/AdminController.php';
require_once '../app/Controllers/LoanController.php';
require_once '../app/Controllers/SavingController.php';
require_once '../app/Controllers/DirectorController.php';
require_once '../app/Controllers/InfoController.php';
require_once '../app/Controllers/StatementController.php';

// Include middleware
require_once '../app/Middleware/AuthMiddleware.php';

// Instantiate the Router
$router = new App\Core\Router();

// Home route
    $router->addRoute('GET', '/', 'HomeController', 'index');
    $router->addRoute('GET', '/home', 'HomeController', 'index');
    $router->addRoute('GET', '/home/index', 'HomeController', 'index');
    $router->addRoute('GET', '/info/loantype', 'InfoController', 'showLoanTypes');

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
        $router->addRoute('POST', '/admin/export-pdf', 'AdminController', 'exportPdf');
        $router->addRoute('POST', '/admin/export-excel', 'AdminController', 'exportExcel');

// User Dashboard Routes
    $router->addRoute('GET', '/users/dashboard', 'UserController', 'dashboard');

    // User routes -- Savings Routes
        $router->addRoute('GET', '/users/savings/verify-member/{id}', 'SavingController', 'verifyMember');
        $router->addRoute('GET', '/users/savings/page', 'SavingController', 'savingsDashboard');
        $router->addRoute('GET', '/users/savings/deposit', 'SavingController', 'showDepositForm');
        $router->addRoute('POST', '/users/savings/deposit', 'SavingController', 'makeDeposit');
        $router->addRoute('GET', '/users/savings/transfer', 'SavingController', 'showTransferForm');
        $router->addRoute('POST', '/users/savings/transfer', 'SavingController', 'makeTransfer');
        $router->addRoute('POST', '/users/savings/goals', 'SavingController', 'createSavingsGoal');
        $router->addRoute('POST', '/users/savings/recurring', 'SavingController', 'createRecurringPayment');

        // User routes --Receipts
        $router->addRoute('GET', '/payment/receipt/{referenceNo}', 'SavingController', 'showReceipt');

        // User routes -- Payment
        $router->addRoute('POST', '/payment/process', 'PaymentController', 'processPayment');
        $router->addRoute('GET', '/payment/simulate/{provider}', 'PaymentController', 'showSimulation');
        $router->addRoute('POST', '/payment/callback', 'PaymentController', 'handleCallback');
        $router->addRoute('GET', '/users/savings/receipt/{referenceNo}', 'SavingController', 'showReceipt');
        
        // User routes -- Savings Goal
        $router->addRoute('GET', '/users/savings/goals/edit/{id}', 'SavingController', 'editGoal');
        $router->addRoute('POST', '/users/savings/goals/update/{id}', 'SavingController', 'updateGoal');
        $router->addRoute('GET', '/users/savings/goals/create', 'SavingController', 'createGoal');
        $router->addRoute('POST', '/users/savings/goals/store', 'SavingController', 'storeGoal');
        $router->addRoute('POST', '/users/savings/goals/delete/{id}', 'SavingController', 'deleteGoal');
        
        // User routes -- Savings Recurring
        $router->addRoute('GET', '/users/savings/recurring/edit', 'SavingController', 'editRecurringPayment');
        $router->addRoute('POST', '/users/savings/recurring/update', 'SavingController', 'updateRecurringPayment');
        
        // User routes --Loans
        $router->addRoute('GET', '/users/loans/request', 'LoanController', 'showRequest');
        $router->addRoute('POST', '/users/loans/submitRequest', 'LoanController', 'submitRequest');
        $router->addRoute('GET', '/users/loans/status', 'LoanController', 'showStatus');
        $router->addRoute('GET', '/users/loans/details/{id}', 'LoanController', 'showDetails');

        // User routes --Statements
        $router->addRoute('GET', '/users/statements', 'StatementController', 'index');
        $router->addRoute('GET', '/users/statements/generate', 'StatementController', 'generate');
        $router->addRoute('GET', '/users/statements/download/{id}', 'StatementController', 'download');
        $router->addRoute('GET', '/users/statements/download', 'StatementController', 'download');

// Director routes
    $router->addRoute('GET', '/director', 'DirectorController', 'dashboard');
    $router->addRoute('GET', '/director/add', 'DirectorController', 'showAddDirector');
    $router->addRoute('POST', '/director/store', 'DirectorController', 'store');
    $router->addRoute('GET', '/director/loans', 'DirectorController', 'showLoans');
    $router->addRoute('POST', '/director/loans/update-status', 'DirectorController', 'updateLoanStatus');

// Guest routes
    $router->addRoute('GET', '/guest/check-status', 'GuestController', 'checkStatusPage');
    $router->addRoute('POST', '/guest/check-status', 'GuestController', 'checkStatus');




// $router->addRoute('GET', '/users/savings/goals/{id}/edit', 'SavingController', 'editSavingsGoal');
// $router->addRoute('POST', '/users/savings/goals/{id}/update', 'SavingController', 'updateSavingsGoal');
// $router->addRoute('POST', '/users/savings/goals/{id}/delete', 'SavingController', 'deleteSavingsGoal');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the route
$router->dispatch();    