<?php
namespace App\Controllers;
use App\Core\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $this->view('home/landing', ['title' => 'Welcome to KADA']);
    }

    public function showVision()
    {
        $this->view('about/vision');
    }

    public function showHistory()
    {
        $this->view('about/history');
    }

    public function showFacts()
    {
        $this->view('about/facts');
    }
}