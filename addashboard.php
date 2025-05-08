<?php
require_once 'config.php';
require_once 'auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard </title>
    <style>
        /* admin.css */
:root {
    --primary-color: #4a6fa5;
    --secondary-color: #166088;
    --accent-color: #4fc3f7;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --sidebar-width: 250px;
    --header-height: 80px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
}

.admin-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: var(--sidebar-width);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 20px 0;
    position: fixed;
    height: 100vh;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 100;
}

.sidebar-header {
    padding: 0 20px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
}

.sidebar-header h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.sidebar-header p {
    font-size: 0.9rem;
    opacity: 0.8;
}

.sidebar-nav ul {
    list-style: none;
}

.sidebar-nav li {
    margin-bottom: 5px;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.sidebar-nav a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    padding-left: 25px;
}

.sidebar-nav a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar-nav .active a {
    background-color: rgba(255, 255, 255, 0.2);
    border-left: 4px solid var(--accent-color);
}

/* Main Content Styles */
.admin-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    padding: 20px;
}

.admin-header {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.admin-header h1 {
    font-size: 1.8rem;
    color: var(--dark-color);
    font-weight: 600;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: rgba(79, 195, 247, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: var(--accent-color);
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 1rem;
    font-weight: 500;
    color: #666;
    margin-bottom: 5px;
}

.stat-info p {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--dark-color);
}

/* Recent Activities */
.recent-activities {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.recent-activities h2 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: var(--dark-color);
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item p {
    font-weight: 500;
    margin-bottom: 5px;
}

.activity-item small {
    color: #777;
    font-size: 0.85rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .admin-content {
        margin-left: 0;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* Animation for activity items */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.activity-item {
    animation: fadeIn 0.3s ease forwards;
    opacity: 0;
}

.activity-item:nth-child(1) { animation-delay: 0.1s; }
.activity-item:nth-child(2) { animation-delay: 0.2s; }
.activity-item:nth-child(3) { animation-delay: 0.3s; }
.activity-item:nth-child(4) { animation-delay: 0.4s; }
.activity-item:nth-child(5) { animation-delay: 0.5s; }
</style>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar" style="color: black;">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, admin</p><!--<?php echo $_SESSION['admin_username']; ?></p> -->
            </div>
            <nav class="sidebar-nav" style="color: black;">
                <ul>
                    <li class="active"><a href="addashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="Index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Dashboard Overview</h1>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Members</h3>
                        <p>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM members");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Upcoming Events</h3>
                        <p>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Income</h3>
                        <p>
                            <?php
                            $result = $conn->query("SELECT SUM(amount) as total FROM finances WHERE transaction_type = 'income'");
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo '₵' . number_format($row['total'] ?? 0, 2);
                            } else {
                                $row = ['total' => 0];
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Recent Visitors</h3>
                        <p>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM visitors WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="recent-activities">
                <h2>Recent Activities</h2>
                <div class="activity-list">
                    <?php
                    // Get recent activities from all tables
                    $activities = [];
                    
                    // Members
                    $result = $conn->query("SELECT CONCAT('New member: ', first_name, ' ', last_name) as activity, created_at FROM members ORDER BY created_at DESC LIMIT 3");
                    while ($row = $result->fetch_assoc()) {
                        $activities[] = $row;
                    }
                    
                    // Events
                    $result = $conn->query("SELECT CONCAT('New event: ', title) as activity, created_at FROM events ORDER BY created_at DESC LIMIT 3");
                    while ($row = $result->fetch_assoc()) {
                        $activities[] = $row;
                    }
                    
                    // Finances
                    $result = $conn->query("SELECT CONCAT('New ', transaction_type, ': ', title) as activity, created_at FROM finances ORDER BY created_at DESC LIMIT 3");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $activities[] = $row;
                        }
                    } else {
                        $activities[] = ['activity' => 'No recent finance activities', 'created_at' => date('Y-m-d H:i:s')];
                    }
                    
                    
                    // Visitors
                    $result = $conn->query("SELECT CONCAT('New visitor: ', first_name, ' ', last_name) as activity, created_at FROM visitors ORDER BY created_at DESC LIMIT 3");
                    while ($row = $result->fetch_assoc()) {
                        $activities[] = $row;
                    }
                    
                    // Sermons
                    $result = $conn->query("SELECT CONCAT('New sermon: ', title) as activity, created_at FROM sermons ORDER BY created_at DESC LIMIT 3");
                    while ($row = $result->fetch_assoc()) {
                        $activities[] = $row;
                    }
                    
                    // Sort all activities by date
                    usort($activities, function($a, $b) {
                        return strtotime($b['created_at']) - strtotime($a['created_at']);
                    });
                    
                    // Display top 5
                    $displayed = array_slice($activities, 0, 5);
                    foreach ($displayed as $activity) {
                        echo '<div class="activity-item">';
                        echo '<p>' . htmlspecialchars($activity['activity']) . '</p>';
                        echo '<small>' . date('M j, Y g:i A', strtotime($activity['created_at'])) . '</small>';
                        echo '</div>';
                    }
                    
                    if (empty($displayed)) {
                        echo '<p>No recent activities found.</p>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script src="admin.js"></script>
</body>
</html>