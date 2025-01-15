<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Saving extends BaseModel
{
    public function getTotalSavings($memberId)
    {
        try {
            $sql = "SELECT SUM(current_amount) as total 
                    FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active'";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan jumlah simpanan');
        }
    }

    public function getSavingsAccount($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active' 
                    AND display_main = 1 
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getRecentTransactions($memberId, $limit = 10)
    {
        try {
            $sql = "SELECT t.* 
                    FROM savings_transactions t
                    JOIN savings_accounts a ON t.savings_account_id = a.id
                    WHERE a.member_id = :member_id
                    ORDER BY t.created_at DESC
                    LIMIT :limit";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah transaksi');
        }
    }

    public function getSavingsGoals($memberId)
    {
        try {
            // Get the current account balance
            $accountBalance = $this->getTotalSavings($memberId);
            
            $sql = "SELECT g.*, 
                    CASE 
                        WHEN g.target_amount > 0 
                        THEN LEAST(100, (:account_balance / g.target_amount * 100))
                        ELSE 0 
                    END as progress
                    FROM savings_goals g 
                    WHERE g.member_id = :member_id 
                    AND g.status = 'active'
                    ORDER BY g.created_at DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $memberId,
                ':account_balance' => $accountBalance
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan matlamat simpanan');
        }
    }

    public function getRecurringPayments($memberId)
    {
        try {
            $sql = "SELECT * FROM recurring_payments 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY created_at DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan pembayaran berulang');
        }
    }

    public function makeDeposit($accountId, $amount, $data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount,
                        updated_at = NOW()
                    WHERE id = :account_id";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $accountId
            ]);

            // Record transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id,
                        type,
                        amount,
                        reference_no,
                        description,
                        payment_method,
                        status,
                        created_at
                    ) VALUES (
                        :account_id,
                        :type,
                        :amount,
                        :reference_no,
                        :description,
                        :payment_method,
                        :status,
                        NOW()
                    )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':type' => $data['type'],
                ':amount' => $amount,
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'],
                ':payment_method' => $data['payment_method'],
                ':status' => $data['status']
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal melakukan deposit');
        }
    }

    public function makeTransfer($fromAccountId, $toAccountNumber, $amount)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get destination account
            $sql = "SELECT id FROM savings_accounts 
                    WHERE account_number = :account_number 
                    AND status = 'active'";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_number' => $toAccountNumber]);
            $toAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$toAccount) {
                throw new \Exception('Akaun penerima tidak sah');
            }

            // Deduct from source account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount - :amount 
                    WHERE id = :account_id 
                    AND current_amount >= :amount";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $fromAccountId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Add to destination account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount 
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $toAccount['id']
            ]);

            // Record transactions
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id, 
                        transaction_type, 
                        amount, 
                        description,
                        created_at
                    ) VALUES 
                    (:from_id, 'transfer_out', :amount, :desc_out, NOW()),
                    (:to_id, 'transfer_in', :amount, :desc_in, NOW())";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':from_id' => $fromAccountId,
                ':to_id' => $toAccount['id'],
                ':amount' => $amount,
                ':desc_out' => 'Transfer keluar ke ' . $toAccountNumber,
                ':desc_in' => 'Transfer masuk dari ' . $fromAccountId
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat pemindahan');
        }
    }

    public function createSavingsGoal($data)
    {
        try {
            $sql = "INSERT INTO savings_goals (
                        member_id,
                        name,
                        target_amount,
                        current_amount,
                        target_date,
                        description,
                        status,
                        created_at
                    ) VALUES (
                        :member_id,
                        :name,
                        :target_amount,
                        :current_amount,
                        :target_date,
                        :description,
                        :status,
                        NOW()
                    )";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':member_id' => $data['member_id'],
                ':name' => $data['name'],
                ':target_amount' => $data['target_amount'],
                ':current_amount' => $data['current_amount'],
                ':target_date' => $data['target_date'],
                ':description' => $data['description'],
                ':status' => $data['status']
            ]);

            return $result;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mencipta matlamat simpanan');
        }
    }

    public function createRecurringPayment($data)
    {
        try {
            $sql = "INSERT INTO recurring_payments (
                        member_id,
                        description,
                        amount,
                        frequency,
                        start_date,
                        end_date,
                        status,
                        created_at
                    ) VALUES (
                        :member_id,
                        :description,
                        :amount,
                        :frequency,
                        :start_date,
                        :end_date,
                        :status,
                        NOW()
                    )";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':member_id' => $data['member_id'],
                ':description' => $data['description'],
                ':amount' => $data['amount'],
                ':frequency' => $data['frequency'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':status' => $data['status']
            ]);

            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mencipta pembayaran berulang');
        }
    }

    public function getAccounts($memberId)
    {
        try {
            $sql = "SELECT 
                    id,
                    member_id,
                    target_amount,
                    current_amount,
                    duration_months,
                    monthly_deposit,
                    start_date,
                    end_date,
                    status,
                    display_main,
                    created_at,
                    updated_at
                FROM savings_accounts 
                WHERE member_id = :member_id 
                ORDER BY display_main DESC, created_at DESC";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting savings accounts: ' . $e->getMessage());
            return [];
        }
    }

    public function createDeposit($data)
    {
        try {
            $sql = "INSERT INTO savings_transactions (
                savings_account_id,
                amount,
                type,
                payment_method,
                reference_no,
                description,
                created_at
            ) VALUES (
                :savings_account_id,
                :amount,
                'deposit',
                :payment_method,
                :reference_no,
                :description,
                CURRENT_TIMESTAMP
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            // Start transaction
            $this->getConnection()->beginTransaction();

            // Insert transaction
            $stmt->execute([
                ':savings_account_id' => $data['savings_account_id'],
                ':amount' => $data['amount'],
                ':payment_method' => $data['payment_method'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'] ?? null
            ]);

            // Update account balance
            $updateSql = "UPDATE savings_accounts 
                         SET current_amount = current_amount + :amount,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE id = :account_id";

            $updateStmt = $this->getConnection()->prepare($updateSql);
            $updateStmt->execute([
                ':amount' => $data['amount'],
                ':account_id' => $data['savings_account_id']
            ]);

            // Commit transaction
            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            // Rollback on error
            $this->getConnection()->rollBack();
            error_log('Error creating deposit: ' . $e->getMessage());
            throw new \Exception('Gagal membuat deposit');
        }
    }

    public function getRecurringPayment($memberId)
    {
        try {
            $sql = "SELECT * FROM recurring_payments 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY created_at DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan pembayaran berulang');
        }
    }

    public function getSavingsAccounts($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_accounts WHERE member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai akaun');
        }
    }

    public function processDeposit($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get the savings account
            $sql = "SELECT * FROM savings_accounts 
                   WHERE member_id = :member_id 
                   AND (display_main = 1 OR target_amount IS NULL)
                   ORDER BY display_main DESC
                   LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $data['member_id']]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                throw new \Exception('Akaun simpanan tidak ditemui');
            }

            // Calculate new balance
            $newBalance = $account['current_amount'] + $data['amount'];

            // Insert transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, payment_method,
                reference_no, description, created_at
            ) VALUES (
                :savings_account_id, :amount, :type, :payment_method,
                :reference_no, :description, :created_at
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':savings_account_id' => $account['id'],
                ':amount' => $data['amount'],
                ':type' => $data['type'],
                ':payment_method' => $data['payment_method'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'],
                ':created_at' => date('Y-m-d H:i:s')
            ]);

            // Update account balance
            $sql = "UPDATE savings_accounts 
                   SET current_amount = :new_balance 
                   WHERE id = :account_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':new_balance' => $newBalance,
                ':account_id' => $account['id']
            ]);

            $this->getConnection()->commit();
            return $newBalance;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memproses deposit');
        }
    }

    public function setRecurringPayment($data)
    {
        try {
            $sql = "INSERT INTO recurring_payments (
                member_id, amount, deduction_day, payment_method,
                status, next_deduction_date
            ) VALUES (
                :member_id, :amount, :deduction_day, :payment_method,
                :status, :next_deduction_date
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menetapkan bayaran berulang');
        }
    }

    public function updateAccountBalance($accountId, $newBalance)
    {
        try {
            $sql = "UPDATE savings_accounts 
                   SET current_amount = :balance 
                   WHERE id = :id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':balance' => $newBalance,
                ':id' => $accountId
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
                reference_no, description, created_at
            ) VALUES (
                :savings_account_id, :amount, :type, :payment_method,
                :reference_no, :description, :created_at
            )";

            // For debugging
            error_log('Transaction Data: ' . print_r($data, true));

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':savings_account_id' => $data['account_id'], // Ensure this matches the key in $data
                ':amount' => $data['amount'],
                ':type' => $data['type'], // This should be 'transfer_out'
                ':payment_method' => 'transfer', // Adjust if necessary
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'],
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error in addTransaction: ' . $e->getMessage());
            error_log('SQL State: ' . $e->errorInfo[0]);
            error_log('Error Code: ' . $e->errorInfo[1]);
            error_log('Error Message: ' . $e->errorInfo[2]);
            throw new \Exception('Gagal merekod transaksi');
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
            $sql = "UPDATE savings_goals SET 
                    name = :name,
                    target_amount = :target_amount,
                    target_date = :target_date,
                    monthly_target = :monthly_target,
                    updated_at = NOW()
                    WHERE id = :id 
                    AND member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':id' => $data['id'],
                ':member_id' => $data['member_id'],
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
            $sql = "UPDATE recurring_payments SET 
                    amount = :amount,
                    deduction_day = :deduction_day,
                    payment_method = :payment_method,
                    status = :status,
                    next_deduction_date = :next_deduction_date
                    WHERE member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini tetapan bayaran berulang');
        }
    }

    public function getTransactionByReference($reference)
    {
        try {
            $sql = "SELECT t.*, a.current_amount as new_balance, 
                    (a.current_amount - t.amount) as previous_balance 
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

    public function deposit($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get current account balance
            $sql = "SELECT current_amount FROM savings_accounts 
                    WHERE id = :account_id AND status = 'active'";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $data['account_id']]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                throw new \Exception('Akaun simpanan tidak ditemui');
            }

            // Calculate new balance
            $newBalance = $account['current_amount'] + $data['amount'];

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = :new_balance, 
                        updated_at = NOW() 
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':new_balance' => $newBalance,
                ':account_id' => $data['account_id']
            ]);

            // Record transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id, 
                        type,
                        amount,
                        reference_no,
                        description,
                        payment_method,
                        status,
                        created_at
                    ) VALUES (
                        :account_id,
                        'deposit',
                        :amount,
                        :reference_no,
                        :description,
                        :payment_method,
                        :status,
                        NOW()
                    )";

            $stmt = $this->getConnection()->prepare($sql);
            $success = $stmt->execute([
                ':account_id' => $data['account_id'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'],
                ':payment_method' => $data['payment_method'],
                ':status' => $data['status']
            ]);

            if ($success) {
                $this->getConnection()->commit();
                return true;
            }

            throw new \Exception('Gagal merekod transaksi');

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal melakukan deposit');
        }
    }

    public function transfer($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get source account
            $sql = "SELECT current_amount FROM savings_accounts 
                    WHERE id = :account_id AND status = 'active'";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $data['from_account_id']]);
            $sourceAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sourceAccount) {
                throw new \Exception('Akaun sumber tidak ditemui');
            }

            if ($sourceAccount['current_amount'] < $data['amount']) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Get destination account
            $stmt->execute([':account_id' => $data['to_account_id']]);
            $destAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$destAccount) {
                throw new \Exception('Akaun penerima tidak ditemui');
            }

            // Update source account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount - :amount,
                        updated_at = NOW()
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $data['amount'],
                ':account_id' => $data['from_account_id']
            ]);

            // Update destination account
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount,
                        updated_at = NOW()
                    WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $data['amount'],
                ':account_id' => $data['to_account_id']
            ]);

            // Record debit transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id,
                        type,
                        amount,
                        reference_no,
                        description,
                        status
                    ) VALUES (
                        :account_id,
                        'transfer_out',
                        :amount,
                        :reference_no,
                        :description,
                        'completed'
                    )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $data['from_account_id'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description']
            ]);

            // Record credit transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id,
                        type,
                        amount,
                        reference_no,
                        description,
                        status
                    ) VALUES (
                        :account_id,
                        'transfer_in',
                        :amount,
                        :reference_no,
                        :description,
                        'completed'
                    )";

            $stmt = $this->getConnection()->prepare($sql);
            $success = $stmt->execute([
                ':account_id' => $data['to_account_id'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description']
            ]);

            if ($success) {
                $this->getConnection()->commit();
                return true;
            }

            throw new \Exception('Gagal merekod transaksi');

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal melakukan pemindahan');
        }
    }

    public function getOtherMembersAccounts($memberId)
    {
        try {
            $sql = "SELECT a.*, m.name as member_name 
                    FROM savings_accounts a
                    JOIN members m ON a.member_id = m.id
                    WHERE a.member_id != :member_id 
                    AND a.status = 'active'
                    ORDER BY m.name";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai akaun');
        }
    }

    public function recordTransaction($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Record transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id,
                        type,
                        amount,
                        reference_no,
                        description,
                        payment_method,
                        status,
                        created_at
                    ) VALUES (
                        :account_id,
                        :type,
                        :amount,
                        :reference_no,
                        :description,
                        :payment_method,
                        :status,
                        NOW()
                    )";

            $stmt = $this->getConnection()->prepare($sql);
            $success = $stmt->execute([
                ':account_id' => $data['account_id'],
                ':type' => $data['type'],
                ':amount' => $data['amount'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description'],
                ':payment_method' => $data['payment_method'] ?? 'transfer',
                ':status' => $data['status']
            ]);

            if ($success) {
                $this->getConnection()->commit();
                return true;
            }

            throw new \Exception('Gagal merekod transaksi');

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal merekod transaksi');
        }
    }
}

