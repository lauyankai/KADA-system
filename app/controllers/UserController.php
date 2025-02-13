<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\FamilyMember;

class UserController extends BaseController
{
    private $user;
    private $saving;
    private $familyMember;

    public function __construct()
    {
        $this->user = new User();
        $this->saving = new Saving();
        $this->familyMember = new FamilyMember();
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
            if (!$member) {
                throw new \Exception('Member not found');
            }

            // Get family members
            $familyMembers = $this->familyMember->getFamilyMembers($_SESSION['member_id'], 'member');

            $this->view('users/profile', [
                'member' => $member,
                'familyMembers' => $familyMembers
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

            $user = new User();
            if ($user->submitResignation($_SESSION['member_id'], $reasons)) {
                $_SESSION['success'] = 'Permohonan berhenti telah berjaya dihantar';
                header('Location: /users/dashboard');
            } else {
                throw new \Exception('Gagal menghantar permohonan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/resign');
        }
        exit();
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

    public function addFamilyMember()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $data = [
                'member_type' => 'member',
                'member_id' => $_SESSION['member_id'],
                'name' => $_POST['family_name'],
                'relationship' => $_POST['family_relationship'],
                'ic_no' => $_POST['family_ic']
            ];

            if ($this->familyMember->addFamilyMember($data)) {
                $_SESSION['success'] = 'Maklumat ahli keluarga berjaya ditambah';
            } else {
                throw new \Exception('Gagal menambah ahli keluarga');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /users/profile');
        exit;
    }

    public function updateFamilyMember($id)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            // Validate input
            if (empty($_POST['family_name']) || empty($_POST['family_ic']) || empty($_POST['family_relationship'])) {
                throw new \Exception('Sila lengkapkan semua maklumat');
            }

            // Validate IC format
            if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $_POST['family_ic'])) {
                throw new \Exception('Format no. kad pengenalan tidak sah');
            }

            $data = [
                'name' => $_POST['family_name'],
                'relationship' => $_POST['family_relationship'],
                'ic_no' => $_POST['family_ic']
            ];

            // Verify ownership of family member
            $familyMember = $this->familyMember->getFamilyMemberById($id);
            if (!$familyMember || $familyMember['member_id'] != $_SESSION['member_id']) {
                throw new \Exception('Tiada kebenaran untuk mengemaskini maklumat ini');
            }

            if ($this->familyMember->updateFamilyMember($id, $data)) {
                $_SESSION['success'] = 'Maklumat waris berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini maklumat waris');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /users/profile#family');
        exit;
    }

    public function deleteFamilyMember($id)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            if ($this->familyMember->deleteFamilyMember($id)) {
                echo json_encode(['success' => true]);
            } else {
                throw new \Exception('Gagal memadam ahli keluarga');
            }

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function updateFamilyMembers()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $familyIds = $_POST['family_id'] ?? [];
            $names = $_POST['family_name'] ?? [];
            $relationships = $_POST['family_relationship'] ?? [];
            $icNumbers = $_POST['family_ic'] ?? [];

            // Begin transaction
            $this->familyMember->getConnection()->beginTransaction();

            // Update existing family members
            foreach ($familyIds as $index => $id) {
                if (isset($names[$index], $relationships[$index], $icNumbers[$index])) {
                    $data = [
                        'name' => $names[$index],
                        'relationship' => $relationships[$index],
                        'ic_no' => $icNumbers[$index]
                    ];
                    $this->familyMember->updateFamilyMember($id, $data);
                }
            }

            // Add new family members
            for ($i = count($familyIds); $i < count($names); $i++) {
                if (!empty($names[$i]) && !empty($relationships[$i]) && !empty($icNumbers[$i])) {
                    $data = [
                        'member_type' => 'member',
                        'member_id' => $_SESSION['member_id'],
                        'name' => $names[$i],
                        'relationship' => $relationships[$i],
                        'ic_no' => $icNumbers[$i]
                    ];
                    $this->familyMember->addFamilyMember($data);
                }
            }

            $this->familyMember->getConnection()->commit();
            $_SESSION['success'] = 'Maklumat waris berjaya dikemaskini';

        } catch (\Exception $e) {
            if ($this->familyMember->getConnection()->inTransaction()) {
                $this->familyMember->getConnection()->rollBack();
            }
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /users/profile#family');
        exit;
    }
}