<?php
include "config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Handle admin actions
$action = $_GET['action'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = "Invalid request";
    } else {
        switch ($action) {
            case 'delete_participant':
                $participant_id = sanitize_input($_POST['participant_id']);
                $stmt = $mysqli->prepare("DELETE FROM participants WHERE id = ?");
                $stmt->bind_param("i", $participant_id);
                if ($stmt->execute()) {
                    $message = "Participant deleted successfully";
                } else {
                    $message = "Error deleting participant";
                }
                break;
                
            case 'update_status':
                $participant_id = sanitize_input($_POST['participant_id']);
                $status = sanitize_input($_POST['status']);
                $stmt = $mysqli->prepare("UPDATE participants SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $participant_id);
                if ($stmt->execute()) {
                    $message = "Status updated successfully";
                } else {
                    $message = "Error updating status";
                }
                break;
        }
    }
}

// Get statistics
$stats = [];
$result = $mysqli->query("SELECT COUNT(*) as total FROM participants");
$stats['total_participants'] = $result->fetch_assoc()['total'];

$result = $mysqli->query("SELECT COUNT(*) as active FROM participants WHERE status = 'active'");
$stats['active_participants'] = $result->fetch_assoc()['active'];

// Get recent registrations
$recent_registrations = [];
$result = $mysqli->query("SELECT * FROM participants ORDER BY created_at DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $recent_registrations[] = $row;
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zephyr</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar p-4">
                <h3>Zephyr Admin</h3>
                <hr>
                <nav class="nav flex-column">
                    <a class="nav-link text-white" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link text-white" href="?view=participants"><i class="fas fa-users"></i> Participants</a>
                    <a class="nav-link text-white" href="?view=events"><i class="fas fa-calendar"></i> Events</a>
                    <a class="nav-link text-white" href="?view=reports"><i class="fas fa-chart-bar"></i> Reports</a>
                    <a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 main-content p-4">
                <h2>Dashboard</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h5>Total Participants</h5>
                            <div class="stat-number"><?php echo $stats['total_participants']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h5>Active Participants</h5>
                            <div class="stat-number"><?php echo $stats['active_participants']; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Registrations -->
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Registrations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $participant): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($participant['participant_id'] ?? $participant['id']); ?></td>
                                        <td><?php echo htmlspecialchars($participant['fname'] . ' ' . ($participant['lname'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($participant['email']); ?></td>
                                        <td><?php echo htmlspecialchars($participant['phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($participant['created_at'] ?? '')); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo ($participant['status'] ?? 'active') == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo htmlspecialchars($participant['status'] ?? 'active'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden forms for actions -->
    <form id="deleteForm" method="post" action="?action=delete_participant" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="participant_id" id="deleteParticipantId">
    </form>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function deleteParticipant(id) {
            if (confirm('Are you sure you want to delete this participant?')) {
                document.getElementById('deleteParticipantId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function editParticipant(id) {
            // Implement edit functionality
            alert('Edit functionality will be implemented');
        }
    </script>
</body>
</html>