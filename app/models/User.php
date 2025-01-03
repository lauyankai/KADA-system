<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class User extends Model
{
    public function all() 
    {
        $stmt = $this->getConnection()->query("SELECT * FROM admins"); // Use query() for SELECT statements
        return $stmt->fetchAll(); // Use fetchAll() to get all records
    }

    public function find($id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Use bindParam() to bind variables
        $stmt->execute(); // Use execute() to run the query
        return $stmt->fetch(); // Use fetch() to get a single record
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO pendingregistermember (
                name, ic_no, gender, religion, race, marital_status,
                member_number, pf_number, position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :member_number, :pf_number, :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            $params = [
                ':name' => $data['name'],
                ':ic_no' => $data['ic_no'],
                ':gender' => $data['gender'],
                ':religion' => $data['religion'],
                ':race' => $data['race'],
                ':marital_status' => $data['marital_status'],
                ':member_number' => $data['member_no'],
                ':pf_number' => $data['pf_no'],
                ':position' => $data['position'],
                ':grade' => $data['grade'],
                ':monthly_salary' => $data['monthly_salary'],
                ':home_address' => $data['home_address'],
                ':home_postcode' => $data['home_postcode'],
                ':home_state' => $data['home_state'],
                ':office_address' => $data['office_address'],
                ':office_postcode' => $data['office_postcode'],
                ':office_phone' => $data['office_phone'],
                ':home_phone' => $data['home_phone'],
                ':fax' => $data['fax'] ?? null,
                ':registration_fee' => $data['registration_fee'] ?? 0,
                ':share_capital' => $data['share_capital'] ?? 0,
                ':fee_capital' => $data['fee_capital'] ?? 0,
                ':deposit_funds' => $data['deposit_funds'] ?? 0,
                ':welfare_fund' => $data['welfare_fund'] ?? 0,
                ':fixed_deposit' => $data['fixed_deposit'] ?? 0,
                ':other_contributions' => $data['other_contributions'] ?? null
            ];

            // Debug log
            error_log('SQL: ' . $sql);
            error_log('Params: ' . print_r($params, true));

            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log('PDO Error: ' . print_r($stmt->errorInfo(), true));
            }
            
            return $result;

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Database error occurred: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        $stmt = $this->getConnection()->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->execute([ // Use execute() to run the query
            ':name' => $data['name'], // Use named placeholders to prevent SQL injection
            ':email' => $data['email'], // Use named placeholders to prevent SQL injection
            ':id' => $id, // Use named placeholders to prevent SQL injection
        ]);
        return $stmt; // Return the PDOStatement object
    }

    public function delete($id)
    {
        $stmt = $this->getConnection()->prepare("DELETE FROM users WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Use bindParam() to bind variables
        $stmt->execute(); // Use execute() to run the query
        return $stmt; // Return the PDOStatement object
    }

    public function getTotalSavings($memberId)
    {
        try {
            // First check if member exists
            $checkMember = "SELECT id FROM admins WHERE id = :member_id";
            $stmt = $this->getConnection()->prepare($checkMember);
            $stmt->execute([':member_id' => $memberId]);
            
            if (!$stmt->fetch()) {
                throw new \Exception('Ahli tidak ditemui');
            }

            // Get total savings from main display account or default account
            $sql = "SELECT COALESCE(current_amount, 0) as total 
                    FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND (display_main = 1 OR target_amount IS NULL)
                    ORDER BY display_main DESC
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 901.00;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getTotalSavings: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan jumlah simpanan: ' . $e->getMessage());
        }
    }

    public function getTotalMonthlyDeduction($memberId)
    {
        try {
            // First check if member exists
            $checkMember = "SELECT id FROM admins WHERE id = :member_id";
            $stmt = $this->getConnection()->prepare($checkMember);
            $stmt->execute([':member_id' => $memberId]);
            
            if (!$stmt->fetch()) {
                throw new \Exception('Ahli tidak ditemui');
            }

            // Get total monthly deduction
            $sql = "SELECT COALESCE(SUM(monthly_deduction), 0) as total 
                    FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active'";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 0;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getTotalMonthlyDeduction: ' . $e->getMessage());
            error_log('SQL State: ' . $e->errorInfo[0]);
            error_log('Error Code: ' . $e->errorInfo[1]);
            error_log('Error Message: ' . $e->errorInfo[2]);
            throw new \Exception('Gagal mendapatkan jumlah potongan: ' . $e->getMessage());
        }
    }

    public function getSavingsAccounts($memberId)
    {
        try {
            // Get the main savings account first (the one with RM901)
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND target_amount IS NULL  -- This identifies the main account
                    ORDER BY created_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $mainAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            // If no main account exists, create one with RM901
            if (!$mainAccount) {
                $sql = "INSERT INTO savings_accounts (
                    member_id, current_amount, target_amount, 
                    duration_months, monthly_deposit, status
                ) VALUES (
                    :member_id, 901.00, NULL, 
                    NULL, 0, 'active'
                )";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':member_id' => $memberId]);
                
                // Get the newly created account
                $sql = "SELECT * FROM savings_accounts 
                        WHERE id = LAST_INSERT_ID()";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute();
                $mainAccount = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Get all accounts including the main one
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    ORDER BY target_amount IS NULL DESC, created_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $accounts;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai akaun');
        }
    }

    public function getRecentTransactions($memberId)
    {
        try {
            $sql = "SELECT t.*, a.current_amount 
                    FROM savings_transactions t
                    JOIN savings_accounts a ON t.savings_account_id = a.id
                    WHERE a.member_id = :member_id
                    ORDER BY t.created_at DESC 
                    LIMIT 10";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format transactions for display
            foreach ($transactions as &$transaction) {
                // Ensure current_amount is set
                if (!isset($transaction['current_amount'])) {
                    $transaction['current_amount'] = 0;
                }
                
                // Format description if empty
                if (empty($transaction['description'])) {
                    switch ($transaction['type']) {
                        case 'deposit':
                            $transaction['description'] = 'Deposit melalui ' . ucfirst($transaction['payment_method']);
                            break;
                        case 'transfer_in':
                            $transaction['description'] = 'Terima pindahan';
                            break;
                        case 'transfer_out':
                            $transaction['description'] = 'Pindah keluar';
                            break;
                        case 'withdrawal':
                            $transaction['description'] = 'Pengeluaran';
                            break;
                    }
                }
            }
            
            return $transactions;
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah transaksi');
        }
    }

    public function createSavingsAccount($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            $sql = "INSERT INTO savings_accounts (
                member_id, target_amount, monthly_deduction,
                duration_months, start_date, end_date, status
            ) VALUES (
                :member_id, :target_amount, :monthly_deduction,
                :duration_months, :start_date, :end_date, :status
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($data);
            $accountId = $this->getConnection()->lastInsertId();

            $this->getConnection()->commit();
            return $accountId;
        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat akaun simpanan');
        }
    }

    public function getSavingsAccount($id)
    {
        try {
            $sql = "SELECT * FROM savings_accounts WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function addDeposit($accountId, $amount, $paymentMethod, $referenceNo = null)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Add transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method, 
                reference_no, description
            ) VALUES (
                :account_id, :amount, 'deposit', :payment_method,
                :reference_no, :description
            )";
            
            $description = 'Simpanan melalui ' . ($paymentMethod === 'cash' ? 'tunai' : 
                          ($paymentMethod === 'bank_transfer' ? 'pindahan bank' : 'potongan gaji'));
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $amount,
                ':payment_method' => $paymentMethod,
                ':reference_no' => $referenceNo,
                ':description' => $description
            ]);

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount 
                    WHERE id = :id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':id' => $accountId,
                ':amount' => $amount
            ]);

            $this->getConnection()->commit();
            return true;
        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menambah simpanan');
        }
    }

    public function createSavingsGoal($data)
    {
        try {
            $sql = "INSERT INTO savings_goals (
                member_id, name, target_amount, current_amount,
                target_date, monthly_target, status
            ) VALUES (
                :member_id, :name, :target_amount, :current_amount,
                :target_date, :monthly_target, :status
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $data['member_id'],
                ':name' => $data['name'],
                ':target_amount' => $data['target_amount'],
                ':current_amount' => $data['current_amount'],
                ':target_date' => $data['target_date'],
                ':monthly_target' => $data['monthly_target'],
                ':status' => $data['status']
            ]);

            return $this->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat sasaran simpanan');
        }
    }

    public function getSavingsGoals($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_goals 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY created_at DESC";
            
            // Debug logging
            error_log("Fetching savings goals for member: " . $memberId);
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Found " . count($goals) . " savings goals");
            
            return $goals;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai sasaran');
        }
    }

    public function getRecurringPayment($memberId)
    {
        try {
            // First check if table exists
            $checkTable = $this->getConnection()->query("SHOW TABLES LIKE 'recurring_payments'");
            if ($checkTable->rowCount() == 0) {
                // Table doesn't exist, return null
                return null;
            }

            $sql = "SELECT * FROM recurring_payments 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Return null if no record found instead of false
            return $result ?: null;
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return null;
        }
    }

    public function setRecurringPayment($data)
    {
        try {
            // Check if user already has an active recurring payment
            $existingPayment = $this->getRecurringPayment($data['member_id']);
            
            if ($existingPayment) {
                // Update existing payment
                $sql = "UPDATE recurring_payments 
                        SET amount = :amount,
                            deduction_day = :deduction_day,
                            payment_method = :payment_method,
                            next_deduction_date = :next_deduction_date,
                            status = :status,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE member_id = :member_id 
                        AND status = 'active'";
            } else {
                // Create new payment
                $sql = "INSERT INTO recurring_payments (
                    member_id, amount, deduction_day, payment_method,
                    next_deduction_date, status
                ) VALUES (
                    :member_id, :amount, :deduction_day, :payment_method,
                    :next_deduction_date, :status
                )";
            }

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':member_id' => $data['member_id'],
                ':amount' => $data['amount'],
                ':deduction_day' => $data['deduction_day'],
                ':payment_method' => $data['payment_method'],
                ':next_deduction_date' => $data['next_deduction_date'],
                ':status' => $data['status']
            ]);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan tetapan bayaran berulang');
        }
    }

    public function processDeposit($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get or create savings account
            $sql = "SELECT id FROM savings_accounts 
                    WHERE member_id = :member_id 
                    LIMIT 1";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $data['member_id']]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                // Create new savings account if doesn't exist
                $sql = "INSERT INTO savings_accounts (member_id, current_amount) 
                        VALUES (:member_id, 0)";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':member_id' => $data['member_id']]);
                $accountId = $this->getConnection()->lastInsertId();
            } else {
                $accountId = $account['id'];
            }

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount
                    WHERE id = :account_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $data['amount']
            ]);

            // Get the new balance
            $sql = "SELECT current_amount FROM savings_accounts 
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $accountId]);
            $newBalance = $stmt->fetch(PDO::FETCH_ASSOC)['current_amount'];

            // Add transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method,
                reference_no, description
            ) VALUES (
                :account_id, :amount, 'deposit', :payment_method,
                :reference_no, :description
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $data['amount'],
                ':payment_method' => $data['payment_method'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'] ?? null
            ]);

            $this->getConnection()->commit();
            return $newBalance;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memproses deposit: ' . $e->getMessage());
        }
    }

    public function processTransfer($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // First verify source account exists and belongs to member
            $sql = "SELECT id, current_amount FROM savings_accounts 
                    WHERE id = :account_id AND member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['from_account'],
                ':member_id' => $data['member_id']
            ]);
            $fromAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fromAccount) {
                throw new \Exception('Akaun sumber tidak sah');
            }

            // Verify destination account exists
            $sql = "SELECT id FROM savings_accounts WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $data['to_account']]);
            if (!$stmt->fetch()) {
                throw new \Exception('Akaun penerima tidak sah');
            }

            // Check sufficient balance
            if ($fromAccount['current_amount'] < $data['amount']) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Deduct from source account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount - :amount
                    WHERE id = :account_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['from_account'],
                ':amount' => $data['amount']
            ]);

            // Add transfer_out transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method,
                reference_no, description
            ) VALUES (
                :account_id, :amount, 'transfer_out', 'bank_transfer',
                :reference_no, :description
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['from_account'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'] ?? 'Pindah keluar'
            ]);

            // Add to destination account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount
                    WHERE id = :account_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['to_account'],
                ':amount' => $data['amount']
            ]);

            // Add transfer_in transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method,
                reference_no, description
            ) VALUES (
                :account_id, :amount, 'transfer_in', 'bank_transfer',
                :reference_no, :description
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['to_account'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'] ?? 'Terima pindahan'
            ]);

            // Get new balance
            $sql = "SELECT current_amount FROM savings_accounts 
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $data['from_account']]);
            $newBalance = $stmt->fetch(PDO::FETCH_ASSOC)['current_amount'];

            $this->getConnection()->commit();
            return $newBalance;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memproses pindahan: ' . $e->getMessage());
        }
    }

    public function getSavingsGoal($id)
    {
        try {
            $sql = "SELECT * FROM savings_goals WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat sasaran');
        }
    }

    public function updateSavingsGoal($data)
    {
        try {
            $sql = "UPDATE savings_goals 
                    SET name = :name,
                        target_amount = :target_amount,
                        target_date = :target_date,
                        monthly_target = :monthly_target,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':target_amount' => $data['target_amount'],
                ':target_date' => $data['target_date'],
                ':monthly_target' => $data['monthly_target']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini sasaran simpanan');
        }
    }

    public function updateRecurringPayment($data)
    {
        try {
            $sql = "UPDATE recurring_payments 
                    SET amount = :amount,
                        deduction_day = :deduction_day,
                        payment_method = :payment_method,
                        next_deduction_date = :next_deduction_date,
                        status = :status,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE member_id = :member_id";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':member_id' => $data['member_id'],
                ':amount' => $data['amount'],
                ':deduction_day' => $data['deduction_day'],
                ':payment_method' => $data['payment_method'],
                ':next_deduction_date' => $data['next_deduction_date'],
                ':status' => $data['status']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini tetapan bayaran berulang');
        }
    }

    public function getMainSavingsAccount($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND target_amount IS NULL
                    AND status = 'active'
                    LIMIT 1";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getMemberDetails($memberId)
    {
        try {
            $sql = "SELECT id, name, status FROM admins WHERE id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat ahli');
        }
    }

    public function updateAccountBalance($accountId, $newBalance)
    {
        try {
            $sql = "UPDATE savings_accounts 
                    SET current_amount = :balance 
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':balance' => $newBalance,
                ':account_id' => $accountId
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini baki akaun');
        }
    }

    public function addTransaction($data)
    {
        try {
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method,
                reference_no, description
            ) VALUES (
                :account_id, :amount, :type, 'bank_transfer',
                :reference_no, :description
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':account_id' => $data['account_id'],
                ':amount' => $data['amount'],
                ':type' => $data['type'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal merekod transaksi');
        }
    }

    public function createNewSavingsAccount($data)
    {
        try {
            $sql = "INSERT INTO savings_accounts (
                member_id, name, current_amount, status
            ) VALUES (
                :member_id, :name, :initial_amount, :status
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $data['member_id'],
                ':name' => $data['account_name'],
                ':initial_amount' => $data['initial_amount'],
                ':status' => $data['status']
            ]);

            return $this->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat akaun baru');
        }
    }

    public function deleteSavingsAccount($id)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Check if account has balance
            $sql = "SELECT current_amount FROM savings_accounts WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account['current_amount'] > 0) {
                throw new \Exception('Akaun masih mempunyai baki');
            }

            // Delete transactions first
            $sql = "DELETE FROM savings_transactions WHERE savings_account_id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Then delete account
            $sql = "DELETE FROM savings_accounts WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memadam akaun');
        }
    }

    public function setMainDisplayAccount($accountId, $memberId)
    {
        try {
            $this->getConnection()->beginTransaction();

            // First verify account belongs to member
            $sql = "SELECT id, target_amount FROM savings_accounts 
                    WHERE id = :id AND member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':id' => $accountId,
                ':member_id' => $memberId
            ]);
            
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$account) {
                throw new \Exception('Akaun tidak ditemui');
            }

            // Reset all accounts' display_main flag
            $sql = "UPDATE savings_accounts 
                    SET display_main = 0 
                    WHERE member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);

            // Set the selected account as main display
            $sql = "UPDATE savings_accounts 
                    SET display_main = 1 
                    WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $accountId]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini tetapan paparan');
        }
    }

    public function getTransactionByReference($reference)
    {
        try {
            $sql = "SELECT t.*, a.current_amount 
                    FROM savings_transactions t
                    JOIN savings_accounts a ON t.savings_account_id = a.id
                    WHERE t.reference_no = :reference";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':reference' => $reference]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat transaksi');
        }
    }

    public function deleteSavingsGoal($id)
    {
        try {
            $sql = "DELETE FROM savings_goals WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memadam sasaran');
        }
    }
}
