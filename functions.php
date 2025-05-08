<?php

// Define the upload directory
// define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Handle file uploads
function handleFileUpload($file, $allowedTypes, $type) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    // Set different max sizes for different file types
    switch($type) {
        case 'audio':
            $maxSize = 100 * 1024 * 1024; // 100MB for audio
            break;
        case 'video':
            $maxSize = 800 * 1024 * 1024; // 800MB for video
            break;
        case 'doc':
            $maxSize = 20 * 1024 * 1024; // 20MB for documents
            break;
        default:
            $maxSize = 20 * 1024 * 1024; // Default 20MB
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return [
            'success' => false, 
            'message' => ucfirst($type) . ' file is too large. Maximum allowed size is ' . formatBytes($maxSize)
        ];
    }

    // Check file type
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)];
    }

    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $type . '_' . uniqid() . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;

    // Create upload directory if it doesn't exist
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Helper function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Delete file
function deleteFile($filename) {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
        return true;
    }
    return false;
}

// Sanitize input data
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags($conn->real_escape_string($data)));
}
?>