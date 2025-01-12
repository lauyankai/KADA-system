<?php
namespace App\Core;

use PDO;
use PDOException;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getConnection()
    {
        return $this->db;
    }
}