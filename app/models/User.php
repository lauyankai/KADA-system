<?php
namespace App\Models;
use App\Core\Database;
use App\Core\BaseModel;
use PDO;

class User extends BaseModel
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

    public function getTotalSavings($memberId)
    {
        try {
            $sql = "SELECT COALESCE(current_amount, 0) as total 
                    FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND (display_main = 1 OR target_amount IS NULL)
                    ORDER BY display_main DESC
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 0;
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan jumlah simpanan');
        }
    }

    public function getSavingsGoals($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_goals 
                    WHERE member_id = :member_id 
                    ORDER BY target_date ASC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sasaran simpanan');
        }
    }

    public function getRecurringPayment($memberId)
    {
        try {
            $sql = "SELECT * FROM recurring_payments 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    LIMIT 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat bayaran berulang');
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah transaksi');
        }
    }

    public function createSavingsAccount($data)
    {
        try {
            $sql = "INSERT INTO savings_accounts (
                member_id, target_amount, duration_months, monthly_deduction,
                start_date, end_date, status, current_amount
            ) VALUES (
                :member_id, :target_amount, :duration_months, :monthly_deduction,
                :start_date, :end_date, :status, 0
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute($data);
            
            return $result ? $this->getConnection()->lastInsertId() : false;
        } catch (\PDOException $e) {
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

    public function createSavingsGoal($data)
    {
        try {
            $sql = "INSERT INTO savings_goals (
                member_id, name, target_amount, target_date,
                current_amount, monthly_target, status
            ) VALUES (
                :member_id, :name, :target_amount, :target_date,
                :current_amount, :monthly_target, :status
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute($data);
            
            return $result ? $this->getConnection()->lastInsertId() : false;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat sasaran simpanan');
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
                    monthly_target = :monthly_target
                    WHERE id = :id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($data);
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

    private function generateAccountNumber($memberId) {
        // Format: SAV-{member_id}-{random_4_digits}
        $random = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return "SAV-" . str_pad($memberId, 6, '0', STR_PAD_LEFT) . "-" . $random;
    }

    public function createNewSavingsAccount($data)
    {
        try {
            // Generate unique account number
            $accountNumber = $this->generateAccountNumber($data['member_id']);
            
            $sql = "INSERT INTO savings_accounts (
                member_id, account_number, account_name, current_amount, status
            ) VALUES (
                :member_id, :account_number, :account_name, :initial_amount, :status
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':member_id' => $data['member_id'],
                ':account_number' => $accountNumber,
                ':account_name' => $data['account_name'],
                ':initial_amount' => $data['initial_amount'],
                ':status' => $data['status']
            ]);
            
            return $result ? $this->getConnection()->lastInsertId() : false;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat akaun baru');
        }
    }

    public function deleteSavingsAccount($id)
    {
        try {
            $sql = "DELETE FROM savings_accounts WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memadam akaun');
        }
    }

    public function setMainDisplayAccount($id, $memberId)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Reset all accounts for this member
            $sql = "UPDATE savings_accounts SET display_main = 0 
                   WHERE member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);

            // Set the selected account as main
            $sql = "UPDATE savings_accounts SET display_main = 1 
                   WHERE id = :id AND member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':id' => $id,
                ':member_id' => $memberId
            ]);

            $this->getConnection()->commit();
            return $result;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini tetapan paparan');
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
