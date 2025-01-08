<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private $charset;
    private $conn;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? null;
        $this->dbname = $_ENV['DB_NAME'] ?? null;
        $this->user = $_ENV['DB_USER'] ?? null;
        $this->pass = $_ENV['DB_PASS'] ?? null;
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }

    public function connect()
    {
        if ($this->conn === null) {
            try {
                if (!$this->host || !$this->dbname || !$this->user || !$this->pass) {
                    throw new PDOException("Missing database configuration");
                }

                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                $this->conn = new PDO($dsn, $this->user, $this->pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}