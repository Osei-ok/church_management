<?php
require_once 'config.php';
require_once 'auth.php';
requireAdminLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(null);
    }
    $stmt->close();
} else {
    echo json_encode(null);
}
?>