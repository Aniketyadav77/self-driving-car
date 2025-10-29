<?php
include "linc.php";
if (!isset($_SESSION['a'])) {
    header('Location: adminlogin.php');
    exit();
}

// Enhanced dashboard analytics with real-time data and error handling
function safe_query($mysqli, $query) {
    $result = mysqli_query($mysqli, $query);
    return $result ? $result : false;
}

// Website views
$view_query = "SELECT v FROM view WHERE id=1";
$view_result = safe_query($mysqli, $view_query);
$total_views = 0;
if ($view_result && $row = mysqli_fetch_assoc($view_result)) {
    $total_views = $row['v'];
}

// Registration analytics with trend data
$reg_query = "SELECT COUNT(*) as total FROM participants";
$reg_result = safe_query($mysqli, $reg_query);
$total_registrations = $reg_result ? mysqli_fetch_assoc($reg_result)['total'] : 0;

// Today's registrations
$today_reg_query = "SELECT COUNT(*) as today FROM participants WHERE DATE(created_at) = CURDATE()";
$today_reg_result = safe_query($mysqli, $today_reg_query);
$today_registrations = $today_reg_result ? mysqli_fetch_assoc($today_reg_result)['today'] : 0;

// Weekly registrations for chart
$weekly_reg_query = "SELECT DATE(created_at) as date, COUNT(*) as count 
                     FROM participants 
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                     GROUP BY DATE(created_at) 
                     ORDER BY date";
$weekly_reg_result = safe_query($mysqli, $weekly_reg_query);
$weekly_data = [];
while ($weekly_reg_result && $row = mysqli_fetch_assoc($weekly_reg_result)) {
    $weekly_data[] = $row;
}

// Event analytics
$events_query = "SELECT COUNT(*) as total FROM events";
$events_result = safe_query($mysqli, $events_query);
$total_events = $events_result ? mysqli_fetch_assoc($events_result)['total'] : 0;

// Event categories for pie chart
$categories_query = "SELECT category, COUNT(*) as count FROM events GROUP BY category";
$categories_result = safe_query($mysqli, $categories_query);
$event_categories = [];
while ($categories_result && $row = mysqli_fetch_assoc($categories_result)) {
    $event_categories[] = $row;
}

// Participation analytics
$participation_query = "SELECT COUNT(*) as total FROM participation";
$participation_result = safe_query($mysqli, $participation_query);
$total_participation = $participation_result ? mysqli_fetch_assoc($participation_result)['total'] : 0;

// Team participation
$team_participation_query = "SELECT COUNT(DISTINCT t_id) as teams FROM t_participation";
$team_participation_result = safe_query($mysqli, $team_participation_query);
$total_teams = $team_participation_result ? mysqli_fetch_assoc($team_participation_result)['teams'] : 0;

// Volunteers analytics
$volunteers_query = "SELECT COUNT(*) as total FROM volunteers";
$volunteers_result = safe_query($mysqli, $volunteers_query);
$total_volunteers = $volunteers_result ? mysqli_fetch_assoc($volunteers_result)['total'] : 0;

// Sponsors analytics
$sponsors_query = "SELECT COUNT(*) as total, COALESCE(SUM(Amount), 0) as amount FROM sponsor";
$sponsors_result = safe_query($mysqli, $sponsors_query);
$sponsor_data = $sponsors_result ? mysqli_fetch_assoc($sponsors_result) : ['total' => 0, 'amount' => 0];
$total_sponsors = $sponsor_data['total'];
$total_amount = $sponsor_data['amount'];

// Calculate percentages for progress bars
$target_events = 20;
$target_volunteers = 50;
$target_sponsors = 10;
$target_amount = 50000;

$events_progress = $total_events > 0 ? min(($total_events / $target_events) * 100, 100) : 0;
$volunteers_progress = $total_volunteers > 0 ? min(($total_volunteers / $target_volunteers) * 100, 100) : 0;
$sponsors_progress = $total_sponsors > 0 ? min(($total_sponsors / $target_sponsors) * 100, 100) : 0;
$amount_progress = $total_amount > 0 ? min(($total_amount / $target_amount) * 100, 100) : 0;

// Recent activity
$recent_activity_query = "SELECT 'participant' as type, name, created_at FROM participants 
                         UNION ALL 
                         SELECT 'volunteer' as type, name, created_at FROM volunteers 
                         ORDER BY created_at DESC LIMIT 5";
