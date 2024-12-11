<?php
// Start session
session_start();

// Autoload classes 
require_once '../vendor/autoload.php'; 

// Include the Router class
require_once '../app/core/Router.php';

// Instantiate the Router
$router = new App\Core\Router();

// Define the routes
$router->addRoute('GET', '/', 'HomeController', 'index'); 
$router->addRoute('GET', '/home', 'HomeController', 'index');
$router->addRoute('GET', '/home/index', 'HomeController', 'index');  // Home page

// User routes
$router->addRoute('GET', '/user', 'UserController', 'index'); // User list page
$router->addRoute('GET', '/user/register', 'UserController', 'register'); // Registration form page
$router->addRoute('POST', '/user/store', 'UserController', 'store'); // Handle form submission
$router->addRoute('GET', '/user/login', 'UserController', 'login');
$router->addRoute('POST', '/user/authenticate', 'UserController', 'authenticate');
$router->addRoute('GET', '/user/logout', 'UserController', 'logout');

// Student routes
$router->addRoute('GET', '/student', 'StudentController', 'index'); // Student index page
$router->addRoute('GET', '/student/index', 'StudentController', 'index');
$router->addRoute('GET', '/student/add', 'StudentController', 'add');  // Student add form
$router->addRoute('POST', '/student/store', 'StudentController', 'store'); // Process student add form submission
$router->addRoute('GET', '/students/edit/{id}', 'StudentController', 'edit');
$router->addRoute('POST', '/students/update/{id}', 'StudentController', 'update');
$router->addRoute('GET', '/student/delete/{id}', 'StudentController', 'delete');

// Handle the incoming request
$router->dispatch();