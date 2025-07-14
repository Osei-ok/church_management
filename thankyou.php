<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You for Your Contribution</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #e9f6ef;
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
    }

    .thank-you-box {
      background: white;
      padding: 50px 30px;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
      max-width: 480px;
      width: 90%;
      animation: fadeIn 1s ease-in;
    }

    .thank-you-box h1 {
      font-size: 2rem;
      color: #28a745;
      margin-bottom: 15px;
      animation: bounce 1.5s infinite;
    }

    .thank-you-box p {
      font-size: 1rem;
      color: #333;
    }

    a.button {
      display: inline-block;
      margin-top: 30px;
      background-color: #4a6fa5;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
    }

    a.button:hover {
      background-color: #375782;
    }

    .redirecting {
      margin-top: 20px;
      font-size: 0.9rem;
      color: #666;
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-8px);
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>

  <script>
    // Redirect to homepage after 7 seconds
    setTimeout(function() {
      window.location.href = "index.php";
    }, 7000);
  </script>
</head>
<body>
  <div class="thank-you-box">
    <h1>🎉 Thank You!</h1>
    <p>Your contribution has been successfully received.<br>May God richly bless you!</p>

    <a href="index.php" class="button">Return to Home</a>
    <div class="redirecting">Redirecting shortly...</div>
  </div>
</body>
</html>
