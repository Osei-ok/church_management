<?php
include 'config.php';

// Get all members data to use in JavaScript
$members_data = [];
$sql = "SELECT * FROM members ORDER BY last_name, first_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $members_data[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - Church of Christ</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2h1cmNofGVufDB8fDB8fHww');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 10px 0;
            position: relative;
        }

        nav a.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #1e88e5;
        }

        .page-header {
            text-align: center;
            margin: 30px 0;
            color: #fff;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .member-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .member-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .member-info h3 {
            margin: 0;
            color: #1e88e5;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .badge.staff {
            background-color: #ff9800;
            color: white;
        }
        
        .badge.regular {
            background-color: #4CAF50;
            color: white;
        }
        
        .badge.youth {
            background-color: #4361ee;
            color: white;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            animation: modalopen 0.4s;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes modalopen {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 15px 0;
            max-height: calc(90vh - 200px);
            overflow-y: auto;
        }

        .modal-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
        }

        .detail-label {
            font-weight: 600;
            width: 120px;
            color: #666;
        }

        .detail-value {
            flex: 1;
        }

        .gender-male { color: #1e88e5; }
        .gender-female { color: #e91e63; }
        .gender-other { color: #8e24aa; }

        .btn {
            background-color: #1e88e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1565c0;
        }

        /* Filters */
        .filters {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        input, select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        
        .alphabet-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 15px;
            justify-content: center;
        }
        
        .alphabet-filter a {
            display: inline-block;
            padding: 5px 10px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        .alphabet-filter a:hover, .alphabet-filter a.active {
            background-color: #1e88e5;
            color: white;
        }
    </style>
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
                        <option value="youth">Youth Members</option>
                    </select>
                </div>
            </div>
            
            <div class="alphabet-filter">
                <a href="#" data-letter="all">All</a>
                <?php 
                foreach(range('A','Z') as $letter) {
                    echo '<a href="#" data-letter="'.$letter.'">'.$letter.'</a>';
                }
                ?>
            </div>

            <div class="members-grid">
                <?php
                if (!empty($members_data)) {
                    foreach ($members_data as $id => $row) {
                        $firstLetter = strtoupper(substr($row['last_name'], 0, 1));
                        $memberType = $row['is_staff'] ? 'staff' : ($row['category'] === 'youth' ? 'youth' : 'regular');
                        
                        echo '<div class="member-card" data-member-id="' . $id . '" 
                              data-staff="' . ($row['is_staff'] ? 'staff' : 'regular') . '" 
                              data-category="' . ($row['category'] ?? 'none') . '"
                              data-letter="' . $firstLetter . '">';
                        echo '<div class="member-avatar">';
                        if (!empty($row['passport_picture'])) {
                            echo '<img src="' . htmlspecialchars($row['passport_picture']) . '" alt="' . htmlspecialchars($row['first_name']) . '">';
                        } else {
                            echo '<img src="assets/default-avatar.png" alt="Member">';
                        }
                        echo '</div>';
                        echo '<div class="member-info">';
                        echo '<h3>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</h3>';
                        
                        if ($row['is_staff']) {
                            echo '<span class="badge staff">Staff</span>';
                        } elseif ($row['category'] === 'youth') {
                            echo '<span class="badge youth">Youth</span>';
                        } else {
                            echo '<span class="badge regular">Regular</span>';
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

    <!-- Member Details Modal -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalMemberName"></h2>
                <div>
                    <span id="modalMemberBadge" class="badge staff" style="display: none;">Staff</span>
                    <span id="modalMemberGender"></span>
                </div>
            </div>
            <div class="modal-body">
                <div style="display: flex; margin-bottom: 20px;">
                    <div class="modal-avatar">
                        <img id="modalMemberImage" src="assets/default-avatar.png" alt="Member">
                    </div>
                    <div class="member-details">
                        <div class="detail-row">
                            <div class="detail-label">Joined:</div>
                            <div class="detail-value" id="modalMemberJoinDate"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Age:</div>
                            <div class="detail-value" id="modalMemberAge"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Birth Date:</div>
                            <div class="detail-value" id="modalMemberBirthday"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value" id="modalMemberEmail"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Phone:</div>
                            <div class="detail-value" id="modalMemberPhone"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Address:</div>
                            <div class="detail-value" id="modalMemberAddress"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" id="closeModal">Close</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Church of Christ. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Utility functions
        function calculateAge(birthDate) {
            if (!birthDate) return 'N/A';
            const today = new Date();
            const birthDateObj = new Date(birthDate);
            let age = today.getFullYear() - birthDateObj.getFullYear();
            const monthDiff = today.getMonth() - birthDateObj.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
                age--;
            }
            return age + ' years';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function getGenderIcon(gender) {
            switch((gender || '').toLowerCase()) {
                case 'male': return '<i class="fas fa-mars gender-male"></i>';
                case 'female': return '<i class="fas fa-venus gender-female"></i>';
                default: return '<i class="fas fa-genderless gender-other"></i>';
            }
        }

        // Main functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get members data from PHP
            const membersData = <?php echo json_encode($members_data); ?>;
            
            // Filter and search functionality
            const searchInput = document.getElementById('search');
            const filterSelect = document.getElementById('filter');
            const memberCards = document.querySelectorAll('.member-card');
            const alphabetLinks = document.querySelectorAll('.alphabet-filter a');
            
            function filterMembers() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value;
                const activeLetter = document.querySelector('.alphabet-filter a.active')?.dataset.letter || 'all';
                
                memberCards.forEach(card => {
                    const name = card.querySelector('h3').textContent.toLowerCase();
                    const isStaff = card.getAttribute('data-staff') === 'staff';
                    const category = card.getAttribute('data-category');
                    const letter = card.getAttribute('data-letter');
                    const matchesSearch = name.includes(searchTerm);
                    const matchesFilter = filterValue === 'all' || 
                                         (filterValue === 'staff' && isStaff) ||
                                         (filterValue === 'regular' && !isStaff && category !== 'youth') ||
                                         (filterValue === 'youth' && category === 'youth');
                    const matchesLetter = activeLetter === 'all' || letter === activeLetter;
                    
                    if (matchesSearch && matchesFilter && matchesLetter) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Alphabet filter
            alphabetLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    alphabetLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    filterMembers();
                });
            });
            
            searchInput.addEventListener('input', filterMembers);
            filterSelect.addEventListener('change', filterMembers);

            // Modal functionality
            const modal = document.getElementById('memberModal');
            const closeBtns = [document.querySelector('.close'), document.getElementById('closeModal')];

            document.querySelectorAll('.member-card').forEach(card => {
                card.addEventListener('click', function() {
                    const memberId = this.dataset.memberId;
                    const memberData = membersData[memberId];
                    
                    if (!memberData) {
                        alert('Member data not found');
                        return;
                    }
                    
                    // Populate modal with member data
                    document.getElementById('modalMemberName').textContent = 
                        `${memberData.first_name} ${memberData.last_name}`;
                    
                    document.getElementById('modalMemberImage').src = 
                        memberData.passport_picture || 'assets/default-avatar.png';
                    
                    document.getElementById('modalMemberJoinDate').textContent = 
                        formatDate(memberData.join_date);
                    
                    document.getElementById('modalMemberAge').textContent = 
                        calculateAge(memberData.birth_date);
                    
                    document.getElementById('modalMemberBirthday').textContent = 
                        formatDate(memberData.birth_date);
                    
                    document.getElementById('modalMemberEmail').textContent = 
                        memberData.email || 'N/A';
                    
                    document.getElementById('modalMemberPhone').textContent = 
                        memberData.phone || 'N/A';
                    
                    document.getElementById('modalMemberAddress').textContent = 
                        memberData.address || 'N/A';
                    
                    document.getElementById('modalMemberGender').innerHTML = 
                        getGenderIcon(memberData.gender);
                    
                    const badge = document.getElementById('modalMemberBadge');
                    if (memberData.is_staff) {
                        badge.className = 'badge staff';
                        badge.textContent = 'Staff';
                    } else if (memberData.category === 'youth') {
                        badge.className = 'badge youth';
                        badge.textContent = 'Youth';
                    } else {
                        badge.className = 'badge regular';
                        badge.textContent = 'Regular';
                    }
                    badge.style.display = 'inline-block';
                    
                    modal.style.display = 'block';
                });
            });

            // Close modal handlers
            closeBtns.forEach(btn => btn.addEventListener('click', () => modal.style.display = 'none'));
            window.addEventListener('click', (e) => e.target === modal && (modal.style.display = 'none'));
        });
    </script>
</body>
</html>