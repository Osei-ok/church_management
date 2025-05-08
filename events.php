<?php
require_once 'config.php';
// require_once 'file_upload_helper.php'; // Include the file where uploadFile is defined

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle event actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
// Add your helper functions here

function uploadFile($inputName, $uploadDir) {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error.");
    }

    $fileName = basename($_FILES[$inputName]['name']);
    $targetPath = $uploadDir . '/' . $fileName;

    // if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
        // throw new Exception("Failed to move uploaded file.");
    // }

    return $targetPath;
}
    
    try {
        $imagePath = uploadFile('image', 'image');
        
        $stmt = $db->prepare("INSERT INTO events (title, description, event_date, event_time, location, image) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $date, $time, $location, $imagePath]);
        
        $_SESSION['message'] = "Event created successfully!";
        header('Location: events.php');
        exit;
    } catch(Exception $e) {
        $error = "Error creating event: " . $e->getMessage();
    }
}

// Handle event deletion
if (isset($_GET['delete'])) {
    $eventId = $_GET['delete'];
    
    try {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        
        $_SESSION['message'] = "Event deleted successfully!";
        header('Location: events.php');
        exit;
    } catch(PDOException $e) {
        $error = "Error deleting event: " . $e->getMessage();
    }
}

// Get all events
 $events = $db->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// include 'header.php';
// include 'sidebar.php';
?>

<div class="main-content">
    <div class="dashboard-header">
        <h2>Manage Events</h2>
        <style>
/* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color:rgba(205, 213, 224, 0.94);
    color: #333;
    line-height: 1.6;
}

.main-content {
    padding: 20px;
    margin-left: 250px; /* Adjust if you have a sidebar */
}

/* Dashboard Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solidrgb(58, 38, 38);
}

.dashboard-header h2 {
    color: #2c3e50;
    font-size: 24px;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-profile span {
    font-weight: 500;
    color: #555;
}

/* Cards */
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
    overflow: hidden;
}

.card-header {
    padding: 15px 20px;
    background: #3498db;
    color: white;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 500;
}

.card-body {
    padding: 20px;
}

/* Forms */
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-col {
    flex: 1;
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

input[type="text"],
input[type="date"],
input[type="time"],
textarea,
input[type="file"] {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="date"]:focus,
input[type="time"]:focus,
textarea:focus {
    outline: none;
    border-color: #3498db;
}

textarea {
    resize: vertical;
    min-height: 80px;
}

/* Buttons */
.btn {
    padding: 8px 15px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.3s;
}

.btn:hover {
    background: #c0392b;
}

.submit-btn {
    padding: 10px 20px;
    background: #2ecc71;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: background 0.3s;
}

.submit-btn:hover {
    background: #27ae60;
}

/* Alerts */
.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    font-size: 14px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Data Table */
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.data-table tr:hover {
    background: #f8f9fa;
}

/* Action Buttons */
.action-btn {
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    margin-right: 5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.edit-btn {
    background: #3498db;
    color: white;
}

.edit-btn:hover {
    background: #2980b9;
}

.delete-btn {
    background: #e74c3c;
    color: white;
}

.delete-btn:hover {
    background: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
}

        </style>
        <div class="user-profile">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="addashboard.php" class="btn">Logout</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h3>Create New Event</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-col">
                        <label for="title">Event Title*</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-col">
                        <label for="date">Date*</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <label for="time">Time*</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                    <div class="form-col">
                        <label for="location">Location*</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <label for="image">Event Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">Create Event</button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Upcoming Events</h3>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <!-- <?php foreach ($events as $event): ?> -->
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($event['event_date'])); ?></td>
                        <td><?php echo date('g:i a', strtotime($event['event_time'])); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="events.php?delete=<?php echo $event['id']; ?>" class="action-btn delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this event?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- <?php include 'footer.php'; ?> -->