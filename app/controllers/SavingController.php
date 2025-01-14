<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Saving;

class SavingController extends BaseController
{
    private $user;
    private $saving;

    public function __construct()
    {
        $this->user = new User();
        $this->saving = new Saving();
    }

    public function savingsDashboard()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses dashboard');
            }

            $memberId = $_SESSION['member_id'];
            error_log('SavingsPage - Member ID: ' . $memberId);
            
            // Get member details
            $member = $this->user->getUserById($memberId);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            // Get savings account details
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            
            // Get recent transactions
            $recentTransactions = $this->saving->getRecentTransactions($memberId, 5);

            $this->view('users/savings/page', [
                'member' => $member,
                'savingsAccount' => $savingsAccount,
                'recentTransactions' => $recentTransactions
            ]);

        } catch (\Exception $e) {
            error_log('Error in savingsPage: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }

    public function showDepositForm()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            $savingsAccount = $this->saving->getSavingsAccount($memberId);

            $this->view('users/savings/deposit', [
                'account' => $savingsAccount
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/savings/page');
            exit;
        }
    }

    public function showTransferForm()
    {
        try {
            $memberId = $_SESSION['member_id'];
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            
            $this->view('users/savings/transfer/index', [
                'accounts' => $savingsAccount
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/savings/transfer/index');
            exit();
        }
    }    
}