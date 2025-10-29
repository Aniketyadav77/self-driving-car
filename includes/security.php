<?php
session_start();

/**
 * Comprehensive Security Enhancement System
 * Zephyr Festival Management System
 * 
 * Features:
 * - Input Sanitization & Validation
 * - CSRF Protection
 * - SQL Injection Prevention
 * - XSS Protection
 * - Rate Limiting
 * - Session Security
 * - File Upload Security
 * - Password Security
 * - Logging & Monitoring
 */

class SecurityManager {
    private $mysqli;
    private $session_timeout = 3600; // 1 hour
    private $max_login_attempts = 5;
    private $rate_limit_window = 300; // 5 minutes
    private $max_requests_per_window = 100;
    private $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    private $max_file_size = 5242880; // 5MB
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
        $this->initializeSecurity();
    }
    
    /**
     * Initialize security measures
     */
    private function initializeSecurity() {
        // Set secure session configuration
        $this->configureSession();
        
        // Set security headers
        $this->setSecurityHeaders();
        
        // Initialize CSRF token
        $this->initCSRFToken();
        
        // Check session validity
        $this->validateSession();
    }
    
    /**
     * Configure secure session settings
     */
    private function configureSession() {
        // Secure session configuration
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Regenerate session ID periodically
        if (isset($_SESSION['last_regeneration']) && 
            (time() - $_SESSION['last_regeneration']) > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        } elseif (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    /**
     * Set security headers
     */
    private function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com; img-src \'self\' data:');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    /**
     * Initialize CSRF token
     */
    private function initCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    /**
     * Validate session security
     */
    private function validateSession() {
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $this->session_timeout) {
            $this->destroySession();
            return false;
        }
        $_SESSION['last_activity'] = time();
        
        // Validate IP address consistency
        if (isset($_SESSION['ip_address'])) {
            if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
                $this->logSecurityEvent('IP address mismatch', 'warning');
                $this->destroySession();
                return false;
            }
        } else {
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        }
        
        // Validate user agent consistency
        if (isset($_SESSION['user_agent'])) {
            if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                $this->logSecurityEvent('User agent mismatch', 'warning');
                $this->destroySession();
                return false;
            }
        } else {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        return true;
    }
    
    /**
     * Input sanitization and validation
     */
    public function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return $this->sanitizeInput($item, $type);
            }, $input);
        }
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            
            case 'string':
            default:
                // HTML encode and trim
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate input data
     */
    public function validateInput($input, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;
            
            foreach ($fieldRules as $rule => $parameter) {
                switch ($rule) {
                    case 'required':
                        if ($parameter && empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;
                    
                    case 'min_length':
                        if (!empty($value) && strlen($value) < $parameter) {
                            $errors[$field][] = ucfirst($field) . ' must be at least ' . $parameter . ' characters';
                        }
                        break;
                    
                    case 'max_length':
                        if (!empty($value) && strlen($value) > $parameter) {
                            $errors[$field][] = ucfirst($field) . ' must not exceed ' . $parameter . ' characters';
                        }
                        break;
                    
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid email address';
                        }
                        break;
                    
                    case 'phone':
                        if (!empty($value) && !preg_match('/^[0-9]{10}$/', $value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid 10-digit phone number';
                        }
                        break;
                    
                    case 'regex':
                        if (!empty($value) && !preg_match($parameter, $value)) {
                            $errors[$field][] = ucfirst($field) . ' format is invalid';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Prepare secure SQL statement
     */
    public function prepareStatement($query, $params = [], $types = '') {
        $stmt = $this->mysqli->prepare($query);
        
        if (!$stmt) {
            $this->logSecurityEvent('SQL prepare failed: ' . $this->mysqli->error, 'error');
            return false;
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                // Auto-detect types
                $types = str_repeat('s', count($params));
                foreach ($params as $i => $param) {
                    if (is_int($param)) {
                        $types[$i] = 'i';
                    } elseif (is_float($param)) {
                        $types[$i] = 'd';
                    }
                }
            }
            
            $stmt->bind_param($types, ...$params);
        }
        
        return $stmt;
    }
    
    /**
     * CSRF token validation
     */
    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->logSecurityEvent('CSRF token validation failed', 'warning');
            return false;
        }
        return true;
    }
    
    /**
     * Generate CSRF token for forms
     */
    public function getCSRFToken() {
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Rate limiting
     */
    public function checkRateLimit($identifier = null) {
        $identifier = $identifier ?: $_SERVER['REMOTE_ADDR'];
        $current_time = time();
        $window_start = $current_time - $this->rate_limit_window;
        
        // Clean old entries
        $cleanup_query = "DELETE FROM rate_limits WHERE timestamp < ?";
        $stmt = $this->prepareStatement($cleanup_query, [$window_start], 'i');
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }
        
        // Check current requests
        $check_query = "SELECT COUNT(*) as count FROM rate_limits WHERE identifier = ? AND timestamp > ?";
        $stmt = $this->prepareStatement($check_query, [$identifier, $window_start], 'si');
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();
            
            if ($count >= $this->max_requests_per_window) {
                $this->logSecurityEvent('Rate limit exceeded for ' . $identifier, 'warning');
                return false;
            }
        }
        
        // Record this request
        $record_query = "INSERT INTO rate_limits (identifier, timestamp) VALUES (?, ?)";
        $stmt = $this->prepareStatement($record_query, [$identifier, $current_time], 'si');
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }
        
        return true;
    }
    
    /**
     * Login attempt tracking
     */
    public function trackLoginAttempt($email, $success = false, $ip = null) {
        $ip = $ip ?: $_SERVER['REMOTE_ADDR'];
        $timestamp = time();
        
        $query = "INSERT INTO login_attempts (email, ip_address, success, timestamp) VALUES (?, ?, ?, ?)";
        $stmt = $this->prepareStatement($query, [$email, $ip, $success ? 1 : 0, $timestamp], 'ssii');
        
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }
        
        // Check for too many failed attempts
        if (!$success) {
            $window_start = $timestamp - 1800; // 30 minutes
            $check_query = "SELECT COUNT(*) as count FROM login_attempts 
                           WHERE (email = ? OR ip_address = ?) AND success = 0 AND timestamp > ?";
            $stmt = $this->prepareStatement($check_query, [$email, $ip, $window_start], 'ssi');
            
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_assoc()['count'];
                $stmt->close();
                
                if ($count >= $this->max_login_attempts) {
                    $this->logSecurityEvent('Too many failed login attempts for ' . $email . ' from ' . $ip, 'warning');
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Password security functions
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
    
    /**
     * File upload security
     */
    public function validateFileUpload($file, $allowed_types = null, $max_size = null) {
        $allowed_types = $allowed_types ?: $this->allowed_file_types;
        $max_size = $max_size ?: $this->max_file_size;
        $errors = [];
        
        // Check if file was uploaded
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'File is too large';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'File was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'No file was uploaded';
                    break;
                default:
                    $errors[] = 'Upload failed';
            }
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed_types)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (isset($allowed_mimes[$extension]) && $mime_type !== $allowed_mimes[$extension]) {
            $errors[] = 'File type mismatch';
        }
        
        // Check for malicious content (basic check)
        $content = file_get_contents($file['tmp_name'], false, null, 0, 1024);
        if (strpos($content, '<?php') !== false || strpos($content, '<script') !== false) {
            $errors[] = 'File contains potentially malicious content';
            $this->logSecurityEvent('Malicious file upload attempt: ' . $file['name'], 'warning');
        }
        
        return $errors;
    }
    
    /**
     * Security logging
     */
    public function logSecurityEvent($message, $level = 'info', $user_id = null) {
        $user_id = $user_id ?: ($_SESSION['participant_id'] ?? $_SESSION['admin_id'] ?? null);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $timestamp = time();
        
        $query = "INSERT INTO security_logs (user_id, message, level, ip_address, user_agent, timestamp) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepareStatement($query, [$user_id, $message, $level, $ip_address, $user_agent, $timestamp], 'issssi');
        
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }
        
        // Also log to file for critical events
        if (in_array($level, ['warning', 'error', 'critical'])) {
            $log_message = date('Y-m-d H:i:s') . " [$level] $message (User: $user_id, IP: $ip_address)\n";
            error_log($log_message, 3, 'logs/security.log');
        }
    }
    
    /**
     * Clean and optimize security tables
     */
    public function cleanupSecurityTables() {
        $week_ago = time() - (7 * 24 * 3600);
        $month_ago = time() - (30 * 24 * 3600);
        
        // Clean old rate limit entries
        $query1 = "DELETE FROM rate_limits WHERE timestamp < ?";
        $stmt1 = $this->prepareStatement($query1, [$week_ago], 'i');
        if ($stmt1) {
            $stmt1->execute();
            $stmt1->close();
        }
        
        // Clean old login attempts
        $query2 = "DELETE FROM login_attempts WHERE timestamp < ?";
        $stmt2 = $this->prepareStatement($query2, [$month_ago], 'i');
        if ($stmt2) {
            $stmt2->execute();
            $stmt2->close();
        }
        
        // Clean old security logs (keep only critical events)
        $query3 = "DELETE FROM security_logs WHERE timestamp < ? AND level NOT IN ('warning', 'error', 'critical')";
        $stmt3 = $this->prepareStatement($query3, [$month_ago], 'i');
        if ($stmt3) {
            $stmt3->execute();
            $stmt3->close();
        }
    }
    
    /**
     * Destroy session securely
     */
    public function destroySession() {
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    /**
     * Generate secure random string
     */
    public function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Check if request is AJAX
     */
    public function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get security dashboard data
     */
    public function getSecurityDashboard() {
        $data = [];
        
        // Recent security events
        $events_query = "SELECT * FROM security_logs WHERE level IN ('warning', 'error', 'critical') 
                        ORDER BY timestamp DESC LIMIT 10";
        $events_result = $this->mysqli->query($events_query);
        $data['recent_events'] = $events_result ? $events_result->fetch_all(MYSQLI_ASSOC) : [];
        
        // Failed login attempts in last 24 hours
        $day_ago = time() - (24 * 3600);
        $failed_logins_query = "SELECT COUNT(*) as count FROM login_attempts 
                               WHERE success = 0 AND timestamp > ?";
        $stmt = $this->prepareStatement($failed_logins_query, [$day_ago], 'i');
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data['failed_logins_24h'] = $result->fetch_assoc()['count'];
            $stmt->close();
        }
        
        // Top IPs by requests
        $top_ips_query = "SELECT ip_address, COUNT(*) as count FROM rate_limits 
                         WHERE timestamp > ? GROUP BY ip_address 
                         ORDER BY count DESC LIMIT 5";
        $stmt = $this->prepareStatement($top_ips_query, [time() - 3600], 'i');
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data['top_ips'] = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        
        return $data;
    }
}

