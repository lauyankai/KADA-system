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

            // Create upload directory if it doesn't exist
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/annual-reports';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception('Gagal membuat direktori muat naik');
                }
            }

            // Verify directory is writable
            if (!is_writable($uploadDir)) {
                chmod($uploadDir, 0755);
                if (!is_writable($uploadDir)) {
                    throw new \Exception('Direktori muat naik tidak boleh ditulis. Sila semak kebenaran direktori.');
                }
            }

            // Validate file upload
            if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Ralat semasa memuat naik fail');
            }

            $file = $_FILES['report_file'];
            $year = $_POST['year'] ?? date('Y');
            $description = $_POST['description'] ?? '';

            // Validate file type
            $allowedTypes = ['application/pdf'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Hanya fail PDF dibenarkan');
            }

            // Validate file size
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if ($file['size'] > $maxSize) {
                throw new \Exception('Saiz fail terlalu besar. Had maksimum adalah 10MB');
            }

            // Generate unique filename
            $fileName = 'annual_report_' . $year . '_' . uniqid() . '.pdf';
            $filePath = $uploadDir . '/' . $fileName;
            error_log('Upload path: ' . $filePath);

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('Gagal memindahkan fail yang dimuat naik');
            }

            error_log('File uploaded successfully to: ' . $filePath);

            // Save to database
            try {
                $data = [
                    'year' => $year,
                    'title' => 'Laporan Tahunan ' . $year,
                    'description' => $description,
                    'filename' => $fileName,
                    'file_path' => '/uploads/annual-reports/' . $fileName,
                    'uploaded_by' => $_SESSION['admin_id']
                ];
                $reportId = $this->annualReport->create($data);
                error_log('Annual report saved to database with ID: ' . $reportId);
            } catch (\Exception $e) {
                // If database save fails, delete the uploaded file
                unlink($filePath);
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