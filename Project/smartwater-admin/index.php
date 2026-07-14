<?php
/**
 * Laravel - XAMPP Entry Point
 * This file serves as an entry point for XAMPP deployments.
 * It forwards requests to the public/index.php (Laravel's actual entry point).
 */

// Get the public directory path
$publicPath = __DIR__ . '/public';

// Check if public/index.php exists
if (!file_exists($publicPath . '/index.php')) {
    die('Error: Could not find public/index.php. Make sure the Laravel application is properly installed.');
}

// Forward the request to public/index.php
require_once $publicPath . '/index.php';
