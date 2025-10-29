<?php
session_start();
include "linc.php";

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: alogin.php');
    exit();
}

// Handle export requests
if ($_GET && isset($_GET['action'])) {
    $action = $_GET['action'];
    $format = $_GET['format'] ?? 'csv';
    
    switch ($action) {
        case 'participants':
            exportParticipants($mysqli, $format);
            break;
        case 'events':
            exportEvents($mysqli, $format);
            break;
        case 'registrations':
            exportRegistrations($mysqli, $format);
            break;
        case 'event_participants':
            $event_id = intval($_GET['event_id']);
            exportEventParticipants($mysqli, $event_id, $format);
            break;
        case 'attendance_report':
            $event_id = intval($_GET['event_id']);
            exportAttendanceReport($mysqli, $event_id, $format);
            break;
        case 'revenue_report':
            exportRevenueReport($mysqli, $format);
            break;
    }
    exit();
}

function exportParticipants($mysqli, $format) {
    $query = "SELECT id, name, email, phone, college, year, created_at FROM participants ORDER BY created_at DESC";
    $result = mysqli_query($mysqli, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, 'participants_' . date('Y-m-d'), [
            'ID', 'Name', 'Email', 'Phone', 'College', 'Year', 'Registration Date'
        ]);
    } else {
        exportToPDF($data, 'Participants Report', 'participants_' . date('Y-m-d'), [
            'ID', 'Name', 'Email', 'Phone', 'College', 'Year', 'Registration Date'
        ]);
    }
}

function exportEvents($mysqli, $format) {
    $query = "SELECT id, name, description, event_date, venue, max_participants, 
              (SELECT COUNT(*) FROM participation WHERE event_id = events.id) as registered_count,
              created_at FROM events ORDER BY event_date DESC";
    $result = mysqli_query($mysqli, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, 'events_' . date('Y-m-d'), [
            'ID', 'Event Name', 'Description', 'Event Date', 'Venue', 'Max Participants', 'Registered Count', 'Created Date'
        ]);
    } else {
        exportToPDF($data, 'Events Report', 'events_' . date('Y-m-d'), [
            'ID', 'Event Name', 'Description', 'Event Date', 'Venue', 'Max Participants', 'Registered Count', 'Created Date'
        ]);
    }
}

function exportRegistrations($mysqli, $format) {
    $query = "SELECT p.id as registration_id, pt.name as participant_name, pt.email, 
              e.name as event_name, p.registration_date, pt.college, pt.year
              FROM participation p 
              JOIN participants pt ON p.participant_id = pt.id 
              JOIN events e ON p.event_id = e.id 
              ORDER BY p.registration_date DESC";
    $result = mysqli_query($mysqli, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, 'registrations_' . date('Y-m-d'), [
            'Registration ID', 'Participant Name', 'Email', 'Event Name', 'Registration Date', 'College', 'Year'
        ]);
    } else {
        exportToPDF($data, 'Registrations Report', 'registrations_' . date('Y-m-d'), [
            'Registration ID', 'Participant Name', 'Email', 'Event Name', 'Registration Date', 'College', 'Year'
        ]);
    }
}

function exportEventParticipants($mysqli, $event_id, $format) {
    $query = "SELECT pt.id, pt.name, pt.email, pt.phone, pt.college, pt.year, p.registration_date
              FROM participation p 
              JOIN participants pt ON p.participant_id = pt.id 
              WHERE p.event_id = $event_id 
              ORDER BY p.registration_date ASC";
    $result = mysqli_query($mysqli, $query);
    
    // Get event name
    $event_query = "SELECT name FROM events WHERE id = $event_id";
    $event_result = mysqli_query($mysqli, $event_query);
    $event_name = mysqli_fetch_assoc($event_result)['name'];
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, 'event_participants_' . $event_id . '_' . date('Y-m-d'), [
            'Participant ID', 'Name', 'Email', 'Phone', 'College', 'Year', 'Registration Date'
        ]);
    } else {
        exportToPDF($data, 'Participants for ' . $event_name, 'event_participants_' . $event_id . '_' . date('Y-m-d'), [
            'Participant ID', 'Name', 'Email', 'Phone', 'College', 'Year', 'Registration Date'
        ]);
    }
}

