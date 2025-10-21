<?php
include "config.php";

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Rate limiting
        if (!check_rate_limit($email, 5, 900)) { // 5 attempts per 15 minutes
            $errors[] = "Too many login attempts. Please try again later.";
        } else {
            if ($action == 'login') {
                // Validate input
                if (!validate_email($email)) {
                    $errors[] = "Valid email is required";
                }
                if (empty($password)) {
                    $errors[] = "Password is required";
                }
                
                if (empty($errors)) {
                    // Check admin credentials
                    $stmt = $mysqli->prepare("SELECT id, email, password, name FROM admin WHERE email = ? AND status = 'active'");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows == 1) {
                        $admin = $result->fetch_assoc();
                        if (verify_password($password, $admin['password'])) {
                            // Login successful
                            $_SESSION['admin_id'] = $admin['id'];
                            $_SESSION['admin_email'] = $admin['email'];
                            $_SESSION['admin_name'] = $admin['name'];
                            $_SESSION['login_time'] = time();
                            
                            // Log successful login
                            $stmt = $mysqli->prepare("INSERT INTO login_logs (user_id, user_type, ip_address, user_agent, status) VALUES (?, 'admin', ?, ?, 'success')");
                            $ip = $_SERVER['REMOTE_ADDR'];
                            $user_agent = $_SERVER['HTTP_USER_AGENT'];
                            $stmt->bind_param("iss", $admin['id'], $ip, $user_agent);
                            $stmt->execute();
                            
                            header('Location: admin_dashboard.php');
                            exit();
                        } else {
                            $errors[] = "Invalid email or password";
                        }
                    } else {
                        $errors[] = "Invalid email or password";
                    }
                }
            } elseif ($action == 'register') {
                // Admin registration (for initial setup)
                $name = sanitize_input($_POST['name'] ?? '');
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                // Validation
                if (empty($name)) {
                    $errors[] = "Name is required";
                }
                if (!validate_email($email)) {
                    $errors[] = "Valid email is required";
                }
                if (strlen($password) < 8) {
                    $errors[] = "Password must be at least 8 characters long";
                }
                if ($password !== $confirm_password) {
                    $errors[] = "Passwords do not match";
                }
                
                if (empty($errors)) {
                    // Check if admin already exists
                    $stmt = $mysqli->prepare("SELECT id FROM admin WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $errors[] = "Admin with this email already exists";
                    } else {
                        // Create new admin
                        $hashed_password = hash_password($password);
                        $stmt = $mysqli->prepare("INSERT INTO admin (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
                        $stmt->bind_param("sss", $name, $email, $hashed_password);
                        
                        if ($stmt->execute()) {
                            $success_message = "Admin account created successfully! You can now login.";
                        } else {
                            $errors[] = "Registration failed. Please try again.";
                        }
                    }
                }
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Zephyr Security Gate</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="modern-3d.css">
    <style>
        .auth-hero {
            background: var(--gradient-dark);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .auth-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 40% 80%, rgba(34, 197, 94, 0.1) 0%, transparent 50%);
            z-index: 1;
        }
        
        .auth-container-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 3rem;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            z-index: 2;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 12px 24px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .auth-container-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 
                0 35px 70px rgba(0, 0, 0, 0.4),
                0 16px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        .auth-header-3d {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .auth-title-3d {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }
        
        .auth-subtitle-3d {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .nav-tabs-3d {
            border: none;
            margin-bottom: 2rem;
            background: rgba(15, 23, 42, 0.3);
            padding: 8px;
            border-radius: 15px;
        }
        
        .nav-tabs-3d .nav-item {
            flex: 1;
        }
        
        .nav-tabs-3d .nav-link {
            border: none;
            color: var(--text-secondary);
            background: transparent;
            text-align: center;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }
        
        .nav-tabs-3d .nav-link:hover {
            color: var(--accent-purple);
            background: rgba(99, 102, 241, 0.1);
        }
        
        .nav-tabs-3d .nav-link.active {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        
        .form-group-3d {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-input-3d {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 15px 20px 15px 50px;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 100%;
        }
        
        .form-input-3d:focus {
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background: rgba(15, 23, 42, 0.6);
            outline: none;
        }
        
        .form-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-purple);
            font-size: 1.1rem;
        }
        
        .submit-btn-3d {
            background: var(--gradient-primary);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn-3d:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
            color: white;
        }
        
        .submit-btn-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .submit-btn-3d:hover::before {
            left: 100%;
        }
        
        .back-home-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .back-home-btn:hover {
            background: var(--accent-purple);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <!-- Back to Home Button -->
    <a href="mainpage.php" class="back-home-btn">
        <i class="fas fa-arrow-left mr-2"></i>Back to Home
    </a>
    
    <div class="auth-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="auth-container-3d floating-element">
                        <div class="auth-header-3d">
                            <h1 class="auth-title-3d">
                                <i class="fas fa-shield-alt mr-3"></i>Security Gate
                            </h1>
                            <p class="auth-subtitle-3d">
                                Access the command center with authorized credentials
                            </p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="card-3d mb-4" style="border-color: var(--accent-orange);">
                                <div class="text-danger">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Access Denied</strong>
                                    <ul class="mb-0 mt-2">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_message): ?>
                            <div class="card-3d mb-4" style="border-color: var(--accent-green);">
                                <div class="text-success">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <?php echo htmlspecialchars($success_message); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 3D Navigation Tabs -->
                        <ul class="nav nav-tabs nav-tabs-3d d-flex" id="authTabs" role="tablist">
                            <li class="nav-item flex-fill" role="presentation">
                                <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Access Portal
                                </a>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab">
                                    <i class="fas fa-user-plus mr-2"></i>Create Admin
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Tab content -->
                        <div class="tab-content" id="authTabContent">
                            <!-- Login Tab -->
                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                <form method="post" class="needs-validation" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="login">
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input type="email" class="form-input-3d" id="loginEmail" name="email" 
                                               placeholder="Enter your admin email" required
                                               value="<?php echo isset($_POST['email']) && $_POST['action'] == 'login' ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    </div>
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <input type="password" class="form-input-3d" id="loginPassword" name="password" 
                                               placeholder="Enter your secure password" required>
                                    </div>
                                    
                                    <button type="submit" class="submit-btn-3d">
                                        <i class="fas fa-rocket mr-2"></i>Launch Access
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Register Tab -->
                            <div class="tab-pane fade" id="register" role="tabpanel">
                                <form method="post" class="needs-validation" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="register">
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input type="text" class="form-input-3d" id="registerName" name="name" 
                                               placeholder="Enter your full name" required
                                               value="<?php echo isset($_POST['name']) && $_POST['action'] == 'register' ? htmlspecialchars($_POST['name']) : ''; ?>">
                                    </div>
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input type="email" class="form-input-3d" id="registerEmail" name="email" 
                                               placeholder="Enter your email address" required
                                               value="<?php echo isset($_POST['email']) && $_POST['action'] == 'register' ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    </div>
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <input type="password" class="form-input-3d" id="registerPassword" name="password" 
                                               placeholder="Create a strong password" required
                                               minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}">
                                    </div>
                                    
                                    <div class="form-group-3d">
                                        <div class="form-input-icon">
                                            <i class="fas fa-shield-check"></i>
                                        </div>
                                        <input type="password" class="form-input-3d" id="confirmPassword" name="confirm_password" 
                                               placeholder="Confirm your password" required>
                                    </div>
                                    
                                    <div class="form-group-3d">
                                        <small class="text-secondary">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Password must contain 8+ characters with uppercase, lowercase, and number
                                        </small>
                                    </div>
                                    
                                    <button type="submit" class="submit-btn-3d">
                                        <i class="fas fa-user-plus mr-2"></i>Create Admin Portal
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Footer Links -->
                        <div class="text-center mt-4">
                            <p class="text-secondary mb-0">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Secured by Zephyr Authentication System
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Password confirmation validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>