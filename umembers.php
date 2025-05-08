<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - Church of Christ</title>
    <style>
        body {
    background-image: url('https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2h1cmNofGVufDB8fDB8fHww');
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
                <h1>Church of Christ</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="umembers.php">Members</a></li>
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
        <section class="page-header">
            <h1>Church Members</h1>
            <p>Our church family members</p>
        </section>

        <section class="content-section">
            <div class="filters">
                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" id="search" placeholder="Search members...">
                </div>
                <div class="filter-group">
                    <label for="filter">Filter by:</label>
                    <select id="filter">
                        <option value="all">All Members</option>
                        <option value="staff">Staff Members</option>
                        <option value="regular">Regular Members</option>
                    </select>
                </div>
            </div>

            <div class="members-grid">
                <?php
                $sql = "SELECT * FROM members ORDER BY last_name, first_name";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="member-card" data-staff="' . ($row['is_staff'] ? 'staff' : 'regular') . '">';
                        echo '<div class="member-avatar">';
                        echo '<i class="fas fa-user"></i>';
                        echo '</div>';
                        echo '<div class="member-info">';
                        echo '<h3>' . htmlspecialchars($row['first_name'] . ' ' . htmlspecialchars($row['last_name'])) . '</h3>';
                        if ($row['is_staff']) {
                            echo '<span class="badge staff">Staff</span>';
                        }
                        if (!empty($row['email'])) {
                            echo '<p><i class="fas fa-envelope"></i> ' . htmlspecialchars($row['email']) . '</p>';
                        }
                        if (!empty($row['phone'])) {
                            echo '<p><i class="fas fa-phone"></i> ' . htmlspecialchars($row['phone']) . '</p>';
                        }
                        echo '<p><i class="fas fa-calendar-alt"></i> Joined: ' . date('M Y', strtotime($row['join_date'])) . '</p>';
                        
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No members found.</p>';
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

    <script src="main.js"></script>
    <script>
        // Filter and search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const filterSelect = document.getElementById('filter');
            const memberCards = document.querySelectorAll('.member-card');
            
            function filterMembers() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value;
                
                memberCards.forEach(card => {
                    const name = card.querySelector('h3').textContent.toLowerCase();
                    const isStaff = card.getAttribute('data-staff') === 'staff';
                    const matchesSearch = name.includes(searchTerm);
                    const matchesFilter = filterValue === 'all' || 
                                         (filterValue === 'staff' && isStaff) || 
                                         (filterValue === 'regular' && !isStaff);
                    
                    if (matchesSearch && matchesFilter) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            searchInput.addEventListener('input', filterMembers);
            filterSelect.addEventListener('change', filterMembers);
        });
    </script>
</body>
</html>