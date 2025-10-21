/**
 * Advanced Interactions & Accessibility Framework
 * Modern 3D UI Enhancement System
 * =============================================
 */

class AdvancedUI {
    constructor() {
        this.isInitialized = false;
        this.animations = new Map();
        this.observers = new Map();
        this.preferences = this.loadUserPreferences();
        
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.setupAccessibility();
        this.initializeAnimations();
        this.setupMagneticElements();
        this.setupTiltEffects();
        this.setupRippleEffects();
        this.setupParallaxScrolling();
        this.setupKeyboardNavigation();
        this.setupVoiceControls();
        this.setupGestureControls();
        this.initializeThemeSystem();
        
        this.isInitialized = true;
        console.log('🚀 Advanced UI System Initialized');
    }

    // ============================================
    // ACCESSIBILITY ENHANCEMENTS
    // ============================================

    setupAccessibility() {
        // Skip links for screen readers
        this.createSkipLinks();
        
        // Focus management
        this.setupFocusManagement();
        
        // ARIA live regions
        this.setupLiveRegions();
        
        // High contrast mode detection
        this.detectHighContrastMode();
        
        // Reduced motion preference
        this.handleReducedMotion();
    }

    createSkipLinks() {
        const skipNav = document.createElement('a');
        skipNav.href = '#main-content';
        skipNav.className = 'skip-link';
        skipNav.textContent = 'Skip to main content';
        skipNav.style.cssText = `
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--surface-glass);
            color: var(--text-primary);
            padding: 8px;
            text-decoration: none;
            border-radius: 8px;
            z-index: 9999;
            transition: top 0.3s;
        `;
        
        skipNav.addEventListener('focus', () => {
            skipNav.style.top = '6px';
        });
        
        skipNav.addEventListener('blur', () => {
            skipNav.style.top = '-40px';
        });
        
        document.body.insertBefore(skipNav, document.body.firstChild);
    }

