<?php
namespace App\Core;

use PDO;
use PDOException;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        try {
            error_log('Initializing BaseModel database connection');
            $database = new Database();
            $this->db = $database->connect();
            if (!$this->db) {
                error_log('Failed to get database connection');
                throw new \Exception('Database connection failed');
            }
            error_log('Database connection successful');
        } catch (\Exception $e) {
            error_log('Error in BaseModel constructor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection()
    {
        if (!$this->db) {
            error_log('No database connection available');
            throw new \Exception('No database connection');
        }
        return $this->db;
    }
}