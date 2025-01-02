<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use PDO;
use PDOException;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Fetch all pending register members
            $sql = "SELECT id, name, ic_no, gender, position, monthly_salary 
                    FROM pendingregistermember 
                    ORDER BY id DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            $pendingregistermembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Pass the data to the view
            $this->view('users/index', compact('pendingregistermembers'));
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching pending members: " . $e->getMessage();
            $this->view('users/index', ['pendingregistermembers' => []]);
        }
    }

    public function create()
    {
        $this->view('users/create');
    }

    public function store()
    {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert main member data
            $sql = "INSERT INTO pendingregistermember (
                name, ic_no, gender, religion, race, marital_status,
                member_number, pf_number, monthly_salary, position, grade,
                home_address, home_postcode, home_state, home_phone,
                office_address, office_postcode, office_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit, other_contributions,
                family_relationship, family_name, family_ic,
                status
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :member_number, :pf_number, :monthly_salary, :position, :grade,
                :home_address, :home_postcode, :home_state, :home_phone,
                :office_address, :office_postcode, :office_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit, :other_contributions,
                :family_relationship, :family_name, :family_ic,
                :status
            )";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'name' => $_POST['name'],
                'ic_no' => $_POST['ic_no'],
                'gender' => $_POST['gender'],
                'religion' => $_POST['religion'],
                'race' => $_POST['race'],
                'marital_status' => $_POST['marital_status'],
                'member_number' => $_POST['member_no'],
                'pf_number' => $_POST['pf_no'],
                'monthly_salary' => $_POST['monthly_salary'],
                'position' => $_POST['position'],
                'grade' => $_POST['grade'],
                'home_address' => $_POST['home_address'],
                'home_postcode' => $_POST['home_postcode'],
                'home_state' => $_POST['home_state'],
                'home_phone' => $_POST['home_phone'],
                'office_address' => $_POST['office_address'],
                'office_postcode' => $_POST['office_postcode'],
                'office_phone' => $_POST['office_phone'],
                'fax' => $_POST['fax'],
                'registration_fee' => $_POST['registration_fee'],
                'share_capital' => $_POST['share_capital'],
                'fee_capital' => $_POST['fee_capital'],
                'deposit_funds' => $_POST['deposit_funds'],
                'welfare_fund' => $_POST['welfare_fund'],
                'fixed_deposit' => $_POST['fixed_deposit'],
                'other_contributions' => $_POST['other_contributions'],
                'family_relationship' => $_POST['family_relationship'][0],
                'family_name' => $_POST['family_name'][0],
                'family_ic' => $_POST['family_ic'][0],
                'status' => 'pending'
            ]);
            
            // Commit transaction
            $conn->commit();
            
            // Redirect with success message
            $_SESSION['success'] = "Pendaftaran anda telah berjaya dihantar dan sedang dalam proses pengesahan.";
            header('Location: /');
            exit;
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($conn) {
                $conn->rollBack();
            }
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /create');
            exit;
        }
    }

    public function edit($id)
    {
        // Fetch the user data using the ID
        $user = $this->user->find($id);

        // Pass the user data to the 'users/edit' view
        $this->view('users/edit', compact('user'));
    }

    public function update($id)
    {
        $this->user->update($id, $_POST);
        header('Location: /');
    }

    public function delete($id)
    {
        $this->user->delete($id);
        header('Location: /');
    }

    public function adminDashboard()
    {
        try {
            $stats = $this->user->getDashboardStats();
            $recentSavings = $this->user->getRecentSavings();
            $recentRecurring = $this->user->getRecentRecurringPayments();

            $this->view('admin/dashboard', [
                'totalSavings' => $stats['totalSavings'],
                'recentSavings' => $recentSavings,
                'recentRecurring' => $recentRecurring
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->view('admin/dashboard', [
                'totalSavings' => 0,
                'recentSavings' => [],
                'recentRecurring' => []
            ]);
        }
    }

    public function savingsManagement()
    {
        try {
            $savingsAccounts = $this->user->getAllSavingsAccounts();
            $recurringPayments = $this->user->getAllRecurringPayments();

            $this->view('admin/savings/index', [
                'savingsAccounts' => $savingsAccounts,
                'recurringPayments' => $recurringPayments
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->view('admin/savings/index', [
                'savingsAccounts' => [],
                'recurringPayments' => []
            ]);
        }
    }

    public function showSavingsApplication()
    {
        try {
            $members = $this->user->getAllMembers();
            $this->view('admin/savings/apply', ['members' => $members]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit;
        }
    }

    public function storeSavingsAccount()
    {
        try {
            if (!isset($_SESSION['admin_id'])) {
                throw new \Exception('Sesi tamat. Sila log masuk semula.');
            }

            error_log('Admin ID from session: ' . $_SESSION['admin_id']);
            error_log('POST data received: ' . print_r($_POST, true));
            
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'target_amount' => $_POST['target_amount'],
                'duration_months' => $_POST['duration_months'],
                'monthly_deposit' => $_POST['target_amount'] / $_POST['duration_months'],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime("+{$_POST['duration_months']} months")),
                'status' => 'active',
                'current_amount' => 0
            ];

            // Validate data
            if (!is_numeric($data['target_amount']) || $data['target_amount'] < 100 || $data['target_amount'] > 10000) {
                throw new \Exception('Jumlah sasaran tidak sah');
            }

            if (!is_numeric($data['duration_months']) || $data['duration_months'] < 6 || $data['duration_months'] > 60) {
                throw new \Exception('Tempoh tidak sah');
            }

            $accountId = $this->user->createSavingsAccount($data);
            
            if ($accountId) {
                $_SESSION['success'] = 'Permohonan akaun simpanan berjaya dihantar';
                $this->view('admin/savings/apply', [
                    'success' => true,
                    'message' => 'Sila pastikan maklumat di atas adalah tepat sebelum menghantar permohonan.'
                ]);
                exit();
            } else {
                throw new \Exception('Gagal membuat akaun simpanan: Tiada ID dikembalikan');
            }

        } catch (\Exception $e) {
            error_log('Error in storeSavingsAccount: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/apply');
            exit();
        }
    }

    public function setupRecurringPayment()
    {
        try {
            // Get the latest savings account for the current admin
            $savingsAccount = $this->user->getLatestSavingsAccount($_SESSION['admin_id']);
            
            if (!$savingsAccount) {
                throw new \Exception('Tiada akaun simpanan aktif ditemui');
            }

            $data = [
                'savings_account_id' => $savingsAccount['id'],
                'amount' => $_POST['amount'],
                'frequency' => $_POST['frequency'],
                'payment_method' => $_POST['payment_method'],
                'next_payment_date' => $_POST['start_date'],
                'status' => 'active'
            ];

            $paymentId = $this->user->createRecurringPayment($data);
            
            if ($paymentId) {
                $_SESSION['success'] = 'Bayaran berulang berjaya didaftarkan';
            } else {
                throw new \Exception('Gagal mendaftar bayaran berulang');
            }

            header('Location: /admin/dashboard');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit;
        }
    }

    public function viewSavings($id)
    {
        try {
            $account = $this->user->getSavingsAccount($id);
            $transactions = $this->user->getSavingsTransactions($id);
            
            $this->view('admin/savings/view', [
                'account' => $account,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit();
        }
    }

    public function showDepositForm($id)
    {
        try {
            $account = $this->user->getSavingsAccount($id);
            $this->view('admin/savings/deposit', ['account' => $account]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit();
        }
    }

    public function makeDeposit($id)
    {
        try {
            $amount = $_POST['amount'];
            if (!is_numeric($amount) || $amount <= 0) {
                throw new \Exception('Jumlah tidak sah');
            }

            $this->user->addDeposit($id, $amount);
            $_SESSION['success'] = 'Simpanan berjaya ditambah';
            header('Location: /admin/savings/view/' . $id);
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/deposit/' . $id);
            exit();
        }
    }

    public function toggleSavingsStatus($id, $status)
    {
        try {
            $this->user->updateSavingsStatus($id, $status);
            $_SESSION['success'] = 'Status akaun simpanan berjaya dikemaskini';
            header('Location: /admin/dashboard');
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit();
        }
    }

    public function showRecurringPaymentForm()
    {
        try {
            // Check if user has active savings account
            $account = $this->user->getLatestSavingsAccount($_SESSION['admin_id']);
            if (!$account) {
                throw new \Exception('Anda perlu membuat akaun simpanan terlebih dahulu');
            }
            $this->view('admin/savings/recurring');
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/dashboard');
            exit();
        }
    }

    public function storeRecurringPayment()
    {
        try {
            $account = $this->user->getLatestSavingsAccount($_SESSION['admin_id']);
            if (!$account) {
                throw new \Exception('Tiada akaun simpanan aktif ditemui');
            }

            // Validate input
            if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
                throw new \Exception('Jumlah bayaran tidak sah');
            }

            if (!in_array($_POST['frequency'], ['weekly', 'biweekly', 'monthly'])) {
                throw new \Exception('Kekerapan bayaran tidak sah');
            }

            if (!in_array($_POST['payment_method'], ['bank_transfer', 'salary_deduction'])) {
                throw new \Exception('Kaedah bayaran tidak sah');
            }

            $data = [
                'savings_account_id' => $account['id'],
                'amount' => $_POST['amount'],
                'frequency' => $_POST['frequency'],
                'payment_method' => $_POST['payment_method'],
                'next_payment_date' => $_POST['start_date'],
                'status' => 'active'
            ];

            $paymentId = $this->user->createRecurringPayment($data);
            
            if ($paymentId) {
                $_SESSION['success'] = 'Bayaran berulang berjaya didaftarkan';
                $this->view('admin/savings/recurring', [
                    'success' => true,
                    'message' => 'Sila pastikan maklumat di atas adalah tepat sebelum menghantar permohonan.'
                ]);
                exit();
            } else {
                throw new \Exception('Gagal mendaftar bayaran berulang: Tiada ID dikembalikan');
            }

        } catch (\Exception $e) {
            error_log('Error in storeRecurringPayment: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/recurring');
            exit();
        }
    }
}
