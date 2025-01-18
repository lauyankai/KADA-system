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
            $account = $this->saving->getSavingsAccount($memberId);
            
            if (!$account) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }

            $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
            $endDate = $_GET['end_date'] ?? date('Y-m-d');

            $transactions = $this->saving->getTransactionsByDateRange(
                $account['id'], 
                $startDate, 
                $endDate
            );

            // Generate PDF statement
            $this->generatePDF($account, $transactions, $startDate, $endDate);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    private function generatePDF($account, $transactions, $startDate, $endDate)
    {
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Set document information
        $pdf->SetCreator('KADA System');
        $pdf->SetAuthor('Koperasi KADA');
        $pdf->SetTitle('Penyata Akaun');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Add content
        $html = $this->generateStatementHTML($account, $transactions, $startDate, $endDate);
        
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('penyata_' . date('Ymd') . '.pdf', 'D');
        exit;
    }

    private function generateStatementHTML($account, $transactions, $startDate, $endDate)
    {
        // Generate HTML content for PDF
        $html = '<h1>Penyata Akaun</h1>';
        $html .= '<p>No. Akaun: ' . $account['account_number'] . '</p>';
        $html .= '<p>Tempoh: ' . date('d/m/Y', strtotime($startDate)) . 
                 ' hingga ' . date('d/m/Y', strtotime($endDate)) . '</p>';
        
        // Add transaction table
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr><th>Tarikh</th><th>Penerangan</th><th>Debit</th><th>Kredit</th><th>Baki</th></tr>';
        
        $balance = $account['opening_balance'] ?? 0;
        foreach ($transactions as $trans) {
            $balance += ($trans['type'] === 'credit' ? $trans['amount'] : -$trans['amount']);
            $html .= '<tr>';
            $html .= '<td>' . date('d/m/Y', strtotime($trans['created_at'])) . '</td>';
            $html .= '<td>' . $trans['description'] . '</td>';
            $html .= '<td>' . ($trans['type'] === 'debit' ? number_format($trans['amount'], 2) : '') . '</td>';
            $html .= '<td>' . ($trans['type'] === 'credit' ? number_format($trans['amount'], 2) : '') . '</td>';
            $html .= '<td>' . number_format($balance, 2) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        return $html;
    }
}