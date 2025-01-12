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
                $_SESSION['success'] = "Pendaftaran anda telah berjaya dihantar dan sedang dalam proses pengesahan.";
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