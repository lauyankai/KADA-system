<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class MemberFee extends BaseModel
{
    public function createInitialFees($memberId)
    {
        try {
            // First check if fees already exist
            $existingFees = $this->getFeesByMemberId($memberId);
            if ($existingFees) {
                return true;
            }

            $sql = "INSERT INTO member_fees (
                member_id,
                registration_fee,
                share_capital,
                membership_fee,
                welfare_fund,
                payment_status,
                created_at
            ) VALUES (
                :member_id,
                20.00,
                100.00,
                10.00,
                10.00,
                'pending',
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':member_id' => $memberId]);

        } catch (\PDOException $e) {
            error_log('Database Error in createInitialFees: ' . $e->getMessage());
            throw new \Exception('Failed to create initial fees');
        }
    }

    public function getFeesByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM member_fees WHERE member_id = :member_id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log('Database Error in getFeesByMemberId: ' . $e->getMessage());
            throw new \Exception('Failed to get member fees');
        }
    }

    public function updatePaymentStatus($memberId, $status)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Update member_fees table
            $sql = "UPDATE member_fees 
                    SET payment_status = :status,
                        paid_at = NOW()
                    WHERE member_id = :member_id";

            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':status' => $status,
                ':member_id' => $memberId
            ]);

            if (!$result) {
                throw new \Exception('Failed to update payment status');
            }

            // Update members table status
            $sql = "UPDATE members 
                    SET status = 'Active',
                        activated_at = NOW() 
                    WHERE id = :member_id";

            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([':member_id' => $memberId]);

            if (!$result) {
                throw new \Exception('Failed to update member status');
            }

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error in updatePaymentStatus: ' . $e->getMessage());
            throw new \Exception('Failed to update payment status');
        }
    }
} 