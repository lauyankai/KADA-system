<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\MemberFee;
use App\Models\User;

class UserFeeController extends BaseController
{
    private $memberFee;
    private $user;

    public function __construct()
    {
        $this->memberFee = new MemberFee();
        $this->user = new User();
    }

    public function showInitialFees()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $fees = $this->memberFee->getFeesByMemberId($_SESSION['member_id']);
            
            if (!$fees) {
                // Create initial fees if not exists
                $this->memberFee->createInitialFees($_SESSION['member_id']);
                $fees = $this->memberFee->getFeesByMemberId($_SESSION['member_id']);
            }

            $this->view('users/fees/initial', ['fees' => $fees]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function confirmPayment()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            // Simply mark the payment as completed
            $this->memberFee->updatePaymentStatus($_SESSION['member_id'], 'completed');

            $_SESSION['success'] = 'Pembayaran berjaya. Keahlian anda telah diaktifkan.';
            header('Location: /users/fees/success');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/fees/initial');
            exit;
        }
    }

    public function showSuccess()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $this->view('users/fees/success');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }
} 