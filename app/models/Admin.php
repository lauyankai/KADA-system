<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Admin extends BaseModel
{
    public function updateStatus($id, $status)
    {
        try {
            $validStatuses = ['Pending', 'Lulus', 'Tolak'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception("Invalid status");
            }

            $checkSql = "SELECT id FROM pendingmember WHERE id = :id";
            $checkStmt = $this->getConnection()->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            
            if (!$checkStmt->fetch()) {
                throw new \Exception("Record with ID $id not found");
            }

            $sql = "UPDATE pendingmember SET status = :status WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            
            $result = $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \Exception("Update failed: " . ($error[2] ?? 'Unknown error'));
            }

            if ($status === 'Lulus') {
                $this->migrateToMembers($id);
            }
    
            return true;
        } catch (\PDOException $e) {
            error_log('Database Error in updateStatus: ' . $e->getMessage());
            throw new \Exception('Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
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
        } catch (PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new Exception('Failed to fetch user details');
        }
    }

    public function migrateToMembers($id)
    {
        try {
            $this->getConnection()->beginTransaction();

            $sql = "SELECT * FROM pendingmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$memberData) {
                throw new \Exception("Member data not found");
            }

            $sql = "INSERT INTO members (
                name, ic_no, gender, religion, race, marital_status,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions,
                family_relationship, family_name, family_ic,
                password,
                status,
                created_at
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions,
                :family_relationship, :family_name, :family_ic,
                :password,
                'Active',
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':name' => $memberData['name'],
                ':ic_no' => $memberData['ic_no'],
                ':gender' => $memberData['gender'],
                ':religion' => $memberData['religion'],
                ':race' => $memberData['race'],
                ':marital_status' => $memberData['marital_status'],
                ':position' => $memberData['position'],
                ':grade' => $memberData['grade'],
                ':monthly_salary' => $memberData['monthly_salary'],
                ':home_address' => $memberData['home_address'],
                ':home_postcode' => $memberData['home_postcode'],
                ':home_state' => $memberData['home_state'],
                ':office_address' => $memberData['office_address'],
                ':office_postcode' => $memberData['office_postcode'],
                ':office_phone' => $memberData['office_phone'],
                ':home_phone' => $memberData['home_phone'],
                ':fax' => $memberData['fax'],
                ':registration_fee' => $memberData['registration_fee'],
                ':share_capital' => $memberData['share_capital'],
                ':fee_capital' => $memberData['fee_capital'],
                ':deposit_funds' => $memberData['deposit_funds'],
                ':welfare_fund' => $memberData['welfare_fund'],
                ':fixed_deposit' => $memberData['fixed_deposit'],
                ':other_contributions' => $memberData['other_contributions'],
                ':family_relationship' => $memberData['family_relationship'],
                ':family_name' => $memberData['family_name'],
                ':family_ic' => $memberData['family_ic'],
                ':password' => $memberData['password']
            ]);

            $newMemberId = $this->getConnection()->lastInsertId();
            $accountNumber = 'SA' . str_pad($newMemberId, 6, '0', STR_PAD_LEFT);
            
            $sql = "INSERT INTO savings_accounts (
                member_id, 
                account_name,
                account_number, 
                current_amount,
                status,
                display_main,
                created_at
            ) VALUES (
                :member_id,
                'Akaun Simpanan Utama',
                :account_number,
                0,
                'active',
                1,
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $newMemberId,
                ':account_number' => $accountNumber
            ]);

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            $this->getConnection()->rollBack();
            error_log('Database Error in migrateToMembers: ' . $e->getMessage());
            throw new \Exception('Failed to migrate member data: ' . $e->getMessage());
        }
    }

    public function getAllPendingMembers()
    {
        $sql = "SELECT * FROM PendingMember ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reject($id)
    {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM PendingMember WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$memberData) {
                throw new \Exception("Ahli tidak dijumpai");
            }

            $sql = "INSERT INTO rejectedmember (
                name, ic_no, gender, religion, race, marital_status,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions,
                family_relationship, family_name, family_ic,
                password,
                status,
                created_at
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions,
                :family_relationship, :family_name, :family_ic,
                :password,
                'Inactive',
                NOW()
            )";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':name' => $memberData['name'],
                ':ic_no' => $memberData['ic_no'],
                ':gender' => $memberData['gender'],
                ':religion' => $memberData['religion'],
                ':race' => $memberData['race'],
                ':marital_status' => $memberData['marital_status'],
                ':position' => $memberData['position'],
                ':grade' => $memberData['grade'],
                ':monthly_salary' => $memberData['monthly_salary'],
                ':home_address' => $memberData['home_address'],
                ':home_postcode' => $memberData['home_postcode'],
                ':home_state' => $memberData['home_state'],
                ':office_address' => $memberData['office_address'],
                ':office_postcode' => $memberData['office_postcode'],
                ':office_phone' => $memberData['office_phone'],
                ':home_phone' => $memberData['home_phone'],
                ':fax' => $memberData['fax'],
                ':registration_fee' => $memberData['registration_fee'],
                ':share_capital' => $memberData['share_capital'],
                ':fee_capital' => $memberData['fee_capital'],
                ':deposit_funds' => $memberData['deposit_funds'],
                ':welfare_fund' => $memberData['welfare_fund'],
                ':fixed_deposit' => $memberData['fixed_deposit'],
                ':other_contributions' => $memberData['other_contributions'],
                ':family_relationship' => $memberData['family_relationship'],
                ':family_name' => $memberData['family_name'],
                ':family_ic' => $memberData['family_ic'],
                ':password' => $memberData['password']
            ]);

            if (!$result) {
                throw new \Exception("Gagal memindahkan data ke rejected_members");
            }

            $sql = "DELETE FROM PendingMember WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $id]);

            if (!$result) {
                throw new \Exception("Gagal memadamkan data dari PendingMember");
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menolak permohonan: ' . $e->getMessage());
        }
    }

    public function getAllMembers()
    {
        try {
            if (!$this->getConnection()) {
                throw new \Exception("Database connection failed");
            }

            $sql = "
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    position COLLATE utf8mb4_general_ci as position,
                    monthly_salary,
                    'Ahli' COLLATE utf8mb4_general_ci as member_type,
                    'Active' COLLATE utf8mb4_general_ci as status
                FROM members
                UNION ALL
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    position COLLATE utf8mb4_general_ci as position,
                    monthly_salary,
                    'Pending' COLLATE utf8mb4_general_ci as member_type,
                    COALESCE(status, 'Pending') COLLATE utf8mb4_general_ci as status
                FROM pendingmember
                UNION ALL
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    position COLLATE utf8mb4_general_ci as position,
                    monthly_salary,
                    'Rejected' COLLATE utf8mb4_general_ci as member_type,
                    'Tolak' COLLATE utf8mb4_general_ci as status
                FROM rejectedmember
                ORDER BY id ASC";

            $stmt = $this->getConnection()->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Failed to prepare statement");
            }

            $result = $stmt->execute();
            
            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \Exception("Query execution failed: " . ($error[2] ?? 'Unknown error'));
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($data === false) {
                throw new \Exception("Failed to fetch data");
            }

            return $data;

        } catch (\PDOException $e) {
            error_log('Database Error in getAllMembers: ' . $e->getMessage());
            throw new \Exception('Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            error_log('Error in getAllMembers: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}