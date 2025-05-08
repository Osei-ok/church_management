<?php
// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not authenticated
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Admin login
function adminLogin($username, $password) {
    global $conn;
    
    $username = $conn->real_escape_string($username);
    $sql = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
    }
    return false;
}

// Admin logout
function adminLogout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Create initial admin if none exists
function createInitialAdmin() {
    global $conn;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM admins");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO admins (username, password) VALUES ('$username', '$password')");
    }
}
?>
