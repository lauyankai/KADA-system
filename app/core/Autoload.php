<?php

spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    $file = str_replace(['\\', 'App/'], ['/', ''], $class) . '.php';
    
    // Set the base directory for the application
    $base_dir = dirname(__DIR__);
    
    // Create the full path
    $file = $base_dir . DIRECTORY_SEPARATOR . $file;
    
    // Debug information
    error_log("Attempting to load class: " . $class);
    error_log("Looking for file: " . $file);
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        error_log("Successfully loaded: " . $file);
        return true;
    } else {
        error_log("File not found: " . $file);
    }
    
    return false;
}); 