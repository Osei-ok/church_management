<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_visitor'])) {
        // Add new visitor
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $visit_date = sanitize($_POST['visit_date']);
        $notes = sanitize($_POST['notes']);

        $sql = "INSERT INTO visitors (first_name, last_name, email, phone, visit_date, notes) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $visit_date, $notes);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Visitor added successfully!";
        } else {
            $_SESSION['error'] = "Error adding visitor: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: visitors.php");
        exit;
    } elseif (isset($_POST['update_visitor'])) {
        // Update visitor
        $id = intval($_POST['visitor_id']);
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $visit_date = sanitize($_POST['visit_date']);
        $notes = sanitize($_POST['notes']);

        $sql = "UPDATE visitors SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                visit_date = ?,
                notes = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $visit_date, $notes, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Visitor updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating visitor: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: visitors.php");
        exit;
    } elseif (isset($_POST['delete_visitor'])) {
        // Delete visitor
        $id = intval($_POST['visitor_id']);
        
        $sql = "DELETE FROM visitors WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Visitor deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting visitor: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: visitors.php");
        exit;
    }
}

// Get all visitors
$visitors = [];
$result = $conn->query("SELECT * FROM visitors ORDER BY visit_date DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Visitorsssss</title>
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
.btn-delete {
            background: #e74c3c;
            color: black !important;
            
        }
        
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(192, 57, 43, 0.2);
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
                    <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li class="active"><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Visitors</h1>
                <button id="addVisitorBtn" class="btn btn-primary">Add New Visitor</button>
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
                            <th>Visit Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($visitors)): ?>
                            <tr>
                                <td colspan="6">No visitors found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($visitors as $visitor): ?>
                                <tr>
                                    <td><?php echo $visitor['id']; ?></td>
                                    <td><?php echo htmlspecialchars($visitor['first_name']) . ' ' . htmlspecialchars($visitor['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($visitor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($visitor['phone']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($visitor['visit_date'])); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" data-id="<?php echo $visitor['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="visitor_id" value="<?php echo $visitor['id']; ?>">
                                            <button type="submit" name="delete_visitor" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this visitor?')">Delete</button>
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

    <!-- Add/Edit Visitor Modal -->
    <div id="visitorModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Visitor</h2>
            <form id="visitorForm" method="POST">
                <input type="hidden" id="visitor_id" name="visitor_id">
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
                    <label for="visit_date">Visit Date</label>
                    <input type="date" id="visit_date" name="visit_date" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" name="add_visitor" class="btn btn-primary">Add Visitor</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
        // Visitor modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('visitorModal');
            const addBtn = document.getElementById('addVisitorBtn');
            const closeBtn = document.querySelector('.close');
            const modalTitle = document.getElementById('modalTitle');
            const visitorForm = document.getElementById('visitorForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Open modal for adding new visitor
            addBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Add New Visitor';
                visitorForm.reset();
                document.getElementById('visitor_id').value = '';
                submitBtn.name = 'add_visitor';
                submitBtn.textContent = 'Add Visitor';
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
            
            // Edit visitor functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const visitorId = this.getAttribute('data-id');
                    
                    // Fetch visitor data via AJAX
                    fetch(`get_visitor.php?id=${visitorId}`)
                        .then(response => response.json())
                        .then(visitor => {
                            if (visitor) {
                                modalTitle.textContent = 'Edit Visitor';
                                document.getElementById('visitor_id').value = visitor.id;
                                document.getElementById('first_name').value = visitor.first_name;
                                document.getElementById('last_name').value = visitor.last_name;
                                document.getElementById('email').value = visitor.email;
                                document.getElementById('phone').value = visitor.phone;
                                document.getElementById('visit_date').value = visitor.visit_date;
                                document.getElementById('notes').value = visitor.notes;
                                
                                submitBtn.name = 'update_visitor';
                                submitBtn.textContent = 'Update Visitor';
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