<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Saving;
use App\Models\Loan;

class UserController extends BaseController
{
    private $user;
    private $saving;

    public function __construct()
    {
        $this->user = new User();
        $this->saving = new Saving();
    }

    // User Dashboard
    public function dashboard()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses dashboard');
            }

            $memberId = $_SESSION['member_id'];
            $member = $this->user->getUserById($memberId);
            
            // Get savings account
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            $totalSavings = $savingsAccount ? $savingsAccount['current_amount'] : 0;
            
            // Get active loans and calculate total loan amount
            $loan = new Loan();
            $activeLoans = $loan->getActiveLoansByMemberId($memberId);
            $totalLoanAmount = 0;
            
            if (!empty($activeLoans)) {
                foreach ($activeLoans as $activeLoan) {
                    $totalLoanAmount += $activeLoan['amount'];
                }
            }

            $totalSavings = $this->saving->getTotalSavings($memberId);
            $recentActivities = $this->user->getRecentActivities($memberId);

            $this->view('users/dashboard', [
                'member' => $member,
                'savingsAccount' => $savingsAccount,
                'activeLoans' => $activeLoans,
                'totalSavings' => $totalSavings,
                'totalLoanAmount' => $totalLoanAmount,
                'recentActivities' => $recentActivities,
                'title' => 'Dashboard Ahli'
            ]);

        } catch (\Exception $e) {
            error_log('Dashboard Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function profile()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            $member = $this->user->getUserById($_SESSION['member_id']);
            $resignationDate = $this->user->hasResignationRecord($_SESSION['member_id']);

            if (!$member) {
                throw new \Exception('Member not found');
            }

            $this->view('users/profile', [
                'member' => $member,
                'hasResignationRecord' => $resignationDate ? true : false,
                'resignationDate' => $resignationDate
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /dashboard');
            exit();
        }
    }

    public function update()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            $section = $_POST['section'] ?? '';
            
            $data = [];
            
            switch($section) {
                case 'personal':
                    $data = [
                        'email' => $_POST['email'] ?? '',
                        'mobile_phone' => $_POST['mobile_phone'] ?? '',
                        'home_phone' => $_POST['home_phone'] ?? '',
                        'marital_status' => $_POST['marital_status'] ?? '',
                        'home_address' => $_POST['home_address'] ?? '',
                        'home_postcode' => $_POST['home_postcode'] ?? '',
                        'home_state' => $_POST['home_state'] ?? ''
                    ];
                    break;
                    
                case 'employment':
                    $data = [
                        'position' => $_POST['position'] ?? '',
                        'grade' => $_POST['grade'] ?? '',
                        'monthly_salary' => $_POST['monthly_salary'] ?? '',
                        'office_address' => $_POST['office_address'] ?? '',
                        'office_postcode' => $_POST['office_postcode'] ?? '',
                        'office_state' => $_POST['office_state'] ?? '',
                        'office_phone' => $_POST['office_phone'] ?? ''
                    ];
                    break;
                    
                case 'family':
                    $data = [
                        'family_name' => $_POST['family_name'] ?? '',
                        'family_ic' => $_POST['family_ic'] ?? '',
                        'family_relationship' => $_POST['family_relationship'] ?? ''
                    ];
                    break;
                    
                default:
                    throw new \Exception('Invalid section');
            }

            // Remove empty values
            $data = array_filter($data, function($value) {
                return $value !== '';
            });

            if ($this->user->updateProfile($memberId, $data)) {
                $_SESSION['success'] = 'Profil berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini profil');
            }

            header('Location: /users/profile');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/profile');
            exit;
        }
    }

    public function showResignForm()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            $user = new User();
            $member = $user->getUserById($_SESSION['member_id']);
            $member = $user->getUserById($_SESSION['member_id']);

            if (!$member) {
                throw new \Exception('Member not found');
            }

            $this->view('users/resign', ['member' => $member]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/profile');
            exit();
        }
    }

    public function submitResignation()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            if (!isset($_POST['reasons']) || empty($_POST['reasons'])) {
                throw new \Exception('Sila nyatakan sebab berhenti');
            }

            $reasons = array_filter($_POST['reasons']); // Remove empty values
            if (count($reasons) > 5) {
                throw new \Exception('Maksimum 5 sebab sahaja dibenarkan');
            }

            if ($this->user->submitResignation($_SESSION['member_id'], $reasons)) {
                // Show success page instead of redirecting with message
                $this->view('users/resign_success');
            } else {
                throw new \Exception('Gagal menghantar permohonan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/resign');
            exit();
        }
    }

    public function showInitialFees()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $fees = $this->user->getMemberFees($_SESSION['member_id']);
            if (!$fees) {
                throw new \Exception('Maklumat yuran tidak dijumpai');
            }

            $this->view('users/fees/initial', ['fees' => $fees]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function processInitialFees()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $paymentMethod = $_POST['payment_method'] ?? null;
            if (!$paymentMethod) {
                throw new \Exception('Sila pilih kaedah pembayaran');
            }

            $result = $this->user->processInitialFeePayment(
                $_SESSION['member_id'], 
                $paymentMethod
            );

            if ($result) {
                header('Location: /users/fees/success');
                exit;
            }

            throw new \Exception('Gagal memproses pembayaran');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/fees/initial');
            exit;
        }
    }

    public function resigned()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            $resignedDate = $_GET['date'] ?? null;
            if (!$resignedDate) {
                $resignationInfo = $this->user->getResignationInfo($_SESSION['member_id']);
                $resignedDate = $resignationInfo['approved_at'];
            }

            $this->view('users/resigned', [
                'resignedDate' => $resignedDate
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit();
        }
    }

    public function showReactivateForm()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            $this->view('users/reactivate');
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit();
        }
    }

    public function submitReactivation()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit();
            }

            if (!isset($_POST['reasons']) || empty($_POST['reasons'])) {
                throw new \Exception('Sila nyatakan sebab memohon semula');
            }

            if (!isset($_POST['agreement'])) {
                throw new \Exception('Sila sahkan maklumat yang diberikan');
            }

            $reasons = array_filter($_POST['reasons']); // Remove empty values
            if (count($reasons) > 5) {
                throw new \Exception('Maksimum 5 sebab sahaja dibenarkan');
            }

            if ($this->user->submitReactivation($_SESSION['member_id'], $reasons)) {
                // Show success page instead of redirecting
                $this->view('users/reactivate_success');
            } else {
                throw new \Exception('Gagal menghantar permohonan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/reactivate');
            exit();
        }
    }
}