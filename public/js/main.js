/**
 * SkinCheck - General Frontend JS Interaction
 */

document.addEventListener("DOMContentLoaded", function () {
    // Automatically fade out success alerts after 5 seconds
    const alerts = document.querySelectorAll(".alert-dismissible");
    alerts.forEach(function (alert) {
        setTimeout(function () {
            // Check if bootstrap object is loaded
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                let bsAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // ─── Interactive Cursor-Following Glow / Spotlight ───
    (function initCursorSpotlight() {
        // Only initialize on devices with mouse/pointer (not touch-only)
        if (window.matchMedia && window.matchMedia('(pointer: coarse)').matches) {
            return;
        }

        let spotlight = document.getElementById('sc-cursor-spotlight');
        if (!spotlight) {
            spotlight = document.createElement('div');
            spotlight.id = 'sc-cursor-spotlight';
            spotlight.className = 'sc-cursor-spotlight';
            document.body.appendChild(spotlight);
        }

        let mouseX = -600, mouseY = -600;
        let posX = -600, posY = -600;
        let isRunning = false;
        let isVisible = false;

        window.addEventListener('pointermove', function (e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            if (!isVisible) {
                isVisible = true;
                spotlight.classList.add('active');
            }
            if (!isRunning) {
                isRunning = true;
                requestAnimationFrame(renderLoop);
            }
        }, { passive: true });

        document.addEventListener('pointerleave', function () {
            spotlight.classList.remove('active');
            isVisible = false;
        });

        function renderLoop() {
            // Smooth lerp (linear interpolation) for butter-smooth lagging trail
            const ease = 0.12;
            posX += (mouseX - posX) * ease;
            posY += (mouseY - posY) * ease;

            spotlight.style.transform = `translate3d(${posX}px, ${posY}px, 0)`;

            // Keep loop running while mouse is moving or easing is catching up
            if (isVisible || Math.abs(mouseX - posX) > 0.5 || Math.abs(mouseY - posY) > 0.5) {
                requestAnimationFrame(renderLoop);
            } else {
                isRunning = false;
            }
        }
    })();

    // ─── Interactive Glass Navigation & ScrollSpy ─────────────
    (function initGlassNav() {
        const navLinks = document.querySelectorAll('.sc-glass-nav-link');
        if (!navLinks.length) return;

        const sections = [
            { id: 'hero', name: 'Home' },
            { id: 'features', name: 'Features' },
            { id: 'technology', name: 'Technology' }
        ];

        let isProgrammaticScroll = false;
        let scrollTimer = null;

        function setActive(activeLink) {
            navLinks.forEach(link => link.classList.remove('active'));
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // Click Handler: Smooth scrolling + immediate active pill transition
        navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href') || '';
                
                if (href.includes('#')) {
                    const hash = href.substring(href.indexOf('#'));
                    const targetEl = document.querySelector(hash);

                    if (targetEl) {
                        e.preventDefault();
                        setActive(this);

                        isProgrammaticScroll = true;
                        if (scrollTimer) clearTimeout(scrollTimer);

                        const navHeight = document.querySelector('.sc-public-navbar')?.offsetHeight || 75;
                        const targetY = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight;

                        window.scrollTo({
                            top: Math.max(0, targetY),
                            behavior: 'smooth'
                        });

                        try {
                            history.pushState(null, null, hash);
                        } catch (err) {}

                        scrollTimer = setTimeout(() => {
                            isProgrammaticScroll = false;
                        }, 800);
                    }
                }
            });
        });

        // ScrollSpy: Update active nav button as user scrolls through sections
        function onScrollSpy() {
            if (isProgrammaticScroll) return;

            // Only run scroll spy on pages that have the hero section
            if (!document.getElementById('hero')) return;

            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const navHeight = document.querySelector('.sc-public-navbar')?.offsetHeight || 75;
            const triggerLine = scrollY + navHeight + 100;

            let currentSectionId = 'hero';

            sections.forEach(sec => {
                const el = document.getElementById(sec.id);
                if (el) {
                    const top = el.offsetTop;
                    if (triggerLine >= top) {
                        currentSectionId = sec.id;
                    }
                }
            });

            // Set active state on matching nav link
            navLinks.forEach(link => {
                const href = link.getAttribute('href') || '';
                if (href.includes('#' + currentSectionId)) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        window.addEventListener('scroll', onScrollSpy, { passive: true });
        
        // Initial check on page load if URL has a hash
        if (window.location.hash) {
            const hash = window.location.hash;
            const matchLink = Array.from(navLinks).find(l => (l.getAttribute('href') || '').includes(hash));
            if (matchLink) {
                setActive(matchLink);
            }
        }
    })();
});

// ─── Global Floating Toast Engine (Bottom-Left) ───
window.SkinCheck = window.SkinCheck || {};

window.SkinCheck.toast = function (message, type = 'info', duration = 4500) {
    if (!message) return null;

    let container = document.getElementById('scToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'scToastContainer';
        container.className = 'sc-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const safeType = ['info', 'success', 'warning', 'error', 'danger'].includes(type) ? type : 'info';
    toast.className = `sc-toast sc-toast-${safeType}`;

    let iconClass = 'fa-solid fa-circle-info';
    if (safeType === 'success') iconClass = 'fa-solid fa-circle-check';
    else if (safeType === 'error' || safeType === 'danger') iconClass = 'fa-solid fa-circle-xmark';
    else if (safeType === 'warning') iconClass = 'fa-solid fa-triangle-exclamation';

    toast.innerHTML = `
        <span class="sc-toast-icon"><i class="${iconClass}"></i></span>
        <span class="sc-toast-msg">${message}</span>
    `;

    function dismissToast() {
        if (toast.dataset.dismissing === 'true') return;
        toast.dataset.dismissing = 'true';
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    // Click to dismiss immediately
    toast.addEventListener('click', dismissToast);

    container.appendChild(toast);

    // Trigger smooth enter animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    let dismissTimer = null;
    function startTimer() {
        if (duration > 0) {
            dismissTimer = setTimeout(dismissToast, duration);
        }
    }

    // Pause auto-dismiss on hover
    toast.addEventListener('mouseenter', () => {
        if (dismissTimer) clearTimeout(dismissTimer);
    });
    toast.addEventListener('mouseleave', () => {
        startTimer();
    });

    startTimer();
    return toast;
};

// Global aliases
window.showToast = window.SkinCheck.toast;
window.showAppToast = window.SkinCheck.toast;
