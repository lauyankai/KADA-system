<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Statement;
use App\Models\User;
use PDO;

class StatementController extends BaseController
{
    private $saving;
    private $loan;
    private $statement;
    private $user;
    
    public function __construct()
    {
        $this->saving = new Saving();
        $this->loan = new Loan();
        $this->statement = new Statement();
        $this->user = new User();
    }

    public function index()
    {
        try {
            // Get member ID from session
            $memberId = $_SESSION['member_id'] ?? null;
            if (!$memberId) {
                throw new \Exception('Sesi telah tamat');
            }

            // Get member details including email
            $member = $this->user->getMemberById($memberId);
            if (!$member) {
                throw new \Exception('Maklumat ahli tidak dijumpai');
            }

            // Get savings account
            $account = $this->saving->getSavingsAccount($memberId);
            
            // Get active loans with remaining amount
            $loans = $this->loan->getActiveLoansByMemberId($memberId);
            
            // Calculate remaining amount for each loan
            if ($loans) {
                foreach ($loans as &$loan) {
                    $totalPaid = $this->loan->getTotalPaidAmount($loan['id']);
                    $loan['remaining_amount'] = $loan['amount'] - $totalPaid;
                }
            }
            
            // Get notification preferences using Statement model
            $notifications = $this->statement->getNotificationSettings($memberId);
            
            $this->view('users/statement/index', [
                'member' => $member,
                'account' => $account,
                'loans' => $loans,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }

    public function savings()
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

            // Get savings account
            $account = $this->saving->getSavingsAccount($memberId);
            if (!$account) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }
            
            // Get transactions for savings account
            $transactions = $this->saving->getTransactionsByDateRange(
                $account['id'], 
                $startDate, 
                $endDate
            );
            
            // Calculate opening balance for savings account
            $currentBalance = $account['current_amount'] ?? 0;
            $openingBalance = $currentBalance;
            
            foreach ($transactions as $trans) {
                if (in_array($trans['type'], ['deposit', 'transfer_in'])) {
                    $openingBalance -= $trans['amount'];
            } else {
                    $openingBalance += $trans['amount'];
                }
            }

            $this->view('users/statement/savings', [
                'accountType' => $accountType,
                'account' => $account,
                'transactions' => $transactions,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'period' => $period,
                'year' => $year,
                'openingBalance' => $openingBalance
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users');
            exit;
        }
    }

    public function loans()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get loan ID from URL parameter
            $loanId = $_GET['id'] ?? null;
            if (!$loanId) {
                throw new \Exception('ID pembiayaan tidak ditemui');
            }

            // Get specific loan details
            $loans = $this->loan->getActiveLoansByMemberId($memberId);
            
            // Find the requested loan
            $selectedLoan = null;
            foreach ($loans as $loan) {
                if ($loan['id'] == $loanId) {
                    $selectedLoan = $loan;
                    break;
                }
            }

            if (!$selectedLoan) {
                throw new \Exception('Pembiayaan tidak dijumpai');
            }

            // Get loan transactions
            $transactions = $this->loan->getTransactionsByDateRange(
                $loanId,
                date('Y-m-01'), // First day of current month
                date('Y-m-d')  // Today
            );

            $this->view('users/statement/loans', [
                'loans' => [$selectedLoan], // Pass as array with single loan
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            error_log('Error in loan statement: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
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
            $loanId = $_GET['loan_id'] ?? null;
            $period = $_GET['period'] ?? date('Y-m');
            $year = $_GET['year'] ?? date('Y');

            // Handle loan statement download
            if ($loanId) {
                // Get loan details
                $loan = null;
                $loans = $this->loan->getActiveLoansByMemberId($memberId);
                foreach ($loans as $l) {
                    if ($l['id'] == $loanId) {
                        $loan = $l;
                        break;
                    }
                }

                if (!$loan) {
                    throw new \Exception('Pembiayaan tidak dijumpai');
                }

                // Get transactions for the specified period
                $startDate = date('Y-m-01', strtotime($period));
                $endDate = date('Y-m-t', strtotime($period));
                
                $transactions = $this->loan->getTransactionsByDateRange(
                    $loanId,
                    $startDate,
                    $endDate
                );

            // Generate PDF
                $pdf = $this->generateLoanReport($loan, $transactions, $period);
                
                // Output PDF
                $filename = 'penyata_pembiayaan_' . $loan['reference_no'] . '_' . $period . '.pdf';
                $pdf->Output($filename, 'D');
                exit;
            }
            
            // Handle savings statement download
            else {
                $account = $this->saving->getSavingsAccount($memberId);
                if (!$account) {
                    throw new \Exception('Akaun simpanan tidak dijumpai');
                }

                // Calculate dates based on period
                $dateRange = $this->calculateDateRange($period, $year);
                $startDate = $dateRange['start'];
                $endDate = $dateRange['end'];

                $transactions = $this->saving->getTransactionsByDateRange(
                    $account['id'],
                    $startDate,
                    $endDate
                );

                // Make sure TCPDF is available
                if (!class_exists('TCPDF')) {
                    require_once dirname(dirname(__DIR__)) . '/vendor/tecnickcom/tcpdf/tcpdf.php';
                }

                // Generate PDF
                $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // Set PDF metadata
                $pdf->SetCreator('KADA System');
                $pdf->SetAuthor('Koperasi KADA');
                $pdf->SetTitle('Penyata Simpanan');

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
                $pdf->SetX(60);
                $pdf->SetFont('helvetica', 'B', 16);
                $pdf->Cell(135, 10, 'KOPERASI KAKITANGAN KADA KELANTAN', 0, 1, 'C');
                $pdf->SetX(60);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(135, 6, 'D/A Lembaga Kemajuan Pertanian Kemubu', 0, 1, 'C');
                $pdf->SetX(60);
                $pdf->Cell(135, 6, 'P/S 127, 15710 Kota Bharu, Kelantan', 0, 1, 'C');
                $pdf->SetX(60);
                $pdf->Cell(135, 6, 'Tel: 09-7447088', 0, 1, 'C');

                // Add line after header
                $pdf->SetY($pdf->GetY() + 3);
                $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                $pdf->Ln(10);

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

                // Footer
                $pdf->Ln(10);
                $pdf->SetFont('helvetica', 'I', 8);
                $pdf->Cell(0, 6, 'Dokumen ini dijana secara automatik. Tandatangan tidak diperlukan.', 0, 1, 'C');
                $pdf->Cell(0, 6, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

                // Output PDF
                $filename = 'penyata_simpanan_' . $account['account_number'] . '_' . $period . '.pdf';
                $pdf->Output($filename, 'D');
                exit;
            }

        } catch (\Exception $e) {
            error_log('PDF Generation Error: ' . $e->getMessage());
            $_SESSION['error'] = 'Ralat menjana PDF: ' . $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    public function notifications()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            $memberId = $_SESSION['member_id'];
            $emailEnabled = isset($_POST['email_notification']);
            $email = $_POST['email'] ?? null;

            // Get current notification settings
            $currentSettings = $this->statement->getNotificationSettings($memberId);

            // Validate email if notification is enabled
            if ($emailEnabled) {
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Alamat emel tidak sah');
                }

                // Check if settings have changed
                if ($currentSettings['email_enabled'] != $emailEnabled || $currentSettings['email'] !== $email) {
                    $this->statement->updateNotificationSettings($memberId, $emailEnabled, $email);
                    $_SESSION['success'] = 'Tetapan notifikasi berjaya dikemaskini';
                }
            } else {
                // If notifications are being disabled
                if ($currentSettings['email_enabled']) {
                    $this->statement->updateNotificationSettings($memberId, false, null);
                    $_SESSION['success'] = 'Tetapan notifikasi berjaya dikemaskini';
                }
            }
            
            header('Location: /users/statements');
            exit;

        } catch (\Exception $e) {
            error_log('Error updating notifications: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
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

    private function generateLoanReport($loan, $transactions, $period)
    {
        try {
            require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('KADA');
            $pdf->SetAuthor('KADA');
            $pdf->SetTitle('Penyata Pembiayaan - ' . $loan['reference_no']);

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
            $pdf->SetX(60); 
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(135, 10, 'KOPERASI KAKITANGAN KADA KELANTAN', 0, 1, 'C');
            $pdf->SetX(60);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(135, 6, 'D/A Lembaga Kemajuan Pertanian Kemubu', 0, 1, 'C');
            $pdf->SetX(60);
            $pdf->Cell(135, 6, 'P/S 127, 15710 Kota Bharu, Kelantan', 0, 1, 'C');
            $pdf->SetX(60);
            $pdf->Cell(135, 6, 'Tel: 09-7447088', 0, 1, 'C');

            // Add line
            $pdf->SetY($pdf->GetY() + 3);
            $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
            $pdf->Ln(10);

            // Statement Period
            $startDate = date('01/m/Y', strtotime($period));
            $endDate = date('t/m/Y', strtotime($period));
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 8, 'PENYATA PEMBIAYAAN: ' . $startDate . ' - ' . $endDate, 0, 1, 'C');
            $pdf->Ln(5);

            // Loan Details
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 8, 'MAKLUMAT PEMBIAYAAN', 0, 1, 'L');
            
            // Create a table for loan details
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFillColor(245, 245, 245);
            
            // Loan details in table format
            $w = array(60, 130);
            $pdf->Cell($w[0], 7, 'No. Rujukan', 1, 0, 'L', true);
            $pdf->Cell($w[1], 7, $loan['reference_no'], 1, 1, 'L');
            
            $pdf->Cell($w[0], 7, 'Jenis Pembiayaan', 1, 0, 'L', true);
            $pdf->Cell($w[1], 7, $loan['loan_type'], 1, 1, 'L');
            
            $pdf->Cell($w[0], 7, 'Jumlah Pembiayaan', 1, 0, 'L', true);
            $pdf->Cell($w[1], 7, 'RM ' . number_format($loan['amount'], 2), 1, 1, 'L');
            
            $pdf->Cell($w[0], 7, 'Tempoh', 1, 0, 'L', true);
            $pdf->Cell($w[1], 7, $loan['duration'] . ' bulan', 1, 1, 'L');
            
            $pdf->Cell($w[0], 7, 'Baki Pembiayaan', 1, 0, 'L', true);
            $pdf->Cell($w[1], 7, 'RM ' . number_format($loan['remaining_amount'], 2), 1, 1, 'L');
            
            $pdf->Ln(5);

            // Transactions Table
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 8, 'TRANSAKSI PEMBIAYAAN', 0, 1, 'L');
            
            // Table header
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(245, 245, 245);
            
            // Column widths
            $w = array(30, 80, 35, 35);
            
            $pdf->Cell($w[0], 7, 'Tarikh', 1, 0, 'C', true);
            $pdf->Cell($w[1], 7, 'Penerangan', 1, 0, 'C', true);
            $pdf->Cell($w[2], 7, 'Bayaran (RM)', 1, 0, 'C', true);
            $pdf->Cell($w[3], 7, 'Baki (RM)', 1, 1, 'C', true);

            // Table rows
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(255, 255, 255);

            if (empty($transactions)) {
                $pdf->Cell(array_sum($w), 7, 'Tiada transaksi untuk tempoh ini', 1, 1, 'C');
            } else {
                foreach ($transactions as $trans) {
                    $pdf->Cell($w[0], 7, date('d/m/Y', strtotime($trans['created_at'])), 1, 0, 'C');
                    $pdf->Cell($w[1], 7, $trans['description'], 1, 0, 'L');
                    $pdf->Cell($w[2], 7, number_format($trans['payment_amount'], 2), 1, 0, 'R');
                    $pdf->Cell($w[3], 7, number_format($trans['remaining_balance'], 2), 1, 1, 'R');
                }
            }

            // Footer
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 6, 'Dokumen ini dijana secara automatik. Tandatangan tidak diperlukan.', 0, 1, 'C');
            $pdf->Cell(0, 6, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

            return $pdf;

        } catch (\Exception $e) {
            error_log('PDF Generation Error: ' . $e->getMessage());
            throw new \Exception('Ralat menjana PDF: ' . $e->getMessage());
        }
    }
}