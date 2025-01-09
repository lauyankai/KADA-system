<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use App\Models\Admin;
use PDO;

class AdminController extends BaseController {
    private $admin;

    public function __construct()
    {
        $this->admin = new Admin();
    }

    public function index()
    {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Fetch all pending register members
            $sql = "SELECT *
                    FROM pendingmember 
                    ORDER BY id DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            $pendingmember = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->view('admin/index', ['pendingmember' => $pendingmember]);
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching pending members: " . $e->getMessage();
            $this->view('admin/index', ['pendingmember' => []]);
        }
    }

    public function viewMember($id)
    {
        // Get user model
        $admin = new Admin();
        
        // Get user data by ID
        $data['member'] = $admin->getUserById($id);
        
        // Load view
        $this->view('admin/view', $data);
    }    

    public function approve($id)
    {
        try {
            $userModel = new Admin();
            $userModel->updateStatus($id, 'Lulus');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Lulus";
            header('Location: /admin');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /admin');
            exit();
        }
    }

    public function reject($id)
    {
        try {
            $userModel = new Admin();
            $userModel->updateStatus($id, 'Tolak');
            
            $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Tolak.";
            header('Location: /admin');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /admin');
            exit();
        }
    }

    // public function edit($id)
    // {
    //     $admin = $this->admin->find($id);

    //     $this->view('admin/edit', compact('admin'));
    // }

    // public function update($id)
    // {
    //     $this->user->update($id, $_POST);
    //     header('Location: /');
    // }

    // private function checkAuth()
    // {
    //     if (!isset($_SESSION['admin_id'])) {
    //         header('Location: /auth/login');
    //         exit();
    //     }
    // }
}