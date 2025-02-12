<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use App\Models\Admin;
use PDO;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\AnnualReport;

class AdminController extends BaseController {
    private $admin;
    private $annualReport;

    public function __construct()
    {
        $this->admin = new Admin();
        $this->annualReport = new AnnualReport();
    }

    public function index()
    {
        try {
            if (!isset($_SESSION['admin_id'])) {
                error_log('No admin_id in session');
                throw new \Exception('Sila log masuk sebagai admin');
            }

            $admin = new Admin();
            $allMembers = $admin->getAllMembers();
            $reports = $this->annualReport->getAllReports();
            
            $this->view('admin/index', [
                'members' => $allMembers,
                'annual_reports' => $reports,
                'stats' => [
                    'total' => count($allMembers),
                    'pending' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Pending')),
                    'active' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Ahli')),
                    'rejected' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Rejected'))
                ]
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = "Error fetching data: " . $e->getMessage();
            $this->view('admin/index', [
                'members' => [], 
                'annual_reports' => [],
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'active' => 0,
                    'rejected' => 0
                ]
            ]);
        }
    }

    public function viewMember($id)
    {
        $admin = new Admin();
        $member = $admin->getUserById($id);
        $this->view('admin/view', ['member' => $member]);
    }

    private function getMemberAccountDetails($id)
    {
        try {
            $sql = "SELECT * FROM savings_accounts WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting account details: ' . $e->getMessage());
            return [];
        }
    }

    private function getMemberSavingsInfo($id)
    {
        try {
            $sql = "SELECT 
                    SUM(current_amount) as total_savings,
                    COUNT(*) as account_count
                    FROM savings_accounts 
                    WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting savings info: ' . $e->getMessage());
            return null;
        }
    }

    private function getMemberLoanInfo($id)
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_loans,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_loans
                    FROM loans 
                    WHERE member_id = :id";
            $stmt = $this->admin->getConnection()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Error getting loan info: ' . $e->getMessage());
            return null;
        }
    }

    public function approve($id)
    {
        try {
            $admin = new Admin();
            $member = $admin->getMemberById($id);

            if ($member['member_type'] === 'Rejected') {
                if ($admin->migrateFromRejected($id)) {
                    $_SESSION['success'] = "Ahli telah berjaya dipindahkan ke senarai ahli aktif";
                } else {
                    throw new \Exception("Gagal memindahkan ahli");
                }
            } else {
                $admin->updateStatus($id, 'Lulus');
                $_SESSION['success'] = "Status telah berjaya dikemaskini kepada Lulus";
            }
            
            header('Location: /admin');
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /admin');
            exit();
        }
    }

    public function reject($id)
    {
        try {
            $admin = new Admin();
            if ($admin->reject($id)) {
                $_SESSION['success'] = "Permohonan telah berjaya ditolak dan dipindahkan ke senarai rejected";
            } else {
                throw new \Exception("Gagal menolak permohonan");
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /admin');
        exit();
    }

    public function rejectMember($id)
    {
        try {
            $admin = new Admin();
            $admin->updateStatus($id, 'rejected');
            $_SESSION['success'] = "Permohonan telah berjaya ditolak dan dipindahkan ke senarai rejected";
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /admin');
        exit();
    }

    // public function edit($id)
    // {
    //     $admin = $this->admin->find($id);

    //     $this->view('admin/edit', compact('admin'));
    // }

    // public function update($id)
    // {
    //     $this->user->update($id, $_POST);
    //     header('Location: /');
    // }

    // private function checkAuth()
    // {
    //     if (!isset($_SESSION['admin_id'])) {
    //         header('Location: /auth/login');
    //         exit();
    //     }
    // }

    public function exportPdf()
    {
        // Use the correct path to TCPDF
        require_once dirname(dirname(__DIR__)) . '/vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('KADA System');
        $pdf->SetAuthor('KADA System');
        $pdf->SetTitle('Senarai Ahli');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Get member data
        $members = $this->admin->getAllMembers();
        
        // Create the HTML content
        $html = '<h1>Senarai Ahli</h1>';
        $html .= '<table border="1" cellpadding="4">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>No. K/P</th>
                    <th>Jantina</th>
                    <th>Jawatan</th>
                    <th>Gaji</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
        
        $counter = 1;
        foreach ($members as $member) {
            $html .= '<tr>
                <td>' . $counter++ . '</td>
                <td>' . htmlspecialchars($member['name']) . '</td>
                <td>' . htmlspecialchars($member['ic_no']) . '</td>
                <td>' . htmlspecialchars($member['gender']) . '</td>
                <td>' . htmlspecialchars($member['position']) . '</td>
                <td>RM ' . number_format($member['monthly_salary'], 2) . '</td>
                <td>' . htmlspecialchars($member['member_type']) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        
        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        $pdf->Output('senarai_ahli.pdf', 'D');
        exit();
    }

    public function exportExcel()
    {
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'No. K/P');
        $sheet->setCellValue('D1', 'Jantina');
        $sheet->setCellValue('E1', 'Jawatan');
        $sheet->setCellValue('F1', 'Gaji');
        $sheet->setCellValue('G1', 'Status');
        
        // Get data
        $members = $this->admin->getAllMembers();
        
        // Fill data
        $row = 2;
        foreach ($members as $index => $member) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $member['name']);
            $sheet->setCellValue('C' . $row, $member['ic_no']);
            $sheet->setCellValue('D' . $row, $member['gender']);
            $sheet->setCellValue('E' . $row, $member['position']);
            $sheet->setCellValue('F' . $row, 'RM ' . number_format($member['monthly_salary'], 2));
            $sheet->setCellValue('G' . $row, $member['member_type']);
            $row++;
        }
        
        // Auto size columns
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="senarai_ahli.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function updateStatus()
    {
        try {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            
            if (!$id || !$status) {
                throw new \Exception("ID and status are required");
            }

            $result = $this->admin->updateStatus($id, $status);
            if ($result) {
                $_SESSION['success'] = "Status telah berjaya dikemaskini";
            }
            
            header('Location: /admin');
            exit;
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Gagal mengemaskini status: " . $e->getMessage();
            header('Location: /admin');
            exit;
        }
    }

    public function memberList() {
        try {
            $admin = new Admin();
            $allMembers = $admin->getAllMembers();
            

            $this->view('admin/member_list', [
                'members' => $allMembers,
                'stats' => [
                    'total' => count($allMembers),

                    'pending' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Pending')),
                    'active' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Ahli')),
                    'rejected' => count(array_filter($allMembers, fn($m) => $m['member_type'] === 'Rejected'))
                ]
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = "Error fetching members: " . $e->getMessage();
            $this->view('admin/member_list', ['members' => [], 'stats' => [
                'total' => 0,
                'pending' => 0,
                'active' => 0,
                'rejected' => 0
            ]]);
        }
    }

    public function uploadReport()
    {
        try {
            if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Sila pilih fail untuk dimuat naik');
            }

            $file = $_FILES['report_file'];
            $year = $_POST['year'] ?? '';
            $title = $_POST['title'] ?? '';

            if (empty($year) || empty($title)) {
                throw new \Exception('Sila isi semua maklumat yang diperlukan');
            }

            $allowedTypes = ['application/pdf'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Hanya fail PDF diterima');
            }

            $maxSize = 100 * 1024 * 1024; 
            if ($file['size'] > $maxSize) {
                throw new \Exception('Saiz fail tidak boleh melebihi 10MB');
            }

            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/annual-reports/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new \Exception('Gagal membuat direktori');
                }
            }

            $fileName = 'annual_report_' . $year . '_' . uniqid() . '.pdf';
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('Gagal memuat naik fail');
            }

            $data = [
                'year' => $year,
                'title' => $year,
                'filename' => $fileName,
                'file_path' => '/uploads/annual-reports/' . $fileName,
                'uploaded_by' => $_SESSION['admin_id']
            ];

            $this->annualReport->create($data);

            $_SESSION['success'] = 'Laporan tahunan berjaya dimuat naik';
            header('Location: /admin');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ralat semasa memuat naik fail: ' . $e->getMessage();
            header('Location: /admin');
            exit;
        }
    }

    public function downloadReport($id)
    {
        try {
            $report = $this->annualReport->getById($id);
            if (!$report) {
                throw new \Exception('Laporan tidak dijumpai');
            }

            $filepath = dirname(__DIR__, 2) . '/public/uploads/annual-reports/' . $report['filename'];
            if (!file_exists($filepath)) {
                throw new \Exception('Fail tidak dijumpai');
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $report['filename'] . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/annual-reports');
            exit;
        }
    }

    public function deleteReport($id)
    {
        try {
            $report = $this->annualReport->getById($id);
            if (!$report) {
                throw new \Exception('Laporan tidak dijumpai');
            }

            if (!$this->annualReport->delete($id)) {
                throw new \Exception('Gagal memadam rekod dari pangkalan data');
            }

            $filepath = dirname(__DIR__, 2) . '/public/uploads/annual-reports/' . $report['file_name'];
            
            if (file_exists($filepath)) {
                chmod($filepath, 0777);
                clearstatcache(true, $filepath);
                
                if (!unlink($filepath)) {
                    $error = error_get_last();
                    throw new \Exception('Gagal memadam fail: ' . ($error['message'] ?? 'Unknown error'));
                }
            }

            $_SESSION['success'] = 'Laporan tahunan berjaya dipadam';
            header('Location: /admin');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin');
            exit;
        }
    }
}