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

    public function getDashboardStats()
    {
        try {
            // Get total savings only
            $sql = "SELECT COALESCE(SUM(current_amount), 0) as total FROM savings_accounts";
            $stmt = $this->getConnection()->query($sql);
            $totalSavings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'totalSavings' => $totalSavings
            ];
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch dashboard stats');
        }
    }

    public function getRecentSavings()
    {
        try {
            // Modified to show member number instead of username
            $sql = "SELECT sa.*, prm.member_number as member_number 
                    FROM savings_accounts sa
                    JOIN admins a ON sa.member_id = a.id
                    JOIN pendingregistermember prm ON a.username = prm.username
                    WHERE sa.member_id = :member_id
                    ORDER BY sa.created_at DESC LIMIT 10";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $_SESSION['admin_id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch recent savings');
        }
    }

    public function getRecentRecurringPayments()
    {
        try {
            // Modified to show member number instead of username
            $sql = "SELECT rp.*, prm.member_number as member_number 
                    FROM savings_recurring_payments rp
                    JOIN savings_accounts sa ON rp.savings_account_id = sa.id
                    JOIN admins a ON sa.member_id = a.id
                    JOIN pendingregistermember prm ON a.username = prm.username
                    WHERE sa.member_id = :member_id
                    ORDER BY rp.created_at DESC LIMIT 10";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $_SESSION['admin_id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch recent recurring payments');
        }
    }

    public function getAllMembers()
    {
        try {
            $sql = "SELECT id, name, member_number FROM pendingregistermember ORDER BY name";
            $stmt = $this->getConnection()->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch members');
        }
    }

    public function createSavingsAccount($data)
    {
        try {
            error_log('Creating savings account with data: ' . print_r($data, true));
            
            $sql = "INSERT INTO savings_accounts (
                member_id, target_amount, duration_months, monthly_deposit,
                start_date, end_date, status, current_amount
            ) VALUES (
                :member_id, :target_amount, :duration_months, :monthly_deposit,
                :start_date, :end_date, :status, :current_amount
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            // Log the SQL query
            error_log('SQL Query: ' . $sql);
            
            // Execute and check for errors
            if (!$stmt->execute($data)) {
                $error = $stmt->errorInfo();
                error_log('Database Error Info: ' . print_r($error, true));
                throw new \PDOException('Database error: ' . $error[2]);
            }

            $newId = $this->getConnection()->lastInsertId();
            error_log('New savings account created with ID: ' . $newId);
            
            return $newId;
            
        } catch (\PDOException $e) {
            error_log('Database Error in createSavingsAccount: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Gagal membuat akaun simpanan: ' . $e->getMessage());
        }
    }

    public function getLatestSavingsAccount($memberId)
    {
        try {
            $sql = "SELECT * FROM savings_accounts 
                    WHERE member_id = :member_id 
                    AND status = 'active'
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun simpanan');
        }
    }

    public function createRecurringPayment($data)
    {
        try {
            error_log('Creating recurring payment with data: ' . print_r($data, true));
            
            $sql = "INSERT INTO savings_recurring_payments (
                savings_account_id, amount, frequency, payment_method,
                next_payment_date, status
            ) VALUES (
                :savings_account_id, :amount, :frequency, :payment_method,
                :next_payment_date, :status
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            // Log the SQL query
            error_log('SQL Query: ' . $sql);
            
            // Execute and check for errors
            if (!$stmt->execute($data)) {
                $error = $stmt->errorInfo();
                error_log('Database Error Info: ' . print_r($error, true));
                throw new \PDOException('Database error: ' . $error[2]);
            }

            $newId = $this->getConnection()->lastInsertId();
            error_log('New recurring payment created with ID: ' . $newId);
            
            return $newId;
            
        } catch (\PDOException $e) {
            error_log('Database Error in createRecurringPayment: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Gagal mendaftar bayaran berulang: ' . $e->getMessage());
        }
    }

    public function getSavingsAccount($id)
    {
        try {
            $sql = "SELECT sa.*, a.username as member_name 
                    FROM savings_accounts sa
                    JOIN admins a ON sa.member_id = a.id
                    WHERE sa.id = :id AND sa.member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':member_id' => $_SESSION['admin_id']
            ]);
            
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$account) {
                throw new \Exception('Akaun simpanan tidak ditemui');
            }
            return $account;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat akaun');
        }
    }

    public function getSavingsTransactions($accountId)
    {
        try {
            $sql = "SELECT * FROM savings_transactions 
                    WHERE savings_account_id = :account_id 
                    ORDER BY created_at DESC";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':account_id' => $accountId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah transaksi');
        }
    }

    public function addDeposit($accountId, $amount)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Add transaction record
            $sql = "INSERT INTO savings_transactions (
                savings_account_id, amount, type, description
            ) VALUES (
                :account_id, :amount, 'deposit', 'Simpanan manual'
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $amount
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
        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menambah simpanan');
        }
    }

    public function updateSavingsStatus($id, $status)
    {
        try {
            $sql = "UPDATE savings_accounts 
                    SET status = :status 
                    WHERE id = :id AND member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':member_id' => $_SESSION['admin_id']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini status akaun');
        }
    }
}
