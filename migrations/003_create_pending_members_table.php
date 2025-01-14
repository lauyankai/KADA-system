<?php

class CreatePendingMembersTable {
    public function up() {
        return "CREATE TABLE IF NOT EXISTS pendingmember (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            ic_no VARCHAR(22) DEFAULT NULL,
            gender ENUM('Male','Female') NOT NULL,
            religion VARCHAR(50) NOT NULL,
            race VARCHAR(50) NOT NULL,
            marital_status ENUM('Single','Married','Divorced','Widowed') NOT NULL,
            position VARCHAR(100) NOT NULL,
            grade VARCHAR(50) NOT NULL,
            monthly_salary DECIMAL(10,2) NOT NULL,
            home_address TEXT NOT NULL,
            home_postcode VARCHAR(10) NOT NULL,
            home_state VARCHAR(50) NOT NULL,
            office_address TEXT NOT NULL,
            office_postcode VARCHAR(10) NOT NULL,
            office_phone VARCHAR(20) NOT NULL,
            home_phone VARCHAR(20) NOT NULL,
            fax VARCHAR(20) DEFAULT NULL,
            family_relationship TEXT NOT NULL,
            family_name TEXT NOT NULL,
            family_ic TEXT NOT NULL,
            registration_fee DECIMAL(10,2) NOT NULL,
            share_capital DECIMAL(10,2) NOT NULL,
            fee_capital DECIMAL(10,2) NOT NULL,
            deposit_funds DECIMAL(10,2) NOT NULL,
            welfare_fund DECIMAL(10,2) NOT NULL,
            fixed_deposit DECIMAL(10,2) NOT NULL,
            other_contributions TEXT,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status ENUM('Pending','Lulus','Tolak') DEFAULT 'Pending',
            PRIMARY KEY (id),
            UNIQUE KEY ic_no (ic_no)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    public function down() {
        return "DROP TABLE IF EXISTS pendingmember;";
    }
} 