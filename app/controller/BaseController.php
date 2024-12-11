<?php

namespace App\Controller;

class BaseController {
    public function render($view, $data = [])
    {
        // Make sure the $view path is correct
        $viewPath = __DIR__ . '/../views' . $view . '.php';

        // Check if the view file exists
        if (file_exists($viewPath)) {
            extract($data);  // Convert the data array into variables

            // Include header
            require_once '../app/views/partials/header.php';

            // Include the view
            require_once $viewPath;  

            // Include footer
            require_once '../app/views/partials/footer.php';
        } else {
            // Handle view not found
            echo "View not found: " . $viewPath;
        }
    }
    
    // Function to handle redirects (e.g., after form submission)
    public function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}