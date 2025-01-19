<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Statement extends BaseModel
{
    public function getStatementsByMemberId($memberId)
    {
        try {
            // First verify if member exists
            $memberSql = "SELECT id FROM members WHERE id = :member_id";
            $memberStmt = $this->getConnection()->prepare($memberSql);
            $memberStmt->execute([':member_id' => $memberId]);
            
            if (!$memberStmt->fetch()) {
                throw new \Exception('Ahli tidak dijumpai');
            }

            $sql = "SELECT s.*, sa.account_number 
                    FROM statements s
                    JOIN savings_accounts sa ON s.account_id = sa.id
                    WHERE s.member_id = :member_id 
                    ORDER BY s.created_at DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            
            // Debug log
            error_log('Executing statement query for member_id: ' . $memberId);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If no statements found, create an initial one
            if (empty($results)) {
                error_log('No statements found, generating initial statement');
                
                // Get member's savings account
                $accountSql = "SELECT id FROM savings_accounts 
                             WHERE member_id = :member_id 
                             AND status = 'active' 
                             LIMIT 1";
                             
                $accountStmt = $this->getConnection()->prepare($accountSql);
                $accountStmt->execute([':member_id' => $memberId]);
                $account = $accountStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($account) {
                    $statementId = $this->generateStatement($memberId, $account['id']);
                    
                    // Fetch the newly created statement
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute([':member_id' => $memberId]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            
            return $results;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getStatementsByMemberId: ' . $e->getMessage());
            error_log('SQL State: ' . $e->errorInfo[0]);
            error_log('Error Code: ' . $e->errorInfo[1]);
            error_log('Error Message: ' . $e->errorInfo[2]);
            throw new \Exception('Gagal mendapatkan senarai penyata');
        }
    }

    public function generateStatement($memberId, $accountId)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Generate statement reference
            $reference = 'STM' . date('Ymd') . rand(1000, 9999);

            $sql = "INSERT INTO statements (
                member_id, account_id, reference_no, 
                start_date, end_date, status, created_at
            ) VALUES (
                :member_id, :account_id, :reference_no,
                DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), CURRENT_DATE,
                'generated', NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $memberId,
                ':account_id' => $accountId,
                ':reference_no' => $reference
            ]);

            $statementId = $this->getConnection()->lastInsertId();
            $this->getConnection()->commit();
            
            return $statementId;

        } catch (\PDOException $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menjana penyata');
        }
    }

    public function getStatementById($id)
    {
        try {
            $sql = "SELECT s.*, sa.account_number, m.name as member_name,
                    st.transaction_date, st.description, st.amount, st.type
                    FROM statements s
                    JOIN savings_accounts sa ON s.account_id = sa.id
                    JOIN members m ON s.member_id = m.id
                    LEFT JOIN savings_transactions st ON (
                        st.savings_account_id = s.account_id 
                        AND st.created_at BETWEEN s.start_date AND s.end_date
                    )
                    WHERE s.id = :id
                    ORDER BY st.transaction_date DESC";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat penyata');
        }
    }
}