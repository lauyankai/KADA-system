<?php
namespace App\Models;
use App\Core\Model;
use PDO;
use App\Core\Database;
use PDOException;

class User extends Model
{
    public function all() 
    {
        $stmt = $this->getConnection()->query("SELECT * FROM admins"); // Use query() for SELECT statements
        return $stmt->fetchAll(); // Use fetchAll() to get all records
    }

    public function find($id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE id = :id"); // Use prepare() for SQL statements with variables
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT); // Use bindParam() to bind variables
        $stmt->execute(); // Use execute() to run the query
        return $stmt->fetch(); // Use fetch() to get a single record
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO pendingregistermember (
                name, ic_no, gender, religion, race, marital_status,
                member_number, pf_number, position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit,
                other_contributions
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :member_number, :pf_number, :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit,
                :other_contributions
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            $params = [
                ':name' => $data['name'],
                ':ic_no' => $data['ic_no'],
                ':gender' => $data['gender'],
                ':religion' => $data['religion'],
                ':race' => $data['race'],
                ':marital_status' => $data['marital_status'],
                ':member_number' => $data['member_no'],
                ':pf_number' => $data['pf_no'],
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
                ':other_contributions' => $data['other_contributions'] ?? null
            ];

            // Debug log
            error_log('SQL: ' . $sql);
            error_log('Params: ' . print_r($params, true));

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

    
    public function deleteById($id)
    {
        require_once 'Database.php'; // Ensure Database.php is included
    
        $db = new Database();
        $conn = $db->connect();
    
        try {
            $stmt = $conn->prepare("DELETE FROM pendingregistermembers WHERE id = :id"); // Use correct table name
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log the error or handle it appropriately
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM pendingregistermember WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Log the error
            error_log($e->getMessage());
            return false;
        }
    }
    



    

}
