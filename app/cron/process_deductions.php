<?php
require_once __DIR__ . '/../bootstrap.php';

$saving = new App\Models\Saving();
try {
    $saving->processAutomaticDeductions();
    echo "Automatic deductions processed successfully\n";
} catch (Exception $e) {
    echo "Error processing deductions: " . $e->getMessage() . "\n";
} 