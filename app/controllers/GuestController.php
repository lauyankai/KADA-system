<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\Guest;

class GuestController extends BaseController
{
    public function store()
    {
        try {
            if ($this->user->create($_POST)) {
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