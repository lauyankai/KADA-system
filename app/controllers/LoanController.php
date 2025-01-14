<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Loan;
use App\Models\User;

class LoanController extends BaseController
{
    private $loan;
    private $user;

    public function __construct()
    {
        $this->user = new User();
        $this->loan = new Loan();
    }

    public function showRequest()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get member details
            $member = $this->user->getUserById($memberId);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            // Debug log to check member data
            error_log('Member Data: ' . print_r($member, true));

            // Generate reference number
            $referenceNo = 'LOAN' . date('Ymd') . rand(1000, 9999);

            $this->view('users/loans/request', [
                'member' => $member,
                'referenceNo' => $referenceNo,
                'defaultData' => [
                    'name' => $member->full_name,
                    'ic_number' => $member->ic_number,
                    'member_no' => $member->member_id,
                    'phone' => $member->phone_number,
                    'address' => $member->address
                ]
            ]);

        } catch (\Exception $e) {
            error_log('Error in showRequest: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }

    public function submitRequest()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk membuat permohonan');
            }

            // Generate reference number
            $reference = 'LOAN' . date('Ymd') . rand(1000, 9999);

            // Prepare loan data
            $data = [
                'user_id' => $_SESSION['member_id'],
                'reference_no' => $reference,
                'loan_type' => $_POST['loan_type'],
                'other_loan_type' => $_POST['other_loan_type'] ?? null,
                'amount' => $_POST['amount'],
                'duration' => $_POST['duration'],
                'monthly_payment' => $_POST['amount'] / $_POST['duration'],
                'status' => 'pending'
            ];

            // Create loan request
            $loanId = $this->loan->create($data);

            $_SESSION['success'] = 'Permohonan pembiayaan berjaya dihantar. No Rujukan: ' . $reference;
            header('Location: /loans/status');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /loans/request');
            exit;
        }
    }

    public function showStatus()
    {
        if (!isset($_SESSION['member_id'])) {
            $_SESSION['error'] = 'Sila log masuk untuk melihat status';
            header('Location: /auth/login');
            exit;
        }

        $loans = $this->loan->getByUserId($_SESSION['member_id']);
        $this->view('loans/status', [
            'loans' => $loans
        ]);
    }

    public function showDetails($id)
    {
        if (!isset($_SESSION['member_id'])) {
            $_SESSION['error'] = 'Sila log masuk untuk melihat maklumat';
            header('Location: /auth/login');
            exit;
        }

        $loan = $this->loan->find($id);
        if (!$loan || $loan['user_id'] != $_SESSION['member_id']) {
            $_SESSION['error'] = 'Maklumat pembiayaan tidak dijumpai';
            header('Location: /loans/status');
            exit;
        }

        $schedule = $this->loan->getPaymentSchedule($id);
        $this->view('loans/details', [
            'loan' => $loan,
            'schedule' => $schedule
        ]);
    }

    public function listLoans()
    {
        if (!isset($_SESSION['is_admin'])) {
            $_SESSION['error'] = 'Akses tidak dibenarkan';
            header('Location: /auth/login');
            exit;
        }

        $loans = $this->loan->getAllPending();
        $this->view('loans/list', [
            'loans' => $loans
        ]);
    }

    public function showReview($id)
    {
        if (!isset($_SESSION['is_admin'])) {
            $_SESSION['error'] = 'Akses tidak dibenarkan';
            header('Location: /auth/login');
            exit;
        }

        $loan = $this->loan->find($id);
        if (!$loan) {
            $_SESSION['error'] = 'Permohonan tidak dijumpai';
            header('Location: /admin/loans');
            exit;
        }

        $existingLoanBalance = $this->loan->getTotalLoanBalance($loan['user_id']);

        $this->view('loans/review', [
            'loan' => $loan,
            'existingLoanBalance' => $existingLoanBalance
        ]);
    }

    public function submitReview($id)
    {
        try {
            if (!isset($_SESSION['is_admin'])) {
                throw new \Exception('Akses tidak dibenarkan');
            }

            $data = [
                'loan_id' => $id,
                'date_received' => $_POST['date_received'],
                'total_shares' => $_POST['total_shares'],
                'loan_balance' => $_POST['loan_balance'],
                'vehicle_repair' => $_POST['vehicle_repair'] ?? 0,
                'carnival' => $_POST['carnival'] ?? 0,
                'others_description' => $_POST['others_description'] ?? null,
                'others_amount' => $_POST['others_amount'] ?? 0,
                'total_deduction' => $_POST['total_deduction'],
                'decision' => $_POST['decision'],
                'reviewed_by' => $_SESSION['admin_id']
            ];

            $this->loan->createReview($data);

            $_SESSION['success'] = 'Keputusan telah disimpan';
            header('Location: /admin/loans');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/loans/review/' . $id);
            exit;
        }
    }
}
