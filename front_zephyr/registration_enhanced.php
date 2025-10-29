<?php
include "front_zephyr/linc.php";

// Handle form submission
if ($_POST) {
    $response = array('success' => false, 'message' => '', 'errors' => array());
    
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid request. Please try again.';
        echo json_encode($response);
        exit();
    }
    
    // Sanitize and validate input
    $name = trim(mysqli_real_escape_string($mysqli, $_POST['name']));
    $email = trim(mysqli_real_escape_string($mysqli, $_POST['email']));
    $phone = trim(mysqli_real_escape_string($mysqli, $_POST['phone']));
    $college = trim(mysqli_real_escape_string($mysqli, $_POST['college']));
    $event_id = intval($_POST['event_id']);
    $year = intval($_POST['year']);
    
    // Validation
    if (empty($name)) {
        $response['errors']['name'] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $response['errors']['name'] = 'Name must be at least 2 characters';
    }
    
    if (empty($email)) {
        $response['errors']['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors']['email'] = 'Invalid email format';
    } else {
        // Check if email already registered for this event
        $email_check = mysqli_query($mysqli, "SELECT id FROM participants WHERE email = '$email' AND event_id = $event_id");
        if (mysqli_num_rows($email_check) > 0) {
            $response['errors']['email'] = 'Email already registered for this event';
        }
    }
    
    if (empty($phone)) {
        $response['errors']['phone'] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $response['errors']['phone'] = 'Phone number must be 10 digits';
    }
    
    if (empty($college)) {
        $response['errors']['college'] = 'College name is required';
    }
    
    if ($event_id <= 0) {
        $response['errors']['event_id'] = 'Please select an event';
    } else {
        // Verify event exists and has capacity
        $event_check = mysqli_query($mysqli, "SELECT * FROM events WHERE id = $event_id");
        if (mysqli_num_rows($event_check) == 0) {
            $response['errors']['event_id'] = 'Invalid event selected';
        } else {
            $event = mysqli_fetch_assoc($event_check);
            $registered_count = mysqli_num_rows(mysqli_query($mysqli, "SELECT id FROM participants WHERE event_id = $event_id"));
            if ($registered_count >= $event['max_participants']) {
                $response['errors']['event_id'] = 'Event is full. Registration closed.';
            }
        }
    }
    
    if ($year < 1 || $year > 4) {
        $response['errors']['year'] = 'Invalid year selected';
    }
    
    // If no errors, proceed with registration
    if (empty($response['errors'])) {
        $registration_id = 'ZEP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $created_at = date('Y-m-d H:i:s');
        
        $insert_query = "INSERT INTO participants (registration_id, name, email, phone, college, year, event_id, created_at) 
                        VALUES ('$registration_id', '$name', '$email', '$phone', '$college', $year, $event_id, '$created_at')";
        
        if (mysqli_query($mysqli, $insert_query)) {
            $response['success'] = true;
            $response['message'] = 'Registration successful!';
            $response['registration_id'] = $registration_id;
            
            // Send confirmation email (in real implementation)
            // sendConfirmationEmail($email, $name, $registration_id, $event['name']);
            
        } else {
            $response['message'] = 'Registration failed. Please try again.';
        }
    } else {
        $response['message'] = 'Please correct the errors below';
    }
    
    echo json_encode($response);
    exit();
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get events for dropdown
$events_query = "SELECT * FROM events WHERE status = 'active' ORDER BY name";
$events_result = mysqli_query($mysqli, $events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Registration | Zephyr Festival</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .registration-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .registration-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        .form-control.is-valid {
            border-color: var(--success-color);
            padding-right: 2.5rem;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
            padding-right: 2.5rem;
        }
        
        .valid-feedback, .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .validation-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .success-message {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .error-message {
            background: var(--danger-color);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .progress-container {
            margin-bottom: 2rem;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .registration-form {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-form">
            <div class="text-center mb-4">
                <h2 class="h3 mb-2">Event Registration</h2>
                <p class="text-muted">Join Zephyr Festival - Register for exciting events!</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Complete all fields</small>
            </div>
            
            <!-- Messages -->
            <div class="success-message" id="successMessage"></div>
            <div class="error-message" id="errorMessage"></div>
            
            <!-- Registration Form -->
            <form id="registrationForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Looks good!</div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Looks good!</div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </label>
                    <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" required>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Looks good!</div>
                </div>
                
                <div class="form-group">
                    <label for="college" class="form-label">
                        <i class="fas fa-graduation-cap"></i>
                        College/University
                    </label>
                    <input type="text" class="form-control" id="college" name="college" required>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Looks good!</div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year" class="form-label">
                                <i class="fas fa-calendar"></i>
                                Year
                            </label>
                            <select class="form-control" id="year" name="year" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                            <div class="validation-icon"></div>
                            <div class="invalid-feedback"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_id" class="form-label">
                                <i class="fas fa-star"></i>
                                Event
                            </label>
                            <select class="form-control" id="event_id" name="event_id" required>
                                <option value="">Select Event</option>
                                <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                                    <option value="<?php echo $event['id']; ?>">
                                        <?php echo htmlspecialchars($event['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="validation-icon"></div>
                            <div class="invalid-feedback"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register" id="submitBtn">
                    <span class="btn-text">Register Now</span>
                    <span class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        class EnhancedRegistration {
            constructor() {
                this.form = document.getElementById('registrationForm');
                this.submitBtn = document.getElementById('submitBtn');
                this.progressBar = document.querySelector('.progress-bar');
                this.fields = this.form.querySelectorAll('input[required], select[required]');
                
                this.init();
            }
            
            init() {
                this.setupRealTimeValidation();
                this.setupFormSubmission();
                this.updateProgress();
            }
            
            setupRealTimeValidation() {
                this.fields.forEach(field => {
                    field.addEventListener('input', () => this.validateField(field));
                    field.addEventListener('blur', () => this.validateField(field));
                });
            }
            
            validateField(field) {
                const value = field.value.trim();
                const fieldName = field.name;
                let isValid = true;
                let message = '';
                
                // Remove existing validation classes
                field.classList.remove('is-valid', 'is-invalid');
                
                // Validation logic
                switch (fieldName) {
                    case 'name':
                        if (!value) {
                            isValid = false;
                            message = 'Name is required';
                        } else if (value.length < 2) {
                            isValid = false;
                            message = 'Name must be at least 2 characters';
                        }
                        break;
                        
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!value) {
                            isValid = false;
                            message = 'Email is required';
                        } else if (!emailRegex.test(value)) {
                            isValid = false;
                            message = 'Invalid email format';
                        }
                        break;
                        
                    case 'phone':
                        const phoneRegex = /^[0-9]{10}$/;
                        if (!value) {
                            isValid = false;
                            message = 'Phone number is required';
                        } else if (!phoneRegex.test(value)) {
                            isValid = false;
                            message = 'Phone number must be 10 digits';
                        }
                        break;
                        
                    case 'college':
                        if (!value) {
                            isValid = false;
                            message = 'College name is required';
                        }
                        break;
                        
                    case 'year':
                    case 'event_id':
                        if (!value) {
                            isValid = false;
                            message = 'Please make a selection';
                        }
                        break;
                }
                
                // Apply validation state
                if (isValid && value) {
                    field.classList.add('is-valid');
                    this.setValidationIcon(field, 'valid');
                } else if (!isValid) {
                    field.classList.add('is-invalid');
                    this.setValidationIcon(field, 'invalid');
                    field.parentElement.querySelector('.invalid-feedback').textContent = message;
                } else {
                    this.setValidationIcon(field, 'none');
                }
                
                this.updateProgress();
                return isValid;
            }
            
            setValidationIcon(field, state) {
                const iconContainer = field.parentElement.querySelector('.validation-icon');
                iconContainer.innerHTML = '';
                
                if (state === 'valid') {
                    iconContainer.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                } else if (state === 'invalid') {
                    iconContainer.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                }
            }
            
            updateProgress() {
                const validFields = Array.from(this.fields).filter(field => {
                    return field.classList.contains('is-valid');
                });
                
                const progress = (validFields.length / this.fields.length) * 100;
                this.progressBar.style.width = progress + '%';
                this.progressBar.setAttribute('aria-valuenow', progress);
                
                // Enable/disable submit button
                this.submitBtn.disabled = progress < 100;
            }
            
            setupFormSubmission() {
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm();
                });
            }
            
            async submitForm() {
                // Show loading state
                this.setLoadingState(true);
                
                const formData = new FormData(this.form);
                
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.showSuccess(result.message + ' Your registration ID is: ' + result.registration_id);
                        this.form.reset();
                        this.fields.forEach(field => {
                            field.classList.remove('is-valid', 'is-invalid');
                            this.setValidationIcon(field, 'none');
                        });
                        this.updateProgress();
                    } else {
                        if (result.errors) {
                            Object.keys(result.errors).forEach(fieldName => {
                                const field = this.form.querySelector(`[name="${fieldName}"]`);
                                if (field) {
                                    field.classList.add('is-invalid');
                                    field.parentElement.querySelector('.invalid-feedback').textContent = result.errors[fieldName];
                                    this.setValidationIcon(field, 'invalid');
                                }
                            });
                        }
                        this.showError(result.message);
                    }
                } catch (error) {
                    this.showError('Network error. Please check your connection and try again.');
                }
                
                this.setLoadingState(false);
            }
            
            setLoadingState(loading) {
                const btnText = this.submitBtn.querySelector('.btn-text');
                const spinner = this.submitBtn.querySelector('.loading-spinner');
                
                if (loading) {
                    btnText.style.display = 'none';
                    spinner.style.display = 'inline';
                    this.submitBtn.disabled = true;
                } else {
                    btnText.style.display = 'inline';
                    spinner.style.display = 'none';
                    this.submitBtn.disabled = false;
                }
            }
            
            showSuccess(message) {
                const successEl = document.getElementById('successMessage');
                successEl.textContent = message;
                successEl.style.display = 'block';
                document.getElementById('errorMessage').style.display = 'none';
                successEl.scrollIntoView({ behavior: 'smooth' });
            }
            
            showError(message) {
                const errorEl = document.getElementById('errorMessage');
                errorEl.textContent = message;
                errorEl.style.display = 'block';
                document.getElementById('successMessage').style.display = 'none';
                errorEl.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        // Initialize enhanced registration
        document.addEventListener('DOMContentLoaded', () => {
            new EnhancedRegistration();
        });
    </script>
</body>
</html>