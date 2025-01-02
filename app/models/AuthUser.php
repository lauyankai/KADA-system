<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class AuthUser extends Model
{
    public function createAdmin($data)
    {
        try {
            $sql = "INSERT INTO admins (username, email, password, name) 
                    VALUES (:username, :email, :password, :name)";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password'],
                ':name' => $data['name'] ?? $data['username']
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return false;
        }
    }

    public function findAdminByUsername($username)
    {
        try {
            $sql = "SELECT id, username, password, name FROM admins WHERE username = :username";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return false;
        }
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
} 