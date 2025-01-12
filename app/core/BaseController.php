<?php
namespace App\Core;

class BaseController
{
    protected function view($view, $data = [])
    {
        // Extract data array into variables
        extract($data);
        
        // Convert view path format (e.g., 'home/landing' to '/home/landing.php')
        $view = str_replace('.', '/', $view);
        
        // Get the base directory path
        $basePath = dirname(__DIR__);
        
        // Include the header
        require_once $basePath . '/views/layouts/header.php';
        
        // Include the main view file
        $viewFile = $basePath . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View file not found: " . $viewFile);
        }
        
        // Include the footer
        require_once $basePath . '/views/layouts/footer.php';
    }
}