<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_event'])) {
        // Add new event
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $event_date = sanitize($_POST['event_date']);
        $event_time = sanitize($_POST['event_time']);
        $location = sanitize($_POST['location']);
        $document_file = '';

        // Handle document upload
        if (!empty($_FILES['document_file']['name'])) {
            $doc = handleFileUpload($_FILES['document_file'], ALLOWED_DOC_TYPES, 'event_doc');
            if ($doc['success']) {
                $document_file = $doc['filename'];
            } else {
                $_SESSION['error'] = $doc['message'];
                header("Location: events.php");
                exit;
            }
        }

        $sql = "INSERT INTO events (title, description, event_date, event_time, location, document_file) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $title, $description, $event_date, $event_time, $location, $document_file);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event added successfully!";
        } else {
            $_SESSION['error'] = "Error adding event: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: events.php");
        exit;
    } elseif (isset($_POST['update_event'])) {
        // Update event
        $id = intval($_POST['event_id']);
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $event_date = sanitize($_POST['event_date']);
        $event_time = sanitize($_POST['event_time']);
        $location = sanitize($_POST['location']);
        $document_file = sanitize($_POST['document_file']);

        // Get current document
        $current_doc = $conn->query("SELECT document_file FROM events WHERE id = $id")->fetch_assoc();
        $document_file = $current_doc['document_file'];

        // Handle document upload
        if (!empty($_FILES['document_file']['name'])) {
            // Delete old document if exists
            if ($document_file) {
                deleteFile($document_file);
            }
            
            $doc = handleFileUpload($_FILES['document_file'], ALLOWED_DOC_TYPES, 'event_doc');
            if ($doc['success']) {
                $document_file = $doc['filename'];
            } else {
                $_SESSION['error'] = $doc['message'];
                header("Location: events.php");
                exit;
            }
        }

        $sql = "UPDATE events SET 
                title = ?,
                description = ?,
                event_date = ?,
                event_time = ?,
                location = ?,
                document_file = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $title, $description, $event_date, $event_time, $location, $document_file, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating event: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: events.php");
        exit;
    } elseif (isset($_POST['delete_event'])) {
        // Delete event
        $id = intval($_POST['event_id']);
        
        // Get document to delete
        $event = $conn->query("SELECT document_file FROM events WHERE id = $id")->fetch_assoc();
        if ($event && $event['document_file']) {
            deleteFile($event['document_file']);
        }
        
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting event: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: events.php");
        exit;
    }
}

// Get all events
$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC, event_time DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
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
.btn-delete {
            background: #e74c3c;
            color: black !important;
            
        }
        
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(192, 57, 43, 0.2);
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
                    <li class="active"><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="addashboard.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Events</h1>
                <button id="addEventBtn" class="btn btn-primary">Add New Event</button>
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
                            <th>Title</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="8">No events found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo $event['id']; ?></td>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo htmlspecialchars($event['description']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($event['event_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td>
                                        <?php if ($event['document_file']): ?>
                                            <a href="../uploads/<?php echo $event['document_file']; ?>" target="_blank" class="btn-download">
                                                <i class="fas fa-file-download"></i> Download
                                            </a>
                                        <?php else: ?>
                                            No document
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" data-id="<?php echo $event['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" name="delete_event" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
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

    <!-- Add/Edit Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Event</h2>
            <form id="eventForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="event_id" name="event_id">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="event_date">Event Date</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label for="event_time">Event Time</label>
                    <input type="time" id="event_time" name="event_time" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="document_file">Event Document (PDF, DOC, DOCX)</label>
                    <input type="file" id="document_file" name="document_file" accept=".pdf,.doc,.docx">
                    <small class="file-info" id="doc-file-info"></small>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" name="add_event" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
        // Event modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('eventModal');
            const addBtn = document.getElementById('addEventBtn');
            const closeBtn = document.querySelector('.close');
            const modalTitle = document.getElementById('modalTitle');
            const eventForm = document.getElementById('eventForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Open modal for adding new event
            addBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Add New Event';
                eventForm.reset();
                document.getElementById('event_id').value = '';
                document.getElementById('doc-file-info').textContent = '';
                submitBtn.name = 'add_event';
                submitBtn.textContent = 'Add Event';
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
            
            // Edit event functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-id');
                    
                    // Fetch event data via AJAX
                    fetch(`get_event.php?id=${eventId}`)
                        .then(response => response.json())
                        .then(event => {
                            if (event) {
                                modalTitle.textContent = 'Edit Event';
                                document.getElementById('event_id').value = event.id;
                                document.getElementById('title').value = event.title;
                                document.getElementById('description').value = event.description;
                                document.getElementById('event_date').value = event.event_date;
                                document.getElementById('event_time').value = event.event_time;
                                document.getElementById('location').value = event.location;
                                
                                // Show current document if exists
                                document.getElementById('doc-file-info').textContent = 
                                    event.document_file ? `Current file: ${event.document_file}` : 'No document uploaded';
                                
                                submitBtn.name = 'update_event';
                                submitBtn.textContent = 'Update Event';
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