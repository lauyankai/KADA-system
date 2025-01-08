<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use PDO;
use PDOException;
use DateTime;
use Exception;

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
            $sql = "SELECT *
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
            if ($this->user->create($_POST)) {
                $_SESSION['success'] = "Pendaftaran anda telah berjaya dihantar dan sedang dalam proses pengesahan.";
                header('Location: /');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /users/create');
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


    public function showSavingsDashboard()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];

            // Get total savings
            $totalSavings = $this->user->getTotalSavings($memberId);

            // Get savings goals
            $savingsGoals = $this->user->getSavingsGoals($memberId);

            // Get recurring payment settings
            $recurringPayment = $this->user->getRecurringPayment($memberId);

            // Get recent transactions
            $recentTransactions = $this->user->getRecentTransactions($memberId);

            $this->view('admin/savings/dashboard', [
                'totalSavings' => $totalSavings,
                'savingsGoals' => $savingsGoals,
                'recurringPayment' => $recurringPayment,
                'recentTransactions' => $recentTransactions
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->view('admin/savings/dashboard', [
                'totalSavings' => 0,
                'savingsGoals' => [],
                'recurringPayment' => null,
                'recentTransactions' => []
            ]);
        }
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /auth/login');
            exit();
        }
    }

    public function approve($id)
    {
        try {
            $userModel = new User();
            $userModel->updateStatus($id, 'Lulus');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Lulus";
            header('Location: /users');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /users');
            exit();
        }
    }

    public function reject($id)
    {
        try {
            $userModel = new User();
            $userModel->updateStatus($id, 'Tolak');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Tolak";
            header('Location: /users');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /users');
            exit();
        }
    }

    public function viewMember($id)
    {
        // Get user model
        $userModel = new User();
        
        // Get user data by ID
        $data['member'] = $userModel->getUserById($id);
        
        // Load view
        $this->view('users/view', $data);
    }
    
    public function showSavingsApplication()
    {
        $this->checkAuth();
        $this->view('admin/savings/apply');
    }

    public function storeSavingsAccount()
    {
        $this->checkAuth();
        try {
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'target_amount' => $_POST['target_amount'],
                'duration_months' => $_POST['duration_months'],
                'monthly_deduction' => min(50, $_POST['target_amount'] / $_POST['duration_months']),
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime("+{$_POST['duration_months']} months")),
                'status' => 'active'
            ];

            $accountId = $this->user->createSavingsAccount($data);
            
            if ($accountId) {
                $_SESSION['success'] = 'Permohonan akaun simpanan berjaya dihantar';
                // Redirect to savings dashboard instead of showing success modal
                header('Location: /admin/savings');
                exit();
            } else {
                throw new \Exception('Gagal membuat akaun simpanan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/apply');
            exit();
        }
    }

    public function showDepositForm($id)
    {
        $this->checkAuth();
        try {
            $account = $this->user->getSavingsAccount($id);
            if (!$account || $account['member_id'] != $_SESSION['admin_id']) {
                throw new \Exception('Akaun simpanan tidak ditemui');
            }
            $this->view('admin/savings/deposit', ['account' => $account]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function makeDeposit()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $amount = $_POST['amount'];
            $paymentMethod = $_POST['payment_method'];
            
            // Get current balance
            $account = $this->user->getSavingsAccounts($memberId)[0];
            $previousBalance = $account['current_amount'];

            // Process the deposit immediately
            $referenceNo = 'DEP' . date('YmdHis') . rand(100, 999);
            $depositData = [
                'member_id' => $memberId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_no' => $referenceNo,
                'type' => 'deposit',
                'description' => 'Deposit segera melalui ' . ucfirst($paymentMethod)
            ];

            // Add deposit and get new balance
            $newBalance = $this->user->processDeposit($depositData);

            // Generate receipt data
            $receipt = [
                'type' => 'deposit',
                'reference_no' => $referenceNo,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
                'created_at' => date('Y-m-d H:i:s'),
                'description' => 'Deposit segera melalui ' . ucfirst($paymentMethod)
            ];

            // For debugging
            error_log('Setting receipt in session: ' . print_r($receipt, true));

            // Store receipt in session and redirect
            $_SESSION['receipt'] = $receipt;
            header('Location: /payment/receipt/' . $referenceNo);
            exit();

        } catch (\Exception $e) {
            error_log('Error in makeDeposit: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/deposit');
            exit();
        }
    }

    public function storeSavingsGoal()
    {
        $this->checkAuth();
        try {
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'name' => $_POST['goal_name'],
                'target_amount' => $_POST['target_amount'],
                'target_date' => $_POST['target_date'],
                'current_amount' => 0,
                'status' => 'active'
            ];

            // Calculate months between now and target date
            $targetDate = new DateTime($data['target_date']);
            $now = new DateTime();
            $months = $targetDate->diff($now)->m + ($targetDate->diff($now)->y * 12);
            
            if ($months < 1) {
                throw new \Exception('Tarikh sasaran mestilah sekurang-kurangnya sebulan dari sekarang');
            }

            // Calculate monthly savings needed
            $data['monthly_target'] = $data['target_amount'] / $months;

            $goalId = $this->user->createSavingsGoal($data);
            
            if ($goalId) {
                $_SESSION['success'] = 'Sasaran simpanan berjaya ditambah';
            } else {
                throw new \Exception('Gagal menambah sasaran simpanan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings');
        exit();
    }

    public function showRecurringSettings()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            
            // Only try to get recurring payment if table exists
            try {
                $recurringPayment = $this->user->getRecurringPayment($memberId);
            } catch (\PDOException $e) {
                // If table doesn't exist, just set payment to null
                $recurringPayment = null;
                error_log('Recurring payments table may not exist: ' . $e->getMessage());
            }
            
            $this->view('admin/savings/recurring', [
                'recurringPayment' => $recurringPayment
            ]);
        } catch (\Exception $e) {
            // Only show user-friendly error messages
            $_SESSION['error'] = 'Maaf, terdapat masalah teknikal. Sila cuba sebentar lagi.';
            $this->view('admin/savings/recurring', [
                'recurringPayment' => null
            ]);
        }
    }

    public function storeRecurringPayment()
    {
        $this->checkAuth();
        try {
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'amount' => $_POST['amount'],
                'deduction_day' => $_POST['deduction_day'],
                'payment_method' => $_POST['payment_method'],
                'status' => 'active',
                'next_deduction_date' => date('Y-m-d', strtotime('+1 month'))
            ];

            if ($this->user->setRecurringPayment($data)) {
                $_SESSION['success'] = 'Tetapan bayaran berulang berjaya disimpan';
                // Change redirect to dashboard
                header('Location: /admin/savings');
                exit();
            } else {
                throw new \Exception('Gagal menyimpan tetapan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            // Stay on recurring page if there's an error
            header('Location: /admin/savings/recurring');
            exit();
        }
    }

    public function showDepositPage()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $accounts = $this->user->getSavingsAccounts($memberId);
            
            $this->view('admin/savings/deposit_page', [
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function showTransferPage()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $accounts = $this->user->getSavingsAccounts($memberId);
            
            $this->view('admin/savings/transfer_page', [
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function makeTransfer()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $fromAccount = $_POST['from_account'];
            $toAccountNumber = $_POST['to_account'];
            $amount = $_POST['amount'];
            $reference = $_POST['reference'] ?? '';

            // Get source account details
            $sourceAccount = $this->user->getSavingsAccount($fromAccount);
            if (!$sourceAccount || $sourceAccount['member_id'] != $memberId) {
                throw new \Exception('Akaun sumber tidak sah');
            }

            // Check sufficient balance
            if ($sourceAccount['current_amount'] < $amount) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Directly deduct the amount from the source account
            $newBalance = $sourceAccount['current_amount'] - $amount;
            $this->user->updateAccountBalance($fromAccount, $newBalance);

            // Generate reference number
            $referenceNo = 'TRF' . date('YmdHis') . rand(100, 999);

            // Prepare transaction data
            $transactionData = [
                'account_id' => $fromAccount,
                'amount' => $amount,
                'type' => 'transfer_out', // Set the type to transfer_out
                'reference_no' => $referenceNo,
                'description' => $reference ?: 'Pindahan ke ' . $toAccountNumber
            ];

            // Record the outgoing transaction
            if (!$this->user->addTransaction($transactionData)) {
                throw new \Exception('Gagal merekod transaksi');
            }

            // Generate receipt data
            $receipt = [
                'type' => 'transfer',
                'reference_no' => $referenceNo,
                'amount' => $amount,
                'payment_method' => 'transfer',
                'from_account' => $memberId . '-SAV',
                'to_account' => $toAccountNumber,
                'recipient_name' => 'Ahli ' . $toAccountNumber, // Adjust as necessary
                'previous_balance' => $sourceAccount['current_amount'],
                'new_balance' => $newBalance,
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $reference ?: 'Pindahan ke ' . $toAccountNumber
            ];

            // Store receipt in session and redirect
            $_SESSION['receipt'] = $receipt;
            header('Location: /payment/receipt/' . $referenceNo);
            exit();

        } catch (\Exception $e) {
            error_log('Transfer Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings/transfer');
            exit();
        }
    }

    public function editSavingsGoal($id)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $goal = $this->user->getSavingsGoal($id);
            
            if (!$goal || $goal['member_id'] != $memberId) {
                throw new \Exception('Sasaran simpanan tidak ditemui');
            }

            $this->view('admin/savings/edit_goal', [
                'goal' => $goal
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function updateSavingsGoal($id)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $goal = $this->user->getSavingsGoal($id);
            
            if (!$goal || $goal['member_id'] != $memberId) {
                throw new \Exception('Sasaran simpanan tidak ditemui');
            }

            $data = [
                'id' => $id,
                'name' => $_POST['goal_name'],
                'target_amount' => $_POST['target_amount'],
                'target_date' => $_POST['target_date']
            ];

            // Calculate months between now and target date
            $targetDate = new DateTime($data['target_date']);
            $now = new DateTime();
            $months = $targetDate->diff($now)->m + ($targetDate->diff($now)->y * 12);
            
            if ($months < 1) {
                throw new \Exception('Tarikh sasaran mestilah sekurang-kurangnya sebulan dari sekarang');
            }

            // Calculate monthly savings needed
            $data['monthly_target'] = $data['target_amount'] / $months;

            if ($this->user->updateSavingsGoal($data)) {
                $_SESSION['success'] = 'Sasaran simpanan berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini sasaran simpanan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings');
        exit();
    }

    public function editRecurringPayment()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $payment = $this->user->getRecurringPayment($memberId);
            
            if (!$payment) {
                throw new \Exception('Tetapan bayaran berulang tidak ditemui');
            }

            $this->view('admin/savings/edit_recurring', [
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function updateRecurringPayment()
    {
        $this->checkAuth();
        try {
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'amount' => $_POST['amount'],
                'deduction_day' => $_POST['deduction_day'],
                'payment_method' => $_POST['payment_method'],
                'status' => 'active',
                'next_deduction_date' => date('Y-m-d', strtotime('+1 month'))
            ];

            if ($this->user->updateRecurringPayment($data)) {
                $_SESSION['success'] = 'Tetapan bayaran berulang berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini tetapan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings');
        exit();
    }

    public function verifyAccount($accountNumber)
    {
        $this->checkAuth();
        try {
            // Parse account number (format: ID-SAV)
            $parts = explode('-', $accountNumber);
            if (count($parts) !== 2 || $parts[1] !== 'SAV') {
                throw new \Exception('Format akaun tidak sah');
            }

            // Just return success with fake details
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'name' => 'Ahli ' . $parts[0], // Fake name based on account number
                'status' => 'active',
                'account_id' => $parts[0]
            ]);
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function showAccounts()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $accounts = $this->user->getSavingsAccounts($memberId);
            
            $this->view('admin/savings/accounts', [
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function showAddAccount()
    {
        $this->checkAuth();
        $this->view('admin/savings/add_account');
    }

    public function storeAccount()
    {
        $this->checkAuth();
        try {
            $data = [
                'member_id' => $_SESSION['admin_id'],
                'account_name' => $_POST['account_name'],
                'initial_amount' => $_POST['initial_amount'] ?? 0,
                'status' => 'active'
            ];

            $accountId = $this->user->createNewSavingsAccount($data);
            if ($accountId) {
                $_SESSION['success'] = 'Akaun baru berjaya ditambah';
            } else {
                throw new \Exception('Gagal menambah akaun baru');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings/accounts');
        exit();
    }

    public function deleteAccount($id)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $account = $this->user->getSavingsAccount($id);
            
            if (!$account || $account['member_id'] != $memberId) {
                throw new \Exception('Akaun tidak ditemui');
            }

            // Don't allow deleting the main account
            if ($account['target_amount'] === null) {
                throw new \Exception('Akaun utama tidak boleh dipadam');
            }

            if ($this->user->deleteSavingsAccount($id)) {
                $_SESSION['success'] = 'Akaun berjaya dipadam';
            } else {
                throw new \Exception('Gagal memadam akaun');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings/accounts');
        exit();
    }

    public function setMainAccount($id)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            if ($this->user->setMainDisplayAccount($id, $memberId)) {
                $_SESSION['success'] = 'Tetapan paparan berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini tetapan paparan');
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /admin/savings/accounts');
        exit();
    }

    public function showReceipt($referenceNo)
    {
        $this->checkAuth();
        try {
            // If receipt is in session, use it and clear it
            if (isset($_SESSION['receipt'])) {
                $receipt = $_SESSION['receipt'];
                unset($_SESSION['receipt']);
                $this->view('payment/receipt', ['receipt' => $receipt]);
                return;
            }

            // Otherwise fetch from database
            $transaction = $this->user->getTransactionByReference($referenceNo);
            if (!$transaction) {
                throw new \Exception('Transaksi tidak ditemui');
            }

            // Format receipt data
            $receipt = [
                'type' => $transaction['type'],
                'reference_no' => $transaction['reference_no'],
                'amount' => $transaction['amount'],
                'payment_method' => $transaction['payment_method'],
                'previous_balance' => $transaction['previous_balance'],
                'new_balance' => $transaction['new_balance'],
                'created_at' => $transaction['created_at'],
                'description' => $transaction['description']
            ];

            // For debugging
            error_log('Receipt Data: ' . print_r($receipt, true));

            $this->view('payment/receipt', ['receipt' => $receipt]);

        } catch (\Exception $e) {
            error_log('Error in showReceipt: ' . $e->getMessage());
            error_log('Transaction data: ' . print_r($transaction ?? null, true));
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    public function deleteGoal($id)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            
            // Verify goal belongs to member
            $goal = $this->user->getSavingsGoal($id);
            if (!$goal || $goal['member_id'] != $memberId) {
                throw new \Exception('Sasaran tidak ditemui');
            }

            if ($this->user->deleteSavingsGoal($id)) {
                $_SESSION['success'] = 'Sasaran berjaya dipadam';
            } else {
                throw new \Exception('Gagal memadam sasaran');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/savings');
        exit();
    }
}