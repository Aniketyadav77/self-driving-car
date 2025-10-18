<?php
// Enhanced database configuration with environment variables support
// Start session securely
if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
}

// Database configuration - use environment variables in production
$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'myuser';
$pw = $_ENV['DB_PASS'] ?? 'mypassword';
$schema = $_ENV['DB_NAME'] ?? 'zephyr';

// Enhanced MySQL connection with error handling
try {
    $mysqli = new mysqli($host, $user, $pw, $schema);
    $mysqli->set_charset("utf8mb4");
    
    if ($mysqli->connect_error) {
        error_log("Database connection failed: " . $mysqli->connect_error);
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    // Log error securely without exposing details to user
    error_log("Database error: " . $e->getMessage());
    die("Service temporarily unavailable. Please try again later.");
}

// Security functions
function sanitize_input($data) {
    global $mysqli;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $mysqli->real_escape_string($data);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Rate limiting function
function check_rate_limit($identifier, $max_attempts = 5, $time_window = 300) {
    $key = 'rate_limit_' . $identifier;
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $data = $_SESSION[$key];
    
    if (time() - $data['first_attempt'] > $time_window) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return true;
    }
    
    if ($data['count'] >= $max_attempts) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

// Password hashing functions
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
?>