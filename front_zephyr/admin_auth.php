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
    <title>Admin Access - Zephyr</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .nav-tabs .nav-link {
            border: none;
            color: #667eea;
        }
        .nav-tabs .nav-link.active {
            background: #667eea;
            color: white;
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
        }
        .form-control {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="text-center mb-4">
                        <h2>Zephyr Admin</h2>
                        <p class="text-muted">Secure Administrative Access</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-4" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">Login</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab">Register</a>
                        </li>
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content" id="authTabContent">
                        <!-- Login Tab -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form method="post" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="login">
                                
                                <div class="form-group">
                                    <label for="loginEmail">
                                        <i class="fas fa-envelope mr-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" id="loginEmail" name="email" required
                                           value="<?php echo isset($_POST['email']) && $_POST['action'] == 'login' ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="invalid-feedback">Please provide a valid email.</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="loginPassword">
                                        <i class="fas fa-lock mr-2"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                    <div class="invalid-feedback">Please provide a password.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </button>
                            </form>
                        </div>
                        
                        <!-- Register Tab -->
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <form method="post" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="register">
                                
                                <div class="form-group">
                                    <label for="registerName">
                                        <i class="fas fa-user mr-2"></i>Full Name
                                    </label>
                                    <input type="text" class="form-control" id="registerName" name="name" required
                                           value="<?php echo isset($_POST['name']) && $_POST['action'] == 'register' ? htmlspecialchars($_POST['name']) : ''; ?>">
                                    <div class="invalid-feedback">Please provide your name.</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="registerEmail">
                                        <i class="fas fa-envelope mr-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" id="registerEmail" name="email" required
                                           value="<?php echo isset($_POST['email']) && $_POST['action'] == 'register' ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="invalid-feedback">Please provide a valid email.</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="registerPassword">
                                        <i class="fas fa-lock mr-2"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="registerPassword" name="password" required
                                           minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}">
                                    <div class="invalid-feedback">Password must be at least 8 characters with uppercase, lowercase, and number.</div>
                                    <small class="text-muted">Password must contain at least 8 characters with uppercase, lowercase, and number.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirmPassword">
                                        <i class="fas fa-lock mr-2"></i>Confirm Password
                                    </label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                    <div class="invalid-feedback">Passwords must match.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-user-plus mr-2"></i>Create Admin Account
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="mainpage.php" class="text-muted">
                            <i class="fas fa-home mr-1"></i>Back to Home
                        </a>
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