<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class FamilyMember extends BaseModel
{
    public function getFamilyMembers($memberId, $memberType)
    {
        try {
            $sql = "SELECT * FROM family_members 
                    WHERE member_id = :member_id 
                    AND member_type = :member_type";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':member_id' => $memberId,
                ':member_type' => $memberType
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting family members: ' . $e->getMessage());
            return [];
        }
    }

    public function addFamilyMember($data)
    {
        try {
            $sql = "INSERT INTO family_members 
                    (member_type, member_id, name, relationship, ic_no) 
                    VALUES 
                    (:member_type, :member_id, :name, :relationship, :ic_no)";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':member_type' => $data['member_type'],
                ':member_id' => $data['member_id'],
                ':name' => $data['name'],
                ':relationship' => $data['relationship'],
                ':ic_no' => $data['ic_no']
            ]);
        } catch (\PDOException $e) {
            error_log('Error adding family member: ' . $e->getMessage());
            throw new \Exception('Gagal menambah ahli keluarga');
        }
    }

    public function updateFamilyMember($id, $data)
    {
        try {
            $sql = "UPDATE family_members 
                    SET name = :name, 
                        relationship = :relationship, 
                        ic_no = :ic_no 
                    WHERE id = :id";
            
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':relationship' => $data['relationship'],
                ':ic_no' => $data['ic_no']
            ]);
        } catch (\PDOException $e) {
            error_log('Error updating family member: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini ahli keluarga');
        }
    }

    public function deleteFamilyMember($id)
    {
        try {
            $sql = "DELETE FROM family_members WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log('Error deleting family member: ' . $e->getMessage());
            throw new \Exception('Gagal memadam ahli keluarga');
        }
    }

    public function getFamilyMemberById($id)
    {
        try {
            $sql = "SELECT * FROM family_members WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error getting family member: ' . $e->getMessage());
            return false;
        }
    }
} 