<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'church_management');

// Create both MySQLi and PDO connections for compatibility
// MySQLi connection

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// PDO connection
try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

// Set timezone
date_default_timezone_set('UTC');

// Start session
session_start();

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/wav', 'audio/mp3']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
?>