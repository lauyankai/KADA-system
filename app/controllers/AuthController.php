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
                try {
                    $cleanIC = str_replace('-', '', $username);
                    error_log("Login attempt - IC: " . $cleanIC);
                    
                    if (empty($password)) {
                        throw new \Exception('Kata laluan diperlukan untuk log masuk');
                    }

                    $member = $this->authUser->findMemberByIC($cleanIC);
                    
                    if (!$member) {
                        error_log("No member found with IC: " . $cleanIC);
                        throw new \Exception('No. K/P atau kata laluan tidak sah');
                    }

                    error_log("Found member: " . print_r($member, true));
                    
                    if (empty($member['password'])) {
                        error_log("Member has no password set");
                        throw new \Exception('Kata laluan belum ditetapkan. Sila semak emel anda untuk pautan penetapan kata laluan.');
                    }

                    if (password_verify($password, $member['password'])) {
                        error_log("Password verified successfully");
                        $_SESSION['member_id'] = $member['id'];
                        $_SESSION['user_id'] = $member['id'];
                        $_SESSION['member_name'] = $member['name'];
                        $_SESSION['username'] = $member['name'];
                        $_SESSION['user_type'] = 'member';
                        
                        header('Location: /users');
                        exit;
                    } else {
                        error_log("Password verification failed");
                        throw new \Exception('No. K/P atau kata laluan tidak sah');
                    }
                } catch (\Exception $e) {
                    error_log("Login error: " . $e->getMessage());
                    $_SESSION['error'] = $e->getMessage();
                    header('Location: /auth/login');
                    exit;
                }
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

    public function showSetupPassword()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $_SESSION['error'] = 'Token tidak sah';
            header('Location: /auth/login');
            exit;
        }

        // Verify token from session
        if (!isset($_SESSION['setup_password_tokens'][$token]) || 
            $_SESSION['setup_password_tokens'][$token]['expires'] < time()) {
            $_SESSION['error'] = 'Pautan telah tamat tempoh atau tidak sah';
            header('Location: /auth/login');
            exit;
        }

        $this->view('auth/setup-password', ['token' => $token]);
    }

    public function setupPassword()
    {
        try {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($token) || empty($password) || empty($confirmPassword)) {
                throw new \Exception('Sila isi semua maklumat yang diperlukan');
            }

            // Verify token from session
            if (!isset($_SESSION['setup_password_tokens'][$token]) || 
                $_SESSION['setup_password_tokens'][$token]['expires'] < time()) {
                throw new \Exception('Pautan telah tamat tempoh atau tidak sah');
            }

            if ($password !== $confirmPassword) {
                throw new \Exception('Kata laluan tidak sepadan');
            }

            if (strlen($password) < 8) {
                throw new \Exception('Kata laluan mestilah sekurang-kurangnya 8 aksara');
            }

            // Get member_id from session token
            $memberId = $_SESSION['setup_password_tokens'][$token]['member_id'];

            // Update password
            if ($this->authUser->setMemberPassword($memberId, $password)) {
                // Clear the used token
                unset($_SESSION['setup_password_tokens'][$token]);
                
                $_SESSION['success'] = 'Kata laluan berjaya ditetapkan. Sila log masuk.';
                header('Location: /auth/login');
                exit;
            }

            throw new \Exception('Gagal menetapkan kata laluan');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/setup-password?token=' . $token);
            exit;
        }
    }
}