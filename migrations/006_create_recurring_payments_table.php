<?php

class CreateRecurringPaymentsTable {
    public function up() {
        return "CREATE TABLE IF NOT EXISTS recurring_payments (
            id INT NOT NULL AUTO_INCREMENT,
            member_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            deduction_day INT NOT NULL,
            payment_method ENUM('salary','fpx','card') NOT NULL,
            status ENUM('active','paused','cancelled') DEFAULT 'active',
            last_deduction_date DATE DEFAULT NULL,
            next_deduction_date DATE DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT recurring_payments_ibfk_1 FOREIGN KEY (member_id) REFERENCES admins (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
    }

    public function down() {
        return "DROP TABLE IF EXISTS recurring_payments;";
    }
} 