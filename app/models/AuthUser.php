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
            
            // First try to find in members table
            $stmt = $this->getConnection()->prepare(
                "SELECT * FROM members 
                 WHERE REPLACE(ic_no, '-', '') = :ic_no 
                 AND status = 'Active'"
            );
            $stmt->execute([':ic_no' => $cleanIC]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Debug logging
                error_log('Found member in members table with IC: ' . $cleanIC);
                return $result;
            }
            
            // If not found in members, check pendingmember table
            $stmt = $this->getConnection()->prepare(
                "SELECT * FROM pendingmember 
                 WHERE REPLACE(ic_no, '-', '') = :ic_no 
                 AND status = 'Lulus'"
            );
            $stmt->execute([':ic_no' => $cleanIC]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log('Finding member with IC: ' . $cleanIC);
            error_log('Result: ' . ($result ? 'Found in pendingmember' : 'Not found'));
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log('Database Error in findMemberByIC: ' . $e->getMessage());
            throw new \Exception('Error finding member: ' . $e->getMessage());
        }
    }
}
