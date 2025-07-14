<?php
session_start();
require_once 'config.php';

$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Anonymous';
    $amount = floatval($_POST['amount']);
    $purpose = $_POST['purpose'];
    $description = $_POST['description'] ?? '';
    $network = $_POST['network'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($anonymous) {
        $name = 'Anonymous';
    }

    // Insert contribution into database
    $stmt = $conn->prepare("INSERT INTO finances (title, description, amount, transaction_type, transaction_date) VALUES (?, ?, ?, 'income', NOW())");

    if ($stmt) {
        $stmt->bind_param("ssd", $purpose, $description, $amount);
        if ($stmt->execute()) {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Make a Contribution - Church of Christ</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #3f37c9;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --success: #4bb543;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #5a5b8aff;
      color: var(--dark);
      line-height: 1.6;
    }
    
    header {
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo h1 {
      color: var(--primary);
      font-size: 1.8rem;
      font-weight: 700;
    }
    
    nav ul {
      display: flex;
      list-style: none;
    }
    
    nav ul li {
      margin-left: 1.5rem;
    }
    
    nav ul li a {
      text-decoration: none;
      color: var(--dark);
      font-weight: 500;
      transition: color 0.3s;
    }
    
    nav ul li a:hover {
      color: var(--primary);
    }
    
    nav ul li a.active {
      color: var(--primary);
      font-weight: 600;
    }
    
    .main-content {
      display: flex;
      gap: 2rem;
      margin: 2rem 0;
    }
    
    .form-section {
      flex: 2;
    }
    
    .payment-section {
      flex: 1;
    }
    
    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .card h2 {
      color: var(--primary);
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }
    
    .card h3 {
      color: var(--secondary);
      margin-bottom: 1rem;
      font-weight: 500;
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }
    
    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      transition: border 0.3s, box-shadow 0.3s;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    
    .checkbox-group {
      display: flex;
      align-items: center;
    }
    
    .checkbox-group input {
      margin-right: 0.75rem;
      width: 1.1rem;
      height: 1.1rem;
    }
    
    textarea.form-control {
      min-height: 100px;
      resize: vertical;
    }
    
    .btn {
      display: block;
      width: 100%;
      padding: 0.75rem;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .btn:hover {
      background-color: var(--primary-dark);
    }
    
    .qr-code {
      text-align: center;
      margin: 1.5rem 0;
    }
    
    .qr-code img {
      max-width: 150px;
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 0.5rem;
      background: white;
    }
    
    .payment-details {
      margin-top: 1rem;
    }
    
    .payment-details p {
      margin-bottom: 0.5rem;
    }
    
    .payment-details strong {
      color: var(--dark);
    }
    
    .spinner {
      display: none;
      text-align: center;
      margin-top: 1rem;
    }
    
    .spinner div {
      width: 24px;
      height: 24px;
      border: 3px solid var(--primary);
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto;
    }
    
    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
    
    .success-message {
      background-color: rgba(75, 181, 67, 0.1);
      color: var(--success);
      padding: 1rem;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 1rem;
      display: none;
    }
    
    @media (max-width: 768px) {
      .header-content {
        flex-direction: column;
      }
      
      nav ul {
        margin-top: 1rem;
      }
      
      nav ul li {
        margin-left: 1rem;
        margin-right: 1rem;
      }
      
      .main-content {
        flex-direction: column;
      }
    }
    
    @media (max-width: 576px) {
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      nav ul li {
        margin: 0.5rem;
      }
      
      .card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-content">
        <div class="logo"><h1>Church of Christ</h1></div>
        <nav>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="umembers.php">Members</a></li>
            <li><a href="uevents.php">Events</a></li>
            <li><a href="ufinance.php" class="active">Finance</a></li>
            <li><a href="uvisitors.php">Visitors</a></li>
            <li><a href="usermons.php">Sermons</a></li>
            <li><a href="adlogin.php" style="color: var(--primary);">Admin Login</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <main class="container">
    <div class="main-content">
      <section class="form-section">
        <div class="card">
          <h2>Make a Contribution</h2>
          
          <?php if ($success): ?>
            <div class="success-message" id="successMessage">
              Thank you for your contribution! Redirecting...
            </div>
          <?php endif; ?>
          
          <form method="POST" onsubmit="showSpinner()" action="">
            <div class="form-group">
              <label for="name">Your Name (optional)</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name">
            </div>

            <div class="form-group checkbox-group">
              <input type="checkbox" id="anonymous" name="anonymous" value="1" onchange="toggleNameField()">
              <label for="anonymous">Make this contribution anonymously</label>
            </div>

            <div class="form-group">
              <label for="amount">Amount (₵)</label>
              <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" required>
            </div>

            <div class="form-group">
              <label for="purpose">Purpose</label>
              <select id="purpose" name="purpose" class="form-control" required>
                <option value="">-- Select Purpose --</option>
                <option value="Sunday Offering">Sunday Offering</option>
                <option value="Building Fund">Building Fund</option>
                <option value="Benevolence">Benevolence</option>
                <option value="Missions">Missions</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label for="description">Description (optional)</label>
              <textarea id="description" name="description" class="form-control" rows="3" placeholder="Any additional information"></textarea>
            </div>

            <div class="form-group">
              <label for="network">Choose Mobile Money Network</label>
              <select id="network" name="network" class="form-control" required>
                <option value="">-- Select Network --</option>
                <option value="MTN">MTN</option>
                <option value="Vodafone">Vodafone</option>
                <option value="AirtelTigo">AirtelTigo</option>
              </select>
            </div>

            <button type="submit" class="btn">Submit Contribution</button>
            <div class="spinner" id="spinner"><div></div></div>
          </form>
        </div>
      </section>
      
      <section class="payment-section">
        <div class="card">
          <h3>Pay via Mobile Money</h3>
          <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=https://pay.hubtel.com/YourChurchPaymentLink" alt="MoMo QR Code">
          </div>
          <div class="payment-details">
            <p>Scan with your mobile wallet or dial:</p>
            <p><strong>*718*555#</strong></p>
            <p><strong>Number:</strong> 024 XXXX XXX</p>
            <p><strong>Name:</strong> Church of Christ</p>
          </div>
        </div>
        
        <div class="card">
          <h3>Bank Transfer Option</h3>
          <div class="payment-details">
            <p><strong>Bank:</strong> Zenith Bank</p>
            <p><strong>Account Name:</strong> Church of Christ</p>
            <p><strong>Account Number:</strong> XXXX XXXX XXXX</p>
            <p><strong>Branch:</strong> Accra Central</p>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script>
    function toggleNameField() {
      const anonymous = document.getElementById('anonymous');
      const nameInput = document.getElementById('name');
      if (anonymous.checked) {
        nameInput.disabled = true;
        nameInput.value = '';
        nameInput.classList.add('disabled-field');
      } else {
        nameInput.disabled = false;
        nameInput.classList.remove('disabled-field');
      }
    }

    function showSpinner() {
      document.getElementById('spinner').style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', function () {
      toggleNameField();

      <?php if ($success): ?>
        document.getElementById('successMessage').style.display = 'block';
        setTimeout(function() {
          window.location.href = "thankyou.php";
        }, 2000);
      <?php endif; ?>
    });
  </script>
</body>
</html>