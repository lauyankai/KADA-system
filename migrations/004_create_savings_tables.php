<?php

class CreateSavingsTables {
    public function up() {
        return "
        CREATE TABLE IF NOT EXISTS savings_accounts (
            id INT NOT NULL AUTO_INCREMENT,
            account_number VARCHAR(20) DEFAULT NULL,
            member_id INT NOT NULL,
            current_amount DECIMAL(10,2) DEFAULT '0.00',
            status ENUM('active','completed','cancelled') DEFAULT 'active',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            display_main TINYINT(1) DEFAULT '0',
            account_name VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_account_number (account_number),
            CONSTRAINT fk_savings_admin FOREIGN KEY (member_id) REFERENCES admins (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

        CREATE TABLE IF NOT EXISTS savings_transactions (
            id INT NOT NULL AUTO_INCREMENT,
            savings_account_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            type ENUM('deposit','withdrawal','transfer_in','transfer_out') NOT NULL,
            payment_method ENUM('cash','bank_transfer','salary_deduction','fpx','card','ewallet') NOT NULL,
            reference_no VARCHAR(50) DEFAULT NULL,
            description TEXT,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT savings_transactions_ibfk_1 FOREIGN KEY (savings_account_id) REFERENCES savings_accounts (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

        CREATE TABLE IF NOT EXISTS savings_goals (
            id INT NOT NULL AUTO_INCREMENT,
            member_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            target_amount DECIMAL(10,2) NOT NULL,
            current_amount DECIMAL(10,2) DEFAULT '0.00',
            target_date DATE NOT NULL,
            monthly_target DECIMAL(10,2) NOT NULL,
            status ENUM('active','completed','cancelled') DEFAULT 'active',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT savings_goals_ibfk_1 FOREIGN KEY (member_id) REFERENCES admins (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
    }

    public function down() {
        return "
        DROP TABLE IF EXISTS savings_transactions;
        DROP TABLE IF EXISTS savings_goals;
        DROP TABLE IF EXISTS savings_accounts;";
    }
} 