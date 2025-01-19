<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Saving;
use App\Models\Loan;

class StatementController extends BaseController
{
    private $saving;
    private $loan;

    public function __construct()
    {
        $this->saving = new Saving();
        $this->loan = new Loan();
    }

    public function index()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get account type and period from query parameters, with defaults
            $accountType = $_GET['account_type'] ?? 'savings';
            $period = $_GET['period'] ?? 'today'; // Set default period to 'today'

            // Calculate dates based on period
            $today = date('Y-m-d');
            switch ($period) {
                case 'today':
                    $startDate = $today;
                    $endDate = $today;
                    break;
                case 'current':
                    $startDate = date('Y-m-01'); // First day of current month
                    $endDate = $today;
                    break;
                case 'last':
                    $startDate = date('Y-m-01', strtotime('last month'));
                    $endDate = date('Y-m-t', strtotime('last month')); // Last day of last month
                    break;
                case 'custom':
                    $startDate = $_GET['start_date'] ?? $today;
                    $endDate = $_GET['end_date'] ?? $today;
                    break;
                default:
                    $startDate = $today;
                    $endDate = $today;
                    $period = 'today'; // Ensure period is set even if invalid value provided
            }

            if ($accountType === 'savings') {
                $account = $this->saving->getSavingsAccount($memberId);
                if (!$account) {
                    throw new \Exception('Akaun simpanan tidak dijumpai');
                }
                $transactions = $this->saving->getTransactionsByDateRange(
                    $account['id'], 
                    $startDate, 
                    $endDate
                );
                $accounts = null;
            } else {
                $accounts = $this->loan->getLoansByMemberId($memberId);
                if (empty($accounts)) {
                    throw new \Exception('Akaun pembiayaan tidak dijumpai');
                }

                $selectedLoanId = $_GET['loan_id'] ?? $accounts[0]['id'];
                
                $account = null;
                foreach ($accounts as $loan) {
                    if ($loan['id'] == $selectedLoanId) {
                        $account = $loan;
                        break;
                    }
                }

                if (!$account) {
                    throw new \Exception('Akaun pembiayaan tidak dijumpai');
                }

                $transactions = $this->loan->getTransactionsByDateRange(
                    $selectedLoanId,
                    $startDate,
                    $endDate
                );
            }

