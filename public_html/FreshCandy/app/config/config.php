<?php
/**
 * Configuration file for FreshCandy application
 * Contains environment-specific settings
 */

// Detect if we're in development or production
$isLocalhost = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' || 
    strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0
);

// Base URL configuration
if ($isLocalhost) {
    // Development environment (localhost)
    define('BASE_URL', 'http://localhost');
    define('APP_PATH', '');
} else {
    // Production environment (VPS)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . '://' . $host);
    define('APP_PATH', ''); // Root level on VPS
}

// Complete base URL for assets
define('ASSETS_BASE_URL', BASE_URL . APP_PATH . '/assets');
define('IMAGES_BASE_URL', ASSETS_BASE_URL . '/images');

// Environment type
define('ENVIRONMENT', $isLocalhost ? 'development' : 'production');

// Debug mode
define('DEBUG_MODE', $isLocalhost);

/**
 * Get the full URL for an image
 * @param string $imagePath - relative path like 'assets/images/filename.jpg' or just 'filename.jpg'
 * @return string - complete URL
 */
function getImageUrl($imagePath) {
    if (empty($imagePath)) {
        return '';
    }
    
    // If it's already a complete URL, return as is
    if (preg_match('/^https?:\/\//', $imagePath)) {
        return $imagePath;
    }
    
    // Clean the path
    $imagePath = ltrim($imagePath, '/');
    
    // If it starts with 'assets/images/', use it as is
    if (strpos($imagePath, 'assets/images/') === 0) {
        return BASE_URL . APP_PATH . '/' . $imagePath;
    }
    
    // If it's just a filename, add the full path
    $filename = basename($imagePath);
    return IMAGES_BASE_URL . '/' . $filename;
}

/**
 * Get the relative path for storing in database
 * @param string $fullUrl - complete URL or path
 * @return string - relative path for database storage
 */
function getRelativeImagePath($fullUrl) {
    if (empty($fullUrl)) {
        return '';
    }
    
    // If it's already relative, clean it up
    if (!preg_match('/^https?:\/\//', $fullUrl)) {
        $path = ltrim($fullUrl, '/');
        if (strpos($path, 'assets/images/') === 0) {
            return $path;
        }
        $filename = basename($path);
        return 'assets/images/' . $filename;
    }
    
    // Extract filename from absolute URL
    $patterns = [
        '/\/assets\/images\/(.+)$/',
        '/\/images\/(.+)$/',
        '/\/(.+\.(jpg|jpeg|png|gif|webp))$/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $fullUrl, $matches)) {
            $filename = basename($matches[1]);
            return 'assets/images/' . $filename;
        }
    }
    
    // Fallback
    $filename = basename(parse_url($fullUrl, PHP_URL_PATH));
    if ($filename && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
        return 'assets/images/' . $filename;
    }
    
    return $fullUrl;
}