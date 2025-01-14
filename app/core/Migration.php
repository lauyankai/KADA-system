<?php

class Migration {
    private $db;
    private $migrations_table = 'migrations';

    public function __construct($db) {
        $this->db = $db;
        $this->createMigrationsTable();
    }

    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrations_table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
    }

    public function getMigratedFiles() {
        $sql = "SELECT migration FROM {$this->migrations_table}";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function run() {
        $files = scandir(__DIR__ . '/../../migrations');
        $migratedFiles = array_column($this->getMigratedFiles(), 'migration');

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            if (!in_array($file, $migratedFiles)) {
                require_once __DIR__ . '/../../migrations/' . $file;
                $className = pathinfo($file, PATHINFO_FILENAME);
                $migration = new $className();
                
                // Run the migration
                $sql = $migration->up();
                if ($this->db->query($sql)) {
                    $this->db->query("INSERT INTO {$this->migrations_table} (migration) VALUES ('$file')");
                    echo "Migrated: $file\n";
                }
            }
        }
    }
}