function exportAttendanceReport($mysqli, $event_id, $format) {
    $query = "SELECT pt.id, pt.name, pt.email, pt.college, pt.year, p.registration_date,
              CASE WHEN a.id IS NOT NULL THEN 'Present' ELSE 'Absent' END as attendance_status
              FROM participation p 
              JOIN participants pt ON p.participant_id = pt.id 
              LEFT JOIN attendance a ON p.participant_id = a.participant_id AND p.event_id = a.event_id
              WHERE p.event_id = $event_id 
              ORDER BY pt.name ASC";
    $result = mysqli_query($mysqli, $query);
    
    // Get event name
    $event_query = "SELECT name FROM events WHERE id = $event_id";
    $event_result = mysqli_query($mysqli, $event_query);
    $event_name = mysqli_fetch_assoc($event_result)['name'];
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, 'attendance_' . $event_id . '_' . date('Y-m-d'), [
            'Participant ID', 'Name', 'Email', 'College', 'Year', 'Registration Date', 'Attendance Status'
        ]);
    } else {
        exportToPDF($data, 'Attendance Report for ' . $event_name, 'attendance_' . $event_id . '_' . date('Y-m-d'), [
            'Participant ID', 'Name', 'Email', 'College', 'Year', 'Registration Date', 'Attendance Status'
        ]);
    }
}

function exportRevenueReport($mysqli, $format) {
    $query = "SELECT e.id, e.name as event_name, e.event_date, e.registration_fee,
              COUNT(p.id) as total_registrations,
              (e.registration_fee * COUNT(p.id)) as total_revenue
              FROM events e 
              LEFT JOIN participation p ON e.id = p.event_id
              GROUP BY e.id 
              ORDER BY total_revenue DESC";
    $result = mysqli_query($mysqli, $query);
    
    $data = [];
    $grand_total = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $grand_total += $row['total_revenue'];
        $data[] = $row;
    }
    
    // Add grand total row
    $data[] = [
        'id' => '',
        'event_name' => 'GRAND TOTAL',
        'event_date' => '',
        'registration_fee' => '',
        'total_registrations' => '',
        'total_revenue' => $grand_total
    ];
    
    if ($format === 'csv') {
        exportToCSV($data, 'revenue_report_' . date('Y-m-d'), [
            'Event ID', 'Event Name', 'Event Date', 'Registration Fee', 'Total Registrations', 'Total Revenue'
        ]);
    } else {
        exportToPDF($data, 'Revenue Report', 'revenue_report_' . date('Y-m-d'), [
            'Event ID', 'Event Name', 'Event Date', 'Registration Fee', 'Total Registrations', 'Total Revenue'
        ]);
    }
}

function exportToCSV($data, $filename, $headers) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write headers
    fputcsv($output, $headers);
    
    // Write data
    foreach ($data as $row) {
        fputcsv($output, array_values($row));
    }
    
    fclose($output);
}

