// Resizable Navbar Implementation
class ResizableNavbar {
    constructor() {
        this.navbar = document.getElementById('navbar');
        this.navBody = document.getElementById('navBody');
        this.wrapper = document.querySelector('.navbar-wrapper');
        this.mobileNav = document.getElementById('mobileNav');
        this.mobileToggle = document.getElementById('mobileToggle');
        this.mobileMenu = document.getElementById('mobileMenu');
        this.menuIcon = document.getElementById('menuIcon');
        this.navHoverBg = document.getElementById('navHoverBg');
        this.navItems = document.querySelectorAll('.nav-item');
        
        this.isVisible = false;
        this.isMobileMenuOpen = false;
        this.hoveredIndex = null;
        
        // Allow pages to force a dark-text navbar (e.g., dashboard)
        this.forceDark = document.body?.dataset?.navTheme === 'dark';

        this.init();
    }

    init() {
        if (!this.navbar || !this.navBody) return; // Guard when markup is missing
        this.setupScrollListener();
        this.setupMobileMenu();
        this.setupNavHover();
        this.setupSmoothScrolling();
    }

    setupScrollListener() {
        let ticking = false;
        
        const updateNavbar = () => {
            const scrollY = window.scrollY;
            const navbarHeight = this.navbar.offsetHeight;

            if (scrollY > 20) {
                if (!this.isVisible) {
                    this.isVisible = true;
                    this.navBody.classList.add('visible');
                    this.mobileNav.classList.add('visible');
                    if (this.wrapper) this.wrapper.classList.add('visible');
                }
            } else {
                if (this.isVisible) {
                    this.isVisible = false;
                    this.navBody.classList.remove('visible');
                    this.mobileNav.classList.remove('visible');
                    if (this.wrapper) this.wrapper.classList.remove('visible');
                }
            }

            // Dynamic text color change based on what's under the navbar (works on all pages)
            const isLightRGB = (r,g,b) => {
                const luminance = (0.2126*r + 0.7152*g + 0.0722*b) / 255;
                return luminance > 0.78; // treat very light backgrounds as light
            };
            const parseRGB = (str) => {
                const m = str.match(/rgba?\((\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*(\d*\.?\d+))?\)/i);
                if (!m) return null;
                return { r: +m[1], g: +m[2], b: +m[3], a: m[4] !== undefined ? +m[4] : 1 };
            };
            const isLightUnderNav = () => {
                const rect = this.navbar.getBoundingClientRect();
                let x = Math.max(0, Math.min(window.innerWidth - 1, Math.floor(window.innerWidth / 2)));
                let y = Math.max(0, Math.min(window.innerHeight - 1, Math.floor(rect.bottom + 1)));
                let el = document.elementFromPoint(x, y);
                let guard = 0;
                while (el && guard++ < 10) {
                    const cs = window.getComputedStyle(el);
                    // If element has a gradient/image, assume not light unless color proves otherwise
                    if (cs.backgroundImage && cs.backgroundImage !== 'none') {
                        // Many gradients used on hero/welcome banners are darker; default to not light
                        // Continue walking up to see if a solid color ancestor exists
                    }
                    const rgb = parseRGB(cs.backgroundColor);
                    if (rgb && rgb.a > 0) {
                        return isLightRGB(rgb.r, rgb.g, rgb.b);
                    }
                    el = el.parentElement;
                }
                // Fallback: check body background color
                const bodyRGB = parseRGB(window.getComputedStyle(document.body).backgroundColor || 'rgba(0,0,0,0)');
                return bodyRGB ? (bodyRGB.a > 0 && isLightRGB(bodyRGB.r, bodyRGB.g, bodyRGB.b)) : false;
            };

            let shouldHaveDarkText = isLightUnderNav();
            // Respect forced dark text if requested by page
            if (this.forceDark) shouldHaveDarkText = true;

            if (shouldHaveDarkText) {
                this.navbar.classList.add('navbar-dark-text');
            } else {
                this.navbar.classList.remove('navbar-dark-text');
            }

            ticking = false;
        };

        const requestTick = () => {
            if (!ticking) {
                requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        };

        window.addEventListener('scroll', requestTick, { passive: true });
        window.addEventListener('resize', requestTick, { passive: true });
        // Initial evaluation
        requestTick();
    }

    setupMobileMenu() {
        if (!this.mobileToggle || !this.mobileMenu) return;

        this.mobileToggle.addEventListener('click', () => {
            this.toggleMobileMenu();
        });

        // Close mobile menu when clicking on links
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        });

