<?php
namespace App\Models;

use App\Core\BaseModel;
use App\Core\Database;
use PDO;

class Loan extends BaseModel
{
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function create($data)
    {
        try {
            $sql = "INSERT INTO loans (
                user_id, reference_no, loan_type, other_loan_type, amount, duration, monthly_payment,
                name, ic_number, birth_date, age, gender, religion, other_religion, race, other_race,
                home_address, home_postcode, home_city, home_state,
                member_no, position, office_address, office_postcode, office_city, office_state,
                phone_fax, mobile, bank_name, bank_account,
                declaration_name, declaration_ic,
                guarantor1_name, guarantor1_ic, guarantor1_member_no,
                guarantor2_name, guarantor2_ic, guarantor2_member_no,
                employer_confirmation_name, employer_confirmation_ic,
                basic_salary, net_salary
            ) VALUES (
                :user_id, :reference_no, :loan_type, :other_loan_type, :amount, :duration, :monthly_payment,
                :name, :ic_number, :birth_date, :age, :gender, :religion, :other_religion, :race, :other_race,
                :home_address, :home_postcode, :home_city, :home_state,
                :member_no, :position, :office_address, :office_postcode, :office_city, :office_state,
                :phone_fax, :mobile, :bank_name, :bank_account,
                :declaration_name, :declaration_ic,
                :guarantor1_name, :guarantor1_ic, :guarantor1_member_no,
                :guarantor2_name, :guarantor2_ic, :guarantor2_member_no,
                :employer_confirmation_name, :employer_confirmation_ic,
                :basic_salary, :net_salary
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            return $this->db->lastInsertId();

        } catch (\PDOException $e) {
            throw new \Exception('Gagal menyimpan permohonan: ' . $e->getMessage());
        }
    }

    public function createReview($data)
    {
        try {
            $sql = "INSERT INTO loan_reviews (
                loan_id, date_received, total_shares, loan_balance,
                vehicle_repair, carnival, others_description, others_amount,
                total_deduction, decision, reviewed_by
            ) VALUES (
                :loan_id, :date_received, :total_shares, :loan_balance,
                :vehicle_repair, :carnival, :others_description, :others_amount,
                :total_deduction, :decision, :reviewed_by
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            // Update loan status
            $this->updateStatus($data['loan_id'], $data['decision']);

            return $this->db->lastInsertId();

        } catch (\PDOException $e) {
            throw new \Exception('Gagal menyimpan keputusan: ' . $e->getMessage());
        }
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM loans WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM loans WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPaymentSchedule($loanId)
    {
        $loan = $this->find($loanId);
        if (!$loan) return [];

        $payments = [];
        $startDate = new \DateTime($loan['created_at']);
        
        for ($i = 1; $i <= $loan['duration']; $i++) {
            $dueDate = clone $startDate;
            $dueDate->modify("+$i month");
            
            $payments[] = [
                'month' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $loan['monthly_payment'],
                'status' => 'pending'
            ];
        }

        return $payments;
    }


    public function updateStatus($id, $status)
    {
        $sql = "UPDATE loans SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }
}