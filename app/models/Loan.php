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

    public function generateMonthlyStatement($loanId, $period)
    {
        try {
            // Check if statement already exists
            $sql = "SELECT * FROM loan_statements 
                    WHERE loan_id = :loan_id 
                    AND statement_period = :period";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':loan_id' => $loanId,
                ':period' => $period
            ]);
            
            if ($stmt->rowCount() > 0) {
                return; // Statement already exists
            }

            // Generate PDF file
            $filename = "statement_" . $loanId . "_" . date('Ym', strtotime($period)) . ".pdf";
            $filepath = "statements/loans/" . $filename;
            
            // Save statement record
            $sql = "INSERT INTO loan_statements (loan_id, statement_period, file_path) 
                    VALUES (:loan_id, :period, :file_path)";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':loan_id' => $loanId,
                ':period' => $period,
                ':file_path' => $filepath
            ]);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menjana penyata bulanan');
        }
    }

    public function getMonthlyStatements($loanId, $limit = 12)
    {
        try {
            $sql = "SELECT * FROM loan_statements 
                    WHERE loan_id = :loan_id 
                    ORDER BY statement_period DESC 
                    LIMIT :limit";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':loan_id', $loanId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai penyata');
        }
    }

    public function find($id)
    {
        try {
            error_log('Finding loan with ID: ' . $id);

            // First check in the loans table
            $sql = "SELECT l.*, 
                    m.name as member_name, 
                    m.ic_no as member_ic,
                    m.member_id as member_no,
                    m.mobile_phone as member_phone,
                    m.home_address as member_address,
                    'approved' as loan_status
                    FROM loans l
                    JOIN members m ON l.member_id = m.id
                    WHERE l.id = :id";

            error_log('Checking loans table with query: ' . $sql);
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log('Result from loans table: ' . ($loan ? 'Found' : 'Not found'));

            if ($loan) {
                error_log('Found loan in loans table');
                // Get loan payments for approved loans
                $sql = "SELECT * FROM loan_payments 
                        WHERE loan_id = :loan_id 
                        ORDER BY payment_date DESC";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':loan_id' => $id]);
                $loan['transactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Calculate remaining amount
                $totalPaid = $this->getTotalPaidAmount($id);
                $loan['remaining_amount'] = $loan['amount'] - $totalPaid;
                
                return $loan;
            }

            // If not found in loans table, check pendingloans table
            $sql = "SELECT pl.*, 
                    m.name as member_name, 
                    m.ic_no as member_ic,
                    m.member_id as member_no,
                    m.mobile_phone as member_phone,
                    m.home_address as member_address,
                    pl.status as loan_status
                    FROM pendingloans pl
                    JOIN members m ON pl.member_id = m.id
                    WHERE pl.id = :id";

            error_log('Checking pendingloans table with query: ' . $sql);
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log('Result from pendingloans table: ' . ($loan ? 'Found' : 'Not found'));

            if ($loan) {
                error_log('Found loan in pendingloans table');
                $loan['transactions'] = []; // Empty array for pending loans
                $loan['remaining_amount'] = $loan['amount']; // Full amount for pending loans
                return $loan;
            }

            error_log('No loan found in either table');
            return null;
            
        } catch (\PDOException $e) {
            error_log('Database Error in find: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat pembiayaan');
        }
    }
}



