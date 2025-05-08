<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Events</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
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
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .event-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid #4a6fa5;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .event-card.upcoming {
            border-left: 5px solid #28a745;
        }
        .event-card.past {
            opacity: 0.8;
            border-left: 5px solid #6c757d;
        }
        .event-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .event-card h3 {
            margin: 0 0 10px 0;
            color: #343a40;
        }
        .event-card p {
            margin: 0 0 10px 0;
            color: #666;
        }
        .event-location {
            display: flex;
            align-items: center;
            color: #4a6fa5;
            font-size: 0.9rem;
        }
        .event-location i {
            margin-right: 5px;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .events-grid {
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
                    <li class="active"><a href="uevents.php">Events</a></li>
                    <li><a href="ufinance.php">Finance</a></li>
                    <li><a href="uvisitors.php">Visitors</a></li>
                    <li><a href="usermons.php">Sermons</a></li>
                    <li><a href="adlogin.php" class="admin-login">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <h1>Church Events</h1>
            <p>Upcoming and past events</p>
        </section>

        <section class="content-section">
            <h2>Upcoming Events</h2>
            <div class="events-grid">
                <?php
                $current_date = date('Y-m-d');
                $sql = "SELECT * FROM events WHERE event_date >= '$current_date' ORDER BY event_date ASC, event_time ASC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="event-card upcoming">';
                        echo '<div class="event-date">' . date('l, F j, Y', strtotime($row['event_date'])) . ' at ' . date('g:i A', strtotime($row['event_time'])) . '</div>';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<div class="event-location"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['location']) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No upcoming events scheduled.</p>';
                }
                ?>
            </div>

            <h2 style="margin-top: 40px;">Past Events</h2>
            <div class="events-grid">
                <?php
                $sql = "SELECT * FROM events WHERE event_date < '$current_date' ORDER BY event_date DESC, event_time DESC LIMIT 6";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="event-card past">';
                        echo '<div class="event-date">' . date('l, F j, Y', strtotime($row['event_date'])) . ' at ' . date('g:i A', strtotime($row['event_time'])) . '</div>';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<div class="event-location"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['location']) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No past events to display.</p>';
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