        // Close mobile menu when clicking on buttons
        document.querySelectorAll('.mobile-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isMobileMenuOpen && 
                !this.mobileMenu.contains(e.target) && 
                !this.mobileToggle.contains(e.target)) {
                this.closeMobileMenu();
            }
        });
    }

    toggleMobileMenu() {
        this.isMobileMenuOpen = !this.isMobileMenuOpen;
        this.mobileMenu.classList.toggle('active');
        
        // Toggle icon
        if (this.menuIcon) {
            this.menuIcon.className = this.isMobileMenuOpen ? 'fas fa-times' : 'fas fa-bars';
        }
    }

    closeMobileMenu() {
        this.isMobileMenuOpen = false;
        this.mobileMenu.classList.remove('active');
        
        if (this.menuIcon) {
            this.menuIcon.className = 'fas fa-bars';
        }
    }

    setupNavHover() {
        if (!this.navHoverBg || !this.navItems.length) return;

        this.navItems.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                this.hoveredIndex = index;
                this.updateHoverBg(item);
            });
        });

        const navItemsContainer = document.querySelector('.nav-items');
        if (navItemsContainer) {
            navItemsContainer.addEventListener('mouseleave', () => {
                this.hoveredIndex = null;
                this.hideHoverBg();
            });
        }
    }

    updateHoverBg(item) {
        if (!this.navHoverBg) return;

        const itemRect = item.getBoundingClientRect();
        const containerRect = document.querySelector('.nav-items').getBoundingClientRect();
        
        const left = itemRect.left - containerRect.left;
        const width = itemRect.width;
        
        this.navHoverBg.style.left = `${left}px`;
        this.navHoverBg.style.width = `${width}px`;
        this.navHoverBg.classList.add('visible');
    }

    hideHoverBg() {
        if (this.navHoverBg) {
            this.navHoverBg.classList.remove('visible');
        }
    }

    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
}

// Initialize the resizable navbar
const resizableNavbar = new ResizableNavbar();

// Loading Screen Handler
class LoadingScreen {
    constructor() {
        this.loadingScreen = document.getElementById('loadingScreen');
        this.minLoadingTime = 2000; // Minimum 2 seconds
        this.startTime = Date.now();
        
        this.init();
    }

    init() {
        // Hide loading screen after minimum time and when page is loaded
        window.addEventListener('load', () => {
            const elapsedTime = Date.now() - this.startTime;
            const remainingTime = Math.max(0, this.minLoadingTime - elapsedTime);
            
            setTimeout(() => {
                this.hideLoadingScreen();
            }, remainingTime);
        });

        // Fallback: hide after 5 seconds maximum
        setTimeout(() => {
            this.hideLoadingScreen();
        }, 5000);
    }

    hideLoadingScreen() {
        if (this.loadingScreen) {
            this.loadingScreen.classList.add('hidden');
            
            // Remove from DOM after animation
            setTimeout(() => {
                this.loadingScreen.remove();
            }, 500);
        }
    }
}

// Initialize loading screen
const loadingScreen = new LoadingScreen();


// Scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
        }
    });
}, observerOptions);

// Add scroll animation to elements
document.addEventListener('DOMContentLoaded', () => {
    const animateElements = document.querySelectorAll('.feature-card, .course-card, .testimonial-card, .section-header');
    animateElements.forEach(el => {
        el.classList.add('scroll-animate');
        observer.observe(el);
    });
});

// Counter animation for hero stats
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start) + '+';
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target + '+';
        }
    }
    
    updateCounter();
}

// Trigger counter animation when hero section is visible
const heroObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const stats = document.querySelectorAll('.stat h3');
            stats.forEach(stat => {
                const text = stat.textContent;
                const number = parseInt(text.replace(/\D/g, ''));
                if (number) {
                    stat.textContent = '0+';
                    animateCounter(stat, number);
                }
            });
            heroObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const heroSection = document.querySelector('.hero');
