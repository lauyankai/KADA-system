<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

class Guest extends BaseModel
{
    public function create($data)
    {
        try {
            // Generate reference number: REF[YEAR][MONTH][DAY][4-DIGIT-SEQUENCE]
            $date = date('Ymd');
            $sequence = $this->getNextSequence();
            $reference_no = 'REF' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // Clean the IC number (remove hyphens) before hashing
            $cleanIC = str_replace('-', '', $data['ic_no']);
            
            // Hash the clean IC number to use as password
            $hashedPassword = password_hash($cleanIC, PASSWORD_DEFAULT);

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
                reference_no,
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
                :reference_no,
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
                ':reference_no' => $reference_no
            ];

            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log('PDO Error: ' . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            // Store reference number in session for display
            $_SESSION['reference_no'] = $reference_no;
            
            // Get the last inserted ID
            $lastId = $this->getConnection()->lastInsertId();
            
            // Fetch and return the created record
            return $this->find($lastId);

        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Database error occurred: ' . $e->getMessage());
        }
    }

    private function getNextSequence()
    {
        try {
            // Get the current date in YYYYMMDD format
            $today = date('Ymd');
            
            // Find the highest sequence number for today
            $sql = "SELECT MAX(SUBSTRING(reference_no, 12)) as max_sequence 
                    FROM pendingmember 
                    WHERE reference_no LIKE :prefix";
            
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':prefix' => 'REF' . $today . '%']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If no records found for today, start with 1
            if (!$result['max_sequence']) {
                return 1;
            }
            
            // Otherwise, increment the highest sequence number
            return intval($result['max_sequence']) + 1;
            
        } catch (\PDOException $e) {
            error_log('Error getting sequence: ' . $e->getMessage());
            // Return 1 as fallback
            return 1;
        }
    }

    public function find($id)
    {
        $query = "SELECT * FROM pendingmember WHERE id = ?";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkApplicationStatus($name) {
        try {
            $conn = $this->getConnection();
            if (!$conn) {
                throw new \Exception("Database connection failed");
            }

            // Convert name to uppercase
            $name = strtoupper(trim($name));

            // Check in pendingmember table
            $sql = "SELECT name, status FROM pendingmember WHERE UPPER(name) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }

            // Check members table
            $sql = "SELECT name, status FROM members WHERE UPPER(name) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }

            // Check rejected table
            $sql = "SELECT name, status FROM rejectedmember WHERE UPPER(name) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }

            return 'not_found';
            
        } catch (\PDOException $e) {
            error_log("Database error in checkApplicationStatus: " . $e->getMessage());
            throw new \Exception("Database error occurred");
        }
    }

    public function checkStatusByReference($reference_no)
    {
        try {
            // Check pending members
            $sql = "SELECT status FROM pendingmember WHERE reference_no = ?";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$reference_no]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }
            
            return 'not_found';
            
        } catch (\PDOException $e) {
            error_log("Database error in checkStatusByReference: " . $e->getMessage());
            throw new \Exception("Database error occurred");
        }
    }

    public function checkStatusByPersonal($name, $ic_no)
    {
        try {
            // Check pending members
            $sql = "SELECT status FROM pendingmember WHERE name = ? AND ic_no = ?";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$name, $ic_no]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }

            // Check members table
            $sql = "SELECT status FROM members WHERE name = ? AND ic_no = ?";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$name, $ic_no]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }

            // Check rejected members
            $sql = "SELECT status FROM rejectedmember WHERE name = ? AND ic_no = ?";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$name, $ic_no]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['status'];
            }
            
            return 'not_found';
            
        } catch (\PDOException $e) {
            error_log("Database error in checkStatusByPersonal: " . $e->getMessage());
            throw new \Exception("Database error occurred");
        }
    }
}