<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Loan;

try {
    $loan = new Loan();
    
    // Only run on 28th of each month
    if (date('d') != '28') {
        exit('Not statement generation day');
    }
    
    // Get all active loans
    $sql = "SELECT id FROM loans WHERE status = 'active'";
    $stmt = $loan->getConnection()->query($sql);
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate statements for each loan
    $period = date('Y-m-01'); // First day of current month
    foreach ($loans as $loanData) {
        $loan->generateMonthlyStatement($loanData['id'], $period);
    }
    
    echo "Statement generation completed\n";
    
} catch (Exception $e) {
    error_log('Statement Generation Error: ' . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
} 