<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Saving;

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
                throw new \Exception('Sila log masuk untuk mengakses dashboard');
            }

            $memberId = $_SESSION['member_id'];
            error_log('Dashboard - Member ID: ' . $memberId);
            
            // Get member details
            $member = $this->user->getUserById($memberId);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            $totalSavings = $this->saving->getTotalSavings($memberId);
            $recentActivities = $this->user->getRecentActivities($memberId);

            $this->view('users/dashboard', [
                'member' => $member,
                'totalSavings' => $totalSavings,
                'recentActivities' => $recentActivities
            ]);

        } catch (\Exception $e) {
            error_log('Error in dashboard: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function savingsDashboard()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses dashboard');
            }

            $memberId = $_SESSION['member_id'];
            
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            if (!$savingsAccount) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }

            $recentTransactions = $this->saving->getRecentTransactions($memberId, 10);
            
            // Get savings goals
            $savingsGoals = $this->saving->getSavingsGoals($memberId);
            
            // Get recurring payments
            $recurringPayments = $this->saving->getRecurringPayments($memberId);

            $this->view('users/savings/page', [
                'account' => $savingsAccount,
                'transactions' => $recentTransactions,
                'goals' => $savingsGoals,
                'recurring' => $recurringPayments
            ]);

        } catch (\Exception $e) {
            error_log('Error in savingsDashboard: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }
}