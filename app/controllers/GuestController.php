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
                $_SESSION['success_message'] = '<div style="font-size: 18px;">Permohonan anda telah berjaya dihantar!<br>Nombor rujukan anda ialah: <strong>' . 
                    $createdGuest['reference_no'] . 
                    '</strong><br>Sila simpan nombor rujukan ini untuk semakan status permohonan anda pada masa hadapan. Terima kasih.</div>';
                header('Location: /');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ralat semasa pendaftaran: " . $e->getMessage();
            header('Location: /guest/create');
            exit;
        }
    }

    public function checkStatus() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $name = trim($data['name'] ?? '');
            
            // Debug log
            error_log("Received name: " . $name);
            
            if (empty($name)) {
                throw new \Exception("Name is required");
            }
            
            // Add route debugging
            error_log("Request URI: " . $_SERVER['REQUEST_URI']);
            error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
            
            $status = $this->guest->checkApplicationStatus($name);
            
            // Debug log
            error_log("Status returned: " . ($status ?? 'null'));
            
            $response = [
                'success' => true,
                'status' => $status,
                'message' => $this->getStatusMessage($status)
            ];
            
            // Debug log
            error_log("Response: " . json_encode($response));
            
            echo json_encode($response);
            
        } catch (\Exception $e) {
            error_log("Error in checkStatus: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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