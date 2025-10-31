<?php
session_start();
include "linc.php";

// Check if user is logged in
if (!isset($_SESSION['participant_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit();
}

$user_id = $_SESSION['participant_id'] ?? $_SESSION['admin_id'];
$user_type = isset($_SESSION['participant_id']) ? 'participant' : 'admin';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'get_notifications':
            $limit = intval($_POST['limit'] ?? 10);
            $offset = intval($_POST['offset'] ?? 0);
            $unread_only = $_POST['unread_only'] ?? false;
            
            $where_clause = "recipient_id = $user_id AND recipient_type = '$user_type'";
            if ($unread_only) {
                $where_clause .= " AND is_read = 0";
            }
            
            $query = "SELECT *, DATE_FORMAT(created_at, '%M %d, %Y at %h:%i %p') as formatted_date 
                     FROM notifications 
                     WHERE $where_clause 
                     ORDER BY created_at DESC 
                     LIMIT $limit OFFSET $offset";
            
            $result = mysqli_query($mysqli, $query);
            $notifications = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $notifications[] = $row;
            }
            
            // Get unread count
            $count_query = "SELECT COUNT(*) as count FROM notifications WHERE recipient_id = $user_id AND recipient_type = '$user_type' AND is_read = 0";
            $count_result = mysqli_query($mysqli, $count_query);
            $unread_count = mysqli_fetch_assoc($count_result)['count'];
            
            $response['success'] = true;
            $response['notifications'] = $notifications;
            $response['unread_count'] = $unread_count;
            break;
            
        case 'mark_read':
            $notification_id = intval($_POST['notification_id']);
            
            $update_query = "UPDATE notifications SET is_read = 1, read_at = NOW() 
                           WHERE id = $notification_id AND recipient_id = $user_id AND recipient_type = '$user_type'";
            
            if (mysqli_query($mysqli, $update_query)) {
                $response['success'] = true;
                $response['message'] = 'Notification marked as read';
            } else {
                $response['message'] = 'Failed to mark notification as read';
            }
            break;
            
        case 'mark_all_read':
            $update_query = "UPDATE notifications SET is_read = 1, read_at = NOW() 
                           WHERE recipient_id = $user_id AND recipient_type = '$user_type' AND is_read = 0";
            
            if (mysqli_query($mysqli, $update_query)) {
                $response['success'] = true;
                $response['message'] = 'All notifications marked as read';
            } else {
                $response['message'] = 'Failed to mark notifications as read';
            }
            break;
            
        case 'delete_notification':
            $notification_id = intval($_POST['notification_id']);
            
            $delete_query = "DELETE FROM notifications 
                           WHERE id = $notification_id AND recipient_id = $user_id AND recipient_type = '$user_type'";
            
            if (mysqli_query($mysqli, $delete_query)) {
                $response['success'] = true;
                $response['message'] = 'Notification deleted';
            } else {
                $response['message'] = 'Failed to delete notification';
            }
            break;
            
        case 'send_notification':
            // Admin only
            if ($user_type !== 'admin') {
                $response['message'] = 'Unauthorized';
                break;
            }
            
            $title = mysqli_real_escape_string($mysqli, $_POST['title']);
            $message = mysqli_real_escape_string($mysqli, $_POST['message']);
            $type = mysqli_real_escape_string($mysqli, $_POST['type']);
            $priority = mysqli_real_escape_string($mysqli, $_POST['priority']);
            $recipient_type = mysqli_real_escape_string($mysqli, $_POST['recipient_type']);
            $recipient_id = $_POST['recipient_id'] ?? null;
            
            // If recipient_id is null, send to all users of recipient_type
            if ($recipient_id) {
                $insert_query = "INSERT INTO notifications (title, message, type, priority, recipient_id, recipient_type, created_at) 
                               VALUES ('$title', '$message', '$type', '$priority', $recipient_id, '$recipient_type', NOW())";
                mysqli_query($mysqli, $insert_query);
            } else {
                // Send to all users of the specified type
                if ($recipient_type === 'participant') {
                    $users_query = "SELECT id FROM participants";
                } else {
                    $users_query = "SELECT id FROM admin";
                }
                
                $users_result = mysqli_query($mysqli, $users_query);
                while ($user = mysqli_fetch_assoc($users_result)) {
                    $insert_query = "INSERT INTO notifications (title, message, type, priority, recipient_id, recipient_type, created_at) 
                                   VALUES ('$title', '$message', '$type', '$priority', {$user['id']}, '$recipient_type', NOW())";
                    mysqli_query($mysqli, $insert_query);
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Notification sent successfully';
            break;
            
        case 'get_notification_settings':
            $settings_query = "SELECT * FROM notification_settings WHERE user_id = $user_id AND user_type = '$user_type'";
            $settings_result = mysqli_query($mysqli, $settings_query);
            
            if (mysqli_num_rows($settings_result) > 0) {
                $settings = mysqli_fetch_assoc($settings_result);
            } else {
                // Default settings
                $settings = [
                    'email_notifications' => 1,
                    'push_notifications' => 1,
                    'event_reminders' => 1,
                    'registration_updates' => 1,
                    'general_announcements' => 1
                ];
            }
            
            $response['success'] = true;
            $response['settings'] = $settings;
            break;
            
        case 'update_notification_settings':
            $email_notifications = intval($_POST['email_notifications']);
            $push_notifications = intval($_POST['push_notifications']);
            $event_reminders = intval($_POST['event_reminders']);
            $registration_updates = intval($_POST['registration_updates']);
            $general_announcements = intval($_POST['general_announcements']);
            
            $check_query = "SELECT id FROM notification_settings WHERE user_id = $user_id AND user_type = '$user_type'";
            $check_result = mysqli_query($mysqli, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                // Update existing settings
                $update_query = "UPDATE notification_settings SET 
                               email_notifications = $email_notifications,
                               push_notifications = $push_notifications,
                               event_reminders = $event_reminders,
                               registration_updates = $registration_updates,
                               general_announcements = $general_announcements,
                               updated_at = NOW()
                               WHERE user_id = $user_id AND user_type = '$user_type'";
            } else {
                // Insert new settings
                $update_query = "INSERT INTO notification_settings 
                               (user_id, user_type, email_notifications, push_notifications, event_reminders, registration_updates, general_announcements, created_at) 
                               VALUES ($user_id, '$user_type', $email_notifications, $push_notifications, $event_reminders, $registration_updates, $general_announcements, NOW())";
            }
            
            if (mysqli_query($mysqli, $update_query)) {
                $response['success'] = true;
                $response['message'] = 'Settings updated successfully';
            } else {
                $response['message'] = 'Failed to update settings';
            }
            break;
    }
    
    echo json_encode($response);
    exit();
}

// If it's a GET request, return the notification center HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Center | Zephyr Festival</title>
    
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
        
        .notification-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .notification-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .notification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .notification-card:hover {
            transform: translateY(-5px);
        }
        
        .notification-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .notification-item.unread {
            border-left: 4px solid var(--primary-color);
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05), white);
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }
        
        .notification-icon.info {
            background: linear-gradient(135deg, var(--info-color), #0ea5e9);
        }
        
        .notification-icon.success {
            background: linear-gradient(135deg, var(--success-color), #16a34a);
        }
        
        .notification-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }
        
        .notification-icon.danger {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .notification-message {
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .notification-time {
            font-size: 0.8rem;
            color: #999;
        }
        
        .notification-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .notification-item:hover .notification-actions {
            opacity: 1;
        }
        
        .badge-unread {
            background: var(--danger-color);
            color: white;
            border-radius: 20px;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .priority-high {
            border-left-color: var(--danger-color) !important;
        }
        
        .priority-medium {
            border-left-color: var(--warning-color) !important;
        }
        
        .priority-low {
            border-left-color: var(--info-color) !important;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        
        .notification-bell .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .notification-dropdown.show {
            display: block;
        }
        
        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        
        .dropdown-notification {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .dropdown-notification:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-notification.unread {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .floating-bell {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .floating-bell:hover {
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .notification-dropdown {
                width: 300px;
                right: -50px;
            }
            
            .notification-card {
                padding: 1.5rem;
            }
            
            .notification-item {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Notification Header -->
    <div class="notification-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-bell me-3"></i>Notification Center
                    </h1>
                    <p class="lead mb-0">Stay updated with all your festival notifications</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="notification-bell" id="notificationBell">
                        <i class="fas fa-bell fa-2x"></i>
                        <div class="badge" id="notificationBadge" style="display: none;">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <!-- Filter and Actions -->
        <div class="notification-card">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <h5 class="mb-0">My Notifications</h5>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-outline-primary btn-sm me-2" id="markAllReadBtn">
                        <i class="fas fa-check-double me-1"></i>Mark All Read
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">
                        <i class="fas fa-sync me-1"></i>Refresh
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select" id="filterType">
                        <option value="">All Types</option>
                        <option value="info">Information</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="danger">Alert</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="unread">Unread Only</option>
                        <option value="read">Read Only</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterPriority">
                        <option value="">All Priorities</option>
                        <option value="high">High Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="low">Low Priority</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Notifications List -->
        <div class="notification-card">
            <div id="notificationsList">
                <!-- Notifications will be loaded here -->
            </div>
            
            <div class="text-center mt-3">
                <button class="btn btn-primary" id="loadMoreBtn" style="display: none;">
                    <i class="fas fa-chevron-down me-2"></i>Load More
                </button>
            </div>
        </div>
        
        <!-- Admin Send Notification Section -->
        <?php if ($user_type === 'admin'): ?>
        <div class="notification-card">
            <h5 class="mb-4">Send New Notification</h5>
            
            <form id="sendNotificationForm">
                <input type="hidden" name="action" value="send_notification">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="info">Information</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Alert</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Send To</label>
                        <select class="form-select" name="recipient_type" required>
                            <option value="participant">All Participants</option>
                            <option value="admin">All Admins</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Specific User ID (Optional)</label>
                        <input type="number" class="form-control" name="recipient_id" placeholder="Leave empty for all">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Send Notification
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Notification Settings -->
        <div class="notification-card">
            <h5 class="mb-4">Notification Settings</h5>
            
            <form id="settingsForm">
                <input type="hidden" name="action" value="update_notification_settings">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" value="1">
                            <label class="form-check-label" for="emailNotifications">
                                Email Notifications
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="pushNotifications" name="push_notifications" value="1">
                            <label class="form-check-label" for="pushNotifications">
                                Push Notifications
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="eventReminders" name="event_reminders" value="1">
                            <label class="form-check-label" for="eventReminders">
                                Event Reminders
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="registrationUpdates" name="registration_updates" value="1">
                            <label class="form-check-label" for="registrationUpdates">
                                Registration Updates
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="generalAnnouncements" name="general_announcements" value="1">
                            <label class="form-check-label" for="generalAnnouncements">
                                General Announcements
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </form>
        </div>
    </div>
    
    <!-- Floating Notification Bell -->
    <div class="floating-bell" id="floatingBell">
        <i class="fas fa-bell"></i>
        <div class="badge" id="floatingBadge" style="display: none;">0</div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        class NotificationCenter {
            constructor() {
                this.notifications = [];
                this.offset = 0;
                this.limit = 10;
                this.loading = false;
                this.pollInterval = null;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.loadNotifications();
                this.loadSettings();
                this.startPolling();
            }
            
            setupEventListeners() {
                // Mark all read
                document.getElementById('markAllReadBtn').addEventListener('click', () => {
                    this.markAllRead();
                });
                
                // Refresh
                document.getElementById('refreshBtn').addEventListener('click', () => {
                    this.refreshNotifications();
                });
                
                // Load more
                document.getElementById('loadMoreBtn').addEventListener('click', () => {
                    this.loadMoreNotifications();
                });
                
                // Filters
                ['filterType', 'filterStatus', 'filterPriority'].forEach(id => {
                    document.getElementById(id).addEventListener('change', () => {
                        this.applyFilters();
                    });
                });
                
                // Send notification form (admin only)
                const sendForm = document.getElementById('sendNotificationForm');
                if (sendForm) {
                    sendForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.sendNotification(new FormData(sendForm));
                    });
                }
                
                // Settings form
                document.getElementById('settingsForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.updateSettings(new FormData(e.target));
                });
                
                // Floating bell
                document.getElementById('floatingBell').addEventListener('click', () => {
                    this.scrollToTop();
                });
            }
            
            async loadNotifications(append = false) {
                if (this.loading) return;
                this.loading = true;
                
                try {
                    const formData = new FormData();
                    formData.append('action', 'get_notifications');
                    formData.append('limit', this.limit);
                    formData.append('offset', append ? this.offset : 0);
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        if (append) {
                            this.notifications = [...this.notifications, ...result.notifications];
                        } else {
                            this.notifications = result.notifications;
                            this.offset = 0;
                        }
                        
                        this.renderNotifications();
                        this.updateUnreadCount(result.unread_count);
                        
                        if (result.notifications.length === this.limit) {
                            document.getElementById('loadMoreBtn').style.display = 'block';
                        } else {
                            document.getElementById('loadMoreBtn').style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Failed to load notifications:', error);
                } finally {
                    this.loading = false;
                }
            }
            
            renderNotifications() {
                const container = document.getElementById('notificationsList');
                
                if (this.notifications.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h6>No notifications yet</h6>
                            <p class="text-muted">You're all caught up!</p>
                        </div>
                    `;
                    return;
                }
                
                const filteredNotifications = this.applyCurrentFilters();
                
                container.innerHTML = filteredNotifications.map(notification => `
                    <div class="notification-item ${notification.is_read == 0 ? 'unread' : ''} priority-${notification.priority}" 
                         data-id="${notification.id}" 
                         onclick="notificationCenter.markAsRead(${notification.id})">
                        <div class="d-flex">
                            <div class="notification-icon ${notification.type} me-3">
                                <i class="fas fa-${this.getIconForType(notification.type)}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                                <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                                <div class="notification-time">
                                    <i class="fas fa-clock me-1"></i>
                                    ${notification.formatted_date}
                                </div>
                            </div>
                            ${notification.is_read == 0 ? '<span class="badge-unread">New</span>' : ''}
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); notificationCenter.deleteNotification(${notification.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }
            
            applyCurrentFilters() {
                const typeFilter = document.getElementById('filterType').value;
                const statusFilter = document.getElementById('filterStatus').value;
                const priorityFilter = document.getElementById('filterPriority').value;
                
                return this.notifications.filter(notification => {
                    if (typeFilter && notification.type !== typeFilter) return false;
                    if (statusFilter === 'unread' && notification.is_read == 1) return false;
                    if (statusFilter === 'read' && notification.is_read == 0) return false;
                    if (priorityFilter && notification.priority !== priorityFilter) return false;
                    return true;
                });
            }
            
            applyFilters() {
                this.renderNotifications();
            }
            
            async markAsRead(notificationId) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'mark_read');
                    formData.append('notification_id', notificationId);
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update local notification
                        const notification = this.notifications.find(n => n.id == notificationId);
                        if (notification) {
                            notification.is_read = 1;
                            this.renderNotifications();
                            this.updateUnreadCount();
                        }
                    }
                } catch (error) {
                    console.error('Failed to mark as read:', error);
                }
            }
            
            async markAllRead() {
                try {
                    const formData = new FormData();
                    formData.append('action', 'mark_all_read');
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.notifications.forEach(n => n.is_read = 1);
                        this.renderNotifications();
                        this.updateUnreadCount(0);
                        this.showToast(result.message, 'success');
                    }
                } catch (error) {
                    console.error('Failed to mark all as read:', error);
                }
            }
            
            async deleteNotification(notificationId) {
                if (!confirm('Are you sure you want to delete this notification?')) return;
                
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete_notification');
                    formData.append('notification_id', notificationId);
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.notifications = this.notifications.filter(n => n.id != notificationId);
                        this.renderNotifications();
                        this.updateUnreadCount();
                        this.showToast(result.message, 'success');
                    }
                } catch (error) {
                    console.error('Failed to delete notification:', error);
                }
            }
            
            async sendNotification(formData) {
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('sendNotificationForm').reset();
                        this.showToast(result.message, 'success');
                    } else {
                        this.showToast(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Failed to send notification:', error);
                }
            }
            
            async loadSettings() {
                try {
                    const formData = new FormData();
                    formData.append('action', 'get_notification_settings');
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        const settings = result.settings;
                        document.getElementById('emailNotifications').checked = settings.email_notifications == 1;
                        document.getElementById('pushNotifications').checked = settings.push_notifications == 1;
                        document.getElementById('eventReminders').checked = settings.event_reminders == 1;
                        document.getElementById('registrationUpdates').checked = settings.registration_updates == 1;
                        document.getElementById('generalAnnouncements').checked = settings.general_announcements == 1;
                    }
                } catch (error) {
                    console.error('Failed to load settings:', error);
                }
            }
            
            async updateSettings(formData) {
                try {
                    // Convert checkboxes to proper values
                    const data = new FormData();
                    data.append('action', 'update_notification_settings');
                    
                    ['email_notifications', 'push_notifications', 'event_reminders', 'registration_updates', 'general_announcements'].forEach(field => {
                        const checkbox = document.getElementById(field.replace('_', '').charAt(0).toUpperCase() + field.replace('_', '').slice(1).replace(/([A-Z])/g, '$1'));
                        data.append(field, checkbox && checkbox.checked ? '1' : '0');
                    });
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: data
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.showToast(result.message, 'success');
                    } else {
                        this.showToast(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Failed to update settings:', error);
                }
            }
            
            loadMoreNotifications() {
                this.offset += this.limit;
                this.loadNotifications(true);
            }
            
            refreshNotifications() {
                this.offset = 0;
                this.loadNotifications();
            }
            
            updateUnreadCount(count = null) {
                if (count === null) {
                    count = this.notifications.filter(n => n.is_read == 0).length;
                }
                
                const badges = [document.getElementById('notificationBadge'), document.getElementById('floatingBadge')];
                badges.forEach(badge => {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                });
            }
            
            startPolling() {
                // Poll for new notifications every 30 seconds
                this.pollInterval = setInterval(() => {
                    this.refreshNotifications();
                }, 30000);
            }
            
            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            
            getIconForType(type) {
                const icons = {
                    'info': 'info-circle',
                    'success': 'check-circle',
                    'warning': 'exclamation-triangle',
                    'danger': 'exclamation-circle'
                };
                return icons[type] || 'bell';
            }
            
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
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
        
        // Initialize notification center
        let notificationCenter;
        document.addEventListener('DOMContentLoaded', () => {
            notificationCenter = new NotificationCenter();
        });
        
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (notificationCenter && notificationCenter.pollInterval) {
                clearInterval(notificationCenter.pollInterval);
            }
        });
    </script>
</body>
</html>