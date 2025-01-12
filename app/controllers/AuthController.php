<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\AuthUser;

class AuthController extends BaseController
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

    public function login()
    {
        try {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // If input contains hyphens, it's an IC number
            if (strpos($username, '-') !== false) {
                // Remove hyphens for IC comparison
                $cleanIC = str_replace('-', '', $username);
                
                // Check member login (no password needed for members)
                $member = $this->authUser->findMemberByIC($cleanIC);
                if ($member) {
                    $_SESSION['member_id'] = $member['id'];
                    $_SESSION['user_id'] = $member['id'];
                    $_SESSION['member_name'] = $member['name'];
                    $_SESSION['username'] = $member['name'];
                    $_SESSION['is_member'] = true;
                    
                    header('Location: /users/dashboard');
                    exit;
                }
                
                throw new \Exception('No. K/P tidak dijumpai atau belum diluluskan');
            } else {
                // Admin login - requires password
                if (empty($password)) {
                    throw new \Exception('Kata laluan diperlukan untuk log masuk admin');
                }

                $admin = $this->authUser->findAdminByUsername($username);
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['is_admin'] = true;
                    
                    header('Location: /admin');
                    exit;
                }
                
                throw new \Exception('ID Admin atau kata laluan tidak sah');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function showRegister()
    {
        $this->view('auth/register', ['title' => 'Register - KADA System']);
    }

    public function register()
    {
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /register');
            exit;
        }

        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

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
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        session_destroy();
        header('Location: /');
        exit;
    }
}