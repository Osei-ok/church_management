<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church of Christ - Home</title>
    <style>
         body {
    background-image: url('https://images.unsplash.com/photo-1474814947326-d835369963a5?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mjk4fHxjaHVyY2h8ZW58MHx8MHx8fDA%3D');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    position: relative;
}
    </style> 
       
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Akweteyman Church of Christ</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="umembers.php">Members</a></li>
                    <li><a href="uevents.php">Events</a></li>
                    <li><a href="ufinance.php">Finance</a></li>
                    <li><a href="uvisitors.php">Visitors</a></li>
                    <li><a href="usermons.php">Sermons</a></li>
                    <li><a href="adlogin.php" class="admin-login">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section id="home" class="hero">
            <div class="hero-content">
                <h2>Welcome to Church of Christ</h2>
                <p>Join us as we grow in faith and love together</p>
                
            </div>
        </section>

        <section class="quick-links">
            <h2>Quick Access</h2>
            <div class="links-grid">
                <a href="umembers.php" class="link-card">
                    <i class="fas fa-users"></i>
                    <h3>Members</h3>
                </a>
                <a href="uevents.php" class="link-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Events</h3>
                </a>
                <a href="ufinance.php" class="link-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Finance</h3>
                </a>
                <a href="uvisitors.php" class="link-card">
                    <i class="fas fa-user-plus"></i>
                    <h3>Visitors</h3>
                </a>
                <a href="usermons.php" class="link-card">
                    <i class="fas fa-bible"></i>
                    <h3>Sermons</h3>
                </a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Church of Christ. All rights reserved.</p>
        </div>
        <!-- <div class="footer-left"> -->
                    <div class="datetime">
                        <span id="current-date"></span>
                        <span id="current-time"></span>
                    </div>
                    <div class="social-links">
                        <a href="https://www.facebook.com/coc_YouthMinistry_Achimota_Akweteman"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/coc_akweteman_youth_ministry"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-right">
                    <div class="location">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.6829536925266!2d-0.23756159999999998!3d5.6137516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9971e6f1c727%3A0x155e67743a4be07f!2sAKWETEMAN%20CHURCH%20OF%20CHRIST!5e0!3m2!1sen!2sgh!4v1744463645075!5m2!1sen!2sgh" width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>JQ76+GX4, Akweteyman</span>  
                        </a>
                    </div>
                   
    </footer>

    <script src="main.js"></script>
</body>
</html>