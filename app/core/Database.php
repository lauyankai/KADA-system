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
        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASS');
        $this->charset = getenv('DB_CHARSET') ?: 'utf8mb4';
    }

    public function connect()
    {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                $this->conn = new PDO($dsn, $this->user, $this->pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