    setupFocusManagement() {
        // Focus trap for modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const modal = document.querySelector('.modal.show');
                if (modal) {
                    this.trapFocus(e, modal);
                }
            }
        });

        // Focus indicators
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('using-keyboard');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('using-keyboard');
        });
    }

    trapFocus(e, container) {
        const focusableElements = container.querySelectorAll(
            'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                lastElement.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === lastElement) {
                firstElement.focus();
                e.preventDefault();
            }
        }
    }

    setupLiveRegions() {
        // Create ARIA live region for announcements
        const liveRegion = document.createElement('div');
        liveRegion.id = 'live-region';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.style.cssText = `
            position: absolute;
            left: -10000px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        `;
        document.body.appendChild(liveRegion);
    }

    announce(message) {
        const liveRegion = document.getElementById('live-region');
        if (liveRegion) {
            liveRegion.textContent = message;
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 1000);
        }
    }

    // ============================================
    // ADVANCED ANIMATIONS
    // ============================================

    initializeAnimations() {
        // Intersection Observer for scroll animations
        const scrollObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        this.announceIfNeeded(entry.target);
                    }
                });
            },
            { threshold: 0.1 }
        );

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            scrollObserver.observe(el);
        });

        this.observers.set('scroll', scrollObserver);
    }

    announceIfNeeded(element) {
        const announcement = element.dataset.announce;
        if (announcement) {
            this.announce(announcement);
        }
    }

    // ============================================
    // MAGNETIC EFFECTS
    // ============================================

    setupMagneticElements() {
        document.querySelectorAll('.magnetic').forEach(element => {
            element.addEventListener('mousemove', (e) => {
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                const maxMove = 15;
                const moveX = (x / rect.width) * maxMove;
                const moveY = (y / rect.height) * maxMove;
                
                element.style.setProperty('--mouse-x', `${moveX}px`);
                element.style.setProperty('--mouse-y', `${moveY}px`);
            });

            element.addEventListener('mouseleave', () => {
                element.style.setProperty('--mouse-x', '0px');
                element.style.setProperty('--mouse-y', '0px');
            });
        });
    }

    // ============================================
    // TILT EFFECTS
    // ============================================

    setupTiltEffects() {
        document.querySelectorAll('.tilt-effect').forEach(element => {
            element.addEventListener('mousemove', (e) => {
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -10;
                const rotateY = ((x - centerX) / centerX) * 10;
                
                element.style.setProperty('--tilt-x', `${rotateX}deg`);
                element.style.setProperty('--tilt-y', `${rotateY}deg`);
            });

            element.addEventListener('mouseleave', () => {
                element.style.setProperty('--tilt-x', '0deg');
                element.style.setProperty('--tilt-y', '0deg');
            });
        });
    }

    // ============================================
    // RIPPLE EFFECTS
    // ============================================

    setupRippleEffects() {
        document.querySelectorAll('.ripple').forEach(element => {
            element.addEventListener('click', (e) => {
                const rect = element.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    pointer-events: none;
                    animation: ripple-animation 0.6s linear;
                `;
                
                element.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    }

    // ============================================
    // PARALLAX SCROLLING
    // ============================================

    setupParallaxScrolling() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length === 0) return;
        
        const handleScroll = () => {
            const scrollTop = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = parseFloat(element.dataset.parallax) || 0.5;
                const yPos = -(scrollTop * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        };
        
        // Throttled scroll handler
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // ============================================
    // KEYBOARD NAVIGATION
    // ============================================

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Escape key closes modals
            if (e.key === 'Escape') {
                const modal = document.querySelector('.modal.show');
                if (modal) {
                    this.closeModal(modal);
                }
            }
            
            // Arrow key navigation for carousels
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                const carousel = document.querySelector('.carousel:focus-within');
                if (carousel) {
                    e.preventDefault();
                    this.navigateCarousel(carousel, e.key === 'ArrowLeft' ? -1 : 1);
                }
            }
        });
    }

    // ============================================
    // VOICE CONTROLS
    // ============================================

    setupVoiceControls() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();
        
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        const voiceButton = document.querySelector('[data-voice-control]');
        if (voiceButton) {
            voiceButton.addEventListener('click', () => {
                recognition.start();
                this.announce('Voice control activated. Speak your command.');
            });

            recognition.onresult = (event) => {
                const command = event.results[0][0].transcript.toLowerCase();
                this.handleVoiceCommand(command);
            };

            recognition.onerror = () => {
                this.announce('Voice recognition error. Please try again.');
            };
        }
    }

    handleVoiceCommand(command) {
        const commands = {
            'go home': () => window.location.href = 'mainpage.php',
            'open menu': () => document.querySelector('.navbar-toggler')?.click(),
            'close menu': () => document.querySelector('.navbar-collapse.show .navbar-toggler')?.click(),
            'scroll to top': () => window.scrollTo({ top: 0, behavior: 'smooth' }),
            'open events': () => window.location.href = 'events.php',
            'register': () => window.location.href = 'participantformnew.php'
        };

        if (commands[command]) {
            commands[command]();
            this.announce(`Executing: ${command}`);
        } else {
            this.announce('Command not recognized. Try: go home, open menu, or scroll to top.');
        }
    }

    // ============================================
    // GESTURE CONTROLS
    // ============================================

    setupGestureControls() {
        let startX, startY, currentX, currentY;
        const threshold = 100;

        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        document.addEventListener('touchmove', (e) => {
            currentX = e.touches[0].clientX;
            currentY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', () => {
            if (!startX || !startY) return;

            const diffX = startX - currentX;
            const diffY = startY - currentY;

            if (Math.abs(diffX) > Math.abs(diffY)) {
                // Horizontal swipe
                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0) {
                        this.handleSwipe('left');
                    } else {
                        this.handleSwipe('right');
                    }
                }
            } else {
                // Vertical swipe
                if (Math.abs(diffY) > threshold) {
                    if (diffY > 0) {
                        this.handleSwipe('up');
                    } else {
                        this.handleSwipe('down');
                    }
                }
            }

            startX = startY = currentX = currentY = null;
        });
    }

    handleSwipe(direction) {
        const carousel = document.querySelector('.carousel');
        if (carousel) {
            switch (direction) {
                case 'left':
                    this.navigateCarousel(carousel, 1);
                    break;
                case 'right':
                    this.navigateCarousel(carousel, -1);
                    break;
            }
        }
    }

    // ============================================
    // THEME SYSTEM
    // ============================================

    initializeThemeSystem() {
        // Auto theme based on time
        this.setAutoTheme();
        
        // Theme toggle
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }
    }

    setAutoTheme() {
        const hour = new Date().getHours();
        const isDark = hour < 7 || hour > 19;
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    }

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme-preference', newTheme);
        this.announce(`Switched to ${newTheme} theme`);
    }

    // ============================================
    // UTILITY METHODS
    // ============================================

    loadUserPreferences() {
        return {
            reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            highContrast: window.matchMedia('(prefers-contrast: high)').matches,
            theme: localStorage.getItem('theme-preference') || 'auto'
        };
    }

    detectHighContrastMode() {
        if (this.preferences.highContrast) {
            document.documentElement.classList.add('high-contrast');
        }
    }

    handleReducedMotion() {
        if (this.preferences.reducedMotion) {
            document.documentElement.classList.add('reduced-motion');
            // Disable complex animations
            const style = document.createElement('style');
            style.textContent = `
                .reduced-motion * {
                    animation-duration: 0.3s !important;
                    transition-duration: 0.3s !important;
                }
                .reduced-motion .floating-element {
                    animation: none !important;
                }
            `;
            document.head.appendChild(style);
        }
    }

    closeModal(modal) {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        this.announce('Modal closed');
    }

    navigateCarousel(carousel, direction) {
        const items = carousel.querySelectorAll('.carousel-item');
        const activeItem = carousel.querySelector('.carousel-item.active');
        const currentIndex = Array.from(items).indexOf(activeItem);
        let newIndex = currentIndex + direction;
        
        if (newIndex < 0) newIndex = items.length - 1;
        if (newIndex >= items.length) newIndex = 0;
        
        activeItem.classList.remove('active');
        items[newIndex].classList.add('active');
        
        this.announce(`Showing item ${newIndex + 1} of ${items.length}`);
    }

    // Performance monitoring
    startPerformanceMonitoring() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.entryType === 'measure') {
                        console.log(`${entry.name}: ${entry.duration.toFixed(2)}ms`);
                    }
                }
            });
            observer.observe({ entryTypes: ['measure'] });
        }
    }

    // Cleanup method
    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.animations.clear();
        this.isInitialized = false;
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.advancedUI = new AdvancedUI();
});

// Performance optimization: Preload critical resources
document.addEventListener('DOMContentLoaded', () => {
    // Preload fonts
    const fontLinks = [
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap'
    ];
    
    fontLinks.forEach(href => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'style';
        link.href = href;
        document.head.appendChild(link);
    });
});

// Add CSS for advanced interactions
const advancedCSS = `
    <style>
    /* Focus indicators for keyboard navigation */
    .using-keyboard *:focus {
        outline: 2px solid var(--accent-cyan) !important;
        outline-offset: 2px;
        border-radius: 4px;
    }
    
    /* High contrast mode adjustments */
    .high-contrast {
        --surface-glass: rgba(255, 255, 255, 0.9);
        --text-primary: #000000;
        --text-secondary: #333333;
    }
    
    /* Ripple animation */
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Voice control indicator */
    [data-voice-control].active::after {
        content: '🎤';
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--accent-red);
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    </style>
`;

document.head.insertAdjacentHTML('beforeend', advancedCSS);