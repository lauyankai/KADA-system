<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Saving;
use App\Models\User;

class SavingController extends BaseController
{
    private $saving;
    private $user;

    public function __construct()
    {
        $this->saving = new Saving();
        $this->user = new User();
    }

    public function createAccount()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Validate and sanitize input
            $targetAmount = filter_input(INPUT_POST, 'target_amount', FILTER_VALIDATE_FLOAT);
            $durationMonths = filter_input(INPUT_POST, 'duration_months', FILTER_VALIDATE_INT);
            $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);

            if (!$targetAmount || !$durationMonths || !$startDate) {
                throw new \Exception('Sila isi semua maklumat yang diperlukan');
            }

            // Calculate monthly deduction
            $monthlyDeduction = $targetAmount / $durationMonths;
            
            // Calculate end date
            $endDate = date('Y-m-d', strtotime($startDate . " +$durationMonths months"));

            $accountData = [
                'member_id' => $memberId,
                'target_amount' => $targetAmount,
                'duration_months' => $durationMonths,
                'monthly_deduction' => $monthlyDeduction,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active'
            ];

            if ($this->saving->createSavingsAccount($accountData)) {
                $_SESSION['success'] = 'Akaun simpanan berjaya dibuat';
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /users/savings');
        exit;
    }

    public function index()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /login');
                exit;
            }

            $account = $this->saving->getAccountByMemberId($_SESSION['member_id']);
            $goals = $this->saving->getAllSavingsGoals($_SESSION['member_id']);
            
            // Get transaction history
            $transactions = [];
            if ($account) {
                $transactions = $this->saving->getTransactionHistory($account['id']);
            }

            $this->view('users/savings/page', [
                'account' => $account,
                'goals' => $goals,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    public function dashboard()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses dashboard');
                }

                $memberId = $_SESSION['member_id'];
                $member = $this->user->getUserById($memberId);
                $totalSavings = $this->savings->getTotalSavings($memberId);

                $this->view('members/dashboard', [
                    'member' => $member,
                    'totalSavings' => $totalSavings
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /auth/login');
                exit;
            }
        }

        public function savingsDashboard()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                
                // Get all required data
                $account = $this->saving->getSavingsAccount($memberId);
                $transactions = $this->saving->getRecentTransactions($memberId, 5);
                $goals = $this->saving->getSavingsGoals($memberId);
                $totalMonthlyPayments = $this->saving->getTotalMonthlyLoanPayments($memberId);
                $totalSavings = $this->saving->getTotalSavings($memberId);
                $member = $this->user->getUserById($memberId);
                $recurringPayments = $this->saving->getRecurringPayments($memberId);

                $this->view('users/savings/page', [
                    'member' => $member,
                    'account' => $account,
                    'transactions' => $transactions,
                    'goals' => $goals,
                    'totalMonthlyPayments' => $totalMonthlyPayments,
                    'totalSavings' => $totalSavings,
                    'accountBalance' => $totalSavings,
                    'recurring' => $recurringPayments,
                    'recurringPayment' => !empty($recurringPayments) ? $recurringPayments[0] : null
                ]);

            } catch (\Exception $e) {
                error_log('Error in savingsDashboard: ' . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/dashboard');
                exit;
            }
        }



        public function depositPage()
        {
            try {
                $memberId = $_SESSION['admin_id'];
                $accounts = $this->user->getSavingsAccounts($memberId);
                
                $this->view('users/savings/deposit/index', [
                    'accounts' => $accounts
                ]);
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users');
                exit();
            }
        }

        public function deposit()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                
                // Validate and sanitize input
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
                
                if (!$amount || $amount <= 0) {
                    throw new \Exception('Sila masukkan jumlah yang sah');
                }

                // Get active savings account
                $account = $this->saving->getSavingsAccount($memberId);
                if (!$account) {
                    throw new \Exception('Akaun simpanan tidak ditemui');
                }

                // Generate reference number
                $reference = 'DEP' . date('YmdHis') . rand(100, 999);

                $data = [
                    'member_id' => $memberId,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'type' => 'deposit',
                    'reference_no' => $reference,
                    'description' => 'Deposit ke akaun simpanan'
                ];

                if ($this->saving->recordTransaction($data)) {
                    $_SESSION['success'] = 'Deposit berjaya dilakukan';
                    $_SESSION['receipt'] = [
                        'type' => 'deposit',
                        'amount' => $amount,
                        'reference_no' => $reference,
                        'payment_method' => $paymentMethod,
                        'created_at' => date('Y-m-d H:i:s'),
                        'description' => 'Deposit ke akaun simpanan'
                    ];
                    header('Location: /payment/receipt/' . $reference);
                    exit;
                }

                throw new \Exception('Gagal melakukan deposit');

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/deposit');
                exit;
            }
        }

        public function storeSavingsGoal()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Validate and sanitize input
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $targetAmount = filter_input(INPUT_POST, 'target_amount', FILTER_VALIDATE_FLOAT);
                $targetDate = filter_input(INPUT_POST, 'target_date', FILTER_SANITIZE_STRING);

                if (!$name || !$targetAmount || !$targetDate) {
                    throw new \Exception('Sila isi semua maklumat yang diperlukan');
                }

                if ($targetAmount < 10) {
                    throw new \Exception('Jumlah sasaran minimum adalah RM10.00');
                }

                // Calculate months between now and target date
                $targetDateTime = new \DateTime($targetDate);
                $now = new \DateTime();
                $interval = $targetDateTime->diff($now);
                $months = ($interval->y * 12) + $interval->m;
                
                if ($months < 1) {
                    throw new \Exception('Tarikh sasaran mestilah sekurang-kurangnya sebulan dari sekarang');
                }

                // Calculate monthly target
                $monthlyTarget = $targetAmount / $months;

                $data = [
                    'member_id' => $_SESSION['member_id'],
                    'name' => $name,
                    'target_amount' => $targetAmount,
                    'current_amount' => 0,
                    'target_date' => $targetDate,
                    'monthly_target' => $monthlyTarget,
                    'status' => 'active'
                ];

                if ($this->saving->createSavingsGoal($data)) {
                    $_SESSION['success'] = 'Sasaran simpanan berjaya ditambah';
                } else {
                    throw new \Exception('Gagal menambah sasaran simpanan');
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /users/savings');
            exit;
        }

        // public function showRecurringSettings()
        // {
        //     try {
        //         $memberId = $_SESSION['admin_id'];
                
        //         // Only try to get recurring payment if table exists
        //         try {
        //             $recurringPayment = $this->user->getRecurringPayment($memberId);
        //         } catch (\PDOException $e) {
        //             // If table doesn't exist, just set payment to null
        //             $recurringPayment = null;
        //             error_log('Recurring payments table may not exist: ' . $e->getMessage());
        //         }
                
        //         $this->view('users/recurring', [
        //             'recurringPayment' => $recurringPayment
        //         ]);
        //     } catch (\Exception $e) {
        //         // Only show user-friendly error messages
        //         $_SESSION['error'] = 'Maaf, terdapat masalah teknikal. Sila cuba sebentar lagi.';
        //         $this->view('users/recurring', [
        //             'recurringPayment' => null
        //         ]);
        //     }
        // }

        public function storeRecurringPayment()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Validate and sanitize input
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $deductionDay = filter_input(INPUT_POST, 'deduction_day', FILTER_VALIDATE_INT);
                $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

                if (!$amount || !$deductionDay || !$paymentMethod) {
                    throw new \Exception('Sila isi semua maklumat yang diperlukan');
                }

                if ($amount < 10) {
                    throw new \Exception('Jumlah minimum adalah RM10.00');
                }

                if ($deductionDay < 1 || $deductionDay > 28) {
                    throw new \Exception('Hari potongan tidak sah');
                }

                $data = [
                    'member_id' => $_SESSION['member_id'],
                    'amount' => $amount,
                    'deduction_day' => $deductionDay,
                    'payment_method' => $paymentMethod,
                    'status' => 'active',
                    'next_deduction_date' => $this->calculateNextDeductionDate($deductionDay)
                ];

                if ($this->saving->createRecurringPayment($data)) {
                    $_SESSION['success'] = 'Tetapan bayaran berulang berjaya disimpan';
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: /users/savings/page');
            exit;
        }

        private function calculateNextDeductionDate($day)
        {
            $today = new \DateTime();
            $nextDeduction = new \DateTime(date('Y-m-') . $day);
            
            // If the day has passed this month, set to next month
            if ($today > $nextDeduction) {
                $nextDeduction->modify('+1 month');
            }
            
            return $nextDeduction->format('Y-m-d');
        }

        public function editRecurringPayment()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $payment = $this->saving->getRecurringPayments($memberId);
                
                if (!$payment) {
                    throw new \Exception('Tetapan bayaran berulang tidak ditemui');
                }

                $this->view('users/savings/recurring/edit_recurring', [
                    'payment' => $payment[0]
                ]);
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function updateRecurringPayment()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Validate and sanitize input
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $deductionDay = filter_input(INPUT_POST, 'deduction_day', FILTER_VALIDATE_INT);
                $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

                if (!$amount || !$deductionDay || !$paymentMethod) {
                    throw new \Exception('Sila isi semua maklumat yang diperlukan');
                }

                if ($amount < 10) {
                    throw new \Exception('Jumlah minimum adalah RM10.00');
                }

                if ($deductionDay < 1 || $deductionDay > 28) {
                    throw new \Exception('Hari potongan tidak sah');
                }

                $data = [
                    'member_id' => $_SESSION['member_id'],
                    'amount' => $amount,
                    'deduction_day' => $deductionDay,
                    'payment_method' => $paymentMethod,
                    'status' => 'active',
                    'next_deduction_date' => $this->calculateNextDeductionDate($deductionDay)
                ];

                if ($this->saving->updateRecurringPayment($data)) {
                    $_SESSION['success'] = 'Tetapan bayaran berulang berjaya dikemaskini';
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: /users/savings');
            exit;
        }

        public function transferPage()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                
                // Get member's main account
                $currentAccount = $this->saving->getMemberAccount($memberId);
                if (!$currentAccount) {
                    throw new \Exception('Akaun tidak ditemui');
                }

                $this->view('users/savings/transfer/index', [
                    'currentAccount' => $currentAccount
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/dashboard');
                exit;
            }
        }

        public function makeTransfer()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $senderAccountNumber = $_POST['sender_account_number'];
                
                // Get member details
                $member = $this->user->getUserById($memberId);
                if (!$member) {
                    throw new \Exception('Gagal membuat pemindahan: Data ahli tidak ditemui');
                }
                
                // Verify sender account belongs to logged in member
                $senderAccount = $this->saving->verifyMemberAccount($memberId, $senderAccountNumber);
                if (!$senderAccount) {
                    throw new \Exception('Nombor akaun anda tidak sah');
                }

                $amount = $_POST['amount'];
                $description = $_POST['description'] ?? '';
                $transferType = $_POST['transfer_type'];

                // Validate amount
                if (!is_numeric($amount)) {
                    throw new \Exception('Gagal membuat pemindahan: Jumlah tidak sah');
                }
                
                if ($amount < 10) {
                    throw new \Exception('Gagal membuat pemindahan: Jumlah minimum pemindahan adalah RM10.00');
                }

                // Check sufficient balance
                if ($senderAccount['current_amount'] < $amount) {
                    throw new \Exception('Gagal membuat pemindahan: Baki tidak mencukupi (Baki: RM' . number_format($senderAccount['current_amount'], 2) . ')');
                }

                if ($transferType === 'member') {
                    $recipientAccountNumber = $_POST['recipient_account_number'];
                    if (empty($recipientAccountNumber)) {
                        throw new \Exception('Gagal membuat pemindahan: Sila masukkan nombor akaun penerima');
                    }

                    // Validate account number format
                    if (!preg_match('/^SAV-\d{6}-\d{4}$/', $recipientAccountNumber)) {
                        throw new \Exception('Gagal membuat pemindahan: Format nombor akaun tidak sah');
                    }

                    // Check not transferring to same account
                    if ($senderAccountNumber === $recipientAccountNumber) {
                        throw new \Exception('Gagal membuat pemindahan: Tidak boleh pindah ke akaun yang sama');
                    }
                    
                    $result = $this->saving->transferToMember($senderAccount['id'], $recipientAccountNumber, $amount, $description);

                    // Generate transaction data for receipt
                    $transaction = [
                        'reference_no' => 'TRF' . date('YmdHis') . rand(100, 999),
                        'created_at' => date('Y-m-d H:i:s'),
                        'type' => 'transfer_out',
                        'amount' => $amount,
                        'member_name' => $member->name,
                        'member_id' => $memberId,
                        'account_number' => $senderAccount['account_number'],
                        'sender_account_number' => $senderAccount['account_number'],
                        'recipient_account_number' => $recipientAccountNumber,
                        'description' => $description,
                        'payment_method' => 'fpx'
                    ];

                    // Show receipt
                    $this->view('users/savings/receipt', ['transaction' => $transaction]);
                    return;
                } else {
                    // Transfer to other bank account
                    $bankName = $_POST['bank_name'];
                    $bankAccountNumber = $_POST['bank_account_number'];
                    $recipientName = $_POST['recipient_name'];
                    
                    if (empty($bankName) || empty($bankAccountNumber) || empty($recipientName)) {
                        throw new \Exception('Sila lengkapkan maklumat bank');
                    }

                    $result = $this->saving->transferToOther($senderAccount['id'], $amount, $description, [
                        'bank_name' => $bankName,
                        'account_number' => $bankAccountNumber,
                        'recipient_name' => $recipientName
                    ]);

                    // Generate transaction data for receipt
                    $transaction = [
                        'reference_no' => $result['reference_no'],
                        'created_at' => $result['date'],
                        'type' => 'transfer_out',
                        'amount' => $amount,
                        'member_name' => $member->name,
                        'member_id' => $memberId,
                        'account_number' => $senderAccount['account_number'],
                        'recipient_account_number' => $bankAccountNumber,
                        'recipient_bank' => $bankName,
                        'recipient_name' => $recipientName,
                        'description' => $description,
                        'payment_method' => 'bank_transfer'
                    ];

                    // Show receipt
                    $this->view('users/savings/receipt', ['transaction' => $transaction]);
                    return;
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                error_log('Transfer Error: ' . $e->getMessage() . ' | Member ID: ' . $_SESSION['member_id'] . 
                         ' | Sender Account: ' . ($_POST['sender_account_number'] ?? 'not set') . 
                         ' | Amount: ' . ($_POST['amount'] ?? 'not set'));
                header('Location: /users/savings/transfer');
                exit;
            }
        }

        public function editSavingsGoal($id)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $goal = $this->saving->getSavingsGoal($id);
                
                if (!$goal || $goal['member_id'] != $memberId) {
                    throw new \Exception('Sasaran simpanan tidak ditemui');
                }

                $this->view('users/savings/goals/edit_goal', [
                    'goal' => $goal,
                    'title' => 'Kemaskini Sasaran Simpanan'
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function updateSavingsGoal($id)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $goal = $this->saving->getSavingsGoal($id);
                
                if (!$goal || $goal['member_id'] != $memberId) {
                    throw new \Exception('Sasaran simpanan tidak ditemui');
                }

                // Validate and sanitize input
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $targetAmount = filter_input(INPUT_POST, 'target_amount', FILTER_VALIDATE_FLOAT);
                $targetDate = filter_input(INPUT_POST, 'target_date', FILTER_SANITIZE_STRING);

                if (!$name || !$targetAmount || !$targetDate) {
                    throw new \Exception('Sila isi semua maklumat yang diperlukan');
                }

                if ($targetAmount < 10) {
                    throw new \Exception('Jumlah sasaran minimum adalah RM10.00');
                }

                // Calculate months between now and target date
                $targetDateTime = new \DateTime($targetDate);
                $now = new \DateTime();
                $interval = $targetDateTime->diff($now);
                $months = ($interval->y * 12) + $interval->m;
                
                if ($months < 1) {
                    throw new \Exception('Tarikh sasaran mestilah sekurang-kurangnya sebulan dari sekarang');
                }

                // Calculate monthly target
                $monthlyTarget = $targetAmount / $months;

                $data = [
                    'id' => $id,
                    'member_id' => $memberId,
                    'name' => $name,
                    'target_amount' => $targetAmount,
                    'target_date' => $targetDate,
                    'monthly_target' => $monthlyTarget
                ];

                if ($this->saving->updateSavingsGoal($data)) {
                    $_SESSION['success'] = 'Sasaran simpanan berjaya dikemaskini';
                } else {
                    throw new \Exception('Gagal mengemaskini sasaran simpanan');
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /users/savings');
            exit;
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

        public function deleteGoal($id)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Verify ownership before deletion
                $goal = $this->saving->getSavingsGoalById($id);
                if (!$goal || $goal['member_id'] != $_SESSION['member_id']) {
                    throw new \Exception('Sasaran simpanan tidak dijumpai');
                }

                if ($this->saving->deleteSavingsGoal($id)) {
                    $_SESSION['success'] = 'Sasaran simpanan berjaya dipadam';
                } else {
                    throw new \Exception('Gagal memadam sasaran simpanan');
                }

                header('Location: /users/savings');
                exit;

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function showReceipt($referenceNo)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $transaction = $this->saving->getTransactionByReference($referenceNo);
                
                if (!$transaction) {
                    throw new \Exception('Resit tidak dijumpai');
                }

                $transactionMemberId = $transaction['member_id'] ?? null;
                
                if (!$transactionMemberId || (string)$transactionMemberId !== (string)$_SESSION['member_id']) {
                    error_log('Access denied - IDs do not match');
                    throw new \Exception('Anda tidak mempunyai akses kepada resit ini');
                }

                $this->view('users/savings/receipt', [
                    'transaction' => $transaction
                ]);
            } catch (\Exception $e) {
                error_log('Error in showReceipt: ' . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function showDepositForm()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $account = $this->saving->getSavingsAccount($memberId);

                if (!$account) {
                    throw new \Exception('Akaun simpanan tidak ditemui');
                }

                $this->view('users/savings/deposit/index', [
                    'account' => $account
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function showTransferForm()
        {
            if (!isset($_SESSION['member_id'])) {
                $_SESSION['error'] = 'Sila log masuk untuk mengakses';
                header('Location: /auth/login');
                exit;
            }
            
            $this->view('users/savings/transfer/index');
        }

        public function editGoal($id)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $goal = $this->saving->getSavingsGoalById($id);
                
                if (!$goal || $goal['member_id'] != $_SESSION['member_id']) {
                    throw new \Exception('Sasaran simpanan tidak dijumpai');
                }

                $this->view('users/savings/goals/edit_goal', ['goal' => $goal]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function updateGoal($id)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Validate input
                if (empty($_POST['name']) || empty($_POST['target_amount']) || empty($_POST['target_date'])) {
                    throw new \Exception('Sila lengkapkan semua maklumat');
                }

                $data = [
                    'id' => $id,
                    'member_id' => $_SESSION['member_id'],
                    'name' => $_POST['name'],
                    'target_amount' => $_POST['target_amount'],
                    'target_date' => $_POST['target_date']
                ];

                if ($this->saving->updateSavingsGoal($data)) {
                    $_SESSION['success'] = 'Sasaran simpanan berjaya dikemaskini';
                } else {
                    throw new \Exception('Gagal mengemaskini sasaran simpanan');
                }

                header('Location: /users/savings');
                exit;

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/goals/edit/' . $id);
                exit;
            }
        }

        public function createGoal()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $this->view('users/savings/goals/create_goal');

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function storeGoal()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Validate input
                if (empty($_POST['name']) || empty($_POST['target_amount']) || empty($_POST['target_date'])) {
                    throw new \Exception('Sila lengkapkan semua maklumat');
                }

                // Calculate monthly target
                $targetDate = new \DateTime($_POST['target_date']);
                $today = new \DateTime();
                $monthsDiff = ($targetDate->diff($today)->y * 12) + $targetDate->diff($today)->m;
                $monthlyTarget = $monthsDiff > 0 ? $_POST['target_amount'] / $monthsDiff : $_POST['target_amount'];

                $data = [
                    'member_id' => $_SESSION['member_id'],
                    'name' => $_POST['name'],
                    'target_amount' => $_POST['target_amount'],
                    'current_amount' => 0,
                    'monthly_target' => $monthlyTarget,
                    'target_date' => $_POST['target_date'],
                    'status' => 'active'
                ];

                if ($this->saving->createSavingsGoal($data)) {
                    $_SESSION['success'] = 'Sasaran simpanan berjaya ditambah';
                    header('Location: /users/savings');
                    exit;
                }

                throw new \Exception('Gagal menambah sasaran simpanan');

            } catch (\Exception $e) {
                error_log('Error in storeGoal: ' . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/goals/create');
                exit;
            }
        }

        public function showSavingsPage()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $account = $this->saving->getSavingsAccount($memberId);
                $goals = $this->saving->getAllSavingsGoals($memberId);
                
                // Get transaction history if account exists
                $transactions = [];
                if ($account) {
                    $transactions = $this->saving->getTransactionHistory($account['id']);
                }
                
                $this->view('users/savings', [
                    'account' => $account,
                    'goals' => $goals,
                    'transactions' => $transactions
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users');
                exit;
            }
        }

        public function makeDeposit()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                // Debug: Log session data
                error_log('Session member_id: ' . $_SESSION['member_id']);

                // Validate input
                if (empty($_POST['amount']) || empty($_POST['payment_method'])) {
                    throw new \Exception('Sila lengkapkan semua maklumat');
                }

                $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
                if ($amount === false || $amount < 10 || $amount > 1000) {
                    throw new \Exception('Jumlah deposit tidak sah. Minimum: RM10, Maksimum: RM1,000');
                }

                $referenceNo = 'DEP' . time() . rand(1000, 9999);
                
                $data = [
                    'member_id' => $_SESSION['member_id'],
                    'amount' => $amount,
                    'payment_method' => $_POST['payment_method'],
                    'type' => 'deposit',
                    'reference_no' => $referenceNo,
                    'description' => 'Deposit ke akaun simpanan'
                ];

                // Debug: Log deposit data
                error_log('Attempting deposit with data: ' . print_r($data, true));

                if ($this->saving->deposit($data)) {
                    header('Location: /users/savings/receipt/' . $referenceNo);
                    exit;
                }

                throw new \Exception('Gagal memproses deposit');

            } catch (\Exception $e) {
                error_log('Error in makeDeposit: ' . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings');
                exit;
            }
        }

        public function processTransfer()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $fromAccountId = $_POST['from_account_id'];
                
                $account = $this->saving->getAccountById($fromAccountId);
                if (!$account || $account['member_id'] != $memberId) {
                    throw new \Exception('Akaun tidak sah');
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/transfer');
                exit;
            }
        }

        public function verifyMember($memberId)
        {
            try {
                if (!isset($_SESSION['member_id']) || $_SESSION['member_id'] != $memberId) {
                    throw new \Exception('ID Ahli tidak sah');
                }

                $account = $this->saving->getCurrentAccount($memberId);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'account' => $account
                ]);
                
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

        public function showRecurring()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $loans = $this->saving->getActiveLoansWithRecurring($memberId);

                $this->view('users/savings/recurring/index', [
                    'loans' => $loans ?? [] // Ensure loans is always an array
                ]);

            } catch (\Exception $e) {
                error_log('Error in showRecurring: ' . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/page'); // Use the correct route
                exit;
            }
        }

        public function updateRecurring($loanId)
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    throw new \Exception('Data tidak sah');
                }

                // Validate data
                if (!isset($data['monthly_payment']) || !is_numeric($data['monthly_payment'])) {
                    throw new \Exception('Jumlah bayaran tidak sah');
                }
                if (!isset($data['next_deduction_date'])) {
                    throw new \Exception('Tarikh potongan seterusnya tidak sah');
                }

                // Format the data
                $data['monthly_payment'] = number_format((float)$data['monthly_payment'], 2, '.', '');

                $result = $this->saving->updateRecurringPayment($loanId, $data);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Pembayaran berulang berjaya dikemaskini'
                ]);
                exit;

            } catch (\Exception $e) {
                error_log('Error in updateRecurring: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }

}
