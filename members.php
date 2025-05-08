<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_member'])) {
        // Add new member
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $join_date = sanitize($_POST['join_date']);

        $sql = "INSERT INTO members (first_name, last_name, email, phone, address, is_staff, join_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssis", $first_name, $last_name, $email, $phone, $address, $is_staff, $join_date);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Member added successfully!";
        } else {
            $_SESSION['error'] = "Error adding member: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: umembers.php");
        exit;
    } elseif (isset($_POST['update_member'])) {
        // Update member
        $id = intval($_POST['member_id']);
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $join_date = sanitize($_POST['join_date']);

        $sql = "UPDATE members SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                address = ?,
                is_staff = ?,
                join_date = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssisi", $first_name, $last_name, $email, $phone, $address, $is_staff, $join_date, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Member updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating member: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: members.php");
        exit;
    } elseif (isset($_POST['delete_member'])) {
        // Delete member
        $id = intval($_POST['member_id']);
        
        $sql = "DELETE FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Member deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting member: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: umembers.php");
        exit;
    }
}

// Get all members
$members = [];
$result = $conn->query("SELECT * FROM members ORDER BY last_name, first_name");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Church of Christ</title>
    <style>
        /* admin.css */
:root {
    --primary-color: #4a6fa5;
    --secondary-color: #166088;
    --accent-color: #4fc3f7;
    --light-color:rgba(241, 230, 230, 0.98);
    --dark-color:rgb(26, 22, 22);
    --success-color: #28a745;
    --warning-color:rgb(202, 185, 33);
    --danger-color:rgb(26, 3, 5);
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
    background-color:rgb(87, 103, 128);
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
.btn-delete {
            background: #e74c3c;
            color: black !important;
            
        }
        
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(192, 57, 43, 0.2);
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
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo $_SESSION['admin_username']; ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="addashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="addashboard.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Members</h1>
                <button id="addMemberBtn" class="btn btn-primary">Add New Member</button>
            </header>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Staff</th>
                            <th>Birth Date</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="7">No members found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo $member['id']; ?></td>
                                    <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                    <td><?php echo $member['is_staff'] ? 'Yes' : 'No'; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($member['birth_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($member['join_date'])); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" data-id="<?php echo $member['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                            <button type="submit" name="delete_member" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this member?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Member Modal -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Member</h2>
            <form id="memberForm" method="POST">
                <input type="hidden" id="member_id" name="member_id">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="birth_date">Birth Date</label>
                    <input type="date" id="birth_date" name="birth_date" required>
                </div>
                <div class="form-group">
                    <label for="join_date">Join Date</label>
                    <input type="date" id="join_date" name="join_date" required>
                </div>
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_staff" name="is_staff">
                    <label for="is_staff">Is Staff Member</label>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" name="add_member" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
        // Member modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('memberModal');
            const addBtn = document.getElementById('addMemberBtn');
            const closeBtn = document.querySelector('.close');
            const modalTitle = document.getElementById('modalTitle');
            const memberForm = document.getElementById('memberForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Open modal for adding new member
            addBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Add New Member';
                memberForm.reset();
                document.getElementById('member_id').value = '';
                submitBtn.name = 'add_member';
                submitBtn.textContent = 'Add Member';
                modal.style.display = 'block';
            });
            
            // Close modal
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Edit member functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const memberId = this.getAttribute('data-id');
                    
                    // Fetch member data via AJAX
                    fetch(`get_member.php?id=${memberId}`)
                        .then(response => response.json())
                        .then(member => {
                            if (member) {
                                modalTitle.textContent = 'Edit Member';
                                document.getElementById('member_id').value = member.id;
                                document.getElementById('first_name').value = member.first_name;
                                document.getElementById('last_name').value = member.last_name;
                                document.getElementById('email').value = member.email;
                                document.getElementById('phone').value = member.phone;
                                document.getElementById('address').value = member.address;
                                document.getElementById('birth_date').value = member.birth_date;
                                document.getElementById('join_date').value = member.join_date;
                                document.getElementById('is_staff').checked = member.is_staff == 1;
                                
                                submitBtn.name = 'update_member';
                                submitBtn.textContent = 'Update Member';
                                modal.style.display = 'block';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>
</html>