$recent_activity_result = safe_query($mysqli, $recent_activity_query);
$recent_activities = [];
while ($recent_activity_result && $row = mysqli_fetch_assoc($recent_activity_result)) {
    $recent_activities[] = $row;
}
?>

<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Enhanced Dashboard | Zephyr Admin</title>
    <meta name="description" content="Modern festival management dashboard">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/zephyr-logo.JPG">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stat-card-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-card-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .progress-modern {
            height: 10px;
            border-radius: 5px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-bar-modern {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
            transition: width 0.6s ease;
        }
        
        .activity-item {
            padding: 0.75rem;
            border-left: 3px solid var(--primary-color);
            background: #f8f9fa;
            margin-bottom: 0.5rem;
            border-radius: 0 8px 8px 0;
        }
        
        .activity-time {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                text-align: center;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header text-center">
            <div class="container">
                <h1 class="display-4 mb-3">Festival Management Dashboard</h1>
                <p class="lead">Real-time analytics and insights for Zephyr Festival</p>
                <div class="row text-center mt-4">
                    <div class="col-md-3">
                        <h3><?php echo number_format($total_views); ?></h3>
                        <small>Total Views</small>
                    </div>
                    <div class="col-md-3">
                        <h3><?php echo $today_registrations; ?></h3>
                        <small>Today's Registrations</small>
                    </div>
                    <div class="col-md-3">
                        <h3><?php echo $total_events; ?></h3>
                        <small>Active Events</small>
                    </div>
                    <div class="col-md-3">
                        <h3>₹<?php echo number_format($total_amount); ?></h3>
                        <small>Total Sponsorship</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background: var(--success-color);">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="stat-card-number"><?php echo number_format($total_registrations); ?></div>
                    <div class="stat-card-label">Total Registrations</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern" style="width: <?php echo min(($total_registrations/1000)*100, 100); ?>%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background: var(--info-color);">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="stat-card-number"><?php echo $total_events; ?></div>
                    <div class="stat-card-label">Events Created</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern" style="width: <?php echo $events_progress; ?>%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background: var(--warning-color);">
                        <i class="fa fa-handshake-o"></i>
                    </div>
                    <div class="stat-card-number"><?php echo $total_volunteers; ?></div>
                    <div class="stat-card-label">Volunteers</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern" style="width: <?php echo $volunteers_progress; ?>%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background: var(--danger-color);">
                        <i class="fa fa-trophy"></i>
                    </div>
                    <div class="stat-card-number"><?php echo $total_sponsors; ?></div>
                    <div class="stat-card-label">Sponsors</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern" style="width: <?php echo $sponsors_progress; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3">Registration Trends (Last 7 Days)</h5>
                    <canvas id="registrationChart" height="100"></canvas>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3">Event Categories</h5>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">Recent Activity</h5>
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <strong><?php echo htmlspecialchars($activity['name']); ?></strong> 
                                <span class="badge badge-primary"><?php echo ucfirst($activity['type']); ?></span>
                                <div class="activity-time">
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="add-Event.php" class="btn btn-primary btn-block">Add Event</a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="all-Participants.php" class="btn btn-success btn-block">View Participants</a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="take-Volunteer-info.php" class="btn btn-warning btn-block">Add Volunteer</a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="take-Sponsor-info.php" class="btn btn-info btn-block">Add Sponsor</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        // Registration Trends Chart
        const registrationCtx = document.getElementById('registrationChart').getContext('2d');
        const registrationChart = new Chart(registrationCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    $dates = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('M j', strtotime("-$i days"));
                        $dates[] = "'$date'";
                    }
                    echo implode(', ', $dates);
                    ?>
                ],
                datasets: [{
                    label: 'Registrations',
                    data: [
                        <?php
                        $data_points = [];
                        for ($i = 6; $i >= 0; $i--) {
                            $target_date = date('Y-m-d', strtotime("-$i days"));
                            $count = 0;
                            foreach ($weekly_data as $data) {
                                if ($data['date'] == $target_date) {
                                    $count = $data['count'];
                                    break;
                                }
                            }
                            $data_points[] = $count;
                        }
                        echo implode(', ', $data_points);
                        ?>
                    ],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Event Categories Pie Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    $category_labels = [];
                    foreach ($event_categories as $category) {
                        $category_labels[] = "'" . htmlspecialchars($category['category']) . "'";
                    }
                    echo implode(', ', $category_labels);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                        $category_counts = [];
                        foreach ($event_categories as $category) {
                            $category_counts[] = $category['count'];
                        }
                        echo implode(', ', $category_counts);
                        ?>
                    ],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Auto-refresh data every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>