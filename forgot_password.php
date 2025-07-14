<?php
require_once 'config.php';
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save token in DB
            $stmt = $conn->prepare("UPDATE admins SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expires, $email);
            if ($stmt->execute()) {
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
                $success = "A reset link has been generated:<br><a href=\"$resetLink\">Reset your password</a>";

                // Optionally: mail($email, "Password Reset", "Reset your password: $resetLink");
            } else {
                $error = "Something went wrong while saving the token.";
            }
        } else {
            $error = "Email not found in the system.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/236339/pexels-photo-236339.jpeg?auto=compress&cs=tinysrgb&w=600');
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
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

        input[type="email"] {
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
            margin-top: 15px;
            text-align: center;
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
        <h2>Forgot Password</h2>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email address:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <div class="message">
            <a href="adlogin.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
