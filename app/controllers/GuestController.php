<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\Guest;

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
            if ($this->guest->create($_POST)) {
                $_SESSION['success_message'] = "Permohonan anda telah berjaya dihantar! Terima kasih.";
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