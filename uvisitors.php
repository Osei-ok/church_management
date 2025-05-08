<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors</title>
    <style>
         body {
    background-image: url('https://images.pexels.com/photos/5764908/pexels-photo-5764908.jpeg?auto=compress&cs=tinysrgb&w=600');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    position: relative;
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
        .visitors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .visitor-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .visitor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .visitor-info h3 {
            margin-bottom: 5px;
            color: #343a40;
        }
        .visitor-info p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
        }
        .visitor-info p i {
            margin-right: 5px;
            color: #4a6fa5;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .visitors-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="ufinance.php">Finance</a></li>
                    <li class="active"><a href="uvisitors.php">Visitors</a></li>
                    <li><a href="usermons.php">Sermons</a></li>
                    <li><a href="adlogin.php" class="admin-login">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <h1>Church Visitors</h1>
            <p>Our recent visitors</p>
        </section>

        <section class="content-section">
            <div class="visitors-grid">
                <?php
                $sql = "SELECT * FROM visitors ORDER BY visit_date DESC LIMIT 20";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="visitor-card">';
                        echo '<div class="visitor-info">';
                        echo '<h3>' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . '</h3>';
                        if (!empty($row['email'])) {
                            echo '<p><i class="fas fa-envelope"></i> ' . htmlspecialchars($row['email']) . '</p>';
                        }
                        if (!empty($row['phone'])) {
                            echo '<p><i class="fas fa-phone"></i> ' . htmlspecialchars($row['phone']) . '</p>';
                        }
                        echo '<p><i class="fas fa-calendar-alt"></i> Visited: ' . date('M j, Y', strtotime($row['visit_date'])) . '</p>';
                        if (!empty($row['notes'])) {
                            echo '<p><i class="fas fa-sticky-note"></i> ' . htmlspecialchars(substr($row['notes'], 0, 50)) . '...</p>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No visitors found.</p>';
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Church of Christ. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
