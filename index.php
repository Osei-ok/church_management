<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church of Christ - Welcome</title>
    <style>
        :root {
            --primary: #2d4b4b;
            --primary-light: #3d6b6b;
            --primary-dark: #1f3a3a;
            --accent: #f4a261;
            --light: #ffffff;
            --dark: #343a40;
            --text-light: #ffffff;
            --text-dark: #212529;
            --card-bg: rgba(45, 75, 75, 0.8);
            --card-border: rgba(255, 255, 255, 0.3);
            --card-hover: rgba(61, 107, 107, 0.9);
            --transition: all 0.3s ease;
            --avatar-size: 36px;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1510519138101-570d1dca3d66?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y2h1cmNoJTIwaW50ZXJpb3J8ZW58MHx8MHx8fDA%3D');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: rgba(45, 75, 75, 0.95);
            padding: 15px 0;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        .logo h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .navbar {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 10px;
            margin: 0;
            padding: 0;
        }

        .navbar ul li a {
            color: var(--text-light);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: var(--transition);
            font-weight: 500;
        }

        .navbar ul li a:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .admin-avatar-container {
            position: relative;
            display: inline-block;
            margin-left: 10px;
        }

        .admin-avatar {
            width: var(--avatar-size);
            height: var(--avatar-size);
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            border: 2px solid var(--accent);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.1rem;
            padding: 5px;
        }

        .admin-avatar:hover {
            background-color: var(--accent);
            color: var(--primary-dark);
            transform: scale(1.1);
        }

        .admin-tooltip {
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 100;
            pointer-events: none;
        }

        .admin-avatar-container:hover .admin-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: -30px;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
            padding: 10px;
        }

        .menu-toggle span {
            height: 3px;
            width: 25px;
            background: var(--text-light);
            border-radius: 3px;
            transition: var(--transition);
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        @media (max-width: 768px) {
            .navbar ul {
                display: none;
                flex-direction: column;
                background-color: var(--primary-dark);
                position: absolute;
                top: 80px;
                right: 20px;
                width: 250px;
                padding: 15px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .navbar ul.active {
                display: flex;
            }

            .menu-toggle {
                display: flex;
            }
        }

        main {
            flex: 1;
            padding: 20px 0;
        }

        .hero {
            text-align: center;
            padding: 100px 20px;
            background-color: rgba(45, 75, 75, 0.8);
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .hero h2 {
            font-size: 2.8rem;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.2rem;
            margin: 10px 0;
            line-height: 1.6;
        }

        .quick-links {
            margin: 60px 0;
            text-align: center;
        }

        .quick-links h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: var(--accent);
            position: relative;
            display: inline-block;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .quick-links h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .links-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .links-grid {
                grid-template-columns: 1fr;
            }
        }

        .link-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            padding: 25px 15px;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
            backdrop-filter: blur(5px);
            cursor: pointer;
            color: var(--text-light);
        }

        .link-card:hover {
            background-color: var(--card-hover);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .link-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--accent);
        }

        .link-card h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--text-light);
        }

        footer {
            background-color: var(--primary-dark);
            padding: 40px 0 0;
            margin-top: 60px;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .footer-left h3, .footer-right h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--accent);
            position: relative;
            padding-bottom: 10px;
        }

        .footer-left h3::after, .footer-right h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent);
        }

        .footer-left p {
            margin: 10px 0;
            line-height: 1.6;
        }

        iframe {
            width: 100%;
            height: 250px;
            border: none;
            border-radius: 10px;
            margin-top: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .social-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: var(--text-light);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            background-color: var(--accent);
            color: var(--text-dark);
            transform: translateY(-3px);
        }

        .datetime {
            font-size: 1rem;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .copyright {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            font-size: 0.9rem;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .btn {
            display: inline-block;
            background-color: var(--accent);
            color: var(--text-dark);
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero, .quick-links, .link-card {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .link-card:nth-child(1) { animation-delay: 0.1s; }
        .link-card:nth-child(2) { animation-delay: 0.2s; }
        .link-card:nth-child(3) { animation-delay: 0.3s; }
        .link-card:nth-child(4) { animation-delay: 0.4s; }
        .link-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="logo.png" alt="Church Logo">
                <h1>Akweteman Church of Christ</h1>
            </div>
            <div class="navbar">
                <div class="menu-toggle" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <ul id="nav-menu">
                    <li><a href="#home">Home</a></li>
                    <li><a href="umembers.php">Members</a></li>
                    <li><a href="uevents.php">Events</a></li>
                    <li><a href="ufinance.php">Finance</a></li>
                    <li><a href="uvisitors.php">Visitors</a></li>
                    <li><a href="usermons.php">Sermons</a></li>
                    <li class="admin-avatar-container">
                        <a href="adlogin.php">
                            <div class="admin-avatar">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <span class="admin-tooltip">Admin Login</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="container">
        <section id="home" class="hero">
            <h2>Welcome to Akweteman Church of Christ</h2>
            <p>Join us as we grow in faith and love together</p>
            <p>Thank you for worshiping with us</p>
            <a href="uevents.php" class="btn">View Upcoming Events</a>
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
        <div class="container footer-container">
            <div class="footer-left">
                <h3>Akweteman Church of Christ</h3>
                <p>P.O.Box, Accra</p>
                <p>Location: Achimota-Akweteman</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.6829536925266!2d-0.23756159999999998!3d5.6137516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9971e6f1c727%3A0x155e67743a4be07f!2sAKWETEMAN%20CHURCH%20OF%20CHRIST!5e0!3m2!1sen!2sgh!4v1744463645075!5m2!1sen!2sgh"></iframe>
                <div class="datetime">
                    <span id="current-date"></span> | <span id="current-time"></span>
                </div>
            </div>
            <div class="footer-right">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com/coc_YouthMinistry_Achimota_Akweteman" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/coc_akweteman_youth_ministry" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; <span id="current-year"></span> Akweteman Church of Christ. All rights reserved.
        </div>
    </footer>

    <script>
        function updateDateTime() {
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString(undefined, { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
            document.getElementById('current-year').textContent = now.getFullYear();
        }

        function toggleMenu() {
            const menu = document.getElementById('nav-menu');
            const toggle = document.querySelector('.menu-toggle');
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        // Initialize
        setInterval(updateDateTime, 1000);
        updateDateTime();
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>