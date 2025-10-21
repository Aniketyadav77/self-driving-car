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
    <title>Admin Command Center - Zephyr</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="modern-3d.css">
    <style>
        .dashboard-sidebar {
            background: var(--secondary-dark);
            backdrop-filter: blur(20px) saturate(180%);
            border-right: 1px solid var(--glass-border);
            min-height: 100vh;
            position: sticky;
            top: 0;
        }
        
        .dashboard-main {
            background: var(--primary-dark);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .stat-card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0.05;
            transition: opacity 0.3s ease;
        }
        
        .stat-card-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-card-3d:hover::before {
            opacity: 0.1;
        }
        
        .stat-number-3d {
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }
        
        .sidebar-nav-3d {
            padding: 2rem 0;
        }
        
        .nav-item-3d {
            margin-bottom: 0.5rem;
        }
        
        .nav-link-3d {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 15px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .nav-link-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            transition: left 0.3s ease;
            z-index: -1;
        }
        
        .nav-link-3d:hover {
            color: var(--text-primary);
            transform: translateX(10px);
            text-decoration: none;
        }
        
        .nav-link-3d:hover::before {
            left: 0;
        }
        
        .nav-link-3d.active {
            background: var(--glass-bg);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
        }
        
        .table-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
        }
        
        .table-3d .table {
            margin-bottom: 0;
            color: var(--text-primary);
        }
        
        .table-3d .table thead th {
            background: var(--tertiary-dark);
            border: none;
            color: var(--accent-purple);
            font-weight: 600;
        }
        
        .table-3d .table tbody tr {
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }
        
        .table-3d .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.01);
        }
        
        .action-btn-3d {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 10px;
            font-size: 0.8rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin: 0 0.2rem;
        }
        
        .action-btn-3d:hover {
            transform: translateY(-2px) scale(1.1);
        }
        
        .btn-edit {
            background: var(--accent-cyan);
            color: white;
        }
        
        .btn-delete {
            background: var(--accent-pink);
            color: white;
        }
        
        .dashboard-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .brand-3d {
            font-size: 2rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <!-- Modern 3D Sidebar -->
            <div class="col-md-3 dashboard-sidebar">
                <div class="p-4">
                    <div class="text-center mb-4">
                        <div class="brand-3d floating-element">
                            <i class="fas fa-rocket mr-2"></i>ZEPHYR
                        </div>
                        <small class="text-secondary">Command Center</small>
                    </div>
                    
                    <nav class="sidebar-nav-3d">
                        <div class="nav-item-3d">
                            <a class="nav-link-3d active" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-item-3d">
                            <a class="nav-link-3d" href="?view=participants">
                                <i class="fas fa-users mr-3"></i>
                                <span>Participants</span>
                            </a>
                        </div>
                        <div class="nav-item-3d">
                            <a class="nav-link-3d" href="?view=events">
                                <i class="fas fa-calendar mr-3"></i>
                                <span>Events</span>
                            </a>
                        </div>
                        <div class="nav-item-3d">
                            <a class="nav-link-3d" href="?view=reports">
                                <i class="fas fa-chart-bar mr-3"></i>
                                <span>Analytics</span>
                            </a>
                        </div>
                        <div class="nav-item-3d">
                            <a class="nav-link-3d" href="?view=settings">
                                <i class="fas fa-cog mr-3"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                        <hr class="my-4" style="border-color: var(--glass-border);">
                        <div class="nav-item-3d">
                            <a class="nav-link-3d" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
            
            <!-- Main Dashboard Content -->
            <div class="col-md-9 dashboard-main">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="text-gradient mb-2">Welcome back, Admin</h1>
                            <p class="text-secondary mb-0">
                                <i class="fas fa-calendar mr-2"></i>
                                <?php echo date('l, F j, Y'); ?>
                            </p>
                        </div>
                        <div class="floating-element">
                            <i class="fas fa-crown" style="font-size: 3rem; color: var(--accent-orange);"></i>
                        </div>
                    </div>
                </div>
                
                <?php if ($message): ?>
                    <div class="card-3d mb-4">
                        <div class="alert alert-info mb-0 bg-transparent border-0 text-primary">
                            <i class="fas fa-info-circle mr-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- 3D Statistics Cards -->
                <div class="grid-3d grid-cols-4 mb-4">
                    <div class="stat-card-3d floating-element" style="animation-delay: 0s;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-users" style="font-size: 2rem; color: var(--accent-purple);"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary mb-1">Total Participants</h6>
                                <div class="stat-number-3d"><?php echo $stats['total_participants']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-3d floating-element" style="animation-delay: 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-user-check" style="font-size: 2rem; color: var(--accent-cyan);"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary mb-1">Active Users</h6>
                                <div class="stat-number-3d"><?php echo $stats['active_participants']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-3d floating-element" style="animation-delay: 0.4s;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-calendar-plus" style="font-size: 2rem; color: var(--accent-pink);"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary mb-1">Today's Registrations</h6>
                                <div class="stat-number-3d">12</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-3d floating-element" style="animation-delay: 0.6s;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-trophy" style="font-size: 2rem; color: var(--accent-orange);"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary mb-1">Active Events</h6>
                                <div class="stat-number-3d">8</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Registrations -->
                <div class="table-3d">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-gradient mb-0">
                                <i class="fas fa-users mr-2"></i>Recent Registrations
                            </h3>
                            <button class="btn-modern btn-secondary">
                                <span><i class="fas fa-plus mr-2"></i>Add New</span>
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-id-card mr-2"></i>ID</th>
                                        <th><i class="fas fa-user mr-2"></i>Name</th>
                                        <th><i class="fas fa-envelope mr-2"></i>Email</th>
                                        <th><i class="fas fa-phone mr-2"></i>Phone</th>
                                        <th><i class="fas fa-calendar mr-2"></i>Date</th>
                                        <th><i class="fas fa-toggle-on mr-2"></i>Status</th>
                                        <th><i class="fas fa-cogs mr-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $participant): ?>
                                    <tr>
                                        <td class="font-weight-bold text-primary">
                                            <?php echo htmlspecialchars($participant['participant_id'] ?? $participant['id']); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-placeholder bg-gradient-primary rounded-circle mr-2" 
                                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                    <?php echo strtoupper(substr($participant['fname'], 0, 1)); ?>
                                                </div>
                                                <span><?php echo htmlspecialchars($participant['fname'] . ' ' . ($participant['lname'] ?? '')); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-secondary"><?php echo htmlspecialchars($participant['email']); ?></td>
                                        <td class="text-secondary"><?php echo htmlspecialchars($participant['phone'] ?? 'N/A'); ?></td>
                                        <td class="text-secondary"><?php echo date('M j, Y', strtotime($participant['created_at'] ?? '')); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo ($participant['status'] ?? 'active') == 'active' ? 'success' : 'secondary'; ?> px-3 py-2 rounded-pill">
                                                <i class="fas fa-circle mr-1" style="font-size: 0.6rem;"></i>
                                                <?php echo ucfirst($participant['status'] ?? 'active'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="action-btn-3d btn-edit" onclick="editParticipant(<?php echo $participant['id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn-3d btn-delete" onclick="deleteParticipant(<?php echo $participant['id']; ?>)" title="Delete">
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