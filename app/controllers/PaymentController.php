<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends BaseController
{
    private $payment;
    private $user;

    public function __construct()
    {
        $this->payment = new Payment();
        $this->user = new User();
    }

    public function processPayment()
    {
        try {
            $amount = $_POST['amount'];
            $method = $_POST['payment_method'];
            $provider = null;

            // Validate amount
            if (!is_numeric($amount) || $amount <= 0 || $amount > 1000) {
                throw new \Exception('Jumlah tidak sah');
            }

            // Set provider based on payment method
            switch ($method) {
                case 'online_banking':
                    $provider = $_POST['bank'] ?? null;
                    if (!$provider) throw new \Exception('Sila pilih bank');
                    break;
                case 'card':
                    // In production, validate card details
                    $provider = 'card';
                    break;
                case 'ewallet':
                    $provider = $_POST['ewallet'] ?? null;
                    if (!$provider) throw new \Exception('Sila pilih e-wallet');
                    break;
                default:
                    throw new \Exception('Kaedah pembayaran tidak sah');
            }

            // Create payment record
            $paymentId = $this->payment->createPayment([
                'member_id' => $_SESSION['admin_id'],
                'amount' => $amount,
                'payment_method' => $method,
                'provider' => $provider
            ]);

            // Redirect to appropriate payment page
            $this->redirectToPaymentGateway($paymentId, $method, $provider);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/savings');
            exit();
        }
    }

    private function redirectToPaymentGateway($paymentId, $method, $provider)
    {
        // In production, integrate with real payment gateways
        $this->view('payment/gateway', [
            'paymentId' => $paymentId,
            'method' => $method,
            'provider' => $provider
        ]);
    }

    public function handleCallback()
    {
        try {
            // In production, verify callback authenticity
            $paymentId = $_POST['payment_id'];
            $status = $_POST['status'];
            $reference = $_POST['reference'];

            if ($status === 'success') {
                $this->payment->updatePaymentStatus($paymentId, 'completed', $reference);
                // Add amount to savings
                $payment = $this->payment->find($paymentId);
                $this->user->addDeposit($payment['member_id'], $payment['amount']);
                $_SESSION['success'] = 'Pembayaran berjaya';
            } else {
                $this->payment->updatePaymentStatus($paymentId, 'failed', $reference);
                $_SESSION['error'] = 'Pembayaran gagal';
            }

            header('Location: /admin/savings');
            exit();

        } catch (\Exception $e) {
            error_log('Payment Callback Error: ' . $e->getMessage());
            header('Location: /admin/savings');
            exit();
        }
    }

    public function showReceipt($reference)
    {
        try {
            if (isset($_SESSION['receipt'])) {
                $receipt = $_SESSION['receipt'];
                unset($_SESSION['receipt']); // Clear after use
                $this->view('payment/receipt', ['receipt' => $receipt]);
                return;
            }

            // If no session receipt, try to get from database
            $transaction = $this->saving->getTransactionByReference($reference);
            if (!$transaction) {
                throw new \Exception('Transaksi tidak ditemui');
            }

            $receipt = [
                'type' => $transaction['type'],
                'amount' => $transaction['amount'],
                'reference_no' => $transaction['reference_no'],
                'payment_method' => $transaction['payment_method'],
                'created_at' => $transaction['created_at'],
                'description' => $transaction['description']
            ];

            $this->view('payment/receipt', ['receipt' => $receipt]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/savings/page');
            exit;
        }
    }
} 