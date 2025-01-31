<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class AuthUser extends BaseModel
{
    public function createAdmin($data)
    {
        try {
            $stmt = $this->getConnection()->prepare(
                "INSERT INTO admins (username, email, password) 
                 VALUES (:username, :email, :password)"
            );
            return $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password']
            ]);
        } catch (\PDOException $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function findAdminByUsername($username)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM admins WHERE username = :username"
        );
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isAdmin($userId)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT is_admin FROM admins WHERE id = :id"
        );
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['is_admin'];
    }

    public function findMemberByIC($ic_no)
    {
        try {
            // Remove any hyphens from the input IC
            $cleanIC = str_replace('-', '', $ic_no);
            
            error_log("Attempting to find member with IC: " . $cleanIC);
            
            // First try to find in members table with more detailed logging
            $sql = "SELECT * FROM members 
                    WHERE REPLACE(ic_no, '-', '') = :ic_no 
                    AND status = 'Active'";
                    
            error_log("SQL Query: " . $sql);
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':ic_no' => $cleanIC]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Found member with data: " . print_r($result, true));
                // Check if password exists
                if (empty($result['password'])) {
                    error_log("Member found but no password set");
                }
                return $result;
            } else {
                error_log("No member found with IC: " . $cleanIC);
                // Try a direct query to see what's in the database
                $stmt = $this->getConnection()->query("SELECT ic_no FROM members");
                $allICs = $stmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("All ICs in database: " . print_r($allICs, true));
            }
            
            return null;
            
        } catch (\PDOException $e) {
            error_log('Database Error in findMemberByIC: ' . $e->getMessage());
            throw new \Exception('Error finding member: ' . $e->getMessage());
        }
    }

    // Add a method to set initial password for members
    public function setMemberPassword($memberId, $password)
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            error_log("Setting password for member ID: " . $memberId);
            
            // First verify the member exists
            $checkSql = "SELECT id FROM members WHERE id = :id";
            $checkStmt = $this->getConnection()->prepare($checkSql);
            $checkStmt->execute([':id' => $memberId]);
            $member = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                error_log("No member found with ID: " . $memberId);
                throw new \Exception('Member not found');
            }
            
            // Update the password
            $sql = "UPDATE members 
                    SET password = :password 
                    WHERE id = :id";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':password' => $hashedPassword,
                ':id' => $memberId
            ]);

            if ($result) {
                // Verify the update
                $verifySql = "SELECT id, password FROM members WHERE id = :id";
                $verifyStmt = $this->getConnection()->prepare($verifySql);
                $verifyStmt->execute([':id' => $memberId]);
                $updated = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($updated && $updated['password'] === $hashedPassword) {
                    error_log("Password successfully updated for member ID: " . $memberId);
                    return true;
                } else {
                    error_log("Password verification failed for member ID: " . $memberId);
                    return false;
                }
            }

            error_log("Failed to update password for member ID: " . $memberId);
            return false;

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Failed to set password: ' . $e->getMessage());
        }
    }
}
