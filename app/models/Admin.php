<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Admin extends BaseModel
{
    public function updateStatus($id, $status)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Validate status value
            $validStatuses = ['Pending', 'Lulus', 'Tolak'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception("Invalid status value");
            }

            // First, check if the record exists and get member data
            $checkSql = "SELECT * FROM pendingmember WHERE id = :id";
            $checkStmt = $this->getConnection()->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            $member = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                throw new \Exception("Record with ID $id not found");
            }

            // Update status in pendingmember table
            $sql = "UPDATE pendingmember SET status = :status WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);

            // If status is 'Lulus', insert into members table and create savings account
            if ($status === 'Lulus') {
                // Generate member_id
                $member_id = 'M' . date('Y') . str_pad($id, 4, '0', STR_PAD_LEFT);
                
                // Generate account_number for member
                $account_number = 'A' . date('Y') . str_pad($id, 4, '0', STR_PAD_LEFT);

                // Insert into members table
                $insertSql = "INSERT INTO members (
                    full_name, 
                    ic_number, 
                    phone_number,
                    email,
                    address,
                    member_id,
                    account_number
                ) VALUES (
                    :full_name,
                    :ic_number,
                    :phone_number,
                    '',
                    :address,
                    :member_id,
                    :account_number
                )";

                $insertStmt = $this->getConnection()->prepare($insertSql);
                $insertResult = $insertStmt->execute([
                    ':full_name' => $member['name'],
                    ':ic_number' => $member['ic_no'],
                    ':phone_number' => $member['home_phone'],
                    ':address' => $member['home_address'],
                    ':member_id' => $member_id,
                    ':account_number' => $account_number
                ]);

                if (!$insertResult) {
                    throw new \Exception("Failed to create member record");
                }

                // Get the newly inserted member's ID
                $newMemberId = $this->getConnection()->lastInsertId();

                // Generate savings account number
                $savingsAccountNumber = 'SAV-' . str_pad($newMemberId, 6, '0', STR_PAD_LEFT) . '-' . substr(str_shuffle('0123456789'), 0, 4);

                // Create savings account
                $savingsSql = "INSERT INTO savings_accounts (
                    account_number,
                    member_id,
                    current_amount,
                    status,
                    display_main,
                    account_name
                ) VALUES (
                    :account_number,
                    :member_id,
                    :current_amount,
                    'active',
                    1,
                    'Akaun Utama'
                )";

                $savingsStmt = $this->getConnection()->prepare($savingsSql);
                $savingsResult = $savingsStmt->execute([
                    ':account_number' => $savingsAccountNumber,
                    ':member_id' => $newMemberId,
                    ':current_amount' => $member['deposit_funds'] ?? 0 // Initial deposit from registration
                ]);

                if (!$savingsResult) {
                    throw new \Exception("Failed to create savings account");
                }
            }

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            error_log('Error in updateStatus: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserById($id)
    {
        try {
            $sql = "SELECT * FROM pendingmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch user details');
        }
    }
}