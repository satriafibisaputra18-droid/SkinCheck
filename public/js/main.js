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

    // ─── Liquid Glass Interactive Pointer & Card Selection (liquidglassdesign.com) ───
    (function initLiquidGlassBorders() {
        const selector = '.sc-card, .sc-action-card, .sc-stat-card, .glass-card, .sc-dashboard-hero, .sc-consult-card, .sc-ai-card, .sc-settings-card, .sc-admin-stat-card, .sc-cta-card, .sc-hero-strip, .card:not(.bg-transparent), .glass-interactive, .sc-spotlight-card';

        document.addEventListener('pointermove', function (e) {
            const card = e.target.closest(selector);
            if (!card) return;
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x.toFixed(1)}px`);
            card.style.setProperty('--mouse-y', `${y.toFixed(1)}px`);
        }, { passive: true });

        document.addEventListener('pointerout', function (e) {
            const card = e.target.closest(selector);
            if (card && (!e.relatedTarget || !card.contains(e.relatedTarget))) {
                card.style.removeProperty('--mouse-x');
                card.style.removeProperty('--mouse-y');
            }
        }, { passive: true });

        document.addEventListener('click', function (e) {
            const card = e.target.closest(selector);
            if (card) {
                const container = card.closest('.row, .container, .container-fluid, .sc-consumer-main') || card.parentElement;
                if (container) {
                    container.querySelectorAll('.sc-selected').forEach(el => el.classList.remove('sc-selected'));
                }
                card.classList.add('sc-selected');
            } else {
                document.querySelectorAll('.sc-selected').forEach(el => el.classList.remove('sc-selected'));
            }
        });
    })();

    // ─── Dynamic Translucent Frosted Glass Navbar on Scroll ───
    (function initScrollBlurNavbar() {
        const navbars = document.querySelectorAll('.sc-public-navbar, .sc-consumer-nav, .navbar.sticky-top');
        if (!navbars.length) return;

        let ticking = false;

        function updateNavbarScroll() {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
            const isScrolled = scrollY > 15;

            navbars.forEach(nav => {
                if (isScrolled) {
                    nav.classList.add('sc-navbar-scrolled');
                } else {
                    nav.classList.remove('sc-navbar-scrolled');
                }
            });
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(updateNavbarScroll);
            }
        }, { passive: true });

        // Initial check on page load
        updateNavbarScroll();
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
                        const targetY = hash === '#hero'
                            ? 0
                            : targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight;

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
            if (hash === '#hero' && window.scrollY > 0 && window.scrollY < 160) {
                window.scrollTo(0, 0);
            }
            const matchLink = Array.from(navLinks).find(l => (l.getAttribute('href') || '').includes(hash));
            if (matchLink) {
                setActive(matchLink);
            }
        }
    })();

    // ─── Multi-Variant Scroll Choreography & Parallax ────────
    (function initScrollChoreography() {
        const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reducedMotion) return;

        // 1. Auto-assign varied reveal choreographies for dashboard/admin panels if not explicitly tagged
        const dashboardCards = document.querySelectorAll('.sc-dashboard-body .sc-card, .sc-dashboard-body .sc-action-card, .sc-dashboard-body .sc-stat-card, body.bg-light .card:not(.sc-admin-stat-card)');
        dashboardCards.forEach((el, idx) => {
            if (el.hasAttribute('data-sc-reveal')) return;
            if (el.closest('.tab-pane, .modal')) return;
            const rect = el.getBoundingClientRect();
            // Skip elements already comfortably inside initial viewport so above-the-fold stays instant
            if (rect.top < window.innerHeight * 0.88) return;

            if (el.classList.contains('sc-action-card') || el.classList.contains('sc-stat-card')) {
                el.setAttribute('data-sc-reveal', 'wave-up');
                el.setAttribute('data-sc-delay', String((idx % 4) * 75));
            } else {
                const cycle = ['tilt-up', 'slide-left', 'slide-right', 'scale-pop'];
                el.setAttribute('data-sc-reveal', cycle[idx % cycle.length]);
                el.setAttribute('data-sc-delay', String((idx % 2) * 80));
            }
        });

        const revealNodes = Array.from(document.querySelectorAll('[data-sc-reveal]'));
        if (!revealNodes.length) return;

        // Enable hidden initial reveal styles safely
        document.documentElement.classList.add('sc-anim-ready');

        const markRevealed = (el) => {
            if (el.classList.contains('is-visible')) return;
            el.classList.add('is-visible');
            const d = parseInt(el.getAttribute('data-sc-delay') || '0', 10);
            setTimeout(() => el.classList.add('is-settled'), 860 + d);
        };

        revealNodes.forEach((el) => {
            const delay = el.getAttribute('data-sc-delay');
            if (delay) {
                el.style.setProperty('--sc-delay', delay + 'ms');
            }
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight + 20 && rect.bottom > 0) {
                requestAnimationFrame(() => markRevealed(el));
            }
        });

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    markRevealed(entry.target);
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.01,
                rootMargin: '0px 0px 40px 0px'
            });

            revealNodes.forEach((el) => {
                if (!el.classList.contains('is-visible')) {
                    io.observe(el);
                }
            });

            const sweepVisibleOnScroll = () => {
                let remaining = 0;
                revealNodes.forEach((el) => {
                    if (el.classList.contains('is-visible')) return;
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= window.innerHeight + 30 && rect.bottom >= 0) {
                        markRevealed(el);
                        io.unobserve(el);
                    } else {
                        remaining++;
                    }
                });
                if (remaining === 0) {
                    window.removeEventListener('scroll', sweepVisibleOnScroll);
                }
            };
            window.addEventListener('scroll', sweepVisibleOnScroll, { passive: true });
        } else {
            revealNodes.forEach((el) => el.classList.add('is-visible', 'is-settled'));
        }

        // 3. Scroll-linked Hero & Technology Image Depth Parallax (Landing Page)
        const heroInner = document.querySelector('#hero .sc-hero-parallax');
        const techImg = document.querySelector('#technology .sc-tech-parallax');
        if (heroInner || techImg) {
            let parallaxTicking = false;
            const applyParallax = () => {
                parallaxTicking = false;
                const sy = window.scrollY || 0;
                if (heroInner && sy < 650) {
                    const yShift = Math.min(48, sy * 0.11);
                    const fade = Math.max(0.25, 1 - sy / 540);
                    heroInner.style.transform = `translate3d(0, ${yShift.toFixed(1)}px, 0)`;
                    heroInner.style.opacity = fade.toFixed(3);
                } else if (heroInner && sy >= 650) {
                    heroInner.style.opacity = '1';
                    heroInner.style.transform = 'none';
                }

                if (techImg) {
                    const rect = techImg.getBoundingClientRect();
                    const vh = window.innerHeight || 800;
                    if (rect.top < vh && rect.bottom > 0) {
                        const progress = (vh - rect.top) / (vh + rect.height); // 0 -> 1
                        const offset = (progress - 0.5) * -28; // smooth float -14px..+14px
                        techImg.style.transform = `translate3d(0, ${offset.toFixed(1)}px, 0)`;
                    }
                }
            };

            window.addEventListener('scroll', () => {
                if (!parallaxTicking) {
                    parallaxTicking = true;
                    requestAnimationFrame(applyParallax);
                }
            }, { passive: true });
        }
    })();
});

// ─── Global Sonner Toast Engine (shadcn/ui style: https://21st.dev/r/shadcn/sonner) ───
(function () {
    let toastCounter = 0;

    function getOrCreateContainer() {
        let container = document.getElementById('scToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'scToastContainer';
            container.className = 'sc-sonner-toaster';
            container.setAttribute('data-position', 'bottom-right');
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
        }
        return container;
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    function createToast(title, opts = {}, type = 'info') {
        if (!title) return null;

        const container = getOrCreateContainer();
        const id = opts.id || ('sc_toast_' + (++toastCounter));

        // Options normalization
        let description = '';
        let action = null;
        let duration = (typeof opts.duration === 'number') ? opts.duration : 4200;

        if (typeof opts === 'string') {
            description = opts;
        } else if (typeof opts === 'object') {
            description = opts.description || '';
            action = opts.action || null;
            if (opts.type) type = opts.type;
        }

        const safeType = ['success', 'error', 'danger', 'warning', 'info', 'loading'].includes(type) ? type : 'info';

        // Icon resolution
        let iconHtml = '<i class="fa-solid fa-circle-info"></i>';
        if (safeType === 'success') {
            iconHtml = '<i class="fa-solid fa-circle-check"></i>';
        } else if (safeType === 'error' || safeType === 'danger') {
            iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
        } else if (safeType === 'warning') {
            iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
        } else if (safeType === 'loading') {
            iconHtml = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
        }

        const toastEl = document.createElement('div');
        toastEl.className = `sc-sonner-toast sc-sonner-toast-${safeType}`;
        toastEl.setAttribute('role', 'status');
        toastEl.dataset.toastId = id;

        toastEl.innerHTML = `
            <span class="sc-sonner-icon">${iconHtml}</span>
            <div class="sc-sonner-content">
                <div class="sc-sonner-title">${escapeHtml(title)}</div>
                ${description ? `<div class="sc-sonner-description">${escapeHtml(description)}</div>` : ''}
            </div>
            ${action && action.label ? `<button type="button" class="sc-sonner-action-btn">${escapeHtml(action.label)}</button>` : ''}
            <button type="button" class="sc-sonner-close-btn" aria-label="Tutup" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        function dismiss() {
            if (toastEl.dataset.dismissing === 'true') return;
            toastEl.dataset.dismissing = 'true';
            toastEl.classList.remove('show');
            toastEl.classList.add('hide');
            setTimeout(() => {
                if (toastEl.parentNode) {
                    toastEl.parentNode.removeChild(toastEl);
                }
            }, 240);
        }

        // Action button event
        const actionBtn = toastEl.querySelector('.sc-sonner-action-btn');
        if (actionBtn && action && typeof action.onClick === 'function') {
            actionBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                try {
                    action.onClick(id);
                } catch (err) {
                    console.error('[Sonner Action Error]', err);
                }
                dismiss();
            });
        }

        // Close button event
        const closeBtn = toastEl.querySelector('.sc-sonner-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dismiss();
            });
        }

        // Prepend or append to toaster container
        container.appendChild(toastEl);

        // Smooth enter transition
        requestAnimationFrame(() => {
            toastEl.classList.add('show');
        });

        // Auto dismiss timer
        let timer = null;
        function startTimer() {
            if (duration > 0 && safeType !== 'loading') {
                timer = setTimeout(dismiss, duration);
            }
        }

        // Pause on mouse hover
        toastEl.addEventListener('mouseenter', () => {
            if (timer) clearTimeout(timer);
        });
        toastEl.addEventListener('mouseleave', () => {
            startTimer();
        });

        startTimer();

        return {
            id: id,
            element: toastEl,
            dismiss: dismiss
        };
    }

    // Modern Sonner API: toast(message, opts), toast.success, toast.error, etc.
    const toast = function (title, opts) {
        return createToast(title, opts, 'info');
    };

    toast.success = (title, opts) => createToast(title, opts, 'success');
    toast.error = (title, opts) => createToast(title, opts, 'error');
    toast.warning = (title, opts) => createToast(title, opts, 'warning');
    toast.info = (title, opts) => createToast(title, opts, 'info');
    toast.loading = (title, opts) => createToast(title, opts, 'loading');

    toast.promise = function (promise, { loading, success, error }) {
        const loadingToast = toast.loading(loading || 'Memproses...');
        return promise
            .then((data) => {
                loadingToast.dismiss();
                const successMsg = typeof success === 'function' ? success(data) : (success || 'Berhasil diselesaikan!');
                toast.success(successMsg);
                return data;
            })
            .catch((err) => {
                loadingToast.dismiss();
                const errorMsg = typeof error === 'function' ? error(err) : (error || 'Terjadi kesalahan sistem.');
                toast.error(errorMsg);
                throw err;
            });
    };

    toast.dismiss = function (id) {
        if (!id) {
            const container = document.getElementById('scToastContainer');
            if (container) {
                const toasts = container.querySelectorAll('.sc-sonner-toast');
                toasts.forEach(t => t.remove());
            }
            return;
        }
        const target = document.querySelector(`[data-toast-id="${id}"]`);
        if (target) {
            target.classList.remove('show');
            target.classList.add('hide');
            setTimeout(() => target.remove(), 240);
        }
    };

    // Backward-compatible bridge for existing codebase
    window.toast = toast;
    window.showToast = function (message, type = 'info', duration = 4200) {
        const fn = toast[type] || toast;
        return fn(message, { duration: duration });
    };
    window.showAppToast = window.showToast;

    window.SkinCheck = window.SkinCheck || {};
    window.SkinCheck.toast = window.showToast;
})();
