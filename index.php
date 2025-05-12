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
        
        /* Footer styles */
        footer {
            background-color: #333;
            color: white;
            padding: 30px 0;
        }
        
        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-left {
            flex: 1;
            margin-right: 30px;
        }
        
        .footer-right {
            flex: 1;
            text-align: right;
        }
        
        .address-section {
            margin-bottom: 20px;
        }
        
        .address-section h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #f8f8f8;
        }
        
        .address-section p {
            margin: 5px 0;
            color: #ddd;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-left: 15px;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: #4fc3f7;
        }
        
        .datetime {
            margin-top: 20px;
            color: #ddd;
            font-style: italic;
            text-align: right;
            text-decoration: dotted;
        }
        
        iframe {
            margin-top: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .copyright {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #aaa;
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
        <div class="footer-container">
            <div class="footer-left">
                <div class="address-section">
                    <h3>Akweteyman Church of Christ</h3>
                    <p>P.O.Box, Accra</p>
                    <p>Location:</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.6829536925266!2d-0.23756159999999998!3d5.6137516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9971e6f1c727%3A0x155e67743a4be07f!2sAKWETEMAN%20CHURCH%20OF%20CHRIST!5e0!3m2!1sen!2sgh!4v1744463645075!5m2!1sen!2sgh" 
                            width="100%" 
                            height="200" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="datetime">
                    <span id="current-date"></span>
                    <span id="current-time"></span>
                </div>
            </div>
            
            <div class="footer-right">
                <div class="social-links">
                    <h3>Connect With Us</h3>
                    <a href="https://www.facebook.com/coc_YouthMinistry_Achimota_Akweteman" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/coc_akweteman_youth_ministry" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> Akweteyman Church of Christ. All rights reserved.</p>
        </div>
    </footer>

    <script src="main.js"></script>
    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');
            
            // Format date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateElement.textContent = now.toLocaleDateString(undefined, options);
            
            // Format time
            timeElement.textContent = now.toLocaleTimeString();
        }
        
        // Update every second
        setInterval(updateDateTime, 1000);
        updateDateTime(); // Initial call
    </script>
</body>
</html>