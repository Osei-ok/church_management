<?php
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Anonymous';
    $amount = floatval($_POST['amount']);
    $purpose = $_POST['purpose'];
    $description = $_POST['description'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    
    if ($anonymous) {
        $name = 'Anonymous';
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO finances (title, description, amount, transaction_type, transaction_date) VALUES (?, ?, ?, 'income', NOW())");
    $stmt->bind_param("ssd", $purpose, $description, $amount);
    
    if ($stmt->execute()) {
        $success_message = "Thank you for your contribution!";
    } else {
        $error_message = "There was an error processing your contribution. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Finances</title>
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            background-color:rgb(125, 144, 163);
            color: #343a40;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            font-size: 16px;
         }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 15px 0;
        }
        .logo h1 {
            color: #4a6fa5;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            margin-left: 20px;
        }
        nav ul li a {
            color: #343a40;
            font-weight: 500;
            padding: 10px 0;
            position: relative;
            text-decoration: none;
        }
        nav ul li a:hover {
            color: #4a6fa5;
        }
        nav ul li.active a {
            color: #4a6fa5;
        }
        .admin-login {
            color: #4a6fa5;
            font-weight: 600;
        }
        .page-header {
            text-align: center;
            margin: 40px 0;
        }
        .page-header h1 {
            color: #166088;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        .content-section {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .finance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .summary-card h3 {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #666;
        }
        .summary-card p {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .summary-card.income {
            border-top: 5px solid #28a745;
        }
        .summary-card.expense {
            border-top: 5px solid #dc3545;
        }
        .summary-card.balance {
            border-top: 5px solid #4a6fa5;
        }
        .finance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .finance-table th, .finance-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .finance-table th {
            background-color: #4a6fa5;
            color: white;
            font-weight: 500;
        }
        .finance-table tr:hover {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge.income {
            background-color: #d4edda;
            color: #155724;
        }
        .badge.expense {
            background-color: #f8d7da;
            color: #721c24;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }
        .payment-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        select.form-control {
            height: 42px;
        }
        .btn {
            background-color: #4a6fa5;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #3a5a80;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        .checkbox-group input {
            margin-right: 10px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .disabled-field {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        @media (max-width: 768px) {
            .finance-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>WELCOME</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="umembers.php">Members</a></li>
                    <li><a href="uevents.php">Events</a></li>
                    <li class="active"><a href="ufinance.php">Finance</a></li>
                    <li><a href="uvisitors.php">Visitors</a></li>
                    <li><a href="usermons.php">Sermons</a></li>
                    <li><a href="adlogin.php" class="admin-login">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <h1>Church Finances</h1>
            <p>Financial transparency report</p>
        </section>

        <section class="content-section">
            <?php
            // Calculate totals
            $income_total = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'income'")->fetch_assoc()['total'] ?? 0;
            $expense_total = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'expense'")->fetch_assoc()['total'] ?? 0;
            $balance = $income_total - $expense_total;
            ?>
            
            <div class="finance-summary">
                <div class="summary-card income">
                    <h3>Total Income</h3>
                    <p>₵<?php echo number_format($income_total, 2); ?></p>
                </div>
                <div class="summary-card expense">
                    <h3>Total Expenses</h3>
                    <p>₵<?php echo number_format($expense_total, 2); ?></p>
                </div>
                <div class="summary-card balance">
                    <h3>Current Balance</h3>
                    <p>₵<?php echo number_format($balance, 2); ?></p>
                </div>
            </div>

            <table class="finance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM finances ORDER BY transaction_date DESC LIMIT 20";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . date('M j, Y', strtotime($row['transaction_date'])) . '</td>';
                            echo '<td>' . htmlspecialchars($row['title']) . '<br><small>' . htmlspecialchars($row['description']) . '</small></td>';
                            echo '<td>₵' . number_format($row['amount'], 2) . '</td>';
                            echo '<td><span class="badge ' . $row['transaction_type'] . '">' . ucfirst($row['transaction_type']) . '</span></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No financial records found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- New Payment Section -->
            <div class="payment-form">
                <h2>Make a Contribution</h2>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
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
                    
                    <button type="submit" class="btn">Submit Contribution</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Church of Christ. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleNameField() {
            const anonymousCheckbox = document.getElementById('anonymous');
            const nameField = document.getElementById('name');
            
            if (anonymousCheckbox.checked) {
                nameField.disabled = true;
                nameField.classList.add('disabled-field');
                nameField.value = '';
            } else {
                nameField.disabled = false;
                nameField.classList.remove('disabled-field');
            }
        }
        
        // Initialize the field state on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleNameField();
        });
    </script>
</body>
</html>