function exportToPDF($data, $title, $filename, $headers) {
    // Simple PDF generation using HTML to PDF conversion
    header('Content-Type: text/html');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; text-align: center; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        </style>
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </head>
    <body>
        <h1>' . htmlspecialchars($title) . '</h1>
        <p><strong>Generated on:</strong> ' . date('F j, Y g:i A') . '</p>
        <table>
            <thead>
                <tr>';
    
    foreach ($headers as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    
    echo '</tr>
            </thead>
            <tbody>';
    
    foreach ($data as $row) {
        echo '<tr>';
        foreach (array_values($row) as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    
    echo '</tbody>
        </table>
        <div class="footer">
            <p>Zephyr Festival Management System - Export Report</p>
        </div>
    </body>
    </html>';
}

// Get events for dropdown
$events_query = "SELECT id, name FROM events ORDER BY name";
$events_result = mysqli_query($mysqli, $events_query);

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM participants) as total_participants,
    (SELECT COUNT(*) FROM events) as total_events,
    (SELECT COUNT(*) FROM participation) as total_registrations,
    (SELECT SUM(e.registration_fee * (SELECT COUNT(*) FROM participation WHERE event_id = e.id)) FROM events e) as total_revenue";
$stats_result = mysqli_query($mysqli, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Reports | Zephyr Festival Admin</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .export-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .export-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .export-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .export-card:hover {
            transform: translateY(-5px);
        }
        
        .export-option {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .export-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }
        
        .export-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .export-icon.participants {
            background: linear-gradient(135deg, var(--info-color), #0ea5e9);
        }
        
        .export-icon.events {
            background: linear-gradient(135deg, var(--success-color), #16a34a);
        }
        
        .export-icon.registrations {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }
        
        .export-icon.revenue {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-export {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.5rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.25rem;
        }
        
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-csv {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .btn-pdf {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .quick-stats {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .export-history {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .export-card {
                padding: 1.5rem;
            }
            
            .export-option {
                padding: 1.5rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Export Header -->
    <div class="export-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-download me-3"></i>Export Reports
                    </h1>
                    <p class="lead mb-0">Generate and download comprehensive reports</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="dashboard_enhanced.php" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <!-- Quick Statistics -->
        <div class="quick-stats">
            <h5 class="mb-3">Quick Statistics</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-info"><?php echo number_format($stats['total_participants']); ?></div>
                        <div class="stat-label">Total Participants</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-success"><?php echo number_format($stats['total_events']); ?></div>
                        <div class="stat-label">Total Events</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-warning"><?php echo number_format($stats['total_registrations']); ?></div>
                        <div class="stat-label">Total Registrations</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-danger">₹<?php echo number_format($stats['total_revenue'] ?? 0); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="row">
            <div class="col-lg-8">
                <!-- General Reports -->
                <div class="export-card">
                    <h5 class="mb-4">General Reports</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon participants">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h6>Participants Report</h6>
                                <p class="text-muted mb-3">Complete list of all registered participants with their details</p>
                                <div>
                                    <a href="?action=participants&format=csv" class="btn btn-export btn-csv">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </a>
                                    <a href="?action=participants&format=pdf" class="btn btn-export btn-pdf">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon events">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h6>Events Report</h6>
                                <p class="text-muted mb-3">List of all events with registration counts and details</p>
                                <div>
                                    <a href="?action=events&format=csv" class="btn btn-export btn-csv">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </a>
                                    <a href="?action=events&format=pdf" class="btn btn-export btn-pdf">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon registrations">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h6>All Registrations</h6>
                                <p class="text-muted mb-3">Complete registration history across all events</p>
                                <div>
                                    <a href="?action=registrations&format=csv" class="btn btn-export btn-csv">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </a>
                                    <a href="?action=registrations&format=pdf" class="btn btn-export btn-pdf">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon revenue">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h6>Revenue Report</h6>
                                <p class="text-muted mb-3">Financial report with revenue breakdown by events</p>
                                <div>
                                    <a href="?action=revenue_report&format=csv" class="btn btn-export btn-csv">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </a>
                                    <a href="?action=revenue_report&format=pdf" class="btn btn-export btn-pdf">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Event-Specific Reports -->
                <div class="export-card">
                    <h5 class="mb-4">Event-Specific Reports</h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Event</label>
                            <select class="form-select" id="eventSelector">
                                <option value="">Choose an event...</option>
                                <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                                    <option value="<?php echo $event['id']; ?>">
                                        <?php echo htmlspecialchars($event['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon participants">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <h6>Event Participants</h6>
                                <p class="text-muted mb-3">List of participants registered for selected event</p>
                                <div>
                                    <button class="btn btn-export btn-csv" onclick="exportEventData('event_participants', 'csv')">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </button>
                                    <button class="btn btn-export btn-pdf" onclick="exportEventData('event_participants', 'pdf')">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="export-option">
                                <div class="export-icon registrations">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h6>Attendance Report</h6>
                                <p class="text-muted mb-3">Attendance tracking for selected event</p>
                                <div>
                                    <button class="btn btn-export btn-csv" onclick="exportEventData('attendance_report', 'csv')">
                                        <i class="fas fa-file-csv me-1"></i>CSV
                                    </button>
                                    <button class="btn btn-export btn-pdf" onclick="exportEventData('attendance_report', 'pdf')">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Export History & Options -->
            <div class="col-lg-4">
                <div class="export-card">
                    <h6 class="mb-3">Export Options</h6>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">CSV Format</h6>
                        <ul class="list-unstyled text-sm">
                            <li><i class="fas fa-check text-success me-2"></i>Excel compatible</li>
                            <li><i class="fas fa-check text-success me-2"></i>Easy to analyze</li>
                            <li><i class="fas fa-check text-success me-2"></i>Smaller file size</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">PDF Format</h6>
                        <ul class="list-unstyled text-sm">
                            <li><i class="fas fa-check text-success me-2"></i>Professional appearance</li>
                            <li><i class="fas fa-check text-success me-2"></i>Ready to print</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fixed formatting</li>
                        </ul>
                    </div>
                </div>
                
                <div class="export-card">
                    <h6 class="mb-3">Quick Actions</h6>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="exportAll('csv')">
                            <i class="fas fa-download me-2"></i>Export All as CSV
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportAll('pdf')">
                            <i class="fas fa-download me-2"></i>Export All as PDF
                        </button>
                        <button class="btn btn-outline-info" onclick="scheduleReport()">
                            <i class="fas fa-clock me-2"></i>Schedule Report
                        </button>
                    </div>
                </div>
                
                <div class="export-history">
                    <h6 class="mb-3">Recent Exports</h6>
                    
                    <div class="history-item">
                        <div>
                            <div class="fw-bold">Participants Report</div>
                            <small class="text-muted">Today, 2:30 PM</small>
                        </div>
                        <span class="badge bg-success">CSV</span>
                    </div>
                    
                    <div class="history-item">
                        <div>
                            <div class="fw-bold">Revenue Report</div>
                            <small class="text-muted">Yesterday, 4:15 PM</small>
                        </div>
                        <span class="badge bg-danger">PDF</span>
                    </div>
                    
                    <div class="history-item">
                        <div>
                            <div class="fw-bold">Event Participants</div>
                            <small class="text-muted">2 days ago, 1:45 PM</small>
                        </div>
                        <span class="badge bg-success">CSV</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function exportEventData(action, format) {
            const eventId = document.getElementById('eventSelector').value;
            
            if (!eventId) {
                alert('Please select an event first.');
                return;
            }
            
            const url = `?action=${action}&event_id=${eventId}&format=${format}`;
            window.open(url, '_blank');
            
            // Add to history (simulation)
            addToHistory(action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()), format.toUpperCase());
        }
        
        function exportAll(format) {
            const actions = ['participants', 'events', 'registrations', 'revenue_report'];
            
            actions.forEach((action, index) => {
                setTimeout(() => {
                    const url = `?action=${action}&format=${format}`;
                    window.open(url, '_blank');
                }, index * 1000); // Stagger downloads by 1 second
            });
            
            showToast('All reports are being generated. Please check your downloads.', 'success');
        }
        
        function scheduleReport() {
            alert('Report scheduling feature coming soon! You will be able to schedule automatic weekly/monthly reports.');
        }
        
        function addToHistory(reportName, format) {
            // This would normally save to database
            console.log(`Added to history: ${reportName} (${format})`);
        }
        
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            `;
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }
        
        // Animation for cards
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.export-option');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>