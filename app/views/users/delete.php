<?php

require_once 'path/to/Database.php'; // Update this path
use App\Core\Database;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the input

    try {
        // Create a Database instance and connect
        $db = new Database();
        $conn = $db->connect();

        // Prepare the DELETE query
        $stmt = $conn->prepare("DELETE FROM members WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to index.php with a success message
            header("Location: index.php?message=Record deleted successfully");
            exit;
        } else {
            // Handle execution failure
            header("Location: index.php?message=Failed to delete the record");
            exit;
        }
    } catch (PDOException $e) {
        // Handle database-related errors
        header("Location: index.php?message=Database error: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Handle invalid or missing ID
    header("Location: index.php?message=Invalid request. No valid ID provided.");
    exit;
}

?>
