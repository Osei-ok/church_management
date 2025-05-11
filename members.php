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
        $gender = sanitize($_POST['gender']);
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $join_date = sanitize($_POST['join_date']);
        $birth_date = sanitize($_POST['birth_date']);
        
        // Handle file upload
        $passport_picture = '';
        if (isset($_FILES['passport_picture']) && $_FILES['passport_picture']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/passport_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['passport_picture']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('passport_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['passport_picture']['tmp_name'], $file_path)) {
                $passport_picture = $file_path;
            }
        }

        $sql = "INSERT INTO members (first_name, last_name, email, phone, address, gender, passport_picture, is_staff, join_date, birth_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssiss", $first_name, $last_name, $email, $phone, $address, $gender, $passport_picture, $is_staff, $join_date, $birth_date);
        
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
        $gender = sanitize($_POST['gender']);
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $join_date = sanitize($_POST['join_date']);
        $birth_date = sanitize($_POST['birth_date']);
        
        // Handle file upload if a new file is provided
        $passport_picture = sanitize($_POST['existing_passport_picture']);
        if (isset($_FILES['passport_picture']) && $_FILES['passport_picture']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/passport_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Delete old picture if it exists
            if (!empty($passport_picture) && file_exists($passport_picture)) {
                unlink($passport_picture);
            }
            
            $file_ext = pathinfo($_FILES['passport_picture']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('passport_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['passport_picture']['tmp_name'], $file_path)) {
                $passport_picture = $file_path;
            }
        }

        $sql = "UPDATE members SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                address = ?,
                gender = ?,
                passport_picture = ?,
                is_staff = ?,
                join_date = ?,
                birth_date = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssissi", $first_name, $last_name, $email, $phone, $address, $gender, $passport_picture, $is_staff, $join_date, $birth_date, $id);
        
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
        
        // First get the passport picture path to delete the file
        $sql = "SELECT passport_picture FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        $stmt->close();
        
        // Delete the passport picture file if it exists
        if (!empty($member['passport_picture']) && file_exists($member['passport_picture'])) {
            unlink($member['passport_picture']);
        }
        
        // Now delete the member record
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

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 700px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="date"],
.form-group input[type="password"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
}

.form-group.checkbox {
    display: flex;
    align-items: center;
}

.form-group.checkbox input {
    margin-right: 10px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Table Styles */
.data-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.data-table th,
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.data-table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 500;
}

.data-table tr:hover {
    background-color: #f5f5f5;
}

.data-table img {
    border-radius: 4px;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.85rem;
}

.btn-edit {
    background-color: var(--accent-color);
    color: white;
}

.btn-edit:hover {
    background-color: #3da8d8;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(61, 168, 216, 0.2);
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(192, 57, 43, 0.2);
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
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
                            <th>Gender</th>
                            <th>Picture</th>
                            <th>Staff</th>
                            <th>Birth Date</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="10">No members found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo $member['id']; ?></td>
                                    <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($member['gender']); ?></td>
                                    <td>
                                        <?php if (!empty($member['passport_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($member['passport_picture']); ?>" style="max-width: 50px; max-height: 50px;">
                                        <?php endif; ?>
                                    </td>
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
            <form id="memberForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="member_id" name="member_id">
                <input type="hidden" id="existing_passport_picture" name="existing_passport_picture" value="">
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
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="passport_picture">Passport Picture</label>
                    <input type="file" id="passport_picture" name="passport_picture" accept="image/*">
                    <div id="passport_preview" style="margin-top: 10px;"></div>
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
                document.getElementById('existing_passport_picture').value = '';
                document.getElementById('passport_preview').innerHTML = '';
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
                                document.getElementById('gender').value = member.gender;
                                document.getElementById('birth_date').value = member.birth_date;
                                document.getElementById('join_date').value = member.join_date;
                                document.getElementById('is_staff').checked = member.is_staff == 1;
                                
                                if (member.passport_picture) {
                                    document.getElementById('existing_passport_picture').value = member.passport_picture;
                                    document.getElementById('passport_preview').innerHTML = `<img src="${member.passport_picture}" style="max-width: 150px; max-height: 150px;">`;
                                } else {
                                    document.getElementById('existing_passport_picture').value = '';
                                    document.getElementById('passport_preview').innerHTML = '';
                                }
                                
                                submitBtn.name = 'update_member';
                                submitBtn.textContent = 'Update Member';
                                modal.style.display = 'block';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });

            // Image preview functionality
            document.getElementById('passport_picture').addEventListener('change', function(e) {
                const preview = document.getElementById('passport_preview');
                preview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '150px';
                        img.style.maxHeight = '150px';
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
</body>
</html>