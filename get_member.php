<?php
require_once 'config.php';
require_once 'auth.php';
requireAdminLogin();

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Member ID']);
    exit;
}

$memberId = intval($_GET['id']);

try {
    $sql = "SELECT *, 
            TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) AS age 
            FROM members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        echo json_encode($member);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Member not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>