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
            
            $member = $this->user->getUserById($memberId);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            $this->view('users/loans/request', [
                'member' => $member,
                'defaultData' => [
                    'name' => $member->name,
                    'ic_number' => $member->ic_no,
                    'member_no' => $member->member_id,
                    'phone' => $member->home_phone,
                    'address' => $member->home_address
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
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            // Generate reference number
            $reference = 'LOAN' . date('Ymd') . rand(1000, 9999);

            // Get member details
            $member = $this->user->getUserById($_SESSION['member_id']);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            // Validate required fields
            $requiredFields = ['loan_type', 'amount', 'duration', 'monthly_payment'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new \Exception('Sila isi semua maklumat yang diperlukan');
                }
            }

            $loanData = [
                'reference_no' => $reference,
                'member_id' => $_SESSION['member_id'],
                'loan_type' => $_POST['loan_type'],
                'amount' => $_POST['amount'],
                'duration' => $_POST['duration'],
                'monthly_payment' => $_POST['monthly_payment'],
                'bank_name' => $_POST['bank_name'] ?? null,
                'bank_account' => $_POST['bank_account'] ?? null,
                'status' => 'pending',
                'date_received' => date('Y-m-d H:i:s')
            ];

            if ($this->loan->createLoan($loanData)) {
                $_SESSION['success'] = 'Permohonan pembiayaan berjaya dihantar. No Rujukan: ' . $reference;
                header('Location: /users/loans/status');
                exit;
            }

            throw new \Exception('Gagal menghantar permohonan');

        } catch (\Exception $e) {
            error_log('Error in submitRequest: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/loans/request');
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

        $loans = $this->loan->getLoansByMemberId($_SESSION['member_id']);
        
        $this->view('users/loans/status', [
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

    public function index()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get all loan applications for the member
            $loans = $this->loan->getLoansByMemberId($memberId);
            
            $this->view('users/loans/index', [
                'loans' => $loans
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }

    public function report($loanId = null)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // If no loan ID specified, get all loans for dropdown
            if (!$loanId) {
                $loans = $this->loan->getLoansByMemberId($memberId);
                $loan = !empty($loans) ? $loans[0] : null;
                $loanId = $loan ? $loan['id'] : null;
            } else {
                $loan = $this->loan->getLoanById($loanId);
            }

            if (!$loan || $loan['member_id'] != $memberId) {
                throw new \Exception('Maklumat pembiayaan tidak dijumpai');
            }

            // Get period from query parameters
            $period = $_GET['period'] ?? 'today';
            
            // Calculate dates based on period
            $dates = $this->calculateDates($period);
            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            // Get loan transactions
            $transactions = $this->loan->getTransactionsByDateRange($loanId, $startDate, $endDate);

            $this->view('users/loans/report', [
                'loan' => $loan,
                'loans' => $loans ?? [],
                'transactions' => $transactions,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/loans');
            exit;
        }
    }
}