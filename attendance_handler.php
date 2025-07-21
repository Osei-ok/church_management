<?php
require_once 'config.php';
require_once 'auth.php';
requireAdminLogin(); // Only allow admins

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = intval($_POST['member_id']);
    $date = date('Y-m-d');

    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        // Remove attendance
        $stmt = $conn->prepare("DELETE FROM attendance WHERE member_id = ? AND attendance_date = ?");
        $stmt->bind_param("is", $member_id, $date);
    } else {
        // Mark attendance (prevent duplicates)
        $stmt = $conn->prepare("INSERT IGNORE INTO attendance (member_id, attendance_date) VALUES (?, ?)");
        $stmt->bind_param("is", $member_id, $date);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
