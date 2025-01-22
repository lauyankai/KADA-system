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
            $period = $_GET['period'] ?? 'today';
            $year = $_GET['year'] ?? date('Y');

            // Calculate dates based on period
            $dateRange = $this->calculateDateRange($period, $year);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

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
                
                // Calculate opening balance for savings account
                $currentBalance = $account['current_amount'] ?? 0;
                $openingBalance = $currentBalance;
                
                // Subtract all transaction amounts to get to opening balance
                foreach ($transactions as $trans) {
                    if (in_array($trans['type'], ['deposit', 'transfer_in'])) {
                        $openingBalance -= $trans['amount'];
                    } else {
                        $openingBalance += $trans['amount'];
                    }
                }
                
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
                
                // For loan accounts, opening balance is the first transaction's balance
                $openingBalance = !empty($transactions) ? 
                    $transactions[0]['remaining_balance'] + $transactions[0]['payment_amount'] : 
                    $account['loan_amount'];
            }

            $this->view('users/statement/index', [
                'accountType' => $accountType,
                'account' => $account,
                'accounts' => $accounts,
                'transactions' => $transactions,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'period' => $period,
                'year' => $year,
                'openingBalance' => $openingBalance // Pass the opening balance to the view
            ]);

        } catch (\Exception $e) {
            error_log('Error in statement index: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users');
            exit;
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
            $year = $_GET['year'] ?? date('Y');

            // Get date range
            $dateRange = $this->calculateDateRange($period, $year);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            // Get account and transactions
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
            } else {
                $loanId = $_GET['loan_id'] ?? null;
                if (!$loanId) {
                    throw new \Exception('ID pembiayaan tidak ditemui');
                }
                $account = $this->loan->getLoanById($loanId);
                if (!$account || $account['member_id'] !== $memberId) {
                    throw new \Exception('Akaun pembiayaan tidak dijumpai');
                }
                $transactions = $this->loan->getTransactionsByDateRange(
                    $loanId,
                    $startDate,
                    $endDate
                );
            }

            // Make sure TCPDF is available
            if (!class_exists('TCPDF')) {
                require_once dirname(dirname(__DIR__)) . '/vendor/tecnickcom/tcpdf/tcpdf.php';
            }

            // Generate PDF
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set PDF metadata
            $pdf->SetCreator('KADA System');
            $pdf->SetAuthor('Koperasi KADA');
            $pdf->SetTitle('Penyata ' . ($accountType === 'savings' ? 'Simpanan' : 'Pembiayaan'));

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Add a page
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

            // Calculate opening balance
            $runningBalance = $account['current_amount'] ?? 0;
            foreach ($transactions as $t) {
                if ($accountType === 'savings') {
                    $isCredit = in_array($t['type'], ['deposit', 'transfer_in']);
                    $runningBalance -= ($isCredit ? $t['amount'] : -$t['amount']);
                }
            }
            $openingBalance = $runningBalance;

            // Add opening balance row
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell($widths[0], 6, '-', 1, 0, 'C', true);
            $pdf->Cell($widths[1], 6, 'Baki Awal', 1, 0, 'L', true);
            $pdf->Cell($widths[2], 6, '-', 1, 0, 'R', true);
            $pdf->Cell($widths[3], 6, '-', 1, 0, 'R', true);
            $pdf->Cell($widths[4], 6, number_format($openingBalance, 2), 1, 0, 'R', true);
            $pdf->Ln();

            // Table data
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(255, 255, 255);

            // Sort transactions by date
            usort($transactions, function($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });

            foreach ($transactions as $trans) {
                if ($accountType === 'savings') {
                    $isDebit = in_array($trans['type'], ['transfer_out', 'withdrawal']);
                    $isCredit = in_array($trans['type'], ['deposit', 'transfer_in']);
                    $runningBalance += ($isCredit ? $trans['amount'] : -$trans['amount']);

                    $pdf->Cell($widths[0], 6, date('d/m/Y', strtotime($trans['created_at'])), 1);
                    $pdf->Cell($widths[1], 6, $trans['description'], 1);
                    $pdf->Cell($widths[2], 6, $isDebit ? number_format($trans['amount'], 2) : '-', 1, 0, 'R');
                    $pdf->Cell($widths[3], 6, $isCredit ? number_format($trans['amount'], 2) : '-', 1, 0, 'R');
                    $pdf->Cell($widths[4], 6, number_format($runningBalance, 2), 1, 0, 'R');
                } else {
                    $runningBalance = $trans['remaining_balance'];
                    $pdf->Cell($widths[0], 6, date('d/m/Y', strtotime($trans['created_at'])), 1);
                    $pdf->Cell($widths[1], 6, $trans['description'], 1);
                    $pdf->Cell($widths[2], 6, number_format($trans['payment_amount'], 2), 1, 0, 'R');
                    $pdf->Cell($widths[3], 6, number_format($trans['remaining_balance'], 2), 1, 0, 'R');
                    $pdf->Cell($widths[4], 6, number_format($runningBalance, 2), 1, 0, 'R');
                }
                $pdf->Ln();
            }

            // Add closing balance row
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell($widths[0], 6, '-', 1, 0, 'C', true);
            $pdf->Cell($widths[1], 6, 'Baki Akhir', 1, 0, 'L', true);
            $pdf->Cell($widths[2], 6, '-', 1, 0, 'R', true);
            $pdf->Cell($widths[3], 6, '-', 1, 0, 'R', true);
            $pdf->Cell($widths[4], 6, number_format($runningBalance, 2), 1, 0, 'R', true);
            $pdf->Ln();

            // Add footer
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 6, 'Dokumen ini dijana secara automatik. Tandatangan tidak diperlukan.', 0, 1, 'C');
            $pdf->Cell(0, 6, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

            // Output the PDF
            $filename = 'penyata_' . ($accountType === 'savings' ? 'simpanan' : 'pembiayaan') . '_' . date('Ymd') . '.pdf';
            $pdf->Output($filename, 'D');
            exit;

        } catch (\Exception $e) {
            error_log('PDF Generation Error: ' . $e->getMessage());
            $_SESSION['error'] = 'Ralat menjana PDF: ' . $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    private function calculateDateRange($period, $year)
    {
        $today = date('Y-m-d');
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $today,
                    'end' => $today
                ];
            
            case 'current':
                return [
                    'start' => date('Y-m-01'), // First day of current month
                    'end' => $today
                ];
            
            case 'last':
                return [
                    'start' => date('Y-m-01', strtotime('last month')),
                    'end' => date('Y-m-t', strtotime('last month')) // Last day of last month
                ];
            
            case 'yearly':
                return [
                    'start' => "$year-01-01", // First day of the selected year
                    'end' => "$year-12-31" // Last day of the selected year
                ];
            
            case 'custom':
                return [
                    'start' => $_GET['start_date'] ?? $today,
                    'end' => $_GET['end_date'] ?? $today
                ];
            
            default:
                return [
                    'start' => $today,
                    'end' => $today
                ];
        }
    }
}