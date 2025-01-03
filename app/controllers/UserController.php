<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use PDO;
use PDOException;
use DateTime;

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
            $db = new Database();
            $conn = $db->connect();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Capitalize name
            $name = mb_convert_case(trim($_POST['name']), MB_CASE_TITLE, "UTF-8");
            
            // Generate Member Number (Format: M-YYYY-XXXX)
            $year = date('Y');
            $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING_INDEX(member_number, '-', -1) AS UNSIGNED)) as max_num 
                                 FROM pendingregistermember 
                                 WHERE member_number LIKE 'M-$year-%'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextNum = ($result['max_num'] ?? 0) + 1;
            $memberNumber = sprintf("M-%s-%04d", $year, $nextNum);
            
            // Generate PF Number (Format: PF-YYYY-XXXX)
            $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING_INDEX(pf_number, '-', -1) AS UNSIGNED)) as max_num 
                                 FROM pendingregistermember 
                                 WHERE pf_number LIKE 'PF-$year-%'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextPfNum = ($result['max_num'] ?? 0) + 1;
            $pfNumber = sprintf("PF-%s-%04d", $year, $nextPfNum);
            
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
                'name' => $name,  // Use the capitalized name
                'ic_no' => $_POST['ic_no'],
                'gender' => $_POST['gender'],
                'religion' => $_POST['religion'],
                'race' => $_POST['race'],
                'marital_status' => $_POST['marital_status'],
                'member_number' => $memberNumber,  // Auto-generated
                'pf_number' => $pfNumber,         // Auto-generated
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

    private function checkAuth()
    {
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error'] = 'Sila log masuk terlebih dahulu';
            header('Location: /auth/login');
            exit();
        }
    }

    public function showSavingsDashboard()
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            
            // Initialize default values
            $data = [
                'totalSavings' => 0,
                'monthlyDeduction' => 0,
                'savingsAccounts' => [],
                'recentTransactions' => [],
                'savingsGoals' => [],
                'recurringPayment' => null
            ];
            
            try {
                $data['totalSavings'] = $this->user->getTotalSavings($memberId);
            } catch (\Exception $e) {
                error_log('Error getting total savings: ' . $e->getMessage());
            }
            
            try {
                $data['monthlyDeduction'] = $this->user->getTotalMonthlyDeduction($memberId);
            } catch (\Exception $e) {
                error_log('Error getting monthly deduction: ' . $e->getMessage());
            }
            
            try {
                $data['savingsAccounts'] = $this->user->getSavingsAccounts($memberId);
            } catch (\Exception $e) {
                error_log('Error getting savings accounts: ' . $e->getMessage());
            }
            
            try {
                $data['recentTransactions'] = $this->user->getRecentTransactions($memberId);
            } catch (\Exception $e) {
                error_log('Error getting recent transactions: ' . $e->getMessage());
            }

            try {
                $data['savingsGoals'] = $this->user->getSavingsGoals($memberId);
            } catch (\Exception $e) {
                error_log('Error getting savings goals: ' . $e->getMessage());
            }

            try {
                $data['recurringPayment'] = $this->user->getRecurringPayment($memberId);
            } catch (\Exception $e) {
                error_log('Error getting recurring payment: ' . $e->getMessage());
            }
            
            $this->view('admin/savings/dashboard', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->view('admin/savings/dashboard', [
                'totalSavings' => 0,
                'monthlyDeduction' => 0,
                'savingsAccounts' => [],
                'recentTransactions' => [],
                'savingsGoals' => [],
                'recurringPayment' => null
            ]);
        }
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
                'payment_details' => json_encode([
                    'method' => $paymentMethod,
                    'status' => 'completed',
                    'timestamp' => date('Y-m-d H:i:s')
                ]),
                'reference_no' => $referenceNo,
                'type' => 'deposit',
                'status' => 'completed',
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

            // Show receipt
            $this->view('payment/receipt', ['receipt' => $receipt]);

        } catch (\Exception $e) {
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
            $toAccountNumber = $_POST['to_account']; // This is the ID-SAV format
            $amount = $_POST['amount'];
            $reference = $_POST['reference'] ?? '';

            // Parse account number to get member ID
            $parts = explode('-', $toAccountNumber);
            if (count($parts) !== 2 || $parts[1] !== 'SAV') {
                throw new \Exception('Format akaun tidak sah');
            }

            // Get source account details
            $sourceAccount = $this->user->getSavingsAccount($fromAccount);
            if (!$sourceAccount || $sourceAccount['member_id'] != $memberId) {
                throw new \Exception('Akaun sumber tidak sah');
            }

            // Check sufficient balance
            if ($sourceAccount['current_amount'] < $amount) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Update source account balance
            $newBalance = $sourceAccount['current_amount'] - $amount;
            $this->user->updateAccountBalance($fromAccount, $newBalance);

            // Record the transaction
            $referenceNo = 'TRF' . date('YmdHis') . rand(100, 999);
            $this->user->addTransaction([
                'account_id' => $fromAccount,
                'amount' => $amount,
                'type' => 'transfer_out',
                'reference_no' => $referenceNo,
                'description' => $reference ?: 'Pindahan ke ' . $toAccountNumber
            ]);

            // Show receipt
            $this->view('payment/receipt', [
                'receipt' => [
                    'type' => 'transfer',
                    'reference_no' => $referenceNo,
                    'amount' => $amount,
                    'from_account' => $memberId . '-SAV',
                    'to_account' => $toAccountNumber,
                    'recipient_name' => 'Ahli ' . $parts[0], // Fake recipient name
                    'previous_balance' => $sourceAccount['current_amount'],
                    'new_balance' => $newBalance,
                    'created_at' => date('Y-m-d H:i:s'),
                    'description' => $reference ?: 'Pindahan ke ' . $toAccountNumber
                ]
            ]);

        } catch (\Exception $e) {
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

    public function showReceipt($reference)
    {
        $this->checkAuth();
        try {
            $memberId = $_SESSION['admin_id'];
            $transaction = $this->user->getTransactionByReference($reference);
            
            if (!$transaction) {
                throw new \Exception('Transaksi tidak ditemui');
            }

            // Verify transaction belongs to member
            $account = $this->user->getSavingsAccount($transaction['savings_account_id']);
            if (!$account || $account['member_id'] != $memberId) {
                throw new \Exception('Tiada akses');
            }

            // Get previous balance
            $previousBalance = $account['current_amount'];
            if ($transaction['type'] == 'deposit' || $transaction['type'] == 'transfer_in') {
                $previousBalance -= $transaction['amount'];
            } else {
                $previousBalance += $transaction['amount'];
            }

            // Prepare receipt data
            $receipt = [
                'type' => $transaction['type'],
                'reference_no' => $transaction['reference_no'],
                'amount' => $transaction['amount'],
                'payment_method' => $transaction['payment_method'],
                'from_account' => $memberId . '-SAV',
                'to_account' => null,
                'recipient_name' => null,
                'previous_balance' => $previousBalance,
                'new_balance' => $account['current_amount'],
                'created_at' => $transaction['created_at'],
                'description' => $transaction['description']
            ];

            // If it's a transfer, add recipient details
            if ($transaction['type'] == 'transfer_out') {
                // Extract recipient account from description
                preg_match('/Pindahan ke (\d+-SAV)/', $transaction['description'], $matches);
                if (isset($matches[1])) {
                    $receipt['to_account'] = $matches[1];
                    $receipt['recipient_name'] = 'Ahli ' . explode('-', $matches[1])[0];
                }
            }

            $this->view('payment/receipt', ['receipt' => $receipt]);

        } catch (\Exception $e) {
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
