<?php

class CreateLoansTable {
    public function up() {
        return "CREATE TABLE IF NOT EXISTS loans (
            id INT NOT NULL AUTO_INCREMENT,
            reference_no VARCHAR(50) NOT NULL,
            loan_type ENUM('al_bai','al_innah','skim_khas','road_tax','al_qardhul','other') NOT NULL,
            other_loan_type VARCHAR(255) DEFAULT NULL,
            amount DECIMAL(10,2) NOT NULL,
            duration INT NOT NULL,
            monthly_payment DECIMAL(10,2) NOT NULL,
            status ENUM('pending','approved','rejected') DEFAULT 'pending',
            date_received DATE DEFAULT NULL,
            total_shares DECIMAL(10,2) DEFAULT NULL,
            loan_balance DECIMAL(10,2) DEFAULT NULL,
            vehicle_repair DECIMAL(10,2) DEFAULT NULL,
            carnival DECIMAL(10,2) DEFAULT NULL,
            others_description VARCHAR(255) DEFAULT NULL,
            others_amount DECIMAL(10,2) DEFAULT NULL,
            total_deduction DECIMAL(10,2) DEFAULT NULL,
            decision ENUM('approved','rejected') DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
    }

    public function down() {
        return "DROP TABLE IF EXISTS loans;";
    }
} 