// Initialize security manager
$security = new SecurityManager($mysqli);

// Example usage for forms
function renderCSRFField($security) {
    return '<input type="hidden" name="csrf_token" value="' . $security->getCSRFToken() . '">';
}

// Example middleware for protected pages
function requireAuthentication($user_type = null) {
    global $security;
    
    if ($user_type === 'admin' && !isset($_SESSION['admin_id'])) {
        header('Location: alogin.php');
        exit();
    } elseif ($user_type === 'participant' && !isset($_SESSION['participant_id'])) {
        header('Location: plogin.php');
        exit();
    }
    
    // Check rate limiting
    if (!$security->checkRateLimit()) {
        http_response_code(429);
        die('Too many requests. Please try again later.');
    }
}

// Example form validation
function validateRegistrationForm($data, $security) {
    $rules = [
        'name' => [
            'required' => true,
            'min_length' => 2,
            'max_length' => 100
        ],
        'email' => [
            'required' => true,
            'email' => true
        ],
        'phone' => [
            'required' => true,
            'phone' => true
        ],
        'password' => [
            'required' => true,
            'min_length' => 8
        ]
    ];
    
    $errors = $security->validateInput($data, $rules);
    
    // Additional password strength validation
    if (!empty($data['password'])) {
        $password_errors = $security->validatePasswordStrength($data['password']);
        if (!empty($password_errors)) {
            $errors['password'] = array_merge($errors['password'] ?? [], $password_errors);
        }
    }
    
    return $errors;
}

// Schedule cleanup (run this periodically via cron)
if (isset($_GET['cleanup']) && $_GET['cleanup'] === 'security') {
    $security->cleanupSecurityTables();
    echo json_encode(['success' => true, 'message' => 'Security tables cleaned']);
    exit();
}

// Security dashboard endpoint
if (isset($_GET['dashboard']) && $_GET['dashboard'] === 'security') {
    requireAuthentication('admin');
    
    header('Content-Type: application/json');
    echo json_encode($security->getSecurityDashboard());
    exit();
}
?>