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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="modern-3d.css">
    <style>
        .events-hero-3d {
            background: var(--gradient-dark);
            color: var(--text-primary);
            padding: 150px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .events-hero-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 25%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(236, 72, 153, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            animation: backgroundPulse 8s ease-in-out infinite;
        }
        
        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }
        
        .event-card-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 2rem;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        
        .event-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }
        
        .event-card-3d:hover {
            transform: translateY(-15px) rotateX(5deg) scale(1.02);
            box-shadow: var(--shadow-hover);
        }
        
        .event-card-3d:hover::before {
            opacity: 0.05;
        }
        
        .event-card-content {
            position: relative;
            z-index: 1;
        }
        
        .event-category-3d {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            letter-spacing: 0.5px;
        }
        
        .category-dance { 
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.8), rgba(255, 142, 142, 0.8)); 
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #fff; 
        }
        .category-music { 
            background: linear-gradient(135deg, rgba(78, 205, 196, 0.8), rgba(127, 219, 218, 0.8)); 
            border: 1px solid rgba(78, 205, 196, 0.3);
            color: #fff; 
        }
        .category-drama { 
            background: linear-gradient(135deg, rgba(69, 183, 209, 0.8), rgba(108, 199, 232, 0.8)); 
            border: 1px solid rgba(69, 183, 209, 0.3);
            color: #fff; 
        }
        .category-literary { 
            background: linear-gradient(135deg, rgba(249, 202, 36, 0.8), rgba(253, 216, 53, 0.8)); 
            border: 1px solid rgba(249, 202, 36, 0.3);
            color: #fff; 
        }
        .category-sports { 
            background: linear-gradient(135deg, rgba(108, 92, 231, 0.8), rgba(162, 155, 254, 0.8)); 
            border: 1px solid rgba(108, 92, 231, 0.3);
            color: #fff; 
        }
        .category-tech { 
            background: linear-gradient(135deg, rgba(253, 121, 168, 0.8), rgba(253, 203, 110, 0.8)); 
            border: 1px solid rgba(253, 121, 168, 0.3);
            color: #fff; 
        }
        
        .event-title-3d {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .event-meta-3d {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-description-3d {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .event-details-3d {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }
        
        .event-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            text-align: center;
        }
        
        .stat-item {
            padding: 0.8rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-purple);
            margin-bottom: 0.3rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .event-actions-3d {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-register-3d {
            padding: 0.8rem 2rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: 25px;
            color: var(--text-primary);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-register-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-secondary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .btn-register-3d:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            color: var(--text-primary);
            text-decoration: none;
        }
        
        .btn-register-3d:hover::before {
            opacity: 1;
        }
        
        .btn-register-3d span {
            position: relative;
            z-index: 1;
        }
        
        .filters-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        
        .search-box-3d {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        }
        
        .search-box-3d:focus {
            outline: none;
            border-color: var(--accent-purple);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
        }
        
        .search-box-3d::placeholder {
            color: var(--text-muted);
        }
        
        .filter-title-3d {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .no-events-3d {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            backdrop-filter: blur(20px) saturate(180%);
        }
        
        .no-events-icon {
            font-size: 4rem;
            color: var(--accent-purple);
            margin-bottom: 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .events-hero-3d {
                padding: 120px 0 80px;
            }
            
            .event-card-3d {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .event-card-3d:hover {
                transform: translateY(-10px) scale(1.01);
            }
            
            .event-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }
            
            .event-actions-3d {
                flex-direction: column;
            }
            
            .filters-3d {
                padding: 1.5rem;
            }
        }
        /* List view styles */
        .list-view .grid-3d {
            display: block;
        }
        .list-view .event-item {
            display: block !important;
            margin-bottom: 1rem;
        }
        .list-view .event-card-3d {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .list-view .event-card-content {
            flex: 1;
        }
    </style>
</head>
<body class="glass">
    <!-- Modern 3D Navigation -->
    <nav class="navbar-3d">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="navbar-brand">
                    <h2 class="text-gradient mb-0 floating-element">
                        <i class="fas fa-rocket mr-2"></i>ZEPHYR
                    </h2>
                    <small class="text-secondary d-block">Experience the Future</small>
                </div>
                
                <div class="d-none d-lg-flex align-items-center">
                    <a href="mainpage.php" class="nav-link-3d mx-2">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="events.php" class="nav-link-3d mx-2 active">
                        <i class="fas fa-calendar mr-2"></i>Events
                    </a>
                    <a href="plogin.php" class="nav-link-3d mx-2">
                        <i class="fas fa-user mr-2"></i>Portal
                    </a>
                    <a href="admin_auth.php" class="nav-link-3d mx-2">
                        <i class="fas fa-cog mr-2"></i>Admin
                    </a>
                    <a href="participantformnew.php" class="btn-modern ml-3">
                        <span><i class="fas fa-rocket mr-2"></i>Join Now</span>
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="btn btn-secondary d-lg-none" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div class="d-lg-none mt-3" id="mobileMenu" style="display: none;">
                <div class="card-3d">
                    <div class="d-flex flex-column">
                        <a href="mainpage.php" class="nav-link-3d mb-2">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                        <a href="events.php" class="nav-link-3d mb-2 active">
                            <i class="fas fa-calendar mr-2"></i>Events
                        </a>
                        <a href="plogin.php" class="nav-link-3d mb-2">
                            <i class="fas fa-user mr-2"></i>Portal
                        </a>
                        <a href="admin_auth.php" class="nav-link-3d mb-2">
                            <i class="fas fa-cog mr-2"></i>Admin
                        </a>
                        <a href="participantformnew.php" class="btn-modern">
                            <span><i class="fas fa-rocket mr-2"></i>Join Now</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Modern 3D Hero Section -->
    <section class="events-hero-3d">
        <div class="container">
            <div class="hero-content-3d floating-element">
                <h1 class="hero-title-3d">
                    <i class="fas fa-calendar-star mr-3"></i>Events Universe
                </h1>
                <p class="hero-subtitle-3d">
                    Dive into a cosmic journey of creativity, innovation, and limitless possibilities
                </p>
            </div>
        </div>
    </section>
    
    <div class="container my-5">
        <!-- Modern 3D Filters -->
        <div class="filters-3d">
            <h2 class="filter-title-3d">
                <i class="fas fa-search mr-2"></i>Discover Your Perfect Event
            </h2>
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" 
                           style="left: 1.5rem; top: 50%; transform: translateY(-50%); color: var(--accent-purple);"></i>
                        <input type="text" id="searchEvents" class="form-control search-box-3d pl-5" 
                               placeholder="Search by event name, description, or category...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-filter position-absolute" 
                           style="left: 1.5rem; top: 50%; transform: translateY(-50%); color: var(--accent-purple);"></i>
                        <select id="categoryFilter" class="form-control search-box-3d pl-5">
                            <option value="">All Categories</option>
                            <option value="dance">🕺 Dance</option>
                            <option value="music">🎵 Music</option>
                            <option value="drama">🎭 Drama</option>
                            <option value="literary">📚 Literary</option>
                            <option value="sports">⚽ Sports</option>
                            <option value="tech">💻 Technology</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end">
                    <div class="btn-group" role="group" aria-label="View toggle">
                        <button class="btn btn-sm btn-outline-light" id="gridViewBtn" aria-pressed="true" title="Grid view">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light" id="listViewBtn" aria-pressed="false" title="List view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modern 3D Events Grid -->
        <div class="grid-3d grid-cols-2" id="eventsContainer">
            <?php if ($events_result && $events_result->num_rows > 0): ?>
                <?php $delay = 0; ?>
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <div class="event-item floating-element" data-category="<?php echo $event['category']; ?>" 
                         style="animation-delay: <?php echo $delay * 0.1; ?>s;">
                        <div class="event-card-3d">
                            <div class="event-card-content">
                                <div class="category-<?php echo $event['category']; ?> event-category-3d">
                                    <i class="fas fa-star mr-2"></i>
                                    <?php echo ucfirst($event['category']); ?>
                                </div>
                                
                                <h3 class="event-title-3d"><?php echo htmlspecialchars($event['name']); ?></h3>
                                
                                <div class="event-meta-3d">
                                    <div class="event-meta-item">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <span><?php echo date('M j, Y', strtotime($event['start_date'])); ?></span>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="fas fa-clock text-cyan"></i>
                                        <span><?php echo date('g:i A', strtotime($event['start_date'])); ?></span>
                                    </div>
                                    <?php if ($event['venue']): ?>
                                        <div class="event-meta-item">
                                            <i class="fas fa-map-marker-alt text-pink"></i>
                                            <span><?php echo htmlspecialchars($event['venue']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="event-description-3d">
                                    <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 150))); ?>
                                    <?php if (strlen($event['description']) > 150): ?>
                                        <span class="text-muted">...</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="event-details-3d">
                                    <div class="event-stats">
                                        <?php if ($event['max_participants']): ?>
                                            <div class="stat-item">
                                                <div class="stat-value"><?php echo $event['max_participants']; ?></div>
                                                <div class="stat-label">Max Seats</div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="stat-item">
                                            <div class="stat-value">
                                                <?php echo $event['registration_fee'] > 0 ? '₹' . $event['registration_fee'] : 'FREE'; ?>
                                            </div>
                                            <div class="stat-label">Entry Fee</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">
                                                <?php echo date('M j', strtotime($event['registration_deadline'])); ?>
                                            </div>
                                            <div class="stat-label">Deadline</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="event-actions-3d">
                                    <?php if (strtotime($event['registration_deadline']) > time()): ?>
                                        <button class="btn-register-3d glow-effect" onclick="registerForEvent(<?php echo $event['id']; ?>)">
                                            <span>
                                                <i class="fas fa-rocket mr-2"></i>
                                                Join Adventure
                                            </span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-register-3d" disabled style="opacity: 0.5;">
                                            <span>
                                                <i class="fas fa-lock mr-2"></i>
                                                Registration Closed
                                            </span>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-register-3d btn-secondary" onclick="viewEventDetails(<?php echo $event['id']; ?>)">
                                        <span>
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Details
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $delay++; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-events-3d">
                        <div class="no-events-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3 class="text-gradient mb-3">No Events in the Cosmos Yet</h3>
                        <p class="text-secondary mb-4">
                            The event universe is being prepared. Check back soon for amazing adventures!
                        </p>
                        <a href="participantformnew.php" class="btn-register-3d">
                            <span>
                                <i class="fas fa-bell mr-2"></i>
                                Get Notified
                            </span>
                        </a>
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
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Utility: debounce
        function debounce(fn, delay) {
            let t;
            return function (...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        // DOM-ready equivalent
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchEvents');
            const categorySelect = document.getElementById('categoryFilter');

            const debouncedFilter = debounce(handleFilterEvents, 250);
            searchInput.addEventListener('input', debouncedFilter);
            categorySelect.addEventListener('change', debouncedFilter);

            // View toggle (grid / list)
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');
            const containerWrapper = document.querySelector('.container');
            const eventsWrapper = document.getElementById('eventsContainer');

            function setView(view) {
                if (view === 'list') {
                    document.body.classList.add('list-view');
                    gridBtn.setAttribute('aria-pressed', 'false');
                    listBtn.setAttribute('aria-pressed', 'true');
                } else {
                    document.body.classList.remove('list-view');
                    gridBtn.setAttribute('aria-pressed', 'true');
                    listBtn.setAttribute('aria-pressed', 'false');
                }
                localStorage.setItem('events_view', view);
            }

            // Initialize from preference
            const pref = localStorage.getItem('events_view') || 'grid';
            setView(pref);

            gridBtn.addEventListener('click', () => setView('grid'));
            listBtn.addEventListener('click', () => setView('list'));

            // Accessibility: allow Enter on search to focus first result
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const firstVisible = document.querySelector('.event-item:not([style*="display: none"])');
                    if (firstVisible) firstVisible.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            // Enhance register buttons: delegate click
            document.getElementById('eventsContainer').addEventListener('click', (e) => {
                const registerBtn = e.target.closest('[data-action="register"]');
                if (registerBtn) {
                    const eventId = registerBtn.dataset.eventId;
                    registerForEvent(eventId);
                }

                const detailsBtn = e.target.closest('[data-action="details"]');
                if (detailsBtn) {
                    const eventId = detailsBtn.dataset.eventId;
                    viewEventDetails(eventId);
                }
            });
        });

        function handleFilterEvents() {
            const searchTerm = document.getElementById('searchEvents').value.trim().toLowerCase();
            const selectedCategory = document.getElementById('categoryFilter').value;

            document.querySelectorAll('.event-item').forEach(item => {
                const titleEl = item.querySelector('.event-title-3d') || item.querySelector('h3');
                const descEl = item.querySelector('.event-description-3d') || item.querySelector('.event-description');
                const eventTitle = titleEl ? titleEl.textContent.toLowerCase() : '';
                const eventDescription = descEl ? descEl.textContent.toLowerCase() : '';
                const eventCategory = item.dataset.category || '';

                const matchesSearch = !searchTerm || eventTitle.includes(searchTerm) || eventDescription.includes(searchTerm);
                const matchesCategory = !selectedCategory || eventCategory === selectedCategory;

                if (matchesSearch && matchesCategory) {
                    item.style.display = '';
                    item.setAttribute('aria-hidden', 'false');
                } else {
                    item.style.display = 'none';
                    item.setAttribute('aria-hidden', 'true');
                }
            });
        }

        function registerForEvent(eventId) {
            <?php if ($participant_id): ?>
                // user logged in -> navigate to registration
                window.location.href = `event_registration.php?event_id=${eventId}`;
            <?php else: ?>
                // prompt login
                if (confirm('You need to login to register for events. Would you like to login now?')) {
                    window.location.href = 'plogin.php?redirect=events.php';
                }
            <?php endif; ?>
        }

        async function viewEventDetails(eventId) {
            const modalEl = document.getElementById('eventDetailsModal');
            const contentEl = document.getElementById('eventDetailsContent');
            try {
                contentEl.innerHTML = '<div class="p-4 text-center"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading...</div>';
                // Use fetch to get details (server should provide event_details.php)
                const res = await fetch(`event_details.php?id=${encodeURIComponent(eventId)}`);
                if (!res.ok) throw new Error('Network response was not ok');
                const html = await res.text();
                contentEl.innerHTML = html;
                // Show bootstrap modal
                $(modalEl).modal('show');
                // Move focus into modal for accessibility
                modalEl.querySelector('.modal-title')?.focus();
            } catch (err) {
                contentEl.innerHTML = '<div class="p-4 text-danger">Failed to load event details. Please try again.</div>';
                console.error(err);
            }
        }

        // Smooth scrolling for anchor links (vanilla)
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#"]')) {
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    </script>
</body>
</html>