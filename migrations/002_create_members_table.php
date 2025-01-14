<?php

class CreateMembersTable {
    public function up() {
        return "CREATE TABLE IF NOT EXISTS members (
            id INT NOT NULL AUTO_INCREMENT,
            member_id VARCHAR(8) DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            ic_no VARCHAR(20) NOT NULL,
            gender VARCHAR(20) DEFAULT NULL,
            religion VARCHAR(50) DEFAULT NULL,
            race VARCHAR(50) DEFAULT NULL,
            marital_status VARCHAR(20) DEFAULT NULL,
            position VARCHAR(100) DEFAULT NULL,
            grade VARCHAR(20) DEFAULT NULL,
            monthly_salary DECIMAL(10,2) DEFAULT NULL,
            home_address TEXT,
            home_postcode VARCHAR(10) DEFAULT NULL,
            home_state VARCHAR(50) DEFAULT NULL,
            office_address TEXT,
            office_postcode VARCHAR(10) DEFAULT NULL,
            office_phone VARCHAR(20) DEFAULT NULL,
            home_phone VARCHAR(20) DEFAULT NULL,
            fax VARCHAR(20) DEFAULT NULL,
            registration_fee DECIMAL(10,2) DEFAULT NULL,
            share_capital DECIMAL(10,2) DEFAULT NULL,
            fee_capital DECIMAL(10,2) DEFAULT NULL,
            deposit_funds DECIMAL(10,2) DEFAULT NULL,
            welfare_fund DECIMAL(10,2) DEFAULT NULL,
            fixed_deposit DECIMAL(10,2) DEFAULT NULL,
            other_contributions TEXT,
            family_relationship VARCHAR(50) DEFAULT NULL,
            family_name VARCHAR(255) DEFAULT NULL,
            family_ic VARCHAR(20) DEFAULT NULL,
            password VARCHAR(255) DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'Active',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY ic_no (ic_no),
            UNIQUE KEY member_id (member_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    public function down() {
        return "DROP TABLE IF EXISTS members;";
    }
} 