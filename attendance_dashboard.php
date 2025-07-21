<?php
require_once 'config.php';
require_once 'auth.php';
requireAdminLogin();

// Handle AJAX for chart data
if (isset($_GET['chart_data'])) {
    header('Content-Type: application/json');

    $dateTotals = [];
    $result = $conn->query("SELECT attendance_date, COUNT(*) as total FROM attendance GROUP BY attendance_date ORDER BY attendance_date");
    while ($row = $result->fetch_assoc()) {
        $dateTotals[$row['attendance_date']] = $row['total'];
    }

    $memberTotals = [];
    $memberNames = [];
    $result2 = $conn->query("SELECT m.first_name, m.last_name, COUNT(a.id) as total 
                             FROM members m
                             LEFT JOIN attendance a ON m.id = a.member_id 
                             GROUP BY m.id 
                             ORDER BY total DESC LIMIT 20"); // Limit to top 20 for better chart readability
    while ($row = $result2->fetch_assoc()) {
        $memberNames[] = $row['first_name'] . ' ' . $row['last_name'];
        $memberTotals[] = $row['total'];
    }

    echo json_encode([
        'attendance_dates' => array_keys($dateTotals),
        'daily_totals' => array_values($dateTotals),
        'member_names' => $memberNames,
        'member_totals' => $memberTotals
    ]);
    exit;
}

// Handle attendance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_date'])) {
    $attendance_date = $_POST['attendance_date'];
    $present_members = isset($_POST['present']) ? $_POST['present'] : [];

    $conn->query("DELETE FROM attendance WHERE attendance_date = '$attendance_date'");

    foreach ($present_members as $member_id) {
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, attendance_date) VALUES (?, ?)");
        $stmt->bind_param("is", $member_id, $attendance_date);
        $stmt->execute();
        $stmt->close();
    }
    
    // Show success message
    $_SESSION['success_message'] = "Attendance updated successfully!";
    header("Location: attendance_dashboard.php");
    exit;
}

// Load members and attendance
$members = $conn->query("SELECT * FROM members ORDER BY first_name ASC");
$attendance_date = date('Y-m-d');
$attendance_records = [];

$checkQuery = $conn->prepare("SELECT member_id FROM attendance WHERE attendance_date = ?");
$checkQuery->bind_param("s", $attendance_date);
$checkQuery->execute();
$result = $checkQuery->get_result();
while ($row = $result->fetch_assoc()) {
    $attendance_records[] = $row['member_id'];
}
$checkQuery->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #eef2ff;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --info: #4895ef;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --white: #ffffff;
      --border-radius: 8px;
      --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fb;
      color: var(--dark);
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .header h1 {
      color: var(--primary);
      font-weight: 600;
    }

    .date-display {
      background: var(--primary-light);
      color: var(--primary);
      padding: 8px 16px;
      border-radius: var(--border-radius);
      font-weight: 500;
    }

    .card {
      background: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      padding: 25px;
      margin-bottom: 30px;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--dark);
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    th {
      background-color: var(--primary-light);
      color: var(--primary);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
    }

    tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .checkbox-input {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: var(--primary);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 10px 20px;
      border-radius: var(--border-radius);
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      font-size: 0.9rem;
    }

    .btn-primary {
      background: var(--primary);
      color: var(--white);
    }

    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }

    .btn i {
      margin-right: 8px;
    }

    .text-center {
      text-align: center;
    }

    .chart-container {
      position: relative;
      height: 300px;
      margin-bottom: 30px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 30px;
    }

    .alert {
      padding: 15px;
      border-radius: var(--border-radius);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .alert-success {
      background-color: rgba(76, 201, 240, 0.2);
      border-left: 4px solid var(--success);
      color: #0c5460;
    }

    .alert i {
      margin-right: 10px;
    }

    .search-box {
      margin-bottom: 20px;
    }

    .search-input {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid rgba(0,0,0,0.1);
      border-radius: var(--border-radius);
      font-size: 0.9rem;
      transition: var(--transition);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    @media (max-width: 768px) {
      .grid {
        grid-template-columns: 1fr;
      }
      
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-calendar-check"></i> Attendance Dashboard</h1>
      <div class="date-display">
        <i class="fas fa-calendar-day"></i> <?php echo date("l, F j, Y"); ?>
      </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-check"></i> Mark Today's Attendance</h2>
      </div>
      <div class="search-box">
        <input type="text" class="search-input" placeholder="Search members..." id="memberSearch">
      </div>
      <div class="table-responsive">
        <form method="POST" action="">
          <input type="hidden" name="attendance_date" value="<?php echo $attendance_date; ?>">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Member Name</th>
                <th class="text-center">Present</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 1;
              $members->data_seek(0); // Reset pointer to beginning
              while ($member = $members->fetch_assoc()):
                  $is_checked = in_array($member['id'], $attendance_records);
              ?>
              <tr class="member-row">
                <td><?php echo $count++; ?></td>
                <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                <td class="text-center">
                  <div class="checkbox-container">
                    <input type="checkbox" class="checkbox-input" name="present[]" value="<?php echo $member['id']; ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
          <div class="text-center" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Attendance</button>
          </div>
        </form>
      </div>
    </div>

    <div class="grid">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title"><i class="fas fa-chart-line"></i> Daily Attendance</h2>
        </div>
        <div class="chart-container">
          <canvas id="attendanceByDateChart"></canvas>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h2 class="card-title"><i class="fas fa-chart-bar"></i> Top Attendees</h2>
        </div>
        <div class="chart-container">
          <canvas id="attendancePerMemberChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Search functionality
    document.getElementById('memberSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('.member-row');
      
      rows.forEach(row => {
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Load chart data
    fetch('attendance_dashboard.php?chart_data=1')
      .then(res => res.json())
      .then(data => {
        // Daily Attendance Chart
        new Chart(document.getElementById('attendanceByDateChart'), {
          type: 'line',
          data: {
            labels: data.attendance_dates,
            datasets: [{
              label: 'Total Present',
              data: data.daily_totals,
              borderColor: 'var(--primary)',
              backgroundColor: 'rgba(67, 97, 238, 0.1)',
              fill: true,
              tension: 0.3,
              borderWidth: 2,
              pointBackgroundColor: 'var(--white)',
              pointBorderColor: 'var(--primary)',
              pointBorderWidth: 2,
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                backgroundColor: 'var(--dark)',
                titleFont: {
                  weight: 'bold'
                },
                bodyFont: {
                  size: 14
                },
                padding: 12,
                cornerRadius: 8
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  color: 'rgba(0,0,0,0.05)'
                }
              },
              x: {
                grid: {
                  display: false
                }
              }
            }
          }
        });

        // Top Attendees Chart
        new Chart(document.getElementById('attendancePerMemberChart'), {
          type: 'bar',
          data: {
            labels: data.member_names,
            datasets: [{
              label: 'Attendance Count',
              data: data.member_totals,
              backgroundColor: 'var(--info)',
              borderRadius: 4,
              borderSkipped: false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                backgroundColor: 'var(--dark)',
                titleFont: {
                  weight: 'bold'
                },
                bodyFont: {
                  size: 14
                },
                padding: 12,
                cornerRadius: 8,
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  color: 'rgba(0,0,0,0.05)'
                }
              },
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  autoSkip: false,
                  maxRotation: 45,
                  minRotation: 45
                }
              }
            }
          }
        });
      });
  </script>
</body>
</html>