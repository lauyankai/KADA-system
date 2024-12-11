<?php

namespace App\Core;
use \PDO;
use Dotenv\Dotenv;

class BaseModel {

    protected $pdo;

    public function __construct() {
        // Load the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Adjust the path based on your project structure
        $dotenv->load();

         // Fetch the database credentials from the .env file
         $host = $_ENV['DB_HOST'];
         $dbname = $_ENV['DB_NAME'];
         $username = $_ENV['DB_USER'];
         $password = $_ENV['DB_PASS'];

        try {
            $this->pdo = new \PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
