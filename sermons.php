<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
requireAdminLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_sermon'])) {
        // Add new sermon
        $title = sanitize($_POST['title']);
        $preacher = sanitize($_POST['preacher']);
        $sermon_date = sanitize($_POST['sermon_date']);
        $description = sanitize($_POST['description']);
        
        // Handle file uploads
        $audio_file = '';
        $video_file = '';
        $document_file = '';
        
        if (!empty($_FILES['audio_file']['name'])) {
            $audio = handleFileUpload($_FILES['audio_file'], ALLOWED_AUDIO_TYPES, 'audio');
            if ($audio['success']) {
                $audio_file = $audio['filename'];
            } else {
                $_SESSION['error'] = $audio['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        if (!empty($_FILES['video_file']['name'])) {
            $video = handleFileUpload($_FILES['video_file'], ALLOWED_VIDEO_TYPES, 'video');
            if ($video['success']) {
                $video_file = $video['filename'];
            } else {
                $_SESSION['error'] = $video['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        if (!empty($_FILES['document_file']['name'])) {
            $doc = handleFileUpload($_FILES['document_file'], ALLOWED_DOC_TYPES, 'doc');
            if ($doc['success']) {
                $document_file = $doc['filename'];
            } else {
                $_SESSION['error'] = $doc['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        $sql = "INSERT INTO sermons (title, preacher, sermon_date, description, audio_file, video_file, document_file) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $title, $preacher, $sermon_date, $description, $audio_file, $video_file, $document_file);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Sermon added successfully!";
        } else {
            $_SESSION['error'] = "Error adding sermon: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: sermons.php");
        exit;
    } elseif (isset($_POST['update_sermon'])) {
        // Update sermon
        $id = intval($_POST['sermon_id']);
        $title = sanitize($_POST['title']);
        $preacher = sanitize($_POST['preacher']);
        $sermon_date = sanitize($_POST['sermon_date']);
        $description = sanitize($_POST['description']);
        
        // Get current files
        $current_files = $conn->query("SELECT audio_file, video_file, document_file FROM sermons WHERE id = $id")->fetch_assoc();
        $audio_file = $current_files['audio_file'];
        $video_file = $current_files['video_file'];
        $document_file = $current_files['document_file'];
        
        // Handle file uploads
        if (!empty($_FILES['audio_file']['name'])) {
            deleteFile($audio_file);
            $audio = handleFileUpload($_FILES['audio_file'], ALLOWED_AUDIO_TYPES, 'audio');
            if ($audio['success']) {
                $audio_file = $audio['filename'];
            } else {
                $_SESSION['error'] = $audio['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        if (!empty($_FILES['video_file']['name'])) {
            deleteFile($video_file);
            $video = handleFileUpload($_FILES['video_file'], ALLOWED_VIDEO_TYPES, 'video');
            if ($video['success']) {
                $video_file = $video['filename'];
            } else {
                $_SESSION['error'] = $video['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        if (!empty($_FILES['document_file']['name'])) {
            deleteFile($document_file);
            $doc = handleFileUpload($_FILES['document_file'], ALLOWED_DOC_TYPES, 'doc');
            if ($doc['success']) {
                $document_file = $doc['filename'];
            } else {
                $_SESSION['error'] = $doc['message'];
                header("Location: sermons.php");
                exit;
            }
        }
        
        $sql = "UPDATE sermons SET 
                title = ?,
                preacher = ?,
                sermon_date = ?,
                description = ?,
                audio_file = ?,
                video_file = ?,
                document_file = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $title, $preacher, $sermon_date, $description, $audio_file, $video_file, $document_file, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Sermon updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating sermon: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: sermons.php");
        exit;
    } elseif (isset($_POST['delete_sermon'])) {
        // Delete sermon
        $id = intval($_POST['sermon_id']);
        
        // Get files to delete
        $sermon = $conn->query("SELECT audio_file, video_file, document_file FROM sermons WHERE id = $id")->fetch_assoc();
        
        if ($sermon) {
            deleteFile($sermon['audio_file']);
            deleteFile($sermon['video_file']);
            deleteFile($sermon['document_file']);
        }
        
        $sql = "DELETE FROM sermons WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Sermon deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting sermon: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: sermons.php");
        exit;
    }
}

// Get all sermons
$sermons = [];
$result = $conn->query("SELECT * FROM sermons ORDER BY sermon_date DESC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sermons[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sermons -Church of christ</title>
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
                    <li><a href="adevents.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="adfinance.php"><i class="fas fa-hand-holding-usd"></i> Finance</a></li>
                    <li><a href="advisitors.php"><i class="fas fa-user-plus"></i> Visitors</a></li>
                    <li class="active"><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
                    <li><a href="addashboard.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Manage Sermons</h1>
                <button id="addSermonBtn" class="btn btn-primary">Add New Sermon</button>
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
                            <th>Preacher</th>
                            <th>Date</th>
                            <th>Audio</th>
                            <th>Video</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sermons)): ?>
                            <tr>
                                <td colspan="8">No sermons found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sermons as $sermon): ?>
                                <tr>
                                    <td><?php echo $sermon['id']; ?></td>
                                    <td><?php echo htmlspecialchars($sermon['title']); ?></td>
                                    <td><?php echo htmlspecialchars($sermon['preacher']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($sermon['sermon_date'])); ?></td>
                                    <td>
                                        <?php if ($sermon['audio_file']): ?>
                                            <a href="../uploads/<?php echo $sermon['audio_file']; ?>" target="_blank">Listen</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($sermon['video_file']): ?>
                                            <a href="../uploads/<?php echo $sermon['video_file']; ?>" target="_blank">Watch</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($sermon['document_file']): ?>
                                            <a href="get_sermon<?php echo $sermon['document_file']; ?>" target="_blank">Download</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" data-id="<?php echo $sermon['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="sermon_id" value="<?php echo $sermon['id']; ?>">
                                            <button type="submit" name="delete_sermon" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this sermon?')">Delete</button>
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

    <!-- Add/Edit Sermon Modal -->
    <div id="sermonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Sermon</h2>
            <form id="sermonForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="sermon_id" name="sermon_id">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="preacher">Preacher</label>
                    <input type="text" id="preacher" name="preacher" required>
                </div>
                <div class="form-group">
                    <label for="sermon_date">Sermon Date</label>
                    <input type="date" id="sermon_date" name="sermon_date" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="audio_file">Audio File (Max 100MB)</label>
                    <input type="file" id="audio_file" name="audio_file" accept="audio/*">
                    <small class="file-info" id="audio-file-info"></small>
                </div>
                <div class="form-group">
                    <label for="video_file">Video File (Max 800MB)</label>
                    <input type="file" id="video_file" name="video_file" accept="video/*">
                    <small class="file-info" id="video-file-info"></small>
                </div>
                <div class="form-group">
                    <label for="document_file">Document File (Max 20MB)</label>
                    <input type="file" id="document_file" name="document_file" accept=".pdf,.doc,.docx">
                    <small class="file-info" id="doc-file-info"></small>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" name="add_sermon" class="btn btn-primary">Add Sermon</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
        // Sermon modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('sermonModal');
            const addBtn = document.getElementById('addSermonBtn');
            const closeBtn = document.querySelector('.close');
            const modalTitle = document.getElementById('modalTitle');
            const sermonForm = document.getElementById('sermonForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Open modal for adding new sermon
            addBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Add New Sermon';
                sermonForm.reset();
                document.getElementById('sermon_id').value = '';
                document.getElementById('audio-file-info').textContent = '';
                document.getElementById('video-file-info').textContent = '';
                document.getElementById('doc-file-info').textContent = '';
                submitBtn.name = 'add_sermon';
                submitBtn.textContent = 'Add Sermon';
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
            
            // Edit sermon functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sermonId = this.getAttribute('data-id');
                    
                    // Fetch sermon data via AJAX
                    fetch(`get_sermon.php?id=${sermonId}`)
                        .then(response => response.json())
                        .then(sermon => {
                            if (sermon) {
                                modalTitle.textContent = 'Edit Sermon';
                                document.getElementById('sermon_id').value = sermon.id;
                                document.getElementById('title').value = sermon.title;
                                document.getElementById('preacher').value = sermon.preacher;
                                document.getElementById('sermon_date').value = sermon.sermon_date;
                                document.getElementById('description').value = sermon.description;
                                
                                // Show current files
                                document.getElementById('audio-file-info').textContent = sermon.audio_file ? 
                                    `Current file: ${sermon.audio_file}` : 'No audio file uploaded';
                                document.getElementById('video-file-info').textContent = sermon.video_file ? 
                                    `Current file: ${sermon.video_file}` : 'No video file uploaded';
                                document.getElementById('doc-file-info').textContent = sermon.document_file ? 
                                    `Current file: ${sermon.document_file}` : 'No document file uploaded';
                                
                                submitBtn.name = 'update_sermon';
                                submitBtn.textContent = 'Update Sermon';
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