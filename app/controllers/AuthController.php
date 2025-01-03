<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\AuthUser;

class AuthController extends Controller
{
    private $authUser;

    public function __construct()
    {
        $this->authUser = new AuthUser();
    }

    public function showLogin()
    {
        $this->view('auth/login', ['title' => 'Login - KADA System']);
    }

    public function authenticate()
    {
        // Handle login form submission
        // ... rest of your authentication code
    }

    public function showRegister()
    {
        $this->view('auth/register', ['title' => 'Register - KADA System']);
    }

    public function register()
    {
        // Validate input
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /register');
            exit;
        }

        // Create admin data
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        // Attempt to create admin
        if ($this->authUser->createAdmin($data)) {
            $_SESSION['success'] = 'Admin registration successful! Please login.';
            header('Location: /login');
            exit;
        }

        $_SESSION['error'] = 'Registration failed. Username or email might already exist.';
        header('Location: /register');
        exit;
    }

    public function logout()
    {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header('Location: /');
        exit;
    }
}