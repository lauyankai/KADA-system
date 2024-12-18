<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
         // Fetch all users from the database
         $users = $this->user->all();

         // Pass the data to the 'users/index' view
         $this->view('users/index', compact('users'));
    }

    public function create()
    {
        $this->view('users/create');
    }

    public function store()
    {
        try {
            // Validate required fields
            $requiredFields = [
                'name', 'ic_no', 'gender', 'religion', 'race', 'marital_status',
                'member_no', 'pf_no', 'position', 'grade', 'monthly_salary',
                'home_address', 'home_postcode', 'home_state',
                'office_address', 'office_postcode',
                'office_phone', 'home_phone'
            ];

            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new \Exception("Field {$field} is required");
                }
            }

            $success = $this->user->create($_POST);

            // Handle AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Member application submitted successfully!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to save data'
                    ]);
                }
                exit;
            }

            // Handle regular form submission
            if ($success) {
                $_SESSION['success'] = 'Member application submitted successfully!';
                header('Location: /');
            } else {
                $_SESSION['error'] = 'Failed to submit application. Please try again.';
                header('Location: /create');
            }
            exit;

        } catch (\Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            }
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
        $this->user->delete($id);
        header('Location: /');
    }
}
