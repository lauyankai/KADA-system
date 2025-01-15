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
                    LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error in getSavingsAccount: ' . $e->getMessage());
            throw new \Exception('Failed to fetch savings account');
        }
    }

    public function getRecentTransactions($memberId, $limit = 5)
    {
        try {
            $sql = "SELECT st.*, sa.account_number 
                    FROM savings_transactions st
                    JOIN savings_accounts sa ON st.savings_account_id = sa.id
                    WHERE sa.member_id = :member_id
                    ORDER BY st.created_at DESC
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

    public function makeDeposit($accountId, $amount, $description = '')
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get current balance
            $sql = "SELECT current_amount FROM savings_accounts WHERE id = :id FOR UPDATE";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $accountId]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                throw new \Exception('Akaun tidak dijumpai');
            }

            // Calculate new balance
            $newBalance = $account['current_amount'] + $amount;

            // Update account balance
            $sql = "UPDATE savings_accounts 
                    SET current_amount = :new_amount 
                    WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':new_amount' => $newBalance,
                ':id' => $accountId
            ]);

            // Record transaction
            $sql = "INSERT INTO savings_transactions 
                    (savings_account_id, transaction_type, amount, description) 
                    VALUES (:account_id, 'deposit', :amount, :description)";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_id' => $accountId,
                ':amount' => $amount,
                ':description' => $description
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat deposit');
        }
    }
}