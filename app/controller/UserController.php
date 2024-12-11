<?php

namespace App\Controller; 
use App\Controller\BaseController;
use App\Models\UserModel; 
 
class UserController extends BaseController {
    public function index() {
        // Example: Retrieve all users from the model and pass them to the view
        $userModel = new UserModel();
        $users = $userModel->getAllUsers();
        $this->render('/user/index', ['users' => $users]);
    }

    // Show the registration form
    public function register() {    
        $this->render('/user/register'); // Render the registration form view
    }

    // Handle user registration
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug: Log the $_POST array
            var_dump($_POST); // Check the form data
    
            // Check if necessary fields exist in POST data
            $username = isset($_POST['name']) ? trim($_POST['name']) : null;
            $email = isset($_POST['email']) ? trim($_POST['email']) : null;
            $password = isset($_POST['password']) ? $_POST['password'] : null;
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    
            // Check if all fields are provided
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                echo "All fields are required!";
                return;
            }
    
            // Check if passwords match
            if ($password !== $confirmPassword) {
                echo "Passwords do not match!";
                return;
            }
    
            // Create a new UserModel instance and call register method
            $userModel = new UserModel();
            $existingUser = $userModel->getUserByEmail($email);
    
            // Check if the email is already taken
            if ($existingUser) {
                echo "Email is already in use!";
                return;
            }
    
            // If all checks pass, register the new user
            $result = $userModel->register($username, $email, $password);
    
            if ($result) {
                // Redirect to login or a success page
                header('Location: /');
                exit;
            } else {
                echo "An error occurred during registration.";
            }
        }
    }

    // Show the login form
    public function login() {
        $this->render('user/index');  // Render login form view
    }

    // Handle the login process
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize form input
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validate the form data (check if fields are empty)
            if (empty($email) || empty($password)) {
                echo "All fields are required!";
                return;
            }

            // Create an instance of UserModel
            $userModel = new UserModel();

            // Verify user credentials
            $user = $userModel->login($email, $password);
            
            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
            // Start session and store user data
            session_start();
            $_SESSION['user_id'] = $user['id'];  // Store user id in session
            $_SESSION['username'] = $user['username'];  // Store username in session (optional)

            // Redirect to the student list page (or other dashboard)
            header('Location: /student'); 
            exit;
            } else {
                echo "Invalid email or password!";
            }
        }
    }

    public function logout() {
        // Start the session to access session variables
        session_start();

        // Unset all session variables
        session_unset();

        // Destroy the session completely
        session_destroy();

        // Redirect the user to the login page after logging out
        header('Location: /');
        exit; // Ensure no further code is executed after redirection
    }
}
