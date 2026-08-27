<?php
/**
 * Router file for PHP built-in server
 * Usage: php -S localhost:8080 public/router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// If URI contains index.php, strip it
if (strpos($uri, '/index.php') === 0) {
    $uri = substr($uri, strlen('/index.php'));
    $_SERVER['REQUEST_URI'] = $uri;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
