<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

// First require the PHPMailer files explicitly
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';

// Then use the PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
                name, ic_no, gender, religion, race, marital_status, email,
                mobile_phone, position, grade, monthly_salary,
                home_address, home_postcode, home_state,
                office_address, office_postcode,
                office_phone, home_phone, fax,
                family_relationship, family_name, family_ic,
                reference_no, status
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status, :email,
                :mobile_phone, :position, :grade, :monthly_salary,
                :home_address, :home_postcode, :home_state,
                :office_address, :office_postcode,
                :office_phone, :home_phone, :fax,
                :family_relationship, :family_name, :family_ic,
                :reference_no, 'Pending'
            )";
            
            $stmt = $this->getConnection()->prepare($sql);
            
            $params = [
                ':name' => $data['name'],
                ':ic_no' => $data['ic_no'],
                ':gender' => $data['gender'],
                ':religion' => $data['religion'],
                ':race' => $data['race'],
                ':marital_status' => $data['marital_status'],
                ':email' => $data['email'],
                ':mobile_phone' => $data['mobile_phone'],
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
            
            // Send confirmation email
            $this->sendConfirmationEmail($data['email'], $data['name'], $reference_no);
            
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

    public function store($data)
    {
        try {
            $sql = "INSERT INTO members (
                name, ic_no, birthday, age, gender, religion, race, 
                marital_status, email, monthly_salary, position, grade,
                home_address, home_postcode, home_state, home_phone,
                office_address, office_postcode, office_phone, fax, birthday, age,
                status
            ) VALUES (
                :name, :ic_no, :birthday, :age, :gender, :religion, :race,
                :marital_status, :email, :monthly_salary, :position, :grade,
                :home_address, :home_postcode, :home_state, :home_phone,
                :office_address, :office_postcode, :office_phone, :fax,
                :birthday, :age,
                'pending'
            )";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':ic_no' => $data['ic_no'],
                ':birthday' => $data['birthday'],
                ':age' => $data['age'],
                ':gender' => $data['gender'],
                ':religion' => $data['religion'],
                ':race' => $data['race'],
                ':marital_status' => $data['marital_status'],
                ':email' => $data['email'],
                ':monthly_salary' => $data['monthly_salary'],
                ':position' => $data['position'],
                ':grade' => $data['grade'],
                ':home_address' => $data['home_address'],
                ':home_postcode' => $data['home_postcode'],
                ':home_state' => $data['home_state'],
                ':home_phone' => $data['home_phone'],
                ':office_address' => $data['office_address'],
                ':office_postcode' => $data['office_postcode'],
                ':office_phone' => $data['office_phone'],
                ':fax' => $data['fax'],
                ':birthday' => $data['birthday'],
                ':age' => $data['age']
            ]);
            return $this->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan data pemohon');
        }
    }

    private function sendConfirmationEmail($email, $name, $reference_no) {
        $mail = new PHPMailer(true);

        try {
            // Get mail config
            $config = require __DIR__ . '/../../config/mail.php';

            // Enable debugging temporarily to see what's happening
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer debug: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            // SSL Options if needed
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Log the email being used
            error_log("Attempting to send email to: " . $email);

            // Recipients
            $mail->setFrom($config['from_address'], $config['from_name']);
            $mail->addAddress($email, $name); // This sends to the user's input email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Pengesahan Pendaftaran Ahli Koperasi';
            
            // Get current timestamp in Malaysia timezone
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $timestamp = date('d/m/Y H:i:s');
            
            // Calculate expected response date (7 days from now)
            $response_date = date('d/m/Y', strtotime('+7 days'));

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Pengesahan Pendaftaran Ahli Koperasi</h2>
                    <p>Salam sejahtera {$name},</p>
                    
                    <p>Terima kasih kerana mendaftar sebagai ahli koperasi. Berikut adalah maklumat permohonan anda:</p>
                    
                    <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                        <p><strong>Nombor Rujukan:</strong> {$reference_no}</p>
                        <p><strong>Tarikh & Masa Pendaftaran:</strong> {$timestamp}</p>
                    </div>
                    
                    <p>Status permohonan anda akan dimaklumkan dalam tempoh 7 hari bekerja (sebelum {$response_date}).</p>
                    
                    <p>Anda boleh menyemak status permohonan dengan menggunakan nombor rujukan di atas atau nama penuh dan no. kad pengenalan melalui laman web kami.</p>
                    
                    <p>Jika ada sebarang pertanyaan, sila hubungi kami.</p>
                    
                    <p>Sekian, terima kasih.</p>
                </div>
            ";

            $result = $mail->send();
            error_log("Email sent successfully to: " . $email);
            return true;
        } catch (Exception $e) {
            error_log("Failed sending email to: " . $email);
            error_log("Email error: " . $mail->ErrorInfo);
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }
}