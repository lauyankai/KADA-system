<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Loan extends BaseModel
{
    public function createLoan($data)
    {
        try {
            $sql = "INSERT INTO pendingloans (
                reference_no, member_id, loan_type, amount, duration, 
                monthly_payment, status, bank_name, bank_account, date_received
            ) VALUES (
                :reference_no, :member_id, :loan_type, :amount, :duration,
                :monthly_payment, :status, :bank_name, :bank_account, :date_received
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            $result = $stmt->execute([
                ':reference_no' => $data['reference_no'],
                ':member_id' => $data['member_id'],
                ':loan_type' => $data['loan_type'],
                ':amount' => $data['amount'],
                ':duration' => $data['duration'],
                ':monthly_payment' => $data['monthly_payment'],
                ':bank_name' => $data['bank_name'],
                ':bank_account' => $data['bank_account'],
                ':status' => $data['status'],
                ':date_received' => $data['date_received']
            ]);
            
            if (!$result) {
                error_log('Execute failed: ' . print_r($stmt->errorInfo(), true));
                throw new \PDOException('Execute failed: ' . implode(', ', $stmt->errorInfo()));
            }
            
            return true;

        } catch (\PDOException $e) {
            error_log('Database Error in createLoan: ' . $e->getMessage());
            throw new \Exception('Gagal membuat permohonan pembiayaan: ' . $e->getMessage());
        }
    }

    public function getLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM pendingloans WHERE member_id = :member_id ORDER BY date_received DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai pembiayaan');
        }
    }

    public function getTransactionsByDateRange($loanId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT 
                    created_at,
                    amount as payment_amount,
                    remaining_balance,
                    description
                    FROM loan_payments 
                    WHERE loan_id = :loan_id 
                    AND created_at BETWEEN :start_date AND :end_date
                    ORDER BY created_at ASC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':loan_id' => $loanId,
                ':start_date' => $startDate,
                ':end_date' => $endDate . ' 23:59:59'
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah pembayaran');
        }
    }

    public function getLoanById($id)
    {
        try {
            $sql = "SELECT * FROM pendingloans WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat pembiayaan');
        }
    }

    public function getMemberLatestLoan($memberId)
    {
        try {
            // Debug logs
            error_log("Executing getMemberLatestLoan for member_id: " . $memberId);
            
            // Verify database connection
            if (!$this->getConnection()) {
                error_log("Database connection failed");
                throw new \PDOException("Database connection failed");
            }
            error_log("Database connection verified");
            
            // Verify member_id is numeric
            if (!is_numeric($memberId)) {
                error_log("Invalid member_id format: " . $memberId);
                throw new \Exception("Invalid member ID format");
            }
            
            $sql = "SELECT * FROM loans 
                    WHERE member_id = :member_id 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            // Debug SQL
            error_log("Executing SQL: " . $sql . " with member_id = " . $memberId);
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            
            // Debug statement execution
            error_log("SQL executed successfully");
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug result
            error_log("Query result: " . ($result ? json_encode($result) : "No loan found"));
            
            if (!$result) {
                throw new \Exception('Tiada permohonan pembiayaan ditemui');
            }
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getMemberLatestLoan: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            error_log('Member ID: ' . $memberId);
            throw new \Exception('Gagal mendapatkan maklumat pembiayaan');
        }
    }
}



