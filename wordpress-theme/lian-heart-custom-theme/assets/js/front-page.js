(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function markInview(element) {
        if (!element) {
            return;
        }
        element.classList.add('is-inview');
        element.classList.add('is-animating');
        window.setTimeout(function () {
            element.classList.remove('is-animating');
        }, 1400);
    }

    function initHero() {
        var heroCopy = document.querySelector('.js-hero-copy-fx');
        var sliderElement = document.getElementById('js-hero-swiper');

        function replayHeroCopy() {
            if (!heroCopy) {
                return;
            }
            heroCopy.classList.remove('is-inview');
            heroCopy.classList.remove('is-animating');
            void heroCopy.offsetWidth;
            markInview(heroCopy);
        }

        if (window.Swiper && sliderElement) {
            new window.Swiper(sliderElement, {
                effect: 'fade',
                fadeEffect: { crossFade: true },
                loop: true,
                speed: 2000,
                autoplay: {
                    delay: 5200,
                    disableOnInteraction: false
                },
                pagination: {
                    el: sliderElement.querySelector('.swiper-pagination'),
                    clickable: true
                },
                on: {
                    init: replayHeroCopy,
                    slideChangeTransitionStart: replayHeroCopy
                }
            });
        } else {
            replayHeroCopy();
        }
    }

    function initNav() {
        var body = document.body;
        var button = document.querySelector('.hamburger-btn');
        var navLinks = document.querySelectorAll('.g-header__nav a, .g-header__cta a');

        if (!button) {
            return;
        }

        button.addEventListener('click', function () {
            var opened = body.classList.toggle('ui-state-nav-opened');
            button.setAttribute('aria-expanded', opened ? 'true' : 'false');
        });

        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                body.classList.remove('ui-state-nav-opened');
                button.setAttribute('aria-expanded', 'false');
            });
        });
    }

    function initScrollIndicator() {
        var trigger = document.querySelector('.scrolldown-indicator');
        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function () {
            var targetSelector = trigger.getAttribute('data-scroll-target');
            var target = targetSelector ? document.querySelector(targetSelector) : null;
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    function initPageTop() {
        var button = document.querySelector('.page-top');
        if (!button) {
            return;
        }

        function updateVisibility() {
            if (window.scrollY > 600) {
                button.classList.add('is-visible');
            } else {
                button.classList.remove('is-visible');
            }
        }

        window.addEventListener('scroll', updateVisibility, { passive: true });
        updateVisibility();

        button.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initScrollScenes() {
        var selectors = [
            '.js-headline-fx',
            '.js-concept-visual-fx',
            '.js-pride-point-fx',
            '.js-pride-aside-fx',
            '.js-menu-item-fx',
            '.js-greeting-fx',
            '.js-qa-item-fx',
            '.js-facility-item-fx',
            '.js-shop-info-fx',
            '.js-contact-fx'
        ];

        var elements = [];
        selectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (element) {
                elements.push(element);
            });
        });

        if (!elements.length) {
            return;
        }

        if (window.gsap && window.ScrollTrigger) {
            window.gsap.registerPlugin(window.ScrollTrigger);
            elements.forEach(function (element) {
                window.ScrollTrigger.create({
                    trigger: element,
                    start: 'top 80%',
                    once: true,
                    onEnter: function () {
                        markInview(element);
                    }
                });
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                markInview(entry.target);
                observer.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -15% 0px' });

        elements.forEach(function (element) {
            observer.observe(element);
        });
    }

    onReady(function () {
        initHero();
        initNav();
        initScrollIndicator();
        initScrollScenes();
        initPageTop();
    });
}());
