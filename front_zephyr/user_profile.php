<?php
session_start();
include "linc.php";

// Check if user is logged in
if (!isset($_SESSION['participant_id'])) {
    header('Location: plogin.php');
    exit();
}

$participant_id = $_SESSION['participant_id'];

// Handle profile updates
if ($_POST && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    if ($_POST['action'] === 'update_profile') {
        $name = trim(mysqli_real_escape_string($mysqli, $_POST['name']));
        $email = trim(mysqli_real_escape_string($mysqli, $_POST['email']));
        $phone = trim(mysqli_real_escape_string($mysqli, $_POST['phone']));
        $college = trim(mysqli_real_escape_string($mysqli, $_POST['college']));
        $year = intval($_POST['year']);
        $bio = trim(mysqli_real_escape_string($mysqli, $_POST['bio']));
        
        // Validation
        if (empty($name) || empty($email) || empty($phone) || empty($college)) {
            $response['message'] = 'Please fill all required fields';
        } else {
            $update_query = "UPDATE participants SET 
                           name = '$name', 
                           email = '$email', 
                           phone = '$phone', 
                           college = '$college', 
                           year = $year, 
                           bio = '$bio', 
                           updated_at = NOW() 
                           WHERE id = $participant_id";
            
            if (mysqli_query($mysqli, $update_query)) {
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully!';
            } else {
                $response['message'] = 'Failed to update profile';
            }
        }
    }
    
    if ($_POST['action'] === 'upload_avatar') {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $file = $_FILES['avatar'];
            
            if (!in_array($file['type'], $allowed_types)) {
                $response['message'] = 'Invalid file type. Please upload JPEG, PNG, or GIF';
            } elseif ($file['size'] > $max_size) {
                $response['message'] = 'File too large. Maximum size is 5MB';
            } else {
                $upload_dir = 'uploads/avatars/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $participant_id . '_' . time() . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Update database
                    $update_query = "UPDATE participants SET avatar = '$filename' WHERE id = $participant_id";
                    if (mysqli_query($mysqli, $update_query)) {
                        $response['success'] = true;
                        $response['message'] = 'Avatar updated successfully!';
                        $response['avatar_url'] = $filepath;
                    } else {
                        $response['message'] = 'Failed to save avatar to database';
                    }
                } else {
                    $response['message'] = 'Failed to upload file';
                }
            }
        } else {
            $response['message'] = 'No file uploaded or upload error';
        }
    }
    
    echo json_encode($response);
    exit();
}

// Get participant data
$participant_query = "SELECT * FROM participants WHERE id = $participant_id";
$participant_result = mysqli_query($mysqli, $participant_query);
$participant = mysqli_fetch_assoc($participant_result);

// Get registered events
$events_query = "SELECT e.*, p.registration_date 
                FROM events e 
                JOIN participation p ON e.id = p.event_id 
                WHERE p.participant_id = $participant_id 
                ORDER BY p.registration_date DESC";
$events_result = mysqli_query($mysqli, $events_query);

// Get achievements/certificates
$achievements_query = "SELECT * FROM achievements WHERE participant_id = $participant_id ORDER BY created_at DESC";
$achievements_result = mysqli_query($mysqli, $achievements_query);

