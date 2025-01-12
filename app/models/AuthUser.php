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
} 