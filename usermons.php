<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sermons</title>
    <style>
        body {
    background-image: url('https://images.pexels.com/photos/2294873/pexels-photo-2294873.jpeg?auto=compress&cs=tinysrgb&w=600');
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
        .sermons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .sermon-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .sermon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .sermon-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .sermon-card h3 {
            margin: 0 0 10px 0;
            color: #343a40;
        }
        .sermon-card p {
            margin: 0 0 15px 0;
            color: #666;
        }
        .sermon-preacher {
            display: flex;
            align-items: center;
            color: #4a6fa5;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .sermon-preacher i {
            margin-right: 5px;
        }
        .sermon-media {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .media-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background-color: #4a6fa5;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .media-btn:hover {
            background-color: #166088;
        }
        .media-btn i {
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
            .sermons-grid {
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
                    <li><a href="uvisitors.php">Visitors</a></li>
                    <li class="active"><a href="usermons.php">Sermons</a></li>
                    <li><a href="adlogin.php" class="admin-login">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <h1>Sermons</h1>
            <p>Listen to our latest messages</p>
        </section>

        <section class="content-section">
            <div class="sermons-grid">
                <?php
                $sql = "SELECT * FROM sermons ORDER BY sermon_date DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="sermon-card">';
                        echo '<div class="sermon-date">' . date('F j, Y', strtotime($row['sermon_date'])) . '</div>';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<div class="sermon-preacher"><i class="fas fa-user"></i> Preached by: ' . htmlspecialchars($row['preacher']) . '</div>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        
                        echo '<div class="sermon-media">';
                        if (!empty($row['audio_file'])) {
                            echo '<a href="uploads/' . $row['audio_file'] . '" class="media-btn" target="_blank"><i class="fas fa-headphones"></i> Audio</a>';
                        }
                        if (!empty($row['video_file'])) {
                            echo '<a href="uploads/' . $row['video_file'] . '" class="media-btn" target="_blank"><i class="fas fa-video"></i> Watch</a>';
                        }
                        if (!empty($row['document_file'])) {
                            echo '<a href="uploads/' . $row['document_file'] . '" class="media-btn" target="_blank"><i class="fas fa-file-alt"></i> Notes</a>';
                        }
                        echo '</div>';
                        
                        echo '</div>';
                    }
                } else {
                    echo '<p>No sermons available.</p>';
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