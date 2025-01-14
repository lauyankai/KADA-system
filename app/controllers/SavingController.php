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
        
        header('Location: /users/savings/page');
        exit;
    }

    public function deleteAccount($id)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            if ($this->saving->deleteSavingsAccount($id)) {
                $_SESSION['success'] = 'Akaun simpanan berjaya dipadam';
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /users/savings/page');
        exit;
    }

    public function setMainAccount($id)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];

            if ($this->saving->setMainDisplayAccount($id, $memberId)) {
                $_SESSION['success'] = 'Tetapan paparan berjaya dikemaskini';
            } else {
                throw new \Exception('Gagal mengemaskini tetapan paparan');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /users/savings/page');
        exit;
    }

    public function index()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get recurring payment data first
            $recurringPayments = $this->saving->getRecurringPayments($memberId);
            
            // Get all required data for the page
            $data = [
                'account' => $this->saving->getSavingsAccount($memberId),
                'goals' => $this->saving->getSavingsGoals($memberId),
                'recurring' => $recurringPayments,
                'transactions' => $this->saving->getRecentTransactions($memberId, 5),
                'recurringPayment' => !empty($recurringPayments) ? $recurringPayments[0] : null
            ];

            $this->view('users/savings/page', $data);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
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
                
                // Get recurring payment data first
                $recurringPayments = $this->saving->getRecurringPayments($memberId);
                
                // Get all required data for the page
                $data = [
                    'member' => $this->user->getUserById($memberId),
                    'account' => $this->saving->getSavingsAccount($memberId),
                    'goals' => $this->saving->getSavingsGoals($memberId),
                    'recurring' => $recurringPayments,
                    'transactions' => $this->saving->getRecentTransactions($memberId, 5),
                    'recurringPayment' => !empty($recurringPayments) ? $recurringPayments[0] : null,
                    'totalSavings' => $this->saving->getTotalSavings($memberId),
                    'accountBalance' => $this->saving->getTotalSavings($memberId)
                ];

                $this->view('users/savings/page', $data);

            } catch (\Exception $e) {
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
                    'account_id' => $account['id'],
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'description' => 'Deposit ke akaun simpanan',
                    'reference_no' => $reference,
                    'type' => 'deposit',
                    'status' => 'completed'
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

            header('Location: /users/savings/page');
            exit;
        }

        public function showRecurringSettings()
        {
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
                
                $this->view('users/recurring', [
                    'recurringPayment' => $recurringPayment
                ]);
            } catch (\Exception $e) {
                // Only show user-friendly error messages
                $_SESSION['error'] = 'Maaf, terdapat masalah teknikal. Sila cuba sebentar lagi.';
                $this->view('users/recurring', [
                    'recurringPayment' => null
                ]);
            }
        }

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
                header('Location: /users/savings/page');
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
            
            header('Location: /users/savings/page');
            exit;
        }

        public function transferPage()
        {
            try {
                $memberId = $_SESSION['admin_id'];
                $accounts = $this->user->getSavingsAccounts($memberId);
                
                $this->view('users/savings/transfer/index', [
                    'accounts' => $accounts
                ]);
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/transfer/index');
                exit();
            }
        }

        public function transfer()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $toAccountId = filter_input(INPUT_POST, 'to_account_id', FILTER_SANITIZE_STRING);
                
                if (!$amount || $amount <= 0) {
                    throw new \Exception('Sila masukkan jumlah yang sah');
                }

                // Get source account
                $fromAccount = $this->saving->getSavingsAccount($memberId);
                if (!$fromAccount) {
                    throw new \Exception('Akaun simpanan tidak ditemui');
                }

                // Generate reference number
                $reference = 'TRF' . date('YmdHis') . rand(100, 999);

                $data = [
                    'account_id' => $fromAccount['id'],
                    'amount' => $amount,
                    'description' => 'Pemindahan dana',
                    'reference_no' => $reference,
                    'type' => 'transfer',
                    'status' => 'completed',
                    'payment_method' => 'transfer'
                ];

                if ($this->saving->recordTransaction($data)) {
                    $_SESSION['success'] = 'Pemindahan berjaya dilakukan';
                    $_SESSION['receipt'] = [
                        'type' => 'transfer',
                        'amount' => $amount,
                        'reference_no' => $reference,
                        'payment_method' => 'transfer',
                        'created_at' => date('Y-m-d H:i:s'),
                        'description' => 'Pemindahan dana'
                    ];
                    header('Location: /payment/receipt/' . $reference);
                    exit;
                }

                throw new \Exception('Gagal melakukan pemindahan');

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
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
                header('Location: /users/savings/page');
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

            header('Location: /users/savings/page');
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

                $memberId = $_SESSION['member_id'];
                $goal = $this->saving->getSavingsGoal($id);
                
                if (!$goal || $goal['member_id'] != $memberId) {
                    throw new \Exception('Sasaran tidak ditemui');
                }

                if ($this->saving->deleteSavingsGoal($id)) {
                    $_SESSION['success'] = 'Sasaran berjaya dipadam';
                } else {
                    throw new \Exception('Gagal memadam sasaran');
                }

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /users/savings/page');
            exit;
        }

        public function storeAccount()
        {
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

            header('Location: /users/accounts/accountList');
            exit();
        }

        public function showReceipt($referenceNo)
        {
            try {
                if (isset($_SESSION['receipt'])) {
                    $receipt = $_SESSION['receipt'];
                    unset($_SESSION['receipt']);
                    $this->view('payment/receipt', ['receipt' => $receipt]);
                    return;
                }

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
                header('Location: /users');
                exit();
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
                header('Location: /users/savings/page');
                exit;
            }
        }

        public function showTransferForm()
        {
            try {
                if (!isset($_SESSION['member_id'])) {
                    throw new \Exception('Sila log masuk untuk mengakses');
                }

                $memberId = $_SESSION['member_id'];
                
                // Get list of other members' accounts for transfer
                $accounts = $this->saving->getOtherMembersAccounts($memberId);

                $this->view('users/savings/transfer/index', [
                    'accounts' => $accounts
                ]);

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /users/savings/page');
                exit;
            }
        }

}
