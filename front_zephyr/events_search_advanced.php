<?php
include "linc.php";

// Handle AJAX search requests
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_asc';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    
    $limit = 12; // Events per page
    $offset = ($page - 1) * $limit;
    
    // Build query
    $where_conditions = ["status = 'active'"];
    $params = [];
    
    if (!empty($search)) {
        $search_escaped = mysqli_real_escape_string($mysqli, $search);
        $where_conditions[] = "(name LIKE '%$search_escaped%' OR description LIKE '%$search_escaped%' OR venue LIKE '%$search_escaped%')";
    }
    
    if (!empty($category) && $category !== 'all') {
        $category_escaped = mysqli_real_escape_string($mysqli, $category);
        $where_conditions[] = "category = '$category_escaped'";
    }
    
    if (!empty($date_from)) {
        $date_from_escaped = mysqli_real_escape_string($mysqli, $date_from);
        $where_conditions[] = "event_date >= '$date_from_escaped'";
    }
    
    if (!empty($date_to)) {
        $date_to_escaped = mysqli_real_escape_string($mysqli, $date_to);
        $where_conditions[] = "event_date <= '$date_to_escaped'";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Sort options
    $order_by = "created_at DESC";
    switch ($sort) {
        case 'name_asc':
            $order_by = "name ASC";
            break;
        case 'name_desc':
            $order_by = "name DESC";
            break;
        case 'date_asc':
            $order_by = "event_date ASC";
            break;
        case 'date_desc':
            $order_by = "event_date DESC";
            break;
        case 'popular':
            $order_by = "(SELECT COUNT(*) FROM participants WHERE event_id = events.id) DESC";
            break;
    }
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM events WHERE $where_clause";
    $count_result = mysqli_query($mysqli, $count_query);
    $total_events = $count_result ? mysqli_fetch_assoc($count_result)['total'] : 0;
    $total_pages = ceil($total_events / $limit);
    
    // Get events
    $events_query = "SELECT e.*, 
                     (SELECT COUNT(*) FROM participants WHERE event_id = e.id) as registered_count,
                     (SELECT MAX(created_at) FROM participants WHERE event_id = e.id) as last_registration
                     FROM events e 
                     WHERE $where_clause 
                     ORDER BY $order_by 
                     LIMIT $limit OFFSET $offset";
    
    $events_result = mysqli_query($mysqli, $events_query);
    $events = [];
    
    while ($events_result && $row = mysqli_fetch_assoc($events_result)) {
        $events[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_events' => $total_events,
            'per_page' => $limit
        ]
    ]);
    exit();
}

