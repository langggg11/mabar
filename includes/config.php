<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'projec15_projectpbw_fixx');
define('DB_USER', 'projec15_roott'); 
define('DB_PASS', '@kaesquare123'); 
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'Mabar');
define('SITE_URL', 'http://mabar/mabar'); 
define('UPLOAD_PATH', 'assets/images/');

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
