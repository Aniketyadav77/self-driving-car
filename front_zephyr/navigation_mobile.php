<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navigation | Zephyr Festival</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --dark-color: #1a1a1a;
            --light-color: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border-color: #e9ecef;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: #f8f9fa;
        }
        
        /* Navigation Styles */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }
        
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow);
            padding: 0.5rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-logo i {
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 500;
            position: relative;
            transition: var(--transition);
            padding: 0.5rem 0;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        .nav-cta {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white !important;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white !important;
        }
        
        /* Mobile Menu Toggle */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
            padding: 0.5rem;
            transition: var(--transition);
        }
        
        .nav-toggle:hover {
            color: var(--primary-color);
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                height: 100vh;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
                gap: 0;
                padding: 5rem 0 2rem;
                transition: right 0.3s ease;
                overflow-y: auto;
            }
            
            .nav-menu.active {
                right: 0;
            }
            
            .nav-menu li {
                width: 100%;
            }
            
            .nav-link {
                display: block;
                color: white;
                padding: 1rem 2rem;
                font-size: 1.1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                transition: var(--transition);
            }
            
            .nav-link:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                padding-left: 2.5rem;
            }
            
            .nav-link::after {
                display: none;
            }
            
            .nav-cta {
                margin: 1rem 2rem;
                background: white;
                color: var(--primary-color) !important;
                text-align: center;
                display: block;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
            
            .nav-cta:hover {
                background: rgba(255, 255, 255, 0.9);
                color: var(--primary-color) !important;
                transform: none;
            }
            
            .nav-toggle {
                display: block;
            }
            
            /* Mobile menu close button */
            .nav-close {
                position: absolute;
                top: 1rem;
                right: 2rem;
                background: none;
                border: none;
                color: white;
                font-size: 2rem;
                cursor: pointer;
                padding: 0.5rem;
                transition: var(--transition);
            }
            
            .nav-close:hover {
                transform: rotate(90deg);
            }
        }
        
        /* Overlay */
        .nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }
        
        .nav-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Touch gestures indicator */
        .gesture-indicator {
            position: fixed;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }
        
        .gesture-indicator.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Demo content */
        .demo-content {
            margin-top: 100px;
            padding: 2rem 1rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .demo-section {
            background: white;
            padding: 3rem;
            margin: 2rem 0;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        
        .demo-section h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .nav-menu.active .nav-link {
            animation: slideIn 0.3s ease forwards;
        }
        
        .nav-menu.active .nav-link:nth-child(1) { animation-delay: 0.1s; }
        .nav-menu.active .nav-link:nth-child(2) { animation-delay: 0.2s; }
        .nav-menu.active .nav-link:nth-child(3) { animation-delay: 0.3s; }
        .nav-menu.active .nav-link:nth-child(4) { animation-delay: 0.4s; }
        .nav-menu.active .nav-link:nth-child(5) { animation-delay: 0.5s; }
        .nav-menu.active .nav-link:nth-child(6) { animation-delay: 0.6s; }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Focus styles */
        .nav-toggle:focus,
        .nav-link:focus,
        .nav-cta:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="nav-logo">
                <i class="fas fa-star"></i>
                Zephyr Festival
            </a>
            
            <ul class="nav-menu" id="navMenu">
                <button class="nav-close" id="navClose" aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>
                
                <li><a href="mainpage.php" class="nav-link active">Home</a></li>
                <li><a href="events_search_advanced.php" class="nav-link">Events</a></li>
                <li><a href="registration_enhanced.php" class="nav-link">Register</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
                <li><a href="plogin.php" class="nav-cta">Participant Login</a></li>
            </ul>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    
    <!-- Overlay -->
    <div class="nav-overlay" id="navOverlay"></div>
    
    <!-- Gesture indicator -->
    <div class="gesture-indicator" id="gestureIndicator">
        Swipe from right edge to open menu
    </div>
    
    <!-- Demo Content -->
    <div class="demo-content">
        <div class="demo-section">
            <h2>Responsive Mobile Navigation</h2>
            <p>This navigation component features:</p>
            <ul>
                <li>Mobile-first responsive design</li>
                <li>Touch gesture support (swipe from right edge)</li>
                <li>Smooth animations and transitions</li>
                <li>Accessibility compliance</li>
                <li>Glassmorphism effects</li>
                <li>Auto-hide on scroll (mobile)</li>
            </ul>
        </div>
        
        <div class="demo-section">
            <h2>Features</h2>
            <p>Try the following interactions:</p>
            <ul>
                <li>Click the hamburger menu on mobile</li>
                <li>Swipe from the right edge of the screen (mobile)</li>
                <li>Scroll to see the navbar behavior change</li>
                <li>Tap outside the menu to close it</li>
            </ul>
        </div>
        
        <!-- Add more sections for demo scrolling -->
        <div class="demo-section"><h2>Section 1</h2><p>Content for scrolling demo...</p></div>
        <div class="demo-section"><h2>Section 2</h2><p>Content for scrolling demo...</p></div>
        <div class="demo-section"><h2>Section 3</h2><p>Content for scrolling demo...</p></div>
        <div class="demo-section"><h2>Section 4</h2><p>Content for scrolling demo...</p></div>
        <div class="demo-section"><h2>Section 5</h2><p>Content for scrolling demo...</p></div>
    </div>

    <script>
        class ResponsiveNavigation {
            constructor() {
                this.navbar = document.getElementById('navbar');
                this.navMenu = document.getElementById('navMenu');
                this.navToggle = document.getElementById('navToggle');
                this.navClose = document.getElementById('navClose');
                this.navOverlay = document.getElementById('navOverlay');
                this.gestureIndicator = document.getElementById('gestureIndicator');
                
                this.isMenuOpen = false;
                this.lastScrollY = window.scrollY;
                this.touchStartX = 0;
                this.touchStartY = 0;
                this.touchEndX = 0;
                this.touchEndY = 0;
                this.gestureThreshold = 50;
                this.edgeThreshold = 30;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.setupTouchGestures();
                this.setupScrollBehavior();
                this.showGestureHint();
            }
            
            setupEventListeners() {
                // Menu toggle
                this.navToggle.addEventListener('click', () => this.toggleMenu());
                this.navClose.addEventListener('click', () => this.closeMenu());
                this.navOverlay.addEventListener('click', () => this.closeMenu());
                
                // Close menu when clicking nav links (mobile)
                this.navMenu.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        if (this.isMenuOpen) {
                            this.closeMenu();
                        }
                    });
                });
                
                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isMenuOpen) {
                        this.closeMenu();
                    }
                });
                
                // Window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth > 768 && this.isMenuOpen) {
                        this.closeMenu();
                    }
                });
            }
            
            setupTouchGestures() {
                let touchStarted = false;
                
                // Touch start
                document.addEventListener('touchstart', (e) => {
                    this.touchStartX = e.touches[0].clientX;
                    this.touchStartY = e.touches[0].clientY;
                    touchStarted = true;
                    
                    // Check if touch started from right edge
                    if (this.touchStartX > window.innerWidth - this.edgeThreshold) {
                        this.gestureIndicator.classList.add('show');
                    }
                }, { passive: true });
                
                // Touch move
                document.addEventListener('touchmove', (e) => {
                    if (!touchStarted) return;
                    
                    const currentX = e.touches[0].clientX;
                    const currentY = e.touches[0].clientY;
                    const deltaX = this.touchStartX - currentX;
                    const deltaY = this.touchStartY - currentY;
                    
                    // Hide gesture indicator if moved too much vertically
                    if (Math.abs(deltaY) > 50) {
                        this.gestureIndicator.classList.remove('show');
                    }
                }, { passive: true });
                
                // Touch end
                document.addEventListener('touchend', (e) => {
                    if (!touchStarted) return;
                    
                    this.touchEndX = e.changedTouches[0].clientX;
                    this.touchEndY = e.changedTouches[0].clientY;
                    
                    this.handleGesture();
                    this.gestureIndicator.classList.remove('show');
                    touchStarted = false;
                }, { passive: true });
            }
            
            handleGesture() {
                const deltaX = this.touchStartX - this.touchEndX;
                const deltaY = this.touchStartY - this.touchEndY;
                
                // Check if it's a horizontal swipe
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    // Swipe from right edge to left (open menu)
                    if (deltaX > this.gestureThreshold && 
                        this.touchStartX > window.innerWidth - this.edgeThreshold &&
                        !this.isMenuOpen) {
                        this.openMenu();
                    }
                    // Swipe from left to right (close menu)
                    else if (deltaX < -this.gestureThreshold && this.isMenuOpen) {
                        this.closeMenu();
                    }
                }
            }
            
            setupScrollBehavior() {
                let scrollTimeout;
                
                window.addEventListener('scroll', () => {
                    const currentScrollY = window.scrollY;
                    
                    // Add/remove scrolled class
                    if (currentScrollY > 50) {
                        this.navbar.classList.add('scrolled');
                    } else {
                        this.navbar.classList.remove('scrolled');
                    }
                    
                    // Auto-hide navbar on scroll down (mobile only)
                    if (window.innerWidth <= 768) {
                        clearTimeout(scrollTimeout);
                        
                        if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
                            // Scrolling down
                            this.navbar.style.transform = 'translateY(-100%)';
                        } else {
                            // Scrolling up
                            this.navbar.style.transform = 'translateY(0)';
                        }
                        
                        scrollTimeout = setTimeout(() => {
                            this.navbar.style.transform = 'translateY(0)';
                        }, 1000);
                    } else {
                        this.navbar.style.transform = 'translateY(0)';
                    }
                    
                    this.lastScrollY = currentScrollY;
                }, { passive: true });
            }
            
            toggleMenu() {
                if (this.isMenuOpen) {
                    this.closeMenu();
                } else {
                    this.openMenu();
                }
            }
            
            openMenu() {
                this.navMenu.classList.add('active');
                this.navOverlay.classList.add('active');
                this.navToggle.querySelector('i').className = 'fas fa-times';
                this.isMenuOpen = true;
                
                // Prevent body scrolling
                document.body.style.overflow = 'hidden';
                
                // Focus management
                setTimeout(() => {
                    const firstLink = this.navMenu.querySelector('.nav-link');
                    if (firstLink) firstLink.focus();
                }, 300);
                
                // Announce to screen readers
                this.announceToScreenReader('Menu opened');
            }
            
            closeMenu() {
                this.navMenu.classList.remove('active');
                this.navOverlay.classList.remove('active');
                this.navToggle.querySelector('i').className = 'fas fa-bars';
                this.isMenuOpen = false;
                
                // Restore body scrolling
                document.body.style.overflow = '';
                
                // Return focus to toggle button
                this.navToggle.focus();
                
                // Announce to screen readers
                this.announceToScreenReader('Menu closed');
            }
            
            showGestureHint() {
                // Show gesture hint on first visit (mobile only)
                if (window.innerWidth <= 768 && !localStorage.getItem('gestureHintShown')) {
                    setTimeout(() => {
                        this.gestureIndicator.classList.add('show');
                        setTimeout(() => {
                            this.gestureIndicator.classList.remove('show');
                        }, 3000);
                    }, 2000);
                    
                    localStorage.setItem('gestureHintShown', 'true');
                }
            }
            
            announceToScreenReader(message) {
                // Create temporary element for screen reader announcements
                const announcement = document.createElement('div');
                announcement.setAttribute('aria-live', 'polite');
                announcement.setAttribute('aria-atomic', 'true');
                announcement.style.cssText = `
                    position: absolute;
                    left: -10000px;
                    width: 1px;
                    height: 1px;
                    overflow: hidden;
                `;
                announcement.textContent = message;
                
                document.body.appendChild(announcement);
                setTimeout(() => {
                    document.body.removeChild(announcement);
                }, 1000);
            }
            
            // Public method to update active nav item
            setActiveNavItem(href) {
                this.navMenu.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === href) {
                        link.classList.add('active');
                    }
                });
            }
            
            // Public method to add notification badge
            addNotificationBadge(navItem, count) {
                const link = this.navMenu.querySelector(`[href="${navItem}"]`);
                if (link) {
                    let badge = link.querySelector('.nav-badge');
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'nav-badge';
                        badge.style.cssText = `
                            position: absolute;
                            top: -5px;
                            right: -10px;
                            background: #ff4757;
                            color: white;
                            border-radius: 50%;
                            width: 20px;
                            height: 20px;
                            font-size: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: 600;
                        `;
                        link.style.position = 'relative';
                        link.appendChild(badge);
                    }
                    badge.textContent = count > 99 ? '99+' : count;
                }
            }
        }
        
        // Initialize navigation
        const navigation = new ResponsiveNavigation();
        
        // Make it globally accessible for other scripts
        window.zephyrNavigation = navigation;
        
        // Demo: Update active nav item based on current page
        const currentPage = window.location.pathname.split('/').pop();
        if (currentPage) {
            navigation.setActiveNavItem(currentPage);
        }
        
        // Demo: Add notification badge (uncomment to test)
        // setTimeout(() => {
        //     navigation.addNotificationBadge('events.php', 3);
        // }, 2000);
    </script>
</body>
</html>