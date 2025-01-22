<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\AnnualReport;

class AnnualReportController extends BaseController
{
    private $annualReport;

    public function __construct()
    {
        $this->annualReport = new AnnualReport();
    }

    public function index()
    {
        try {
            \App\Middleware\AuthMiddleware::validateAccess('admin');
            
            error_log('Starting AnnualReportController::index');
            
            // Check admin session
            if (!isset($_SESSION['admin_id'])) {
                error_log('No admin_id in session');
                throw new \Exception('Sila log masuk sebagai admin');
            }
            
            error_log('Admin ID: ' . $_SESSION['admin_id']);
            
            $reports = $this->annualReport->getAllReports();
            error_log('Got reports: ' . print_r($reports, true));
            
            $this->view('admin/annual-reports/index', [
                'reports' => $reports
            ]);
            
        } catch (\Exception $e) {
            error_log('Error in AnnualReportController::index: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin');
            exit;
        }
    }

    public function upload()
    {
        try {
            \App\Middleware\AuthMiddleware::validateAccess('admin');
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->view('admin/annual-reports/upload');
                return;
            }

            // Verify admin session
            if (!isset($_SESSION['admin_id'])) {
                error_log('No admin_id in session');
                throw new \Exception('Sila log masuk sebagai admin');
            }

            // Handle file upload
            if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
                error_log('File upload error: ' . ($_FILES['report_file']['error'] ?? 'No file uploaded'));
                throw new \Exception('Sila pilih fail untuk dimuat naik');
            }

            $file = $_FILES['report_file'];
            $year = $_POST['year'] ?? date('Y');
            $description = $_POST['description'] ?? '';

            // Validate file type
            error_log('File type: ' . $file['type']);
            $allowedTypes = ['application/pdf'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new \Exception('Hanya fail PDF dibenarkan');
            }

            // Validate file size
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if ($file['size'] > $maxSize) {
                throw new \Exception('Saiz fail terlalu besar. Had maksimum adalah 10MB');
            }

            // Check if directory is writable
            if (!is_writable($uploadDir)) {
                error_log('Upload directory is not writable: ' . $uploadDir);
                throw new \Exception('Direktori muat naik tidak boleh ditulis');
            }

            // Generate unique filename
            $filename = 'annual_report_' . $year . '_' . uniqid() . '.pdf';
            $filepath = $uploadDir . $filename;
            error_log('Upload path: ' . $filepath);

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                error_log('Failed to move uploaded file. Error: ' . error_get_last()['message']);
                throw new \Exception('Gagal memuat naik fail');
            }

            error_log('File uploaded successfully to: ' . $filepath);

            // Save to database
            try {
                $reportId = $this->annualReport->create([
                    'year' => $year,
                    'filename' => $filename,
                    'description' => $description,
                    'uploaded_by' => $_SESSION['admin_id']
                ]);
                error_log('Annual report saved to database with ID: ' . $reportId);
            } catch (\Exception $e) {
                // If database save fails, delete the uploaded file
                unlink($filepath);
                throw $e;
            }

            $_SESSION['success'] = 'Laporan tahunan berjaya dimuat naik';
            header('Location: /admin/annual-reports');
            exit;

        } catch (\Exception $e) {
            error_log('Error in upload: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/annual-reports/upload');
            exit;
        }
    }

    public function download($id)
    {
        try {
            \App\Middleware\AuthMiddleware::validateAccess('admin');
            
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

    public function delete($id)
    {
        try {
            \App\Middleware\AuthMiddleware::validateAccess('admin');
            
            $report = $this->annualReport->getById($id);
            if (!$report) {
                throw new \Exception('Laporan tidak dijumpai');
            }

            // Delete file
            $filepath = dirname(__DIR__, 2) . '/public/uploads/annual-reports/' . $report['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete from database
            $this->annualReport->delete($id);

            $_SESSION['success'] = 'Laporan tahunan berjaya dipadam';
            header('Location: /admin/annual-reports');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /admin/annual-reports');
            exit;
        }
    }
} 