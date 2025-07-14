<?php
require_once 'config.php';
require_once 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create default admin if not exists
createInitialAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (adminLogin($email, $password)) {
        header("Location: addashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/236339/pexels-photo-236339.jpeg?auto=compress&cs=tinysrgb&w=600');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #667eea;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .show-password {
            margin-top: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .show-password input {
            margin-right: 8px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #556cd6;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #667eea;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .error {
            text-align: center;
            color: red;
            margin-bottom: 15px;
        }

        .button-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required />
                <div class="show-password">
                    <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()" />
                    <label for="togglePassword">Show Password</label>
                </div>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="button-group">
            <a class="forgot-password" href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("password");
            passwordField.type = passwordField.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