            $this->view('users/statement/index', [
                'accountType' => $accountType,
                'account' => $account,
                'accounts' => $accounts,
                'transactions' => $transactions,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'period' => $period
            ]);

        } catch (\Exception $e) {
            error_log('Error in statement index: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }
    }

    public function download()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            $accountType = $_GET['account_type'] ?? 'savings';
            $period = $_GET['period'] ?? 'today';
            
            // Calculate dates based on period - same logic as index method
            $today = date('Y-m-d');
            switch ($period) {
                case 'today':
                    $startDate = $today;
                    $endDate = $today;
                    break;
                case 'current':
                    $startDate = date('Y-m-01'); // First day of current month
                    $endDate = $today;
                    break;
                case 'last':
                    $startDate = date('Y-m-01', strtotime('last month'));
                    $endDate = date('Y-m-t', strtotime('last month'));
                    break;
                case 'custom':
                    $startDate = $_GET['start_date'] ?? $today;
                    $endDate = $_GET['end_date'] ?? $today;
                    break;
                default:
                    $startDate = $today;
                    $endDate = $today;
            }
            
            if ($accountType === 'savings') {
                $account = $this->saving->getSavingsAccount($memberId);
                if (!$account) {
                    throw new \Exception('Akaun simpanan tidak dijumpai');
                }
                $accountId = isset($_GET['account_id']) ? $_GET['account_id'] : $account['id'];
                $transactions = $this->saving->getTransactionsByDateRange($accountId, $startDate, $endDate);
                
                // Calculate opening balance
                $runningBalance = $account['current_amount'] ?? 0;
                foreach ($transactions as $t) {
                    $isCredit = in_array($t['type'], ['deposit', 'transfer_in']);
                    $runningBalance -= ($isCredit ? $t['amount'] : -$t['amount']);
                }
                $account['opening_balance'] = $runningBalance;
                
            } else {
                $loanId = $_GET['loan_id'] ?? null;
                if (!$loanId) {
                    throw new \Exception('ID pembiayaan tidak ditemui');
                }
                
                $account = $this->loan->getLoanById($loanId);
                if (!$account || $account['member_id'] != $memberId) {
                    throw new \Exception('Akaun pembiayaan tidak dijumpai');
                }
                
                $transactions = $this->loan->getTransactionsByDateRange($loanId, $startDate, $endDate);
            }

            // Generate PDF statement
            $this->generatePDF($account, $transactions, $startDate, $endDate, $accountType);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    private function generatePDF($account, $transactions, $startDate, $endDate, $accountType)
    {
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('KADA System');
        $pdf->SetAuthor('Koperasi KADA');
        $pdf->SetTitle('Penyata Akaun - ' . ($accountType === 'savings' ? 'Simpanan' : 'Pembiayaan'));

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $logoPath = dirname(dirname(__DIR__)) . '/public/img/logo-kada.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 10, 40);
        }

        // Add header
        $pdf->SetY(10);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'KOPERASI KAKITANGAN KADA KELANTAN', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'D/A Lembaga Kemajuan Pertanian Kemubu', 0, 1, 'C');
        $pdf->Cell(0, 6, 'P/S 127, 15710 Kota Bharu, Kelantan', 0, 1, 'C');
        $pdf->Cell(0, 6, 'Tel: 09-7447088', 0, 1, 'C');

        // Add statement title
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'PENYATA ' . ($accountType === 'savings' ? 'SIMPANAN' : 'PEMBIAYAAN'), 0, 1, 'C');

        // Add account information
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(40, 6, 'No. Akaun:', 0);
        $pdf->Cell(0, 6, $accountType === 'savings' ? $account['account_number'] : $account['reference_no'], 0, 1);
        $pdf->Cell(40, 6, 'Tempoh:', 0);
        $pdf->Cell(0, 6, date('d/m/Y', strtotime($startDate)) . ' hingga ' . date('d/m/Y', strtotime($endDate)), 0, 1);

        // Add transaction table
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);

        // Table header
        if ($accountType === 'savings') {
            $header = ['Tarikh', 'Penerangan', 'Debit (RM)', 'Kredit (RM)', 'Baki (RM)'];
            $widths = [30, 70, 30, 30, 30];
        } else {
            $header = ['Tarikh', 'Penerangan', 'Bayaran (RM)', 'Baki Pinjaman (RM)', 'Baki (RM)'];
            $widths = [30, 70, 30, 30, 30];
        }

        // Draw header
        $pdf->SetFillColor(240, 240, 240);
        for($i = 0; $i < count($header); $i++) {
            $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Table data
        $pdf->SetFont('helvetica', '', 9);
        
        // Initialize balance with opening balance for savings
        if ($accountType === 'savings') {
            $balance = $account['opening_balance'];
        } else {
            $balance = $account['current_amount'] ?? 0;
        }

        // Sort transactions by date
        usort($transactions, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        foreach ($transactions as $trans) {
            if ($accountType === 'savings') {
                $isDebit = in_array($trans['type'], ['transfer_out', 'withdrawal']);
                $isCredit = in_array($trans['type'], ['deposit', 'transfer_in']);
                $balance += ($isCredit ? $trans['amount'] : -$trans['amount']);

                $pdf->Cell($widths[0], 6, date('d/m/Y', strtotime($trans['created_at'])), 1);
                $pdf->Cell($widths[1], 6, $trans['description'], 1);
                $pdf->Cell($widths[2], 6, $isDebit ? number_format($trans['amount'], 2) : '-', 1, 0, 'R');
                $pdf->Cell($widths[3], 6, $isCredit ? number_format($trans['amount'], 2) : '-', 1, 0, 'R');
                $pdf->Cell($widths[4], 6, number_format($balance, 2), 1, 0, 'R');
            } else {
                $balance = $trans['remaining_balance'];
                $pdf->Cell($widths[0], 6, date('d/m/Y', strtotime($trans['created_at'])), 1);
                $pdf->Cell($widths[1], 6, $trans['description'], 1);
                $pdf->Cell($widths[2], 6, number_format($trans['payment_amount'], 2), 1, 0, 'R');
                $pdf->Cell($widths[3], 6, number_format($trans['remaining_balance'], 2), 1, 0, 'R');
                $pdf->Cell($widths[4], 6, number_format($balance, 2), 1, 0, 'R');
            }
            $pdf->Ln();
        }

        // Add footer
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 6, 'Dokumen ini dijana secara automatik. Tandatangan tidak diperlukan.', 0, 1, 'C');
        $pdf->Cell(0, 6, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

        // Output PDF
        $filename = 'penyata_' . ($accountType === 'savings' ? 'simpanan' : 'pembiayaan') . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}