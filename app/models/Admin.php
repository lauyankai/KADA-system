<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use PDO;

class Admin extends Model
{
    public function updateStatus($id, $status)
    {
        try {
            // Validate status value
            $validStatuses = ['Pending', 'Lulus', 'Tolak'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status value");
            }

            // First, check if the record exists
            $checkSql = "SELECT id FROM pendingregistermember WHERE id = :id";
            $checkStmt = $this->getConnection()->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            
            if (!$checkStmt->fetch()) {
                throw new Exception("Record with ID $id not found");
            }

            // Proceed with update if record exists
            $sql = "UPDATE pendingregistermember SET status = :status WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            
            $result = $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                throw new Exception("Update failed: " . ($error[2] ?? 'Unknown error'));
            }
            
            return true;
        } catch (PDOException $e) {
            error_log('Database Error in updateStatus: ' . $e->getMessage());
            throw new Exception('Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log('Error in updateStatus: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserById($id)
    {
        try {
            $sql = "SELECT * FROM pendingregistermember WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new Exception('Failed to fetch user details');
        }
    }
}