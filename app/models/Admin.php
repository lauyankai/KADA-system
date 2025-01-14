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

            $validStatuses = ['Pending', 'Lulus', 'Tolak'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception("Invalid status");
            }

            $checkSql = "SELECT id FROM pendingmember WHERE id = :id";
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

    private function generateMemberId()
    {
        try {
            // Get the current year
            $year = date('Y');
            
            // Get the latest member number for the current year
            $sql = "SELECT member_id FROM members 
                    WHERE member_id LIKE :year 
                    ORDER BY member_id DESC LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':year' => $year . '%']);
            $lastId = $stmt->fetchColumn();
            
            if ($lastId) {
                // Extract the sequence number and increment
                $sequence = intval(substr($lastId, -4)) + 1;
            } else {
                // Start with 0001 if no existing members for this year
                $sequence = 1;
            }
            
            // Format: YYYY0001
            return $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
        } catch (\Exception $e) {
            error_log('Error generating member ID: ' . $e->getMessage());
            throw new \Exception('Failed to generate member ID');
        }
    }

    public function migrateToMembers($id, $memberData = null, $useTransaction = true)
    {
        try {
            if ($useTransaction) {
                $this->getConnection()->beginTransaction();
            }

            if (!$memberData) {
                // Get data from pendingmember if memberData not provided
                $sql = "SELECT * FROM pendingmember WHERE id = :id";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':id' => $id]);
                $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$memberData) {
                    throw new \Exception("Member data not found");
                }
            }

            // Generate member ID
            $memberId = $this->generateMemberId();

            // Insert into members table
            $sql = "INSERT INTO members (
                member_id, name, ic_no, gender, religion, race, marital_status,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions,
                family_relationship, family_name, family_ic,
                status,
                created_at
            ) VALUES (
                :member_id, :name, :ic_no, :gender, :religion, :race, :marital_status,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions,
                :family_relationship, :family_name, :family_ic,
                'Active',
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $memberId,
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
                ':family_ic' => $memberData['family_ic']
            ]);

            // Delete from source table (pendingmember)
            if (!$memberData) {
                $sql = "DELETE FROM pendingmember WHERE id = :id";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':id' => $id]);
            }

            if ($useTransaction) {
                $this->getConnection()->commit();
            }
            return true;

        } catch (\Exception $e) {
            if ($useTransaction && $this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            throw $e;
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

    public function getMemberById($id)
    {
        try {
            $sql = "SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    religion COLLATE utf8mb4_general_ci as religion,
                    race COLLATE utf8mb4_general_ci as race,
                    marital_status COLLATE utf8mb4_general_ci as marital_status,
                    position COLLATE utf8mb4_general_ci as position,
                    grade COLLATE utf8mb4_general_ci as grade,
                    monthly_salary,
                    home_address COLLATE utf8mb4_general_ci as home_address,
                    home_postcode COLLATE utf8mb4_general_ci as home_postcode,
                    home_state COLLATE utf8mb4_general_ci as home_state,
                    office_address COLLATE utf8mb4_general_ci as office_address,
                    office_postcode COLLATE utf8mb4_general_ci as office_postcode,
                    office_phone COLLATE utf8mb4_general_ci as office_phone,
                    home_phone COLLATE utf8mb4_general_ci as home_phone,
                    fax COLLATE utf8mb4_general_ci as fax,
                    registration_fee,
                    share_capital,
                    fee_capital,
                    deposit_funds,
                    welfare_fund,
                    fixed_deposit,
                    other_contributions,
                    family_relationship COLLATE utf8mb4_general_ci as family_relationship,
                    family_name COLLATE utf8mb4_general_ci as family_name,
                    family_ic COLLATE utf8mb4_general_ci as family_ic,
                    'Pending' COLLATE utf8mb4_general_ci as member_type 
                FROM pendingmember 
                WHERE id = :id
                UNION
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci,
                    ic_no COLLATE utf8mb4_general_ci,
                    gender COLLATE utf8mb4_general_ci,
                    religion COLLATE utf8mb4_general_ci,
                    race COLLATE utf8mb4_general_ci,
                    marital_status COLLATE utf8mb4_general_ci,
                    position COLLATE utf8mb4_general_ci,
                    grade COLLATE utf8mb4_general_ci,
                    monthly_salary,
                    home_address COLLATE utf8mb4_general_ci,
                    home_postcode COLLATE utf8mb4_general_ci,
                    home_state COLLATE utf8mb4_general_ci,
                    office_address COLLATE utf8mb4_general_ci,
                    office_postcode COLLATE utf8mb4_general_ci,
                    office_phone COLLATE utf8mb4_general_ci,
                    home_phone COLLATE utf8mb4_general_ci,
                    fax COLLATE utf8mb4_general_ci,
                    registration_fee,
                    share_capital,
                    fee_capital,
                    deposit_funds,
                    welfare_fund,
                    fixed_deposit,
                    other_contributions,
                    family_relationship COLLATE utf8mb4_general_ci,
                    family_name COLLATE utf8mb4_general_ci,
                    family_ic COLLATE utf8mb4_general_ci,
                    'Rejected' COLLATE utf8mb4_general_ci as member_type
                FROM rejectedmember 
                WHERE id = :id
                UNION
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci,
                    ic_no COLLATE utf8mb4_general_ci,
                    gender COLLATE utf8mb4_general_ci,
                    religion COLLATE utf8mb4_general_ci,
                    race COLLATE utf8mb4_general_ci,
                    marital_status COLLATE utf8mb4_general_ci,
                    position COLLATE utf8mb4_general_ci,
                    grade COLLATE utf8mb4_general_ci,
                    monthly_salary,
                    home_address COLLATE utf8mb4_general_ci,
                    home_postcode COLLATE utf8mb4_general_ci,
                    home_state COLLATE utf8mb4_general_ci,
                    office_address COLLATE utf8mb4_general_ci,
                    office_postcode COLLATE utf8mb4_general_ci,
                    office_phone COLLATE utf8mb4_general_ci,
                    home_phone COLLATE utf8mb4_general_ci,
                    fax COLLATE utf8mb4_general_ci,
                    registration_fee,
                    share_capital,
                    fee_capital,
                    deposit_funds,
                    welfare_fund,
                    fixed_deposit,
                    other_contributions,
                    family_relationship COLLATE utf8mb4_general_ci,
                    family_name COLLATE utf8mb4_general_ci,
                    family_ic COLLATE utf8mb4_general_ci,
                    'Ahli' COLLATE utf8mb4_general_ci as member_type
                FROM members 
                WHERE id = :id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                throw new \Exception("Member not found");
            }

            return $result;
        } catch (\PDOException $e) {
            error_log('Database Error in getMemberById: ' . $e->getMessage());
            throw new \Exception('Database error: ' . $e->getMessage());
        }
    }

    public function migrateFromRejected($id)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get member data from rejected table
            $sql = "SELECT * FROM rejectedmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$memberData) {
                throw new \Exception("Member not found in rejected list");
            }

            // Use migrateToMembers with the rejected member data
            $this->migrateToMembers($id, $memberData, false);

            // Delete from rejected table
            $sql = "DELETE FROM rejectedmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Error in migrateFromRejected: ' . $e->getMessage());
            throw $e;
        }
    }
}