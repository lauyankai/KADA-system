<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Admin extends BaseModel
{
    public function updateStatus($id, $status)
    {
        try {
            $this->getConnection()->beginTransaction();

            if ($status === 'Lulus') {
                // Get member data first
                $memberData = $this->getMemberById($id);
                if (!$memberData) {
                    throw new \Exception("Member not found");
                }

                // Migrate to members table and get the new ID
                $newMemberId = $this->migrateToMembers($id, $memberData, false);

                // Generate member ID
                $memberId = $this->generateMemberId();

                // Send approval email with the correct database ID
                $this->sendStatusEmail(
                    $memberData['email'],
                    $memberData['name'],
                    'Lulus',
                    $memberId,
                    $newMemberId  // Pass the actual database ID
                );

                // Delete from pending table
                $sql = "DELETE FROM pendingmember WHERE id = :id";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([':id' => $id]);
            } else {
                // For rejections
            $sql = "UPDATE pendingmember SET status = :status WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
                $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
            
                // Send rejection email
                $this->sendStatusEmail(
                    $memberData['email'],
                    $memberData['name'],
                    'Tolak'
                );
            }

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Error updating status: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserById($id)
    {
        try {
            // First check in members table (for 'Ahli' status)
            $sql = "SELECT *, 'Ahli' as member_type FROM members WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                return $result;
            }

            // If not found in members, check pendingmember table
            $sql = "SELECT *, 'Pending' as member_type FROM pendingmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                return $result;
            }

            // If not found in pendingmember, check rejectedmember table
            $sql = "SELECT *, 'Rejected' as member_type FROM rejectedmember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                return $result;
            }

            throw new \Exception('Member not found in any table');

        } catch (\PDOException $e) {
            error_log('Database Error in getUserById: ' . $e->getMessage());
            throw new \Exception('Failed to fetch user details');
        }
    }

    private function generateMemberId()
    {
        try {
            // Get current year
            $year = date('Y');
            
            // Get the latest member number for the current year
            $sql = "SELECT member_id FROM members 
                    WHERE member_id LIKE :year 
                    ORDER BY member_id DESC 
                    LIMIT 1";
            
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
            
            // Format: YYYYNNNN (e.g., 20250001)
            return $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
        } catch (\PDOException $e) {
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
                member_id, name, ic_no, gender, religion, race, marital_status, email,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                family_relationship, family_name, family_ic,
                password,
                status,
                created_at
            ) VALUES (
                :member_id, :name, :ic_no, :gender, :religion, :race, :marital_status, :email,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :family_relationship, :family_name, :family_ic,
                NULL,
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
                ':email' => $memberData['email'],
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
                ':family_relationship' => $memberData['family_relationship'],
                ':family_name' => $memberData['family_name'],
                ':family_ic' => $memberData['family_ic'],
            ]);

            // Get the new member's ID
            $newMemberId = $this->getConnection()->lastInsertId();
            
            // Generate account number
            $accountNumber = 'SAV-' . str_pad($newMemberId, 6, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999);
            
            // Create savings account
            $sql = "INSERT INTO savings_accounts (
                account_number,
                member_id,
                current_amount,
                status,
                created_at,
                updated_at
            ) VALUES (
                :account_number,
                :member_id,
                0.00,
                'active',
                NOW(),
                NOW()
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':account_number' => $accountNumber,
                ':member_id' => $newMemberId
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
            return $newMemberId;

        } catch (\Exception $e) {
            if ($useTransaction && $this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Error in migrateToMembers: ' . $e->getMessage());
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

            // Get member data first
            $memberData = $this->getMemberById($id);
            if (!$memberData) {
                throw new \Exception("Ahli tidak dijumpai");
            }

            // Move to rejected table
            $sql = "INSERT INTO rejectedmember (
                name, ic_no, gender, religion, race, marital_status, email,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                family_relationship, family_name, family_ic,
                password,
                status,
                created_at
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status, :email,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
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
                ':email' => $memberData['email'],
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
                ':family_relationship' => $memberData['family_relationship'],
                ':family_name' => $memberData['family_name'],
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

            // Send rejection email
            $this->sendStatusEmail(
                $memberData['email'],
                $memberData['name'],
                'Tolak'
            );

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
                    CASE 
                        WHEN status = 'Active' THEN 'Active'
                        WHEN status = 'Resigned' THEN 'Resigned'
                        ELSE status
                    END as status
                FROM members
                UNION ALL
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    position COLLATE utf8mb4_general_ci as position,
                    monthly_salary,
                    'Pending' COLLATE utf8mb4_general_ci as status
                FROM pendingmember
                UNION ALL
                SELECT 
                    id,
                    name COLLATE utf8mb4_general_ci as name,
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci as gender,
                    position COLLATE utf8mb4_general_ci as position,
                    monthly_salary,
                    'Rejected' COLLATE utf8mb4_general_ci as status
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
                    email COLLATE utf8mb4_general_ci as email,
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
                    ic_no COLLATE utf8mb4_general_ci as ic_no,
                    gender COLLATE utf8mb4_general_ci,
                    religion COLLATE utf8mb4_general_ci,
                    race COLLATE utf8mb4_general_ci,
                    marital_status COLLATE utf8mb4_general_ci,
                    email COLLATE utf8mb4_general_ci,
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
                    email COLLATE utf8mb4_general_ci,
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

    private function sendStatusEmail($email, $name, $status, $memberId = null, $databaseId = null)
    {
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);

        try {
            $config = require __DIR__ . '/../../config/mail.php';

            // Server settings
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($email, $name);

            if ($status === 'Lulus') {
                // Get the member's IC number
                $stmt = $this->getConnection()->prepare(
                    "SELECT ic_no FROM members WHERE id = :id"
                );
                $stmt->execute([':id' => $databaseId]);
                $member = $stmt->fetch(PDO::FETCH_ASSOC);
                $icNo = $member['ic_no'];

                // Create setup password link
                $setupLink = "http://" . $_SERVER['HTTP_HOST'] . "/auth/setup-password";

                $mail->Subject = 'Tahniah! Permohonan Keahlian Anda Telah Diluluskan';
                $mail->isHTML(true); // Set email format to HTML
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px;'>
                        <h2>Permohonan Keahlian Diluluskan</h2>
                        <p>Salam sejahtera {$name},</p>
                        
                        <p>Tahniah! Permohonan keahlian anda telah diluluskan.</p>
                        
                        <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                            <p><strong>ID Ahli:</strong> {$memberId}</p>
                            <p><strong>ID Pengguna:</strong> {$icNo}</p>
                            <p>Untuk menetapkan kata laluan akaun anda, sila klik pautan di bawah:</p>
                            <p><a href='{$setupLink}' style='background-color: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Tetapkan Kata Laluan</a></p>
                        </div>
                        
                        <p>Selepas menetapkan kata laluan, anda boleh log masuk menggunakan ID Pengguna (No. K/P) anda.</p>
                        
                        <p>Selamat datang ke keluarga Koperasi KADA!</p>
                        
                        <p>Sekian, terima kasih.</p>
                    </div>
                ";
            } else {
                $mail->Subject = 'Status Permohonan Keahlian Anda';
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px;'>
                        <h2>Keputusan Permohonan Keahlian</h2>
                        <p>Salam sejahtera {$name},</p>
                        
                        <p>Harap maaf dimaklumkan bahawa permohonan keahlian anda tidak berjaya.</p>
                        
                        <p>Anda boleh membuat permohonan baharu.</p>
                        
                        <p>Sekian, terima kasih.</p>
                    </div>
                ";
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Failed to send status email to {$email}: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function getInterestRates()
    {
        try {
            $sql = "SELECT * FROM interest_rates WHERE id = 1";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            $rates = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Return default values if no rates found
            if (!$rates) {
                return [
                    'savings_rate' => 0.00,
                    'loan_rate' => 0.00
                ];
            }
            
            return [
                'savings_rate' => (float)$rates['savings_rate'],
                'loan_rate' => (float)$rates['loan_rate']
            ];
        } catch (\PDOException $e) {
            error_log('Database Error in getInterestRates: ' . $e->getMessage());
            // Return default values on error
            return [
                'savings_rate' => 0.00,
                'loan_rate' => 0.00
            ];
        }
    }

    public function updateInterestRates($data)
    {
        try {
            $sql = "UPDATE interest_rates 
                    SET savings_rate = :savings_rate, 
                        loan_rate = :loan_rate,
                        updated_at = NOW() 
                    WHERE id = 1";
                    
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':savings_rate' => $data['savings_rate'],
                ':loan_rate' => $data['loan_rate']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error in updateInterestRates: ' . $e->getMessage());
            throw new \Exception('Failed to update interest rates');
        }
    }

    public function getLoanStatistics()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_loans,
                    COALESCE(SUM(amount), 0) as total_amount
                    FROM loans 
                    WHERE status = 'approved'";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_loans' => (int)$stats['total_loans'],
                'total_amount' => (float)$stats['total_amount']
            ];
            
        } catch (\PDOException $e) {
            error_log('Database Error in getLoanStatistics: ' . $e->getMessage());
            return [
                'total_loans' => 0,
                'total_amount' => 0.00
            ];
        }
    }

    public function approveResignation($memberId)
    {
        try {
            $this->getConnection()->beginTransaction();

            // Get member details first
            $sql = "SELECT m.*, r.reasons, r.created_at as resignation_date 
                    FROM members m 
                    JOIN resignation_reasons r ON m.id = r.member_id 
                    WHERE m.id = :member_id";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                throw new \Exception('Member not found');
            }

            // Debug log
            error_log('Found member: ' . json_encode($member));

            // Update member status
            $updateSql = "UPDATE members 
                         SET status = 'Resigned',
                             updated_at = NOW()
                         WHERE id = :member_id";
            
            $stmt = $this->getConnection()->prepare($updateSql);
            $result = $stmt->execute([':member_id' => $memberId]);
            
            if (!$result) {
                throw new \Exception('Failed to update member status');
            }

            // Update resignation_reasons with approval date
            $updateResignationSql = "UPDATE resignation_reasons 
                                    SET approved_at = NOW() 
                                    WHERE member_id = :member_id";
            
            $stmt = $this->getConnection()->prepare($updateResignationSql);
            $result = $stmt->execute([':member_id' => $memberId]);

            if (!$result) {
                throw new \Exception('Failed to update resignation approval date');
            }

            // Debug log
            error_log('Updated member status and approval date successfully');

            // Send email notification
            $emailResult = $this->sendResignationApprovalEmail(
                $member['email'],
                $member['name'],
                $member['resignation_date']
            );

            if (!$emailResult) {
                error_log('Failed to send resignation approval email');
            }

            $this->getConnection()->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Error approving resignation: ' . $e->getMessage());
            throw new \Exception('Gagal meluluskan permohonan berhenti: ' . $e->getMessage());
        }
    }

    private function sendResignationApprovalEmail($email, $name, $resignationDate)
    {
        $mail = new PHPMailer(true);
        try {
            $config = require __DIR__ . '/../../config/mail.php';

            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Pengesahan Permohonan Berhenti';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Permohonan Berhenti Diluluskan</h2>
                    <p>Salam sejahtera {$name},</p>
                    
                    <p>Permohonan berhenti anda bertarikh " . date('d/m/Y', strtotime($resignationDate)) . " telah diluluskan.</p>
                    
                    <p>Anda kini telah berhenti menjadi ahli koperasi.</p>
                    
                    <p>Sekian, terima kasih.</p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Error sending resignation approval email: ' . $e->getMessage());
            return false;
        }
    }

    public function getPendingResignations()
    {
        try {
            $sql = "SELECT m.*, r.reasons, r.created_at as resignation_date 
                    FROM members m 
                    JOIN resignation_reasons r ON m.id = r.member_id 
                    WHERE m.status = 'Inactive' 
                    AND r.approved_at IS NULL
                    ORDER BY r.created_at DESC";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug log
            error_log('Found ' . count($results) . ' pending resignations');
            
            return $results;
        } catch (\PDOException $e) {
            error_log('Error getting pending resignations: ' . $e->getMessage());
            return [];
        }
    }

    public function getMemberStats()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'Resigned' THEN 1 ELSE 0 END) as resigned
                    FROM (
                        SELECT 'Pending' as status FROM pendingmember
                        UNION ALL
                        SELECT status FROM members
                        UNION ALL
                        SELECT 'Rejected' as status FROM rejectedmember
                    ) all_members";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total' => (int)($stats['total'] ?? 0),
                'pending' => (int)($stats['pending'] ?? 0),
                'active' => (int)($stats['active'] ?? 0),
                'rejected' => (int)($stats['rejected'] ?? 0),
                'resigned' => (int)($stats['resigned'] ?? 0)
            ];
        } catch (\PDOException $e) {
            error_log('Error getting member stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'active' => 0,
                'rejected' => 0,
                'resigned' => 0
            ];
        }
    }

    public function getDirectors()
    {
        try {
            $sql = "SELECT * FROM directors ORDER BY created_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting directors: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllAdmins()
    {
        try {
            $sql = "SELECT id, username, email FROM admins ORDER BY created_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting admins: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteAdmin($id)
    {
        try {
            $sql = "DELETE FROM admins WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log('Error deleting admin: ' . $e->getMessage());
            return false;
        }
    }

    public function createAdmin($data)
    {
        try {
            $sql = "INSERT INTO admins (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password']
            ]);
        } catch (\PDOException $e) {
            error_log('Error creating admin: ' . $e->getMessage());
            return false;
        }
    }

    public function getAdminById($id)
    {
        try {
            $sql = "SELECT id, username, email FROM admins WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting admin details: ' . $e->getMessage());
            return null;
        }
    }

    public function updateAdmin($data)
    {
        try {
            $sql = "UPDATE admins SET username = :username, email = :email";
            $params = [
                ':id' => $data['id'],
                ':username' => $data['username'],
                ':email' => $data['email']
            ];

            if (isset($data['password'])) {
                $sql .= ", password = :password";
                $params[':password'] = $data['password'];
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log('Error updating admin: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyPassword($id, $password)
    {
        try {
            $sql = "SELECT password FROM admins WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                return false;
            }

            return password_verify($password, $admin['password']);
        } catch (\PDOException $e) {
            error_log('Error verifying password: ' . $e->getMessage());
            return false;
        }
    }

    public function getDirectorById($id)
    {
        try {
            $sql = "SELECT * FROM directors WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting director: ' . $e->getMessage());
            return null;
        }
    }

    public function updateDirector($data)
    {
        try {
            $sql = "UPDATE directors SET 
                    name = :name, 
                    position = :position, 
                    email = :email, 
                    phone_number = :phone_number 
                    WHERE id = :id";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':position' => $data['position'],
                ':email' => $data['email'],
                ':phone_number' => $data['phone_number']
            ]);
        } catch (\PDOException $e) {
            error_log('Error updating director: ' . $e->getMessage());
            return false;
        }
    }
}