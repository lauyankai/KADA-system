<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use PDO;
use PDOException;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Fetch all pending register members
            $sql = "SELECT id, name, ic_no, gender, position, monthly_salary 
                    FROM pendingregistermember 
                    ORDER BY id DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            $pendingregistermembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Pass the data to the view
            $this->view('users/index', compact('pendingregistermembers'));
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching pending members: " . $e->getMessage();
            $this->view('users/index', ['pendingregistermembers' => []]);
        }
    }

    public function create()
    {
        $this->view('users/create');
    }

    public function store()
    {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert main member data
            $sql = "INSERT INTO pendingregistermember (
                name, ic_no, gender, religion, race, marital_status,
                member_number, pf_number, monthly_salary, position, grade,
                home_address, home_postcode, home_state, home_phone,
                office_address, office_postcode, office_phone, fax,
                registration_fee, share_capital, fee_capital,
                deposit_funds, welfare_fund, fixed_deposit, other_contributions,
                family_relationship, family_name, family_ic,
                status
            ) VALUES (
                :name, :ic_no, :gender, :religion, :race, :marital_status,
                :member_number, :pf_number, :monthly_salary, :position, :grade,
                :home_address, :home_postcode, :home_state, :home_phone,
                :office_address, :office_postcode, :office_phone, :fax,
                :registration_fee, :share_capital, :fee_capital,
                :deposit_funds, :welfare_fund, :fixed_deposit, :other_contributions,
                :family_relationship, :family_name, :family_ic,
                :status
            )";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'name' => $_POST['name'],
                'ic_no' => $_POST['ic_no'],
                'gender' => $_POST['gender'],
                'religion' => $_POST['religion'],
                'race' => $_POST['race'],
                'marital_status' => $_POST['marital_status'],
                'member_number' => $_POST['member_no'],
                'pf_number' => $_POST['pf_no'],
                'monthly_salary' => $_POST['monthly_salary'],
                'position' => $_POST['position'],
                'grade' => $_POST['grade'],
                'home_address' => $_POST['home_address'],
                'home_postcode' => $_POST['home_postcode'],
                'home_state' => $_POST['home_state'],
                'home_phone' => $_POST['home_phone'],
                'office_address' => $_POST['office_address'],
                'office_postcode' => $_POST['office_postcode'],
                'office_phone' => $_POST['office_phone'],
                'fax' => $_POST['fax'],
                'registration_fee' => $_POST['registration_fee'],
                'share_capital' => $_POST['share_capital'],
                'fee_capital' => $_POST['fee_capital'],
                'deposit_funds' => $_POST['deposit_funds'],
                'welfare_fund' => $_POST['welfare_fund'],
                'fixed_deposit' => $_POST['fixed_deposit'],
                'other_contributions' => $_POST['other_contributions'],
                'family_relationship' => $_POST['family_relationship'][0],
                'family_name' => $_POST['family_name'][0],
                'family_ic' => $_POST['family_ic'][0],
                'status' => 'pending'
            ]);
            
            // Commit transaction
            $conn->commit();
            
            // Redirect with success message
            $_SESSION['success'] = "Pendaftaran anda telah berjaya dihantar dan sedang dalam proses pengesahan.";
            header('Location: /');
            exit;
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($conn) {
                $conn->rollBack();
            }
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /create');
            exit;
        }
    }

    public function edit($id)
    {
        // Fetch the user data using the ID
        $user = $this->user->find($id);

        // Pass the user data to the 'users/edit' view
        $this->view('users/edit', compact('user'));
    }

    public function update($id)
    {
        $this->user->update($id, $_POST);
        header('Location: /');
    }

    public function delete($id)
    {
        require_once 'models/User.php'; // Ensure the User model is included
    
        if (is_numeric($id)) {
            $userModel = new User(); // Create an instance of the User model
    
            if ($userModel->deleteById($id)) {
                // Redirect back to the users index page with a success message
                header("Location: /users/index?message=User deleted successfully");
                exit;
            } else {
                // Redirect back with an error message
                header("Location: /users/index?message=Failed to delete the user");
                exit;
            }
        } else {
            // Handle invalid ID
            header("Location: /users/index?message=Invalid request");
            exit;
        }
    }
    

}
