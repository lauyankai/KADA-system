<?php
require_once '../app/Core/Database.php';

try {
    $db = new App\Core\Database();
    $conn = $db->connect();

    // Add display_main column
    $sql = "ALTER TABLE savings_accounts ADD COLUMN IF NOT EXISTS display_main TINYINT(1) DEFAULT 0";
    $conn->exec($sql);

    echo "Migration successful: Added display_main column to savings_accounts table\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
} 