<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\AnnualReport;

class HomeController extends BaseController
{
    private $annualReport;

    public function __construct()
    {
        $this->annualReport = new AnnualReport();
    }

    public function index()
    {
        try {
            // Get the latest annual reports
            $annualReports = $this->annualReport->getLatestReports(6); // Get last 6 reports
            
            $this->view('home/landing', [
                'title' => 'Welcome to KADA',
                'annualReports' => $annualReports
            ]);
        } catch (\Exception $e) {
            // If there's an error, just show the page without reports
            $this->view('home/landing', [
                'title' => 'Welcome to KADA',
                'annualReports' => []
            ]);
        }
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