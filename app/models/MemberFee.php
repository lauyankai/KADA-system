<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class MemberFee extends BaseModel
{
    public function createInitialFees($memberId)
    {
        try {
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
                20.00,  // Registration fee
                100.00, // Share capital
                10.00,  // Membership fee
                10.00,  // Welfare fund
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
            $sql = "UPDATE member_fees 
                    SET payment_status = :status,
                        paid_at = NOW()
                    WHERE member_id = :member_id";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':member_id' => $memberId
            ]);

        } catch (\PDOException $e) {
            error_log('Database Error in updatePaymentStatus: ' . $e->getMessage());
            throw new \Exception('Failed to update payment status');
        }
    }
} 