<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class AnnualReport extends BaseModel
{
    public function getAllReports()
    {
        try {
            // Debug connection
            error_log('Database connection status: ' . ($this->getConnection() ? 'Connected' : 'Not connected'));
            
            $sql = "SELECT ar.*, a.username as uploader_name 
                    FROM annual_report ar
                    LEFT JOIN admins a ON ar.uploaded_by = a.id
                    WHERE ar.status = 'active'
                    ORDER BY ar.year DESC, ar.uploaded_at DESC";
            
            error_log('Executing SQL: ' . $sql);
            
            $stmt = $this->getConnection()->query($sql);
            if (!$stmt) {
                error_log('Query failed: ' . print_r($this->getConnection()->errorInfo(), true));
                throw new \PDOException('Query failed');
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Query results count: ' . count($results));
            
            return $results;
            
        } catch (\PDOException $e) {
            error_log('Database Error in getAllReports: ' . $e->getMessage());
            error_log('Error code: ' . $e->getCode());
            error_log('Error info: ' . print_r($e->errorInfo, true));
            throw new \Exception('Gagal mendapatkan senarai laporan: ' . $e->getMessage());
        }
    }

    public function create($data)
    {
        try {
            error_log('Starting create annual report with data: ' . print_r($data, true));
            
            // Verify admin exists
            $adminSql = "SELECT id FROM admins WHERE id = :admin_id";
            $adminStmt = $this->getConnection()->prepare($adminSql);
            $adminStmt->execute([':admin_id' => $data['uploaded_by']]);
            if (!$adminStmt->fetch()) {
                error_log('Admin not found with ID: ' . $data['uploaded_by']);
                throw new \Exception('Admin tidak dijumpai');
            }

            $sql = "INSERT INTO annual_report (
                year, title, file_name, file_path, file_size, 
                description, uploaded_by, status
            ) VALUES (
                :year, :title, :file_name, :file_path, :file_size,
                :description, :uploaded_by, 'active'
            )";
            
            error_log('Executing SQL: ' . $sql);
            
            $fileSize = filesize(dirname(__DIR__, 2) . '/public/uploads/annual-reports/' . $data['filename']);
            $params = [
                ':year' => $data['year'],
                ':title' => 'Laporan Tahunan ' . $data['year'],
                ':file_name' => $data['filename'],
                ':file_path' => '/uploads/annual-reports/' . $data['filename'],
                ':file_size' => $fileSize,
                ':description' => $data['description'],
                ':uploaded_by' => $data['uploaded_by']
            ];
            
            error_log('With parameters: ' . print_r($params, true));

            $stmt = $this->getConnection()->prepare($sql);
            if (!$stmt) {
                error_log('Prepare failed: ' . print_r($this->getConnection()->errorInfo(), true));
                throw new \PDOException('Failed to prepare statement');
            }

            $result = $stmt->execute($params);

            if (!$result) {
                error_log('Execute failed: ' . print_r($stmt->errorInfo(), true));
                throw new \PDOException('Failed to execute statement');
            }

            $lastId = $this->getConnection()->lastInsertId();
            error_log('Successfully created annual report with ID: ' . $lastId);
            
            return $lastId;
            
        } catch (\PDOException $e) {
            error_log('Database Error in create: ' . $e->getMessage());
            error_log('Error code: ' . $e->getCode());
            error_log('Error info: ' . print_r($e->errorInfo, true));
            throw new \Exception('Gagal menyimpan laporan: ' . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM annual_report WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat laporan');
        }
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM annual_report WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal memadam laporan');
        }
    }

    public function getLatestReports($limit = 6)
    {
        try {
            $sql = "SELECT * FROM annual_report 
                    WHERE status = 'active' 
                    ORDER BY year DESC, uploaded_at DESC 
                    LIMIT :limit";
                    
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log('Database Error in getLatestReports: ' . $e->getMessage());
            return [];
        }
    }
} 