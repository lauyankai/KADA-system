<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Loan extends BaseModel
{
    public function createLoan($data)
{
    try {
        $sql = "INSERT INTO loan_applications (
            reference_no, member_id, loan_type, other_loan_type, amount, 
            duration, monthly_payment, birth_date, age, gender, religion, 
            race, member_no, pf_no, position, address_line1, address_line2, 
            postcode, city, state, office_address, office_phone, phone, 
            bank_name, bank_account, guarantor1_name, guarantor1_ic, 
            guarantor1_member_no, guarantor2_name, guarantor2_ic, 
            guarantor2_member_no, status, created_at
        ) VALUES (
            :reference_no, :member_id, :loan_type, :other_loan_type, :amount,
            :duration, :monthly_payment, :birth_date, :age, :gender, :religion,
            :race, :member_no, :pf_no, :position, :address_line1, :address_line2,
            :postcode, :city, :state, :office_address, :office_phone, :phone,
            :bank_name, :bank_account, :guarantor1_name, :guarantor1_ic,
            :guarantor1_member_no, :guarantor2_name, :guarantor2_ic,
            :guarantor2_member_no, 'pending', NOW()
        )";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat permohonan pembiayaan');
        }
    }

    public function getLoansByMemberId($memberId)
    {
        try {
            $sql = "SELECT * FROM loan_applications WHERE member_id = :member_id ORDER BY created_at DESC";
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
            $sql = "SELECT * FROM loan_applications WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat pembiayaan');
        }
    }
}

public function getAllPending()
{
    try {
        $sql = "SELECT * FROM loan_applications WHERE status = 'pending' ORDER BY created_at DESC";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        throw new \Exception('Gagal mendapatkan senarai permohonan');
    }
}

public function getTotalLoanBalance($memberId)
{
    try {
        $sql = "SELECT SUM(amount) as total FROM loan_applications WHERE member_id = :member_id AND status = 'approved'";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    } catch (\PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        throw new \Exception('Gagal mendapatkan jumlah pinjaman');
    }
}

public function createReview($data)
{
    try {
        $sql = "INSERT INTO loan_reviews (loan_id, date_received, total_shares, loan_balance, vehicle_repair, carnival, others_description, others_amount, total_deduction, decision, reviewed_by, created_at) VALUES (:loan_id, :date_received, :total_shares, :loan_balance, :vehicle_repair, :carnival, :others_description, :others_amount, :total_deduction, :decision, :reviewed_by, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    } catch (\PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        throw new \Exception('Gagal menyimpan keputusan');
    }
}

public function getPaymentSchedule($loanId)
{
    try {
        $sql = "SELECT * FROM loan_payments WHERE loan_id = :loan_id ORDER BY payment_date";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':loan_id' => $loanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        throw new \Exception('Gagal mendapatkan jadual pembayaran');
    }
}