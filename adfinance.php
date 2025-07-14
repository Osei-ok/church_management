<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

$finances = [];
$result = $conn->query("SELECT * FROM finances ORDER BY transaction_date DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $finances[] = $row;
    }
}

// Calculate totals
$income_total = 0;
$expense_total = 0;

$income_result = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'income'");
if ($income_result) {
    $income_total = $income_result->fetch_assoc()['total'] ?? 0;
}

$expense_result = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'expense'");
if ($expense_result) {
    $expense_total = $expense_result->fetch_assoc()['total'] ?? 0;
}

$balance = $income_total - $expense_total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Finance</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f0f2f5;
            color: #333;
        }
        header, footer {
            background: #2c3e50;
            color: white;
            padding: 1em 2em;
            text-align: center;
        }
        main {
            padding: 2em;
        }
        h1 {
            text-align: center;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2em;
        }
        .summary div {
            background: white;
            padding: 1em 2em;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2em;
        }
        th, td {
            padding: 12px;
            background: white;
            border: 1px solid #ccc;
            text-align: left;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #f1c40f;
            color: white;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
        .chart-container {
            width: 100%;
            margin-top: 2em;
            background: white;
            padding: 1em;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .export-buttons {
            margin-top: 20px;
            text-align: right;
        }
        .export-buttons button {
            margin-left: 10px;
        }
        .admin-container {
    display: flex;
}

.sidebar {
    width: 250px;
    background-color: #2c3e50;
    min-height: 100vh;
    padding: 20px;
    color: white;
}

.sidebar .logo {
    font-size: 1.5rem;
    margin-bottom: 30px;
    font-weight: bold;
    text-align: center;
}

.nav-links {
    list-style: none;
    padding: 0;
}

.nav-links li {
    margin: 15px 0;
}

.nav-links a {
    color: #ecf0f1;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 4px;
    transition: background 0.3s;
}

.nav-links a i {
    margin-right: 10px;
}

.nav-links a:hover, .nav-links .active a, .nav-links li.active a {
    background-color: #34495e;
}

.main-content {
    flex: 1;
    padding: 20px 40px;
}

    </style>
</head>
<body>
    <div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <h2 class="logo">Admin Panel</h2>
        <ul class="nav-links">
            <li><a href="addashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
            <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li class="active"><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
            <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- [Keep everything else like summary, chart, table, footer here] -->

<header>
    <h1>Finance Management Dashboard</h1>
</header>
<main>
    <div class="summary">
        <div>
            <h3>Total Income</h3>
            <p>₵<?php echo number_format($income_total, 2); ?></p>
        </div>
        <div>
            <h3>Total Expenses</h3>
            <p>₵<?php echo number_format($expense_total, 2); ?></p>
        </div>
        <div>
            <h3>Balance</h3>
            <p>₵<?php echo number_format($balance, 2); ?></p>
        </div>
    </div>

    <div class="chart-container">
        <canvas id="financeChart"></canvas>
    </div>

    <div class="export-buttons">
        <button class="btn" onclick="exportToExcel()">Export to Excel</button>
        <button class="btn" onclick="exportToPDF()">Export to PDF</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($finances as $finance): ?>
            <tr>
                <td><?php echo htmlspecialchars($finance['title']); ?></td>
                <td><?php echo htmlspecialchars($finance['description']); ?></td>
                <td>₵<?php echo number_format($finance['amount'], 2); ?></td>
                <td><?php echo ucfirst($finance['transaction_type']); ?></td>
                <td><?php echo date('M j, Y', strtotime($finance['transaction_date'])); ?></td>
                <td>
                    <button class="btn edit-btn">Edit</button>
                    <button class="btn delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
<footer>
    &copy; <?php echo date('Y'); ?> Church of Christ. All rights reserved.
</footer>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    // Chart Data
    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                label: 'GHS',
                data: [<?php echo $income_total; ?>, <?php echo $expense_total; ?>],
                backgroundColor: ['#2ecc71', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function exportToExcel() {
        const ws = XLSX.utils.table_to_sheet(document.querySelector('table'));
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Finance");
        XLSX.writeFile(wb, "Finance_Report.xlsx");
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text("Finance Report", 10, 10);
        doc.autoTable({ html: 'table', startY: 20 });
        doc.save("Finance_Report.pdf");
    }
</script>
</body>
</html>
