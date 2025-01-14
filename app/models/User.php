<?php
namespace App\Models;
use App\Core\Database;
use App\Core\BaseModel;
use PDO;

class User extends BaseModel
{
    public function all() 
    {
        $stmt = $this->getConnection()->query("SELECT * FROM admins"); // Use query() for SELECT statements
        return $stmt->fetchAll(); // Use fetchAll() to get all records
    }

    /*public function getTransactionByReference($referenceNo)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM transactions WHERE reference_no = :referenceNo");
        $stmt->bindParam(':referenceNo', $referenceNo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }*/

    public function create($data)
    {
        try {
            // Clean the IC number (remove hyphens) before hashing
            $cleanIC = str_replace('-', '', $data['ic_no']);
            
            // Hash the clean IC number to use as password
            $hashedPassword = password_hash($cleanIC, PASSWORD_DEFAULT);
            
            // Debug log
            error_log('Clean IC: ' . $cleanIC);
            error_log('Hashed Password: ' . $hashedPassword);

            $sql = "INSERT INTO pendingmember (
                name, ic_no, gender, religion, race, marital_status,
                position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions,
                family_relationship, family_name, family_ic,
                password,
                status
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions,
                :family_relationship, :family_name, :family_ic,
                :password,
                'Pending'
            )";
            $stmt = $this->getConnection()->prepare($sql);
            
            $params = [
                ':name' => $data['name'],
                ':ic_no' => $data['ic_no'],
                ':gender' => $data['gender'],
                ':religion' => $data['religion'],
                ':race' => $data['race'],
                ':marital_status' => $data['marital_status'],
                ':position' => $data['position'],
                ':grade' => $data['grade'],
                ':monthly_salary' => $data['monthly_salary'],
                ':home_address' => $data['home_address'],
                ':home_postcode' => $data['home_postcode'],
                ':home_state' => $data['home_state'],
                ':office_address' => $data['office_address'],
                ':office_postcode' => $data['office_postcode'],
                ':office_phone' => $data['office_phone'],
                ':home_phone' => $data['home_phone'],
                ':fax' => $data['fax'] ?? null,
                ':registration_fee' => $data['registration_fee'] ?? 0,
                ':share_capital' => $data['share_capital'] ?? 0,
                ':fee_capital' => $data['fee_capital'] ?? 0,
                ':deposit_funds' => $data['deposit_funds'] ?? 0,
                ':welfare_fund' => $data['welfare_fund'] ?? 0,
                ':fixed_deposit' => $data['fixed_deposit'] ?? 0,
                ':other_contributions' => $data['other_contributions'] ?? null,
                ':family_relationship' => $data['family_relationship'][0] ?? null,
                ':family_name' => $data['family_name'][0] ?? null,
                ':family_ic' => $data['family_ic'][0] ?? null,
                ':password' => $hashedPassword
            ];

            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log('PDO Error: ' . print_r($stmt->errorInfo(), true));
            }
            
            return $result;

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Database error occurred: ' . $e->getMessage());
        }
}

    public function find($id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Use bindParam() to bind variables
        $stmt->execute(); // Use execute() to run the query
        return $stmt->fetch(); // Use fetch() to get a single record
    }

    public function update($id, $data)
    {
        $stmt = $this->getConnection()->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->execute([ // Use execute() to run the query
            ':name' => $data['name'], // Use named placeholders to prevent SQL injection
            ':email' => $data['email'], // Use named placeholders to prevent SQL injection
            ':id' => $id, // Use named placeholders to prevent SQL injection
        ]);
        return $stmt; // Return the PDOStatement object
    }

    public function getUserById($id)
    {
        try {
            $sql = "SELECT m.* 
                    FROM members m
                    WHERE m.id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            // Debug log
            error_log('getUserById Result: ' . print_r($result, true));
            
            return $result;
        } catch (\PDOException $e) {
            error_log('Database Error in getUserById: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan maklumat pengguna');
        }
    }

    public function getRecentActivities($memberId, $limit = 5)
    {
        try {
            $sql = "SELECT 
                    'savings' as type,
                    st.transaction_type as action,
                    st.amount,
                    st.created_at,
                    CASE 
                        WHEN st.transaction_type = 'deposit' THEN 'Deposit ke akaun simpanan'
                        WHEN st.transaction_type = 'withdrawal' THEN 'Pengeluaran dari akaun simpanan'
                        WHEN st.transaction_type = 'transfer' THEN 'Pemindahan wang'
                        ELSE 'Transaksi simpanan'
                    END as description
                FROM savings_transactions st
                JOIN savings_accounts sa ON st.savings_account_id = sa.id
                WHERE sa.member_id = :member_id
                
                UNION ALL
                
                SELECT 
                    'loan' as type,
                    status as action,
                    amount,
                    created_at,
                    CONCAT('Permohonan pembiayaan ', 
                        CASE 
                            WHEN status = 'pending' THEN 'sedang diproses'
                            WHEN status = 'approved' THEN 'telah diluluskan'
                            WHEN status = 'rejected' THEN 'telah ditolak'
                            ELSE status
                        END
                    ) as description
                FROM loans
                WHERE member_id = :member_id
                
                ORDER BY created_at DESC
                LIMIT :limit";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Database Error in getRecentActivities: ' . $e->getMessage());
            return [];
        }
    }

}



