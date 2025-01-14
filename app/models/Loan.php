<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Loan extends BaseModel
{
    public function createLoanRequest($data)
    {
        try {
            $sql = "INSERT INTO loans (
                member_id, reference_no, loan_type, other_loan_type,
                amount, duration, monthly_payment, name, ic_number,
                birth_date, age, gender, religion, other_religion,
                race, other_race, home_address, home_postcode,
                home_city, home_state, member_no, position,
                office_address, office_postcode, office_city,
                office_state, phone_fax, mobile, bank_name,
                bank_account, declaration_name, declaration_ic,
                guarantor1_name, guarantor1_ic, guarantor1_member_no,
                guarantor2_name, guarantor2_ic, guarantor2_member_no,
                employer_confirmation_name, employer_confirmation_ic,
                basic_salary, net_salary, status, created_at
            ) VALUES (
                :member_id, :reference_no, :loan_type, :other_loan_type,
                :amount, :duration, :monthly_payment, :name, :ic_number,
                :birth_date, :age, :gender, :religion, :other_religion,
                :race, :other_race, :home_address, :home_postcode,
                :home_city, :home_state, :member_no, :position,
                :office_address, :office_postcode, :office_city,
                :office_state, :phone_fax, :mobile, :bank_name,
                :bank_account, :declaration_name, :declaration_ic,
                :guarantor1_name, :guarantor1_ic, :guarantor1_member_no,
                :guarantor2_name, :guarantor2_ic, :guarantor2_member_no,
                :employer_confirmation_name, :employer_confirmation_ic,
                :basic_salary, :net_salary, 'pending', NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($data);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat permohonan pembiayaan');
        }
    }

    public function getLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM loans WHERE member_id = :member_id ORDER BY created_at DESC";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':member_id' => $memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan senarai pembiayaan');
        }
    }

    public function getLoanById($id)
    {
        try {
            $sql = "SELECT * FROM loans WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat pembiayaan');
        }
    }
}