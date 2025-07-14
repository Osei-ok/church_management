<?php
require_once 'config.php';
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$success = '';
$error = '';
$token = $_GET['token'] ?? '';

// Function to verify reset token and return admin data if valid
function verifyResetToken($token) {
    global $conn; // Use the database connection from config.php
    $stmt = $conn->prepare("SELECT * FROM admins WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    return $admin;
}

// Validate token
if (empty($token)) {
    $error = "Invalid or missing reset token.";
} else {
    $admin = verifyResetToken($token);

    if (!$admin) {
        $error = "This reset link is invalid or has expired.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $error = "All fields are required.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            if (updateAdminPassword($admin['email'], $newPassword)) {
                $success = "Password reset successfully. Redirecting to <a href='adlogin.php'>login</a>...";
                header("refresh:4;url=login.php");
            } else {
                $error = "Failed to update password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/236339/pexels-photo-236339.jpeg?auto=compress&cs=tinysrgb&w=600');
            background-size: cover;
            background-position: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            background-color: #667eea;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #556cd6;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        a {
            color: #667eea;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!$success && $admin): ?>
        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <?php endif; ?>

        <div class="message">
            <a href="adlogin.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
