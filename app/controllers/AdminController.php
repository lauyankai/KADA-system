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
            $admin = new Admin();
            $allMembers = $admin->getAllMembers();
            
            $this->view('admin/index', [
                'members' => $allMembers,
                'stats' => [
                    'total' => count($allMembers),
                    'pending' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Pending')),
                    'active' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Ahli')),
                    'rejected' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Rejected'))
                ]
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = "Error fetching members: " . $e->getMessage();
            $this->view('admin/index', ['members' => [], 'stats' => [
                'total' => 0,
                'pending' => 0,
                'active' => 0,
                'rejected' => 0
            ]]);
        }
    }

    public function viewMember($id)
    {
        $admin = new Admin();
        $data['member'] = $admin->getUserById($id);
        $this->view('admin/view', $data);
    }    

    public function approve($id)
    {
        try {
            $admin = new Admin();
            $member = $admin->getMemberById($id);

            if ($member['member_type'] === 'Rejected') {
                // If member is from rejected table, migrate to members
                if ($admin->migrateFromRejected($id)) {
                    $_SESSION['success'] = "Ahli telah berjaya dipindahkan ke senarai ahli aktif";
                } else {
                    throw new \Exception("Gagal memindahkan ahli");
                }
            } else {
                // Normal approval process for pending members
                $admin->updateStatus($id, 'Lulus');
                $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Lulus";
            }
            
            header('Location: /admin');
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /admin');
            exit();
        }
    }

    public function reject($id)
    {
        try {
            $admin = new Admin();
            if ($admin->reject($id)) {
                $_SESSION['success'] = "Permohonan telah berjaya ditolak dan dipindahkan ke senarai rejected";
            } else {
                throw new \Exception("Gagal menolak permohonan");
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /admin');
        exit();
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