if (heroSection) {
    heroObserver.observe(heroSection);
}

// Remove 3D tilt on course cards (handled by CSS hover only)
document.querySelectorAll('.course-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        // Use subtle 2D lift only; no inline transform to allow CSS control
        this.style.transform = '';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});

// Feature card hover effects
document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-12px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Button click animations
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        // Create ripple effect
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple effect CSS
const style = document.createElement('style');
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Newsletter form submission
const newsletterForm = document.querySelector('.newsletter-form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;
        
        if (email) {
            // Show success message
            const button = this.querySelector('.btn');
            const originalText = button.textContent;
            button.textContent = 'Subscribed!';
            button.style.background = '#10b981';
            
            setTimeout(() => {
                button.textContent = originalText;
                button.style.background = '';
                this.querySelector('input[type="email"]').value = '';
            }, 2000);
        }
    });
}

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    if (hero) {
        const rate = scrolled * -0.5;
        hero.style.transform = `translateY(${rate}px)`;
    }
});

// Hero Highlight Animation
function initHeroHighlight() {
    const heroTitle = document.querySelector('.hero-title');
    const highlightText = document.querySelector('.highlight-text');
    
    if (heroTitle && highlightText) {
        // Add intersection observer for hero highlight
        const heroObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Ensure text is visible first
                    highlightText.style.color = 'white';
                    highlightText.style.opacity = '1';
                    highlightText.style.visibility = 'visible';
                    
                    // Trigger highlight animation with enhanced visibility
                    /* highlightText.style.animation = 'highlightPulse 1.5s ease-in-out infinite, highlightGlow 1.5s ease-in-out infinite'; */
                    highlightText.style.textShadow = '0 0 40px rgba(139, 92, 246, 0.8), 0 0 80px rgba(139, 92, 246, 0.4), 0 0 120px rgba(79, 70, 229, 0.3)';
                    
                    // Removed typing effect to highlight text
                    /* const text = highlightText.textContent;
                    highlightText.textContent = '';
                    highlightText.style.borderRight = '3px solid #8b5cf6';
                    highlightText.style.boxShadow = '0 0 20px rgba(139, 92, 246, 0.6)';
                    
                    let i = 0;
                    const typeWriter = () => {
                        if (i < text.length) {
                            highlightText.textContent += text.charAt(i);
                            i++;
                            setTimeout(typeWriter, 80);
                        } else {
                            highlightText.style.borderRight = 'none';
                            highlightText.style.boxShadow = 'none';
                        }
                    };
                    
                    setTimeout(typeWriter, 300); */
                }
            });
        }, { threshold: 0.3 });
        
        heroObserver.observe(heroTitle);
    }
}

// Loading animation
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
    initHeroHighlight();
});

// Add loading styles
const loadingStyle = document.createElement('style');
loadingStyle.textContent = `
    body {
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    body.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(loadingStyle);

// Testimonial carousel (optional enhancement)
let currentTestimonial = 0;
const testimonials = document.querySelectorAll('.testimonial-card');

function showTestimonial(index) {
    testimonials.forEach((testimonial, i) => {
        testimonial.style.display = i === index ? 'block' : 'none';
    });
}

// Auto-rotate testimonials every 5 seconds
if (testimonials.length > 0) {
    setInterval(() => {
        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
        showTestimonial(currentTestimonial);
    }, 5000);
}

// Form validation for contact forms
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Add validation to email inputs
document.querySelectorAll('input[type="email"]').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value && !validateEmail(this.value)) {
            this.style.borderColor = '#ef4444';
            this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        } else {
            this.style.borderColor = '';
            this.style.boxShadow = '';
        }
    });
});

// Scroll to top functionality
const scrollToTopBtn = document.createElement('button');
scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollToTopBtn.className = 'scroll-to-top';
scrollToTopBtn.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
`;

document.body.appendChild(scrollToTopBtn);

scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollToTopBtn.style.display = 'flex';
    } else {
        scrollToTopBtn.style.display = 'none';
    }
});

// Add hover effect to scroll to top button
scrollToTopBtn.addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-2px) scale(1.1)';
});

scrollToTopBtn.addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0) scale(1)';
});

console.log('EduLearn LMS Landing Page loaded successfully! 🎓');
