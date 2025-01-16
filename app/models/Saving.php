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

    public function makeDeposit($accountId, $amount)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = current_amount + :amount 
                    WHERE id = :account_id";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $accountId
            ]);

            // Record transaction
            $sql = "INSERT INTO savings_transactions (
                        savings_account_id, 
                        transaction_type, 
                        amount, 
                        description,
                        created_at
                    ) VALUES (
                        :account_id,
                        'deposit',
                        :amount,
                        'Deposit manual',
                        NOW()
                    )";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $amount
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat deposit');
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
                monthly_target,
                target_date, 
                status, 
                created_at, 
                updated_at
            ) VALUES (
                :member_id, 
                :name, 
                :target_amount, 
                :current_amount,
                :monthly_target,
                :target_date,
                :status, 
                NOW(), 
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            $params = [
                ':member_id' => $data['member_id'],
                ':name' => $data['name'],
                ':target_amount' => $data['target_amount'],
                ':current_amount' => $data['current_amount'],
                ':monthly_target' => $data['monthly_target'],
                ':target_date' => $data['target_date'],
                ':status' => $data['status']
            ];

            $result = $stmt->execute($params);
            return $result;

        } catch (\PDOException $e) {
            error_log('Database Error in createSavingsGoal: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan sasaran simpanan: ' . $e->getMessage());
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
            // Get all active accounts for member
            $sql = "SELECT sa.*, m.name as member_name 
                    FROM savings_accounts sa
                    INNER JOIN members m ON sa.member_id = m.id
                    WHERE sa.member_id = :member_id 
                    AND sa.status = 'active'
                    AND m.status = 'Active'
                    ORDER BY sa.display_main DESC, sa.id ASC";
            
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
            // Verify ownership
            $sql = "SELECT member_id FROM savings_goals WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $data['id']]);
            $goal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$goal || $goal['member_id'] != $data['member_id']) {
                throw new \Exception('Unauthorized access');
            }

            $sql = "UPDATE savings_goals SET 
                    name = :name,
                    target_amount = :target_amount,
                    target_date = :target_date,
                    updated_at = NOW()
                    WHERE id = :id AND member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':id' => $data['id'],
                ':member_id' => $data['member_id'],
                ':name' => $data['name'],
                ':target_amount' => $data['target_amount'],
                ':target_date' => $data['target_date']
            ]);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini sasaran');
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

    public function getTransactionByReference($referenceNo)
    {
        try {
            // Modified query to match actual database structure
            $sql = "SELECT 
                    t.*,
                    sa.member_id,
                    m.name as member_name,
                    m.member_id as member_number  -- Using member_id instead of member_number
                    FROM savings_transactions t
                INNER JOIN savings_accounts sa ON t.savings_account_id = sa.id
                INNER JOIN members m ON sa.member_id = m.id
                WHERE t.reference_no = :reference_no
                LIMIT 1";
                
            $stmt = $this->getConnection()->prepare($sql);
            
            // Debug log before execution
            error_log('Searching for transaction with reference: ' . $referenceNo);
            
            $stmt->execute([':reference_no' => $referenceNo]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug log the result
            error_log('Query result: ' . print_r($result, true));
            
            if (!$result) {
                error_log('No transaction found for reference: ' . $referenceNo);
                return null;
            }
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getTransactionByReference: ' . $e->getMessage());
            error_log('SQL State: ' . $e->errorInfo[0]);
            error_log('Error Code: ' . $e->errorInfo[1]);
            error_log('Error Message: ' . $e->errorInfo[2]);
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

            // Get the member's active savings account
            $sql = "SELECT id, current_amount FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $data['member_id']]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }

            // Update account balance
            $newBalance = $account['current_amount'] + $data['amount'];
            $sql = "UPDATE savings_accounts 
                    SET current_amount = :new_balance 
                    WHERE id = :account_id";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $updateResult = $stmt->execute([
                ':new_balance' => $newBalance,
                ':account_id' => $account['id']
            ]);

            // Record the transaction with explicit reference_no
            $sql = "INSERT INTO savings_transactions (
                savings_account_id,
                amount,
                type,
                payment_method,
                reference_no,
                description,
                created_at
            ) VALUES (
                :account_id,
                :amount,
                :type,
                :payment_method,
                :reference_no,
                :description,
                NOW()
            )";

            $params = [
                ':account_id' => $account['id'],
                ':amount' => $data['amount'],
                ':type' => $data['type'],
                ':payment_method' => $data['payment_method'],
                ':reference_no' => $data['reference_no'],
                ':description' => $data['description']
            ];

            // Debug log
            error_log('Inserting transaction with reference: ' . $data['reference_no']);
            error_log('Transaction parameters: ' . print_r($params, true));

            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                $this->getConnection()->commit();
                return true;
            }

            throw new \Exception('Gagal merekod transaksi');

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error in deposit: ' . $e->getMessage());
            error_log('SQL State: ' . $e->errorInfo[0]);
            error_log('Error Code: ' . $e->errorInfo[1]);
            error_log('Error Message: ' . $e->errorInfo[2]);
            throw new \Exception('Gagal merekod transaksi: ' . $e->getMessage());
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

    public function getSavingsGoalById($id)
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

    public function getAllSavingsGoals($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_goals 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY created_at DESC";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai sasaran simpanan');
        }
    }

    public function getTransactionHistory($accountId, $limit = 10)
    {
        try {
            $sql = "SELECT 
                    t.*,
                    DATE_FORMAT(t.created_at, '%d/%m/%Y %H:%i') as formatted_date,
                    sa.current_amount as balance
                FROM savings_transactions t
                INNER JOIN savings_accounts sa ON t.savings_account_id = sa.id
                WHERE t.savings_account_id = :account_id
                ORDER BY t.created_at DESC
                LIMIT :limit";
                
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':account_id', $accountId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error in getTransactionHistory: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah transaksi');
        }
    }

    public function getMemberAccounts($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY display_main DESC";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai akaun');
        }
    }

    public function getAllMembersWithAccounts($excludeMemberId)
    {
        try {
            $sql = "SELECT m.id, m.name, sa.id as savings_account_id, sa.account_number 
                    FROM members m
                    INNER JOIN savings_accounts sa ON m.id = sa.member_id
                    WHERE m.id != :exclude_id 
                    AND m.status = 'Active'
                    AND sa.status = 'active'
                    ORDER BY m.name";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':exclude_id' => $excludeMemberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai ahli');
        }
    }

    public function transferToMember($fromAccountId, $recipientAccountNumber, $amount, $description = '')
    {
        try {
            // Start transaction
            $this->getConnection()->beginTransaction();

            // Get source account
            $sourceAccount = $this->getAccountById($fromAccountId);
            if (!$sourceAccount) {
                throw new \Exception('Akaun pemindah tidak ditemui');
            }

            // Get recipient account
            $sql = "SELECT * FROM savings_accounts 
                    WHERE account_number = :account_number 
                    AND status = 'active'";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_number' => $recipientAccountNumber]);
            $recipientAccount = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$recipientAccount) {
                throw new \Exception('Akaun penerima tidak ditemui');
            }

            // Check if trying to transfer to same account
            if ($sourceAccount['id'] === $recipientAccount['id']) {
                throw new \Exception('Tidak boleh pindah ke akaun yang sama');
            }

            // Check sufficient balance
            if ($sourceAccount['current_amount'] < $amount) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Deduct from source account
            $updateSourceSql = "UPDATE savings_accounts 
                               SET current_amount = current_amount - :amount,
                                   updated_at = NOW()
                               WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($updateSourceSql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $sourceAccount['id']
            ]);

            // Add to recipient account
            $updateRecipientSql = "UPDATE savings_accounts 
                                  SET current_amount = current_amount + :amount,
                                      updated_at = NOW()
                                  WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($updateRecipientSql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $recipientAccount['id']
            ]);

            // Record transaction for source account (debit)
            $this->recordTransfer($sourceAccount['id'], 'debit', $amount, $description, [
                'recipient_account' => $recipientAccount['account_number'],
                'recipient_name' => $recipientAccount['account_name'],
                'transfer_type' => 'member'
            ]);

            // Record transaction for recipient account (credit)
            $this->recordTransfer($recipientAccount['id'], 'credit', $amount, $description, [
                'source_account' => $sourceAccount['account_number'],
                'source_name' => $sourceAccount['account_name'],
                'transfer_type' => 'member'
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            error_log('Transfer error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function transferToOther($fromAccountId, $amount, $description = '', $bankDetails = [])
    {
        try {
            // Start transaction
            $this->getConnection()->beginTransaction();

            // Get source account
            $sourceAccount = $this->getAccountById($fromAccountId);
            if (!$sourceAccount) {
                throw new \Exception('Akaun pemindah tidak ditemui');
            }

            // Check sufficient balance
            if ($sourceAccount['current_amount'] < $amount) {
                throw new \Exception('Baki tidak mencukupi');
            }

            // Deduct from source account
            $updateSourceSql = "UPDATE savings_accounts 
                               SET current_amount = current_amount - :amount,
                                   updated_at = NOW()
                               WHERE id = :account_id";
            $stmt = $this->getConnection()->prepare($updateSourceSql);
            $stmt->execute([
                ':amount' => $amount,
                ':account_id' => $sourceAccount['id']
            ]);

            // Record transaction
            $this->recordTransfer($sourceAccount['id'], 'debit', $amount, $description, [
                'bank_name' => $bankDetails['bank_name'],
                'bank_account' => $bankDetails['account_number'],
                'recipient_name' => $bankDetails['recipient_name'],
                'transfer_type' => 'other'
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            error_log('Transfer error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function recordTransfer($accountId, $type, $amount, $description, $details = [])
    {
        $sql = "INSERT INTO savings_transactions (
            savings_account_id,
            type,
            amount,
            description,
            transfer_details,
            created_at
        ) VALUES (
            :account_id,
            :type,
            :amount,
            :description,
            :transfer_details,
            NOW()
        )";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([
            ':account_id' => $accountId,
            ':type' => $type,
            ':amount' => $amount,
            ':description' => $description,
            ':transfer_details' => json_encode($details)
        ]);
    }

    public function getAccountByNumber($accountNumber) 
    {
        try {
            $sql = "SELECT sa.*, m.name as member_name 
                    FROM savings_accounts sa
                    INNER JOIN members m ON sa.member_id = m.id
                    WHERE sa.account_number = :account_number 
                    AND sa.status = 'active'";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_number' => $accountNumber]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getCurrentAccount($memberId)
    {
        try {
            // First check if member exists and is active
            $memberSql = "SELECT * FROM members WHERE id = :member_id AND status = 'Active'";
            $memberStmt = $this->getConnection()->prepare($memberSql);
            $memberStmt->execute([':member_id' => $memberId]);
            $member = $memberStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                throw new \Exception('Ahli tidak aktif');
            }
            
            // Get member's main active account
            $sql = "SELECT sa.*, m.name as member_name 
                    FROM savings_accounts sa
                    INNER JOIN members m ON sa.member_id = m.id
                    WHERE sa.member_id = :member_id 
                    AND sa.status = 'active'
                    AND m.status = 'Active'
                    ORDER BY sa.display_main DESC, sa.id ASC
                    LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                // Create new savings account if none exists
                $accountNumber = 'SAV-' . str_pad($memberId, 6, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999);
                
                $insertSql = "INSERT INTO savings_accounts (
                    account_number,
                    member_id,
                    current_amount,
                    status,
                    display_main,
                    account_name,
                    created_at,
                    updated_at
                ) VALUES (
                    :account_number,
                    :member_id,
                    :initial_amount,
                    'active',
                    1,
                    'Akaun Utama',
                    NOW(),
                    NOW()
                )";
                
                $this->getConnection()->beginTransaction();
                
                try {
                    $insertStmt = $this->getConnection()->prepare($insertSql);
                    $insertStmt->execute([
                        ':account_number' => $accountNumber,
                        ':member_id' => $memberId,
                        ':initial_amount' => 0.00
                    ]);
                    
                    // Get the newly created account
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute([':member_id' => $memberId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $this->getConnection()->commit();
                    
                } catch (\Exception $e) {
                    $this->getConnection()->rollBack();
                    error_log('Failed to create account: ' . $e->getMessage());
                    throw new \Exception('Gagal membuat akaun baru');
                }
            }
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getCurrentAccount: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getAccountById($accountId)
    {
        try {
            // Get account with member details and ensure both account and member are active
            $sql = "SELECT sa.*, m.name as member_name, m.id as member_id 
                    FROM savings_accounts sa
                    INNER JOIN members m ON sa.member_id = m.id
                    WHERE sa.id = :account_id 
                    AND sa.status = 'active'
                    AND m.status = 'Active'";  // Removed display_main constraint
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $accountId]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$account) {
                error_log("Account not found or invalid - ID: $accountId");
                throw new \Exception('Akaun tidak ditemui');
            }
            
            return $account;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getAccountById: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getMemberAccount($memberId)
    {
        try {
            // First check if member exists and is active
            $memberSql = "SELECT * FROM members WHERE id = :member_id AND status = 'Active'";
            $memberStmt = $this->getConnection()->prepare($memberSql);
            $memberStmt->execute([':member_id' => $memberId]);
            $member = $memberStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                throw new \Exception('Ahli tidak aktif');
            }
            
            // Get any active savings account for the member
            $sql = "SELECT sa.*, m.name as member_name 
                    FROM savings_accounts sa
                    INNER JOIN members m ON sa.member_id = m.id
                    WHERE sa.member_id = :member_id 
                    AND sa.status = 'active'
                    ORDER BY sa.display_main DESC, sa.id ASC
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                // Create new savings account
                $accountNumber = 'SAV-' . str_pad($memberId, 6, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999);
                
                $insertSql = "INSERT INTO savings_accounts (
                    account_number,
                    member_id,
                    current_amount,
                    status,
                    display_main,
                    account_name,
                    created_at,
                    updated_at
                ) VALUES (
                    :account_number,
                    :member_id,
                    :initial_amount,
                    'active',
                    1,
                    'Akaun Utama',
                    NOW(),
                    NOW()
                )";
                
                $this->getConnection()->beginTransaction();
                
                try {
                    $insertStmt = $this->getConnection()->prepare($insertSql);
                    $insertStmt->execute([
                        ':account_number' => $accountNumber,
                        ':member_id' => $memberId,
                        ':initial_amount' => $member['deposit_funds'] ?? 0.00
                    ]);
                    
                    // Get the newly created account
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute([':member_id' => $memberId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $this->getConnection()->commit();
                    
                } catch (\Exception $e) {
                    $this->getConnection()->rollBack();
                    error_log('Failed to create account: ' . $e->getMessage());
                    throw new \Exception('Gagal membuat akaun baru');
                }
            }
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getMemberAccount: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }
}

