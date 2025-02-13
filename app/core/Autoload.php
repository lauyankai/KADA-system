<?php

spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Set the base directory for the application
    $baseDir = dirname(__DIR__, 2);
    
    // Build the full path to the class file
    $file = $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $class . '.php';
    
    // Debug information
    error_log("Class being loaded: " . $class);
    error_log("Full path being checked: " . $file);
    error_log("File exists: " . (file_exists($file) ? 'true' : 'false'));
    error_log("Current working directory: " . getcwd());
    
    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
        error_log("Successfully loaded: " . $file);
        return true;
    } else {
        error_log("Failed to load file: " . $file);
    }
    
    return false;
}); 