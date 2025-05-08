<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_finance'])) {
        // Add new finance record
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $amount = floatval($_POST['amount']);
        $transaction_type = sanitize($_POST['transaction_type']);
        $transaction_date = sanitize($_POST['transaction_date']);

        $sql = "INSERT INTO finances (title, description, amount, transaction_type, transaction_date) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        // Bind parameters
        $stmt->bind_param("ssdss", $title, $description, $amount, $transaction_type, $transaction_date);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Finance record added successfully!";
        } else {
            $_SESSION['error'] = "Error adding finance record: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: adfinance.php");
        exit;
    } elseif (isset($_POST['update_finance'])) {
        // Update finance record
        $id = intval($_POST['finance_id']);
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $amount = floatval($_POST['amount']);
        $transaction_type = sanitize($_POST['transaction_type']);
        $transaction_date = sanitize($_POST['transaction_date']);

        $sql = "UPDATE finances SET 
                title = ?,
                description = ?,
                amount = ?,
                transaction_type = ?,
                transaction_date = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $title, $description, $amount, $transaction_type, $transaction_date, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Finance record updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating finance record: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: finance.php");
        exit;
    } elseif (isset($_POST['delete_finance'])) {
        // Delete finance record
        $id = intval($_POST['finance_id']);
        
        $sql = "DELETE FROM finances WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Finance record deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting finance record: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: finance.php");
        exit;
    }
}

// Get all financial records
$finances = [];
$result = $conn->query("SELECT * FROM finances ORDER BY transaction_date DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $finances[] = $row;
    }
}

// Calculate totals
// Initialize totals to 0 as fallback
$income_total = 0;
$expense_total = 0;

// Get income total
$income_result = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'income'");
if ($income_result && $income_result->num_rows > 0) {
    $row = $income_result->fetch_assoc();
    $income_total = (float)$row['total'] ?? 0;
}

// Get expense total
$expense_result = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'expense'");
if ($expense_result && $expense_result->num_rows > 0) {
    $row = $expense_result->fetch_assoc();
    $expense_total = (float)$row['total'] ?? 0;
}
$balance = $income_total - $expense_total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Finances</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        /* Admin Container */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .sidebar-nav ul {
            list-style: none;
        }
        
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .sidebar-nav li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        
        .sidebar-nav li a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 25px;
        }
        
        .sidebar-nav li.active a {
            background: rgba(255,255,255,0.15);
            font-weight: 500;
            border-left: 4px solid #3498db;
        }
        
        /* Main Content Styles */
        .admin-content {
            flex: 1;
            padding: 30px;
            background: #f8f9fa;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .admin-header h1 {
            font-size: 1.8rem;
            color: #2c3e50;
        }
        
        /* Button Styles */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(41, 128, 185, 0.2);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        
        .btn-edit:hover {
            background: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(230, 126, 34, 0.2);
        }
        
        .btn-delete {
            background: #e74c3c;
            color: black !important;
            
        }
        
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(192, 57, 43, 0.2);
        }
        
        /* Finance Summary Cards */
        .finance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            padding: 25px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .summary-card p {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .summary-card.income {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }
        
        .summary-card.expense {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .summary-card.balance {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background: #d5f5e3;
            color: #27ae60;
            border-left: 5px solid #27ae60;
        }
        
        .alert-error {
            background: #fadbd8;
            color: #e74c3c;
            border-left: 5px solid #e74c3c;
        }
        
        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge.income {
            background: #d5f5e3;
            color: #27ae60;
        }
        
        .badge.expense {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        /* Actions Column */
        .actions {
            display: flex;
            gap: 8px;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(3px);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .close {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #7f8c8d;
            transition: all 0.3s;
        }
        
        .close:hover {
            color: #333;
            transform: rotate(90deg);
        }
        
        #modalTitle {
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.95rem;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group input[type="date"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .admin-content {
                padding: 20px;
            }
            
            .finance-summary {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo $_SESSION['admin_username']; ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="addashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li class="active"><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="addashboard.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Finances</h1>
                <button id="addFinanceBtn" class="btn btn-primary">Add New Record</button>
            </header>

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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($finances)): ?>
                            <tr>
                                <td colspan="7">No financial records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($finances as $finance): ?>
                                <tr>
                                    <td><?php echo $finance['id']; ?></td>
                                    <td><?php echo htmlspecialchars($finance['title']); ?></td>
                                    <td><?php echo htmlspecialchars($finance['description']); ?></td>
                                    <td>₵<?php echo number_format($finance['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $finance['transaction_type'] === 'income' ? 'income' : 'expense'; ?>">
                                            <?php echo ucfirst($finance['transaction_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($finance['transaction_date'])); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" data-id="<?php echo $finance['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="finance_id" value="<?php echo $finance['id']; ?>">
                                            <button type="submit" name="delete_finance" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Finance Modal -->
    <div id="financeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Financial Record</h2>
            <form id="financeForm" method="POST">
                <input type="hidden" id="finance_id" name="finance_id">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="amount">Amount (₵)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="transaction_type">Transaction Type</label>
                    <select id="transaction_type" name="transaction_type" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="transaction_date">Transaction Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" required>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" name="add_finance" class="btn btn-primary">Add Record</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
        // Finance modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('financeModal');
            const addBtn = document.getElementById('addFinanceBtn');
            const closeBtn = document.querySelector('.close');
            const modalTitle = document.getElementById('modalTitle');
            const financeForm = document.getElementById('financeForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Open modal for adding new finance record
            addBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Add New Financial Record';
                financeForm.reset();
                document.getElementById('finance_id').value = '';
                submitBtn.name = 'add_finance';
                submitBtn.textContent = 'Add Record';
                modal.style.display = 'block';
            });
            
            // Close modal
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Edit finance functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const financeId = this.getAttribute('data-id');
                    
                    // Fetch finance data via AJAX
                    fetch(`get_finance.php?id=${financeId}`)
                        .then(response => response.json())
                        .then(finance => {
                            if (finance) {
                                modalTitle.textContent = 'Edit Financial Record';
                                document.getElementById('finance_id').value = finance.id;
                                document.getElementById('title').value = finance.title;
                                document.getElementById('description').value = finance.description;
                                document.getElementById('amount').value = finance.amount;
                                document.getElementById('transaction_type').value = finance.transaction_type;
                                document.getElementById('transaction_date').value = finance.transaction_date;
                                
                                submitBtn.name = 'update_finance';
                                submitBtn.textContent = 'Update Record';
                                modal.style.display = 'block';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>
</html>