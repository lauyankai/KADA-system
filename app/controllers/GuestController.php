<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\Guest;
use App\Core\Database;
use PDOException;
use Exception;

class GuestController extends BaseController
{
    private $guest;
    
    public function __construct()
    {
        $this->guest = new Guest();
    }

    public function create()
    {
        $this->view('guest/create');
    }
    
    public function store()
    {
        try {
            $createdGuest = $this->guest->create($_POST);
            
            if ($createdGuest) {
                // Pass the reference number to the success view
                $this->view('guest/success', [
                    'reference_no' => $createdGuest['reference_no']
                ]);
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /guest/create');
            exit;
        }
    }

    public function checkStatusPage()
    {
        $this->view('guest/check-status');
    }

    public function checkStatus()
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['reference_no'])) {
                $status = $this->guest->checkStatusByReference($data['reference_no']);
            } else if (isset($data['name']) && isset($data['ic_no'])) {
                $status = $this->guest->checkStatusByPersonal($data['name'], $data['ic_no']);
            } else {
                throw new \Exception("Invalid request data");
            }
            
            echo json_encode([
                'success' => true,
                'status' => $status,
                'message' => $this->getStatusMessage($status)
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    private function getStatusMessage($status) {
        switch($status) {
            case 'Pending':
                return 'Permohonan anda masih dalam proses semakan. Sila tunggu makluman selanjutnya.';
            case 'Lulus':
            case 'Active':
                return 'Tahniah! Permohonan anda telah diluluskan. Anda boleh log masuk sebagai ahli menggunakan nombor kad pengenalan anda.';
            case 'Tolak':
            case 'Inactive':
                return 'Harap maaf, permohonan anda tidak berjaya. Anda boleh mendaftar semula sebagai ahli.';
            case 'not_found':
                return 'Tiada permohonan dijumpai dengan nama ini.';
            default:
                return 'Status tidak diketahui.';
        }
    }
}