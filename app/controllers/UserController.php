<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Core\Database;
use PDO;

class UserController extends BaseController
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
            $sql = "SELECT *
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
            if ($this->user->create($_POST)) {
                $_SESSION['success'] = "Pendaftaran anda telah berjaya dihantar dan sedang dalam proses pengesahan.";
                header('Location: /');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /users/create');
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

    private function checkAuth()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /auth/login');
            exit();
        }
    }

    public function approve($id)
    {
        try {
            $userModel = new User();
            $userModel->updateStatus($id, 'Lulus');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Lulus";
            header('Location: /users');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /users');
            exit();
        }
    }

    public function reject($id)
    {
        try {
            $userModel = new User();
            $userModel->updateStatus($id, 'Tolak');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Tolak";
            header('Location: /users');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /users');
            exit();
        }
    }

    public function viewMember($id)
    {
        // Get user model
        $userModel = new User();
        
        // Get user data by ID
        $data['member'] = $userModel->getUserById($id);
        
        // Load view
        $this->view('users/view', $data);
    }    
}