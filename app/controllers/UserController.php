<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Saving;
use App\Models\Loan;

class UserController extends BaseController
{
    private $user;
    private $saving;

    public function __construct()
    {
        $this->user = new User();
        $this->saving = new Saving();
    }

    // User Dashboard
    public function dashboard()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                header('Location: /auth/login');
                exit;
            }

            $memberId = $_SESSION['member_id'];
            $member = $this->user->getUserById($memberId);
            
            // Get savings account
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            $totalSavings = $savingsAccount ? $savingsAccount['current_amount'] : 0;
            
            // Get active loans and calculate total loan amount
            $loan = new Loan();
            $activeLoans = $loan->getActiveLoansByMemberId($memberId);
            $totalLoanAmount = 0;
            
            if (!empty($activeLoans)) {
                foreach ($activeLoans as $activeLoan) {
                    $totalLoanAmount += $activeLoan['amount'];
                }
            }

            $totalSavings = $this->saving->getTotalSavings($memberId);
            $recentActivities = $this->user->getRecentActivities($memberId);

            $this->view('users/dashboard', [
                'member' => $member,
                'savingsAccount' => $savingsAccount,
                'activeLoans' => $activeLoans,
                'totalSavings' => $totalSavings,
                'totalLoanAmount' => $totalLoanAmount,
                'recentActivities' => $recentActivities
            ]);

        } catch (\Exception $e) {
            error_log('Dashboard Error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
}