// Get categories for filter
$categories_query = "SELECT DISTINCT category FROM events WHERE status = 'active' ORDER BY category";
$categories_result = mysqli_query($mysqli, $categories_query);
$categories = [];
while ($categories_result && $row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Event Search | Zephyr Festival</title>
    
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
        
        .search-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-top: -4rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }
        
        .search-input {
            position: relative;
        }
        
        .search-input input {
            border: 2px solid #e9ecef;
            border-radius: 50px;
            padding: 1rem 3rem 1rem 1.5rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .search-input input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-50%) scale(1.05);
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .filter-tag {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0.25rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-tag:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }
        
        .filter-tag.active {
            background: var(--secondary-color);
        }
        
        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            height: 100%;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .event-image {
            height: 200px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            position: relative;
        }
        
        .event-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .event-meta {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .event-meta i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .event-description {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .event-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .registered-count {
            font-size: 0.9rem;
            color: var(--success-color);
            font-weight: 600;
        }
        
        .register-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .register-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }
        
        .loading-spinner {
            text-align: center;
            padding: 3rem;
            display: none;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #666;
            display: none;
        }
        
        .pagination-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 3rem;
        }
        
        .pagination-custom .page-link {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .pagination-custom .page-link:hover,
        .pagination-custom .page-link.active {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .search-header {
                padding: 2rem 0;
            }
            
            .search-container {
                padding: 1.5rem;
                margin-top: -2rem;
            }
            
            .filter-section {
                padding: 1rem;
            }
            
            .event-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Search Header -->
    <div class="search-header">
        <div class="container text-center">
            <h1 class="display-4 mb-3">Discover Events</h1>
            <p class="lead">Find and register for amazing events at Zephyr Festival</p>
        </div>
    </div>
    
    <div class="container">
        <!-- Search Container -->
        <div class="search-container">
            <div class="row align-items-end">
                <div class="col-lg-6">
                    <div class="search-input">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search events, venues, descriptions...">
                        <button class="search-btn" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <select class="form-select" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-2">
                    <input type="date" class="form-control" id="dateFrom" placeholder="From Date">
                </div>
                
                <div class="col-lg-2">
                    <input type="date" class="form-control" id="dateTo" placeholder="To Date">
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-lg-6">
                    <select class="form-select" id="sortBy">
                        <option value="date_asc">Sort by Date (Earliest)</option>
                        <option value="date_desc">Sort by Date (Latest)</option>
                        <option value="name_asc">Sort by Name (A-Z)</option>
                        <option value="name_desc">Sort by Name (Z-A)</option>
                        <option value="popular">Sort by Popularity</option>
                    </select>
                </div>
                
                <div class="col-lg-6">
                    <button class="btn btn-outline-secondary me-2" id="clearFilters">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                    <button class="btn btn-primary" id="advancedSearch">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Filters -->
        <div class="filter-section">
            <h6 class="mb-3">Quick Filters</h6>
            <div id="quickFilters">
                <span class="filter-tag" data-category="all">All Events</span>
                <?php foreach ($categories as $category): ?>
                    <span class="filter-tag" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div id="resultsInfo" class="text-muted"></div>
            <div id="viewToggle">
                <button class="btn btn-sm btn-outline-primary active" data-view="grid">
                    <i class="fas fa-th"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary" data-view="list">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        
        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Searching events...</p>
        </div>
        
        <!-- No Results -->
        <div class="no-results" id="noResults">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5>No events found</h5>
            <p>Try adjusting your search criteria or browse all events</p>
        </div>
        
        <!-- Events Grid -->
        <div class="row" id="eventsContainer">
            <!-- Events will be loaded here via AJAX -->
        </div>
        
        <!-- Pagination -->
        <div class="pagination-custom" id="paginationContainer">
            <!-- Pagination will be loaded here via AJAX -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        class AdvancedEventSearch {
            constructor() {
                this.currentPage = 1;
                this.currentView = 'grid';
                this.searchTimeout = null;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.loadEvents();
            }
            
            setupEventListeners() {
                // Search input with debouncing
                document.getElementById('searchInput').addEventListener('input', (e) => {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.currentPage = 1;
                        this.loadEvents();
                    }, 300);
                });
                
                // Filter changes
                ['categoryFilter', 'dateFrom', 'dateTo', 'sortBy'].forEach(id => {
                    document.getElementById(id).addEventListener('change', () => {
                        this.currentPage = 1;
                        this.loadEvents();
                    });
                });
                
                // Quick filters
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.addEventListener('click', (e) => {
                        document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
                        e.target.classList.add('active');
                        document.getElementById('categoryFilter').value = e.target.dataset.category;
                        this.currentPage = 1;
                        this.loadEvents();
                    });
                });
                
                // Clear filters
                document.getElementById('clearFilters').addEventListener('click', () => {
                    document.getElementById('searchInput').value = '';
                    document.getElementById('categoryFilter').value = 'all';
                    document.getElementById('dateFrom').value = '';
                    document.getElementById('dateTo').value = '';
                    document.getElementById('sortBy').value = 'date_asc';
                    document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
                    document.querySelector('.filter-tag[data-category="all"]').classList.add('active');
                    this.currentPage = 1;
                    this.loadEvents();
                });
                
                // View toggle
                document.querySelectorAll('[data-view]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
                        e.target.classList.add('active');
                        this.currentView = e.target.dataset.view;
                        this.renderEvents(this.lastResults);
                    });
                });
                
                // Search button
                document.getElementById('searchBtn').addEventListener('click', () => {
                    this.currentPage = 1;
                    this.loadEvents();
                });
            }
            
            async loadEvents() {
                const params = new URLSearchParams({
                    ajax: '1',
                    search: document.getElementById('searchInput').value,
                    category: document.getElementById('categoryFilter').value,
                    date_from: document.getElementById('dateFrom').value,
                    date_to: document.getElementById('dateTo').value,
                    sort: document.getElementById('sortBy').value,
                    page: this.currentPage
                });
                
                this.showLoading(true);
                
                try {
                    const response = await fetch(`?${params.toString()}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.lastResults = data;
                        this.renderEvents(data);
                        this.renderPagination(data.pagination);
                        this.updateResultsInfo(data.pagination);
                    } else {
                        this.showError('Failed to load events');
                    }
                } catch (error) {
                    this.showError('Network error occurred');
                }
                
                this.showLoading(false);
            }
            
            renderEvents(data) {
                const container = document.getElementById('eventsContainer');
                
                if (data.events.length === 0) {
                    container.innerHTML = '';
                    document.getElementById('noResults').style.display = 'block';
                    return;
                }
                
                document.getElementById('noResults').style.display = 'none';
                
                const eventsHtml = data.events.map(event => {
                    const colClass = this.currentView === 'grid' ? 'col-lg-4 col-md-6' : 'col-12';
                    const cardClass = this.currentView === 'list' ? 'd-flex' : '';
                    
                    return `
                        <div class="${colClass}">
                            <div class="event-card ${cardClass}">
                                <div class="event-image">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div class="event-badge">${this.formatCategory(event.category)}</div>
                                </div>
                                <div class="event-content">
                                    <h5 class="event-title">${this.escapeHtml(event.name)}</h5>
                                    <div class="event-meta">
                                        <span><i class="fas fa-calendar"></i> ${this.formatDate(event.event_date)}</span>
                                        <span class="ms-3"><i class="fas fa-map-marker-alt"></i> ${this.escapeHtml(event.venue || 'TBA')}</span>
                                    </div>
                                    <p class="event-description">${this.truncateText(event.description || '', 120)}</p>
                                    <div class="event-stats">
                                        <span class="registered-count">
                                            <i class="fas fa-users"></i> ${event.registered_count} registered
                                        </span>
                                        <button class="register-btn" onclick="registerForEvent(${event.id})">
                                            Register Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                container.innerHTML = eventsHtml;
            }
            
            renderPagination(pagination) {
                const container = document.getElementById('paginationContainer');
                
                if (pagination.total_pages <= 1) {
                    container.innerHTML = '';
                    return;
                }
                
                let paginationHtml = '';
                
                // Previous button
                if (pagination.current_page > 1) {
                    paginationHtml += `
                        <button class="page-link" onclick="eventSearch.changePage(${pagination.current_page - 1})">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    `;
                }
                
                // Page numbers
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === pagination.current_page ? 'active' : '';
                    paginationHtml += `
                        <button class="page-link ${activeClass}" onclick="eventSearch.changePage(${i})">
                            ${i}
                        </button>
                    `;
                }
                
                // Next button
                if (pagination.current_page < pagination.total_pages) {
                    paginationHtml += `
                        <button class="page-link" onclick="eventSearch.changePage(${pagination.current_page + 1})">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    `;
                }
                
                container.innerHTML = paginationHtml;
            }
            
            updateResultsInfo(pagination) {
                const info = document.getElementById('resultsInfo');
                const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
                const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_events);
                
                info.textContent = `Showing ${start}-${end} of ${pagination.total_events} events`;
            }
            
            changePage(page) {
                this.currentPage = page;
                this.loadEvents();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            
            showLoading(show) {
                document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
                document.getElementById('eventsContainer').style.display = show ? 'none' : 'block';
            }
            
            showError(message) {
                alert(message); // In production, use a proper toast/notification system
            }
            
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }
            
            formatCategory(category) {
                return category.charAt(0).toUpperCase() + category.slice(1);
            }
            
            truncateText(text, length) {
                return text.length > length ? text.substring(0, length) + '...' : text;
            }
            
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
        
        // Initialize search
        const eventSearch = new AdvancedEventSearch();
        
        // Registration function (placeholder)
        function registerForEvent(eventId) {
            window.location.href = `registration_enhanced.php?event_id=${eventId}`;
        }
    </script>
</body>
</html>