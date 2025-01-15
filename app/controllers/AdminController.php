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
        try {
            $admin = new Admin();
            $member = $admin->getUserById($id);

            if (!$member) {
                throw new \Exception('Member not found');
            }

            switch ($member->member_type) {
                case 'Ahli':
                    $member->account_details = $this->getMemberAccountDetails($id);
                    $member->savings_info = $this->getMemberSavingsInfo($id);
                    $member->loan_info = $this->getMemberLoanInfo($id);
                    break;

                case 'Pending':
                    $member->submission_date = $member->created_at;
                    break;

                case 'Rejected':
                    $member->rejection_date = $member->updated_at;
                    break;
            }

            $this->view('admin/view', ['member' => $member]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin');
            exit;
        }
    }

    private function getMemberAccountDetails($id)
    {
        try {
            $sql = "SELECT * FROM savings_accounts WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting account details: ' . $e->getMessage());
            return [];
        }
    }

    private function getMemberSavingsInfo($id)
    {
        try {
            $sql = "SELECT 
                    SUM(current_amount) as total_savings,
                    COUNT(*) as account_count
                    FROM savings_accounts 
                    WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting savings info: ' . $e->getMessage());
            return null;
        }
    }

    private function getMemberLoanInfo($id)
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_loans,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_loans
                    FROM loans 
                    WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting loan info: ' . $e->getMessage());
            return null;
        }
    }

    public function approve($id)
    {
        try {
            $admin = new Admin();
            $member = $admin->getMemberById($id);

            if ($member['member_type'] === 'Rejected') {
                if ($admin->migrateFromRejected($id)) {
                    $_SESSION['success'] = "Ahli telah berjaya dipindahkan ke senarai ahli aktif";
                } else {
                    throw new \Exception("Gagal memindahkan ahli");
                }
            } else {
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

    public function rejectMember($id)
    {
        try {
            $admin = new Admin();
            $admin->updateMemberStatus($id, 'rejected');
            $_SESSION['success'] = "Permohonan telah berjaya ditolak dan dipindahkan ke senarai rejected";
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