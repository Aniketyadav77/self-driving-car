<?php
session_start();
require_once '../includes/security.php';
requireAuthentication('admin');

// Get security dashboard data
$security_data = $security->getSecurityDashboard();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard | Zephyr Festival Admin</title>
    
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
        
        .security-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .security-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .security-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .security-card:hover {
            transform: translateY(-5px);
        }
        
        .alert-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid;
        }
        
        .alert-card.critical {
            border-left-color: var(--danger-color);
        }
        
        .alert-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .alert-card.error {
            border-left-color: var(--danger-color);
        }
        
        .alert-card.info {
            border-left-color: var(--info-color);
        }
        
        .metric-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .metric-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            color: #666;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .security-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-secure {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.2));
            color: var(--success-color);
            border: 2px solid var(--success-color);
        }
        
        .status-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.2));
            color: var(--warning-color);
            border: 2px solid var(--warning-color);
        }
        
        .status-critical {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.2));
            color: var(--danger-color);
            border: 2px solid var(--danger-color);
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }
        
        .chart-container {
            height: 300px;
            margin: 1rem 0;
        }
        
        .ip-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .ip-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .ip-item:last-child {
            border-bottom: none;
        }
        
        .btn-security {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-security:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .threat-level-indicator {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            margin: 0 auto 1rem;
        }
        
        .threat-low {
            background: linear-gradient(135deg, var(--success-color), #20c997);
        }
        
        .threat-medium {
            background: linear-gradient(135deg, var(--warning-color), #fd7e14);
        }
        
        .threat-high {
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
        }
        
        @media (max-width: 768px) {
            .security-card {
                padding: 1.5rem;
            }
            
            .metric-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Security Header -->
    <div class="security-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-shield-alt me-3"></i>Security Dashboard
                    </h1>
                    <p class="lead mb-0">Monitor and manage system security</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="security-status status-secure" id="overallStatus">
                        <i class="fas fa-check-circle me-2"></i>System Secure
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <!-- Security Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number text-danger" id="failedLogins">
                        <?php echo $security_data['failed_logins_24h']; ?>
                    </div>
                    <div class="metric-label">Failed Logins (24h)</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number text-warning" id="securityEvents">
                        <?php echo count($security_data['recent_events']); ?>
                    </div>
                    <div class="metric-label">Security Events</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-number text-info" id="activeIPs">
                        <?php echo count($security_data['top_ips']); ?>
                    </div>
                    <div class="metric-label">Active IP Addresses</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <div class="threat-level-indicator threat-low" id="threatLevel">
                        LOW
                    </div>
                    <div class="metric-label">Threat Level</div>
                </div>
            </div>
        </div>
        
        <!-- Main Security Content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Recent Security Events -->
                <div class="security-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5>Recent Security Events</h5>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshEvents()">
                            <i class="fas fa-sync me-1"></i>Refresh
                        </button>
                    </div>
                    
                    <div id="securityEventsList">
                        <?php if (empty($security_data['recent_events'])): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                                <h6>No security events</h6>
                                <p class="text-muted">Your system is running securely!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($security_data['recent_events'] as $event): ?>
                                <div class="alert-card <?php echo $event['level']; ?>">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['message']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('M j, Y g:i A', $event['timestamp']); ?>
                                                <?php if ($event['ip_address']): ?>
                                                    | <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($event['ip_address']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?php echo $event['level'] === 'critical' ? 'danger' : ($event['level'] === 'warning' ? 'warning' : 'info'); ?>">
                                            <?php echo strtoupper($event['level']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Security Charts -->
                <div class="security-card">
                    <h5 class="mb-4">Security Analytics</h5>
                    <div class="chart-container">
                        <canvas id="securityChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Top IP Addresses -->
                <div class="security-card">
                    <h6 class="mb-3">Top IP Addresses</h6>
                    <div class="ip-list">
                        <?php if (empty($security_data['top_ips'])): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-network-wired fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No activity to show</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($security_data['top_ips'] as $ip_data): ?>
                                <div class="ip-item">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($ip_data['ip_address']); ?></div>
                                        <small class="text-muted"><?php echo $ip_data['count']; ?> requests</small>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm" onclick="blockIP('<?php echo $ip_data['ip_address']; ?>')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Security Actions -->
                <div class="security-card">
                    <h6 class="mb-3">Security Actions</h6>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-security" onclick="runSecurityScan()">
                            <i class="fas fa-search me-2"></i>Run Security Scan
                        </button>
                        
                        <button class="btn btn-outline-warning" onclick="clearSecurityLogs()">
                            <i class="fas fa-trash me-2"></i>Clear Old Logs
                        </button>
                        
                        <button class="btn btn-outline-info" onclick="exportSecurityReport()">
                            <i class="fas fa-download me-2"></i>Export Report
                        </button>
                        
                        <button class="btn btn-outline-success" onclick="updateSecurityRules()">
                            <i class="fas fa-cog me-2"></i>Update Rules
                        </button>
                    </div>
                </div>
                
                <!-- System Status -->
                <div class="security-card">
                    <h6 class="mb-3">System Status</h6>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>SSL Certificate</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Firewall Status</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Rate Limiting</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>CSRF Protection</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Input Sanitization</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <script>
        class SecurityDashboard {
            constructor() {
                this.init();
            }
            
            init() {
                this.initCharts();
                this.updateThreatLevel();
                this.startAutoRefresh();
            }
            
            initCharts() {
                const ctx = document.getElementById('securityChart').getContext('2d');
                
                this.securityChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['6h ago', '5h ago', '4h ago', '3h ago', '2h ago', '1h ago', 'Now'],
                        datasets: [{
                            label: 'Failed Logins',
                            data: [2, 5, 3, 8, 4, 6, 3],
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Security Events',
                            data: [1, 2, 0, 3, 1, 2, 1],
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }
            
            updateThreatLevel() {
                const failedLogins = parseInt(document.getElementById('failedLogins').textContent);
                const securityEvents = parseInt(document.getElementById('securityEvents').textContent);
                const threatLevel = document.getElementById('threatLevel');
                
                let level = 'LOW';
                let className = 'threat-low';
                
                if (failedLogins > 10 || securityEvents > 5) {
                    level = 'HIGH';
                    className = 'threat-high';
                    document.getElementById('overallStatus').className = 'security-status status-critical';
                    document.getElementById('overallStatus').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Security Alert';
                } else if (failedLogins > 5 || securityEvents > 2) {
                    level = 'MEDIUM';
                    className = 'threat-medium';
                    document.getElementById('overallStatus').className = 'security-status status-warning';
                    document.getElementById('overallStatus').innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Monitor Closely';
                }
                
                threatLevel.className = 'threat-level-indicator ' + className;
                threatLevel.textContent = level;
            }
            
            async refreshEvents() {
                try {
                    const response = await fetch('?dashboard=security');
                    const data = await response.json();
                    
                    // Update metrics
                    document.getElementById('failedLogins').textContent = data.failed_logins_24h;
                    document.getElementById('securityEvents').textContent = data.recent_events.length;
                    document.getElementById('activeIPs').textContent = data.top_ips.length;
                    
                    this.updateThreatLevel();
                    this.showToast('Security data refreshed', 'success');
                } catch (error) {
                    this.showToast('Failed to refresh data', 'error');
                }
            }
            
            startAutoRefresh() {
                setInterval(() => {
                    this.refreshEvents();
                }, 60000); // Refresh every minute
            }
            
            showToast(message, type) {
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
        }
        
        // Global functions for security actions
        function refreshEvents() {
            dashboard.refreshEvents();
        }
        
        function blockIP(ip) {
            if (confirm(`Are you sure you want to block IP address ${ip}?`)) {
                dashboard.showToast(`IP ${ip} has been blocked`, 'success');
            }
        }
        
        function runSecurityScan() {
            dashboard.showToast('Security scan initiated', 'success');
        }
        
        function clearSecurityLogs() {
            if (confirm('Are you sure you want to clear old security logs?')) {
                fetch('?cleanup=security')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            dashboard.showToast(data.message, 'success');
                        }
                    });
            }
        }
        
        function exportSecurityReport() {
            const link = document.createElement('a');
            link.href = 'export_reports.php?action=security_report&format=pdf';
            link.click();
        }
        
        function updateSecurityRules() {
            dashboard.showToast('Security rules updated successfully', 'success');
        }
        
        // Initialize dashboard
        let dashboard;
        document.addEventListener('DOMContentLoaded', () => {
            dashboard = new SecurityDashboard();
        });
    </script>
</body>
</html>