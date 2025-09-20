import './bootstrap';

// Mobile Enhancements
/**
 * Mobile Enhancements for Inventory Management System
 * Handles touch interactions, mobile-specific features, and responsive behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile Navigation Enhancements
    initMobileNavigation();
    
    // Touch Gestures
    initTouchGestures();
    
    // Mobile Form Enhancements
    initMobileForms();
    
    // Mobile Table Enhancements
    initMobileTables();
    
    // Mobile Modal Enhancements
    initMobileModals();
    
    // Performance Optimizations
    initPerformanceOptimizations();
    
    // Accessibility Enhancements
    initAccessibilityFeatures();
});

/**
 * Initialize mobile navigation
 */
function initMobileNavigation() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (!mobileMenuBtn || !sidebar || !overlay) return;
    
    // Mobile menu toggle
    mobileMenuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isOpen = sidebar.classList.contains('show');
        
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });
    
    // Close menu when clicking overlay
    overlay.addEventListener('click', function() {
        closeMobileMenu();
    });
    
    // Close menu when clicking nav links on mobile
    sidebar.addEventListener('click', function(e) {
        if (e.target.classList.contains('nav-link') && window.innerWidth < 768) {
            closeMobileMenu();
        }
    });
    
    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeMobileMenu();
        }
    });
    
    // Auto-close on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });
    
    function openMobileMenu() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('menu-open');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
        mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
    }
    
    function closeMobileMenu() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('menu-open');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    }
}

/**
 * Initialize touch gestures
 */
function initTouchGestures() {
    if (!('ontouchstart' in window)) return;
    
    // Add touch device class
    document.body.classList.add('touch-device');
    
    // Improve touch targets
    document.querySelectorAll('.btn, .nav-link, .dropdown-item, .form-control').forEach(function(el) {
        el.style.minHeight = '44px';
        el.style.display = 'flex';
        el.style.alignItems = 'center';
    });
    
    // Swipe gestures for tables
    let startX = 0;
    let startY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        if (!startX || !startY) return;
        
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Check if horizontal swipe is more significant than vertical
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            const tableContainer = e.target.closest('.table-responsive');
            if (tableContainer) {
                // Add visual feedback for swipe
                tableContainer.style.transform = `translateX(${diffX > 0 ? '-10px' : '10px'})`;
                setTimeout(() => {
                    tableContainer.style.transform = '';
                }, 200);
            }
        }
        
        startX = 0;
        startY = 0;
    });
}

/**
 * Initialize mobile form enhancements
 */
function initMobileForms() {
    // Auto-focus on form inputs
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const firstInput = form.querySelector('input:not([type="hidden"]), select, textarea');
        if (firstInput && window.innerWidth < 768) {
            // Delay focus to prevent scroll issues
            setTimeout(() => {
                firstInput.focus();
            }, 300);
        }
    });
    
    // Enhanced form validation feedback
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Mobile-friendly date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Set default date to today if not set
        if (!input.value) {
            input.value = new Date().toISOString().split('T')[0];
        }
    });
}

/**
 * Initialize mobile table enhancements
 */
function initMobileTables() {
    const tables = document.querySelectorAll('.table-responsive');
    
    tables.forEach(table => {
        // Add horizontal scroll indicator
        const scrollIndicator = document.createElement('div');
        scrollIndicator.className = 'scroll-indicator';
        scrollIndicator.innerHTML = '<i class="fas fa-arrows-alt-h"></i> Swipe to scroll';
        scrollIndicator.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            z-index: 10;
        `;
        table.style.position = 'relative';
        table.appendChild(scrollIndicator);
        
        // Hide indicator after user scrolls
        let hasScrolled = false;
        table.addEventListener('scroll', function() {
            if (!hasScrolled) {
                hasScrolled = true;
                scrollIndicator.style.opacity = '0';
                setTimeout(() => {
                    scrollIndicator.remove();
                }, 500);
            }
        });
        
        // Add sticky first column for mobile
        if (window.innerWidth < 768) {
            const firstColumn = table.querySelector('thead th:first-child, tbody td:first-child');
            if (firstColumn) {
                firstColumn.style.position = 'sticky';
                firstColumn.style.left = '0';
                firstColumn.style.backgroundColor = '#f8f9fa';
                firstColumn.style.zIndex = '10';
            }
        }
    });
}

/**
 * Initialize mobile modal enhancements
 */
function initMobileModals() {
    // Handle modal sizing for mobile
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            if (window.innerWidth < 768) {
                this.querySelector('.modal-dialog').classList.add('modal-fullscreen-sm-down');
            }
        });
        
        modal.addEventListener('shown.bs.modal', function() {
            // Focus on first input in modal
            const firstInput = this.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => {
                    firstInput.focus();
                }, 300);
            }
        });
    });
    
    // Close modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modal = bootstrap.Modal.getInstance(openModal);
                if (modal) {
                    modal.hide();
                }
            }
        }
    });
}

/**
 * Initialize performance optimizations
 */
function initPerformanceOptimizations() {
    // Lazy load images
    const images = document.querySelectorAll('img[data-src]');
    if (images.length > 0) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Debounce search inputs
    const searchInputs = document.querySelectorAll('input[type="search"], input[name*="search"]');
    searchInputs.forEach(input => {
        let timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // Trigger search after user stops typing
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }, 500);
        });
    });
    
    // Optimize scroll performance
    let ticking = false;
    function updateScrollElements() {
        // Update any scroll-dependent elements
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollElements);
            ticking = true;
        }
    });
}

/**
 * Initialize accessibility features
 */
function initAccessibilityFeatures() {
    // Add skip links for keyboard navigation
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.textContent = 'Skip to main content';
    skipLink.className = 'skip-link';
    skipLink.style.cssText = `
        position: absolute;
        top: -40px;
        left: 6px;
        background: #000;
        color: #fff;
        padding: 8px;
        text-decoration: none;
        z-index: 1000;
        transition: top 0.3s;
    `;
    
    skipLink.addEventListener('focus', function() {
        this.style.top = '6px';
    });
    
    skipLink.addEventListener('blur', function() {
        this.style.top = '-40px';
    });
    
    document.body.insertBefore(skipLink, document.body.firstChild);
    
    // Improve focus management
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });
    
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });
    
    // Add ARIA labels to interactive elements
    const buttons = document.querySelectorAll('button:not([aria-label])');
    buttons.forEach(button => {
        if (button.querySelector('i')) {
            const icon = button.querySelector('i');
            const text = button.textContent.trim();
            if (text) {
                button.setAttribute('aria-label', text);
            }
        }
    });
}

/**
 * Utility functions
 */
const MobileUtils = {
    // Check if device is mobile
    isMobile: function() {
        return window.innerWidth < 768;
    },
    
    // Check if device is tablet
    isTablet: function() {
        return window.innerWidth >= 768 && window.innerWidth < 1024;
    },
    
    // Check if device is desktop
    isDesktop: function() {
        return window.innerWidth >= 1024;
    },
    
    // Get device type
    getDeviceType: function() {
        if (this.isMobile()) return 'mobile';
        if (this.isTablet()) return 'tablet';
        return 'desktop';
    },
    
    // Show toast notification
    showToast: function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 9999;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
};

// Export for global use
window.MobileUtils = MobileUtils;
