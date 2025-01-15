<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\Guest;
use PDOException;

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
}