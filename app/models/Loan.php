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

    public function getTotalPaidAmount($loanId)
    {
        try {
            $sql = "SELECT COALESCE(SUM(payment_amount), 0) as total_paid 
                    FROM loan_payments 
                    WHERE loan_id = :loan_id";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':loan_id' => $loanId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($result['total_paid']);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan jumlah pembayaran');
        }
    }

    public function getTransactionsByDateRange($loanId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT 
                    lp.created_at,
                    lp.payment_amount,
                    lp.remaining_balance,
                    CONCAT('Bayaran Pembiayaan - ', l.reference_no) as description
                    FROM loan_payments lp
                    JOIN loans l ON l.id = lp.loan_id 
                    WHERE lp.loan_id = :loan_id 
                    AND DATE(lp.created_at) BETWEEN :start_date AND :end_date
                    ORDER BY lp.created_at ASC";
                    
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

    public function getLoanPaymentsByLoanId($loanId)
    {
        try {
            $sql = "SELECT * FROM loan_payments 
                    WHERE loan_id = :loan_id 
                    ORDER BY created_at ASC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':loan_id' => $loanId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan sejarah pembayaran');
        }
    }

    public function getPendingLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM pendingloans 
                    WHERE member_id = :member_id 
                    AND status = 'pending'
                    ORDER BY date_received DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai pembiayaan dalam proses');
        }
    }

    public function getActiveLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT l.*, 
                    COALESCE(l.amount - IFNULL((
                        SELECT SUM(lp.payment_amount) 
                        FROM loan_payments lp 
                        WHERE lp.loan_id = l.id
                    ), 0), l.amount) as remaining_amount
                    FROM loans l 
                    WHERE l.member_id = :member_id 
                    ORDER BY l.approved_at DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai pembiayaan aktif');
        }
    }

    public function getRejectedLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM rejectedloans 
                    WHERE member_id = :member_id 
                    ORDER BY rejected_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai pembiayaan ditolak');
        }
    }
}



