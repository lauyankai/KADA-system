<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\AuthUser;
use App\Models\Director;

class AuthController extends BaseController
{
    private $authUser;
    private $director;

    public function __construct()
    {
        $this->authUser = new AuthUser();
        $this->director = new Director();
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
            
            // Auto identify user type
            $userType = \App\Middleware\AuthMiddleware::identifyUserType($username);

            // Director login
            if ($userType === 'director') {
                $director = $this->director->findByUsername($username);
                
                if ($director && password_verify($password, $director['password'])) {
                    $_SESSION['director_id'] = $director['id'];
                    $_SESSION['director_name'] = $director['name'];
                    $_SESSION['user_type'] = 'director';
                    
                    // Update last login
                    $this->director->updateLastLogin($director['id']);
                    
                    header('Location: /director');
                    exit;
                }
                throw new \Exception('ID Pengarah atau kata laluan tidak sah');
            }

            // Member login
            if ($userType === 'member') {
                // Remove hyphens for IC comparison
                $cleanIC = str_replace('-', '', $username);
                
                $member = $this->authUser->findMemberByIC($cleanIC);
                if ($member) {
                    $_SESSION['member_id'] = $member['id'];
                    $_SESSION['user_id'] = $member['id'];
                    $_SESSION['member_name'] = $member['name'];
                    $_SESSION['username'] = $member['name'];
                    $_SESSION['user_type'] = 'member';
                    
                    header('Location: /users');
                    exit;
                }
                throw new \Exception('No. K/P tidak dijumpai atau belum diluluskan');
            }

            // Admin login
            if ($userType === 'admin') {
                if (empty($password)) {
                    throw new \Exception('Kata laluan diperlukan untuk log masuk admin');
                }

                $admin = $this->authUser->findAdminByUsername($username);
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['user_type'] = 'admin';
                    
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