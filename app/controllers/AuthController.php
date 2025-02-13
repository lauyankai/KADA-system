<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\AuthUser;
use App\Models\User;
use App\Models\Director;
use PDO;

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
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                throw new \Exception('Sila isi semua maklumat yang diperlukan');
            }

            $user = new User();
            $member = $user->authenticate($_POST['username'], $_POST['password']);

            if ($member) {
                $_SESSION['member_id'] = $member['id'];
                $_SESSION['member_type'] = $member['member_type'];

                // Check if member is resigned
                if ($member['status'] === 'Resigned') {
                    // Get resignation date
                    $resignationInfo = $user->getResignationInfo($member['id']);
                    header('Location: /users/resigned?date=' . urlencode($resignationInfo['approved_at']));
                    exit();
                }

                header('Location: /users/dashboard');
                exit();
            }

            throw new \Exception('ID Pengguna atau kata laluan tidak sah');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit();
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
        $this->view('auth/setup-password', []);
    }

    public function setupPassword()
    {
        try {
            $ic = $_POST['ic'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($ic) || empty($password) || empty($confirmPassword)) {
                throw new \Exception('Sila isi semua maklumat yang diperlukan');
            }

            if ($password !== $confirmPassword) {
                throw new \Exception('Kata laluan tidak sepadan');
            }

            if (strlen($password) < 8) {
                throw new \Exception('Kata laluan mestilah sekurang-kurangnya 8 aksara');
            }

            // Find member by IC
            $member = $this->authUser->findMemberByIC($ic);
            if (!$member) {
                throw new \Exception('No. K/P tidak dijumpai. Sila pastikan anda telah diluluskan sebagai ahli.');
            }

            // Update password
            if ($this->authUser->setMemberPassword($member['id'], $password)) {
                $_SESSION['success'] = 'Kata laluan berjaya ditetapkan. Sila log masuk.';
                header('Location: /auth/login');
                exit;
            }

            throw new \Exception('Gagal menetapkan kata laluan');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/setup-password');
            exit;
        }
    }
}