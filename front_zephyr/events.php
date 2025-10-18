<?php
include "config.php";

// Get all events
$events_query = "SELECT * FROM events WHERE status IN ('upcoming', 'ongoing') ORDER BY start_date ASC";
$events_result = $mysqli->query($events_query);

// Get participant session if logged in
$participant_id = $_SESSION['pid'] ?? null;

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Zephyr Festival</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .events-hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8)), 
                        url('images/events-bg.jpg') center/cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .event-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .event-category {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        
        .category-dance { background: linear-gradient(45deg, #ff6b6b, #ff8e8e); color: white; }
        .category-music { background: linear-gradient(45deg, #4ecdc4, #7fdbda); color: white; }
        .category-drama { background: linear-gradient(45deg, #45b7d1, #6cc7e8); color: white; }
        .category-literary { background: linear-gradient(45deg, #f9ca24, #fdd835); color: white; }
        .category-sports { background: linear-gradient(45deg, #6c5ce7, #a29bfe); color: white; }
        .category-tech { background: linear-gradient(45deg, #fd79a8, #fdcb6e); color: white; }
        
        .event-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .event-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .event-description {
            color: #34495e;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .event-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .event-actions {
            text-align: center;
        }
        
        .btn-register {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .filters {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .search-box {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
        }
        
        .search-box:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        @media (max-width: 768px) {
            .events-hero {
                padding: 60px 0;
            }
            .event-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body class="glass">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(0, 0, 0, 0.9);">
        <div class="container">
            <a class="navbar-brand" href="mainpage.php">
                <i class="fas fa-music mr-2"></i>Zephyr
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="mainpage.php">Home</a></li>
                    <li class="nav-item active"><a class="nav-link" href="events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="plogin.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="participantformnew.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="events-hero">
        <div class="container">
            <h1 class="display-4 mb-4">Festival Events</h1>
            <p class="lead">Discover amazing events and showcase your talents at Zephyr Festival</p>
        </div>
    </section>
    
    <div class="container my-5">
        <!-- Filters -->
        <div class="filters">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <input type="text" id="searchEvents" class="form-control search-box" 
                           placeholder="Search events...">
                </div>
                <div class="col-md-6">
                    <select id="categoryFilter" class="form-control search-box">
                        <option value="">All Categories</option>
                        <option value="dance">Dance</option>
                        <option value="music">Music</option>
                        <option value="drama">Drama</option>
                        <option value="literary">Literary</option>
                        <option value="sports">Sports</option>
                        <option value="tech">Technology</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Events Grid -->
        <div class="row" id="eventsContainer">
            <?php if ($events_result && $events_result->num_rows > 0): ?>
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <div class="col-lg-6 event-item" data-category="<?php echo $event['category']; ?>">
                        <div class="event-card">
                            <div class="category-<?php echo $event['category']; ?> event-category">
                                <?php echo ucfirst($event['category']); ?>
                            </div>
                            
                            <h3 class="event-title"><?php echo htmlspecialchars($event['name']); ?></h3>
                            
                            <div class="event-meta">
                                <i class="fas fa-calendar mr-2"></i>
                                <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-clock mr-2"></i>
                                <?php echo date('g:i A', strtotime($event['start_date'])); ?>
                                <?php if ($event['venue']): ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <?php echo htmlspecialchars($event['venue']); ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="event-description">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 200))); ?>
                                <?php if (strlen($event['description']) > 200): ?>
                                    <span class="text-muted">...</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="event-details">
                                <div class="row text-center">
                                    <?php if ($event['max_participants']): ?>
                                        <div class="col-4">
                                            <strong>Max Participants</strong><br>
                                            <span class="text-primary"><?php echo $event['max_participants']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-4">
                                        <strong>Registration Fee</strong><br>
                                        <span class="text-success">
                                            <?php echo $event['registration_fee'] > 0 ? '₹' . $event['registration_fee'] : 'Free'; ?>
                                        </span>
                                    </div>
                                    <div class="col-4">
                                        <strong>Deadline</strong><br>
                                        <span class="text-warning">
                                            <?php echo date('M j', strtotime($event['registration_deadline'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="event-actions">
                                <?php if (strtotime($event['registration_deadline']) > time()): ?>
                                    <button class="btn btn-register" onclick="registerForEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-calendar-plus mr-2"></i>Register Now
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        Registration Closed
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-primary ml-2" onclick="viewEventDetails(<?php echo $event['id']; ?>)">
                                    <i class="fas fa-info-circle mr-2"></i>Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No Events Available</h3>
                        <p class="text-muted">Check back later for upcoming events!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="eventDetailsContent">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Search and filter functionality
        $(document).ready(function() {
            $('#searchEvents').on('keyup', filterEvents);
            $('#categoryFilter').on('change', filterEvents);
        });
        
        function filterEvents() {
            const searchTerm = $('#searchEvents').val().toLowerCase();
            const selectedCategory = $('#categoryFilter').val();
            
            $('.event-item').each(function() {
                const eventTitle = $(this).find('.event-title').text().toLowerCase();
                const eventCategory = $(this).data('category');
                const eventDescription = $(this).find('.event-description').text().toLowerCase();
                
                const matchesSearch = eventTitle.includes(searchTerm) || eventDescription.includes(searchTerm);
                const matchesCategory = !selectedCategory || eventCategory === selectedCategory;
                
                if (matchesSearch && matchesCategory) {
                    $(this).fadeIn();
                } else {
                    $(this).fadeOut();
                }
            });
        }
        
        function registerForEvent(eventId) {
            <?php if ($participant_id): ?>
                // User is logged in, proceed with registration
                window.location.href = `event_registration.php?event_id=${eventId}`;
            <?php else: ?>
                // User not logged in, redirect to login
                if (confirm('You need to login to register for events. Would you like to login now?')) {
                    window.location.href = 'plogin.php?redirect=events.php';
                }
            <?php endif; ?>
        }
        
        function viewEventDetails(eventId) {
            // Load event details via AJAX
            $.get(`event_details.php?id=${eventId}`, function(data) {
                $('#eventDetailsContent').html(data);
                $('#eventDetailsModal').modal('show');
            }).fail(function() {
                alert('Failed to load event details. Please try again.');
            });
        }
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(event) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
            }
        });
        
        // Add loading animation for registration buttons
        $('.btn-register').on('click', function() {
            const btn = $(this);
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
            btn.prop('disabled', true);
        });
    </script>
</body>
</html>