// Dashboard stats
$total_events = mysqli_num_rows($events_result);
$upcoming_events = mysqli_num_rows(mysqli_query($mysqli, 
    "SELECT e.id FROM events e 
     JOIN participation p ON e.id = p.event_id 
     WHERE p.participant_id = $participant_id AND e.event_date >= CURDATE()"));
$completed_events = $total_events - $upcoming_events;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Zephyr Festival</title>
    
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
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .avatar-container {
            position: relative;
            display: inline-block;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        
        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--success-color);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .avatar-upload:hover {
            transform: scale(1.1);
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .event-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-upcoming {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info-color);
        }
        
        .status-completed {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        
        .tab-content {
            margin-top: 2rem;
        }
        
        .nav-pills .nav-link {
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background: var(--primary-color);
        }
        
        .achievement-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            margin: 0.25rem;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .progress-circle {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }
        
        .progress-circle svg {
            width: 100px;
            height: 100px;
            transform: rotate(-90deg);
        }
        
        .progress-circle-bg {
            fill: none;
            stroke: #e9ecef;
            stroke-width: 8;
        }
        
        .progress-circle-fill {
            fill: none;
            stroke: var(--primary-color);
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 283;
            stroke-dashoffset: 283;
            transition: stroke-dashoffset 1s ease-in-out;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                padding: 1.5rem 0;
                text-align: center;
            }
            
            .dashboard-card {
                padding: 1.5rem;
            }
            
            .avatar {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="avatar-container">
                        <?php if (!empty($participant['avatar'])): ?>
                            <img src="uploads/avatars/<?php echo htmlspecialchars($participant['avatar']); ?>" 
                                 alt="Avatar" class="avatar" id="avatarImage">
                        <?php else: ?>
                            <div class="avatar" id="avatarImage">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <button class="avatar-upload" onclick="document.getElementById('avatarUpload').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                    </div>
                </div>
                
                <div class="col-md-9">
                    <h1 class="display-5 mb-2"><?php echo htmlspecialchars($participant['name']); ?></h1>
                    <p class="lead mb-2"><?php echo htmlspecialchars($participant['college']); ?></p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($participant['email']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-phone me-2"></i>
                        <?php echo htmlspecialchars($participant['phone']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Year <?php echo $participant['year']; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <!-- Dashboard Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-primary"><?php echo $total_events; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-info"><?php echo $upcoming_events; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-success"><?php echo $completed_events; ?></div>
                    <div class="stat-label">Completed Events</div>
                </div>
            </div>
        </div>
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills justify-content-center mb-4" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="pill" href="#dashboard">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#events">My Events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#achievements">Achievements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="pill" href="#settings">Settings</a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <h5 class="mb-4">Recent Activity</h5>
                            
                            <?php mysqli_data_seek($events_result, 0); ?>
                            <?php $count = 0; ?>
                            <?php while ($event = mysqli_fetch_assoc($events_result) && $count < 3): ?>
                                <div class="event-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6><?php echo htmlspecialchars($event['name']); ?></h6>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
                                            </p>
                                            <small class="text-muted">
                                                Registered: <?php echo date('M j, Y', strtotime($event['registration_date'])); ?>
                                            </small>
                                        </div>
                                        <span class="event-status <?php echo strtotime($event['event_date']) > time() ? 'status-upcoming' : 'status-completed'; ?>">
                                            <?php echo strtotime($event['event_date']) > time() ? 'Upcoming' : 'Completed'; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php $count++; ?>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="dashboard-card">
                            <h6 class="mb-3">Profile Completion</h6>
                            <div class="progress-circle">
                                <svg>
                                    <circle cx="50" cy="50" r="45" class="progress-circle-bg"></circle>
                                    <circle cx="50" cy="50" r="45" class="progress-circle-fill" id="progressCircle"></circle>
                                </svg>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: 600;">
                                    <span id="progressPercent">0</span>%
                                </div>
                            </div>
                            <p class="text-center mt-3 text-muted">Complete your profile to unlock features</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Events Tab -->
            <div class="tab-pane fade" id="events">
                <div class="dashboard-card">
                    <h5 class="mb-4">My Registered Events</h5>
                    
                    <?php mysqli_data_seek($events_result, 0); ?>
                    <?php if (mysqli_num_rows($events_result) > 0): ?>
                        <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                            <div class="event-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6><?php echo htmlspecialchars($event['name']); ?></h6>
                                        <p class="text-muted mb-1"><?php echo htmlspecialchars($event['description'] ?? ''); ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($event['event_date'])); ?>
                                            <?php if (!empty($event['venue'])): ?>
                                                | <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($event['venue']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="event-status <?php echo strtotime($event['event_date']) > time() ? 'status-upcoming' : 'status-completed'; ?>">
                                            <?php echo strtotime($event['event_date']) > time() ? 'Upcoming' : 'Completed'; ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            Reg: <?php echo date('M j', strtotime($event['registration_date'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h6>No events registered yet</h6>
                            <p class="text-muted">Start exploring and register for exciting events!</p>
                            <a href="events_search_advanced.php" class="btn btn-primary">Browse Events</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Achievements Tab -->
            <div class="tab-pane fade" id="achievements">
                <div class="dashboard-card">
                    <h5 class="mb-4">My Achievements</h5>
                    
                    <?php if (mysqli_num_rows($achievements_result) > 0): ?>
                        <?php while ($achievement = mysqli_fetch_assoc($achievements_result)): ?>
                            <div class="achievement-badge">
                                <i class="fas fa-trophy me-2"></i>
                                <?php echo htmlspecialchars($achievement['title']); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                            <h6>No achievements yet</h6>
                            <p class="text-muted">Participate in events to earn achievements!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings">
                <div class="dashboard-card">
                    <h5 class="mb-4">Profile Settings</h5>
                    
                    <form id="profileForm">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($participant['name']); ?>" required>
                                    <label for="name">Full Name</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($participant['email']); ?>" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($participant['phone']); ?>" required>
                                    <label for="phone">Phone</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-control" id="year" name="year" required>
                                        <option value="1" <?php echo $participant['year'] == 1 ? 'selected' : ''; ?>>1st Year</option>
                                        <option value="2" <?php echo $participant['year'] == 2 ? 'selected' : ''; ?>>2nd Year</option>
                                        <option value="3" <?php echo $participant['year'] == 3 ? 'selected' : ''; ?>>3rd Year</option>
                                        <option value="4" <?php echo $participant['year'] == 4 ? 'selected' : ''; ?>>4th Year</option>
                                    </select>
                                    <label for="year">Year</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="college" name="college" 
                                   value="<?php echo htmlspecialchars($participant['college']); ?>" required>
                            <label for="college">College/University</label>
                        </div>
                        
                        <div class="form-floating">
                            <textarea class="form-control" id="bio" name="bio" style="height: 100px;" 
                                      placeholder="Tell us about yourself..."><?php echo htmlspecialchars($participant['bio'] ?? ''); ?></textarea>
                            <label for="bio">Bio</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        class UserProfile {
            constructor() {
                this.init();
            }
            
            init() {
                this.setupAvatarUpload();
                this.setupProfileForm();
                this.calculateProfileCompletion();
                this.setupProgressCircle();
            }
            
            setupAvatarUpload() {
                document.getElementById('avatarUpload').addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        this.uploadAvatar(file);
                    }
                });
            }
            
            async uploadAvatar(file) {
                const formData = new FormData();
                formData.append('avatar', file);
                formData.append('action', 'upload_avatar');
                
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update avatar image
                        const avatarImg = document.getElementById('avatarImage');
                        if (result.avatar_url) {
                            avatarImg.innerHTML = '';
                            const img = document.createElement('img');
                            img.src = result.avatar_url;
                            img.alt = 'Avatar';
                            img.className = 'avatar';
                            avatarImg.appendChild(img);
                        }
                        this.showMessage(result.message, 'success');
                        this.calculateProfileCompletion();
                    } else {
                        this.showMessage(result.message, 'error');
                    }
                } catch (error) {
                    this.showMessage('Upload failed. Please try again.', 'error');
                }
            }
            
            setupProfileForm() {
                document.getElementById('profileForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const formData = new FormData(e.target);
                    
                    try {
                        const response = await fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showMessage(result.message, 'success');
                            this.calculateProfileCompletion();
                        } else {
                            this.showMessage(result.message, 'error');
                        }
                    } catch (error) {
                        this.showMessage('Update failed. Please try again.', 'error');
                    }
                });
            }
            
            calculateProfileCompletion() {
                const fields = ['name', 'email', 'phone', 'college', 'year'];
                const bioField = document.getElementById('bio');
                const avatarImg = document.querySelector('#avatarImage img');
                
                let completed = 0;
                let total = fields.length + 2; // +2 for bio and avatar
                
                // Check required fields
                fields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field && field.value.trim()) {
                        completed++;
                    }
                });
                
                // Check bio
                if (bioField && bioField.value.trim()) {
                    completed++;
                }
                
                // Check avatar
                if (avatarImg) {
                    completed++;
                }
                
                const percentage = Math.round((completed / total) * 100);
                this.updateProgressCircle(percentage);
            }
            
            updateProgressCircle(percentage) {
                const circle = document.getElementById('progressCircle');
                const percentText = document.getElementById('progressPercent');
                
                const circumference = 283; // 2 * PI * 45
                const offset = circumference - (percentage / 100) * circumference;
                
                circle.style.strokeDashoffset = offset;
                percentText.textContent = percentage;
            }
            
            setupProgressCircle() {
                // Animate on page load
                setTimeout(() => {
                    this.calculateProfileCompletion();
                }, 500);
            }
            
            showMessage(message, type) {
                // Create toast notification
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
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 5000);
            }
        }
        
        // Initialize user profile
        document.addEventListener('DOMContentLoaded', () => {
            new UserProfile();
        });
    </script>
</body>
</html>