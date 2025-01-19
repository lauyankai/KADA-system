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
            
            // Get period from query parameters
            $period = $_GET['period'] ?? 'today';
            
            // Calculate dates based on period
            $dates = $this->calculateDates($period);
            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            // Get savings account
            $savingsAccount = $this->saving->getSavingsAccount($memberId);
            if (!$savingsAccount) {
                throw new \Exception('Akaun simpanan tidak ditemui');
            }

            // Get all loan accounts
            $loanAccounts = $this->loan->getLoansByMemberId($memberId);

            // Determine selected account and type
            $selectedAccount = $_GET['account_id'] ?? 'S' . $savingsAccount['id'];
            $accountType = substr($selectedAccount, 0, 1) === 'S' ? 'savings' : 'loan';
            $accountId = substr($selectedAccount, 1);

            // Get account details and transactions based on type
            if ($accountType === 'savings') {
                $account = $savingsAccount;
                $transactions = $this->saving->getTransactionsByDateRange($accountId, $startDate, $endDate);
            } else {
                $account = null;
                foreach ($loanAccounts as $loan) {
                    if ($loan['id'] == $accountId) {
                        $account = $loan;
                        break;
                    }
                }
                if (!$account) {
                    throw new \Exception('Akaun pembiayaan tidak ditemui');
                }
                $transactions = $this->loan->getTransactionsByDateRange($accountId, $startDate, $endDate);
            }

            $this->view('users/statement/index', [
                'accountType' => $accountType,
                'account' => $account,
                'savingsAccount' => $savingsAccount,
                'loanAccounts' => $loanAccounts,
                'transactions' => $transactions,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
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

        // Output PDF
        $filename = 'penyata_' . ($accountType === 'savings' ? 'simpanan' : 'pembiayaan') . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    public function generateLoanReport($loanId, $startDate, $endDate)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            $loan = $this->loan->getLoanById($loanId);

            if (!$loan || $loan['member_id'] != $memberId) {
                throw new \Exception('Maklumat pembiayaan tidak dijumpai');
            }

            // Create PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('KADA System');
            $pdf->SetAuthor('KADA Kelantan');
            $pdf->SetTitle('Penyata Pembiayaan');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('helvetica', '', 10);

            // Add letterhead
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Platform Digital Koperasi KADA Kelantan', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 6, 'D/A Lembaga Kemajuan Pertanian Kemubu,', 0, 1, 'C');
            $pdf->Cell(0, 6, 'P/S 127, 15710 Kota Bharu, Kelantan', 0, 1, 'C');
            $pdf->Ln(10);

            // Report title
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'PENYATA PEMBIAYAAN', 0, 1, 'C');
            $pdf->Ln(5);

            // Loan details
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(40, 6, 'No. Rujukan:', 0);
            $pdf->Cell(0, 6, $loan['reference_no'], 0, 1);
            $pdf->Cell(40, 6, 'Jenis Pembiayaan:', 0);
            $pdf->Cell(0, 6, ucfirst($loan['loan_type']), 0, 1);
            $pdf->Cell(40, 6, 'Jumlah Pembiayaan:', 0);
            $pdf->Cell(0, 6, 'RM ' . number_format($loan['amount'], 2), 0, 1);
            $pdf->Cell(40, 6, 'Bayaran Bulanan:', 0);
            $pdf->Cell(0, 6, 'RM ' . number_format($loan['monthly_payment'], 2), 0, 1);
            $pdf->Cell(40, 6, 'Tempoh:', 0);
            $pdf->Cell(0, 6, $loan['duration'] . ' bulan', 0, 1);
            $pdf->Cell(40, 6, 'Status:', 0);
            $pdf->Cell(0, 6, ucfirst($loan['status']), 0, 1);
            $pdf->Cell(40, 6, 'Bank:', 0);
            $pdf->Cell(0, 6, $loan['bank_name'], 0, 1);
            $pdf->Cell(40, 6, 'No. Akaun Bank:', 0);
            $pdf->Cell(0, 6, $loan['bank_account'], 0, 1);
            $pdf->Cell(40, 6, 'Tarikh Mohon:', 0);
            $pdf->Cell(0, 6, date('d/m/Y', strtotime($loan['created_at'])), 0, 1);
            if ($loan['date_received']) {
                $pdf->Cell(40, 6, 'Tarikh Terima:', 0);
                $pdf->Cell(0, 6, date('d/m/Y', strtotime($loan['date_received'])), 0, 1);
            }
            $pdf->Ln(5);

            // Table header
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(30, 7, 'Tarikh', 1, 0, 'C');
            $pdf->Cell(70, 7, 'Status Permohonan', 1, 0, 'C');
            $pdf->Cell(45, 7, 'Jumlah (RM)', 1, 0, 'C');
            $pdf->Cell(45, 7, 'Status', 1, 0, 'C');
            $pdf->Ln();

            // Table data
            $pdf->SetFont('helvetica', '', 9);
            
            // Application submitted
            $pdf->Cell(30, 6, date('d/m/Y', strtotime($loan['created_at'])), 1);
            $pdf->Cell(70, 6, 'Permohonan Dihantar', 1);
            $pdf->Cell(45, 6, number_format($loan['amount'], 2), 1, 0, 'R');
            $pdf->Cell(45, 6, 'Dalam Proses', 1, 0, 'C');
            $pdf->Ln();

            // If loan is approved/received
            if ($loan['date_received']) {
                $pdf->Cell(30, 6, date('d/m/Y', strtotime($loan['date_received'])), 1);
                $pdf->Cell(70, 6, 'Permohonan Diluluskan', 1);
                $pdf->Cell(45, 6, number_format($loan['amount'], 2), 1, 0, 'R');
                $pdf->Cell(45, 6, 'Diluluskan', 1, 0, 'C');
                $pdf->Ln();
            }

            // Footer
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 6, 'Dokumen ini dijana secara automatik. Tandatangan tidak diperlukan.', 0, 1, 'C');
            $pdf->Cell(0, 6, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

            // Output PDF
            $pdf->Output('penyata_pembiayaan_' . $loan['reference_no'] . '.pdf', 'D');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    private function calculateDates($period)
    {
        $today = date('Y-m-d');
        $startDate = $today;
        $endDate = $today;

        switch ($period) {
            case 'today':
                // Already set to today
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
                // Default to today if invalid period
                break;
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
}