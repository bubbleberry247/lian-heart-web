(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    }

    function onWindowLoad(callback) {
        if (document.readyState === 'complete') {
            callback();
            return;
        }

        window.addEventListener('load', callback, { once: true });
    }

    function prefersReducedMotion() {
        return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }

    function addClass(element, className) {
        if (!element) {
            return;
        }

        element.classList.add(className);
    }

    function addClassList(elements, className) {
        Array.prototype.forEach.call(elements || [], function (element) {
            addClass(element, className);
        });
    }

    function createTrigger(config) {
        var target = config && config.target;
        if (!target) {
            return;
        }

        if (prefersReducedMotion()) {
            if (typeof config.onEnter === 'function') {
                config.onEnter();
            }

            if (config.toggleClass) {
                addClass(target, config.toggleClass);
            }

            return;
        }

        if (window.gsap && window.ScrollTrigger) {
            window.gsap.registerPlugin(window.ScrollTrigger);

            if (typeof config.onEnter === 'function') {
                window.ScrollTrigger.create({
                    trigger: target,
                    start: config.start || 'center bottom',
                    once: true,
                    onEnter: config.onEnter
                });
                return;
            }

            window.gsap.to(target, {
                scrollTrigger: {
                    trigger: target,
                    start: config.start || 'center bottom',
                    toggleClass: config.toggleClass,
                    once: true
                }
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                observer.unobserve(entry.target);

                if (typeof config.onEnter === 'function') {
                    config.onEnter();
                    return;
                }

                if (config.toggleClass) {
                    addClass(entry.target, config.toggleClass);
                }
            });
        }, { rootMargin: config.rootMargin || '0px 0px -30% 0px' });

        observer.observe(target);
    }

    function splitHeadlineLabel(label) {
        if (!label || label.dataset.lhSplitReady === '1' || typeof window.Splitting !== 'function') {
            return;
        }

        window.Splitting({
            target: label,
            by: 'chars',
            key: null
        });

        Array.prototype.forEach.call(label.querySelectorAll('.char'), function (char) {
            window.Splitting({
                target: char,
                by: 'words',
                key: 'moving-symbol-'
            });
        });

        label.dataset.lhSplitReady = '1';
    }

    function replayHeroCopy() {
        var heroCopy = document.querySelector('.js-hero-copy-fx');
        if (!heroCopy) {
            return;
        }

        var targets = [
            heroCopy.querySelector('.mv__eyebrow'),
            heroCopy.querySelector('.mv__title'),
            heroCopy.querySelector('.mv__description'),
            heroCopy.querySelector('.mv__actions')
        ];

        targets.forEach(function (element) {
            if (!element) {
                return;
            }

            element.classList.remove('is-animating');
            void element.offsetWidth;
        });

        window.setTimeout(function () { addClass(targets[0], 'is-animating'); }, 0);
        window.setTimeout(function () { addClass(targets[1], 'is-animating'); }, 120);
        window.setTimeout(function () { addClass(targets[2], 'is-animating'); }, 260);
        window.setTimeout(function () { addClass(targets[3], 'is-animating'); }, 380);
    }

    function initHeroScene() {
        var sliderElement = document.getElementById('js-hero-swiper');
        if (!sliderElement || !window.Swiper) {
            replayHeroCopy();
            return;
        }

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

        Array.prototype.forEach.call(navLinks, function (link) {
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

    function initFloatingButtons() {
        var floating = document.querySelector('.floating-btns');
        var contact = document.getElementById('contact');

        if (!floating || !contact) {
            return;
        }

        function setHidden(hidden) {
            floating.classList.toggle('is-hidden-on-contact', hidden);
        }

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    setHidden(entry.isIntersecting);
                });
            }, { rootMargin: '-18% 0px -18% 0px', threshold: 0.01 }).observe(contact);
            return;
        }

        function updateVisibility() {
            var rect = contact.getBoundingClientRect();
            setHidden(rect.top < window.innerHeight && rect.bottom > 0);
        }

        window.addEventListener('scroll', updateVisibility, { passive: true });
        window.addEventListener('resize', updateVisibility);
        updateVisibility();
    }

    function globalHeadlineAnimation() {
        Array.prototype.forEach.call(document.querySelectorAll('.js-headline-fx'), function (headline) {
            splitHeadlineLabel(headline.querySelector('.wp-headline__alphabetic'));
            createTrigger({
                target: headline,
                start: 'center bottom',
                toggleClass: 'is-animating'
            });
        });
    }

    function trustScene() {
        var section = document.querySelector('.js-trust-points');
        if (!section) {
            return;
        }

        createTrigger({
            target: section,
            start: 'center bottom',
            toggleClass: 'is-inview'
        });
    }

    function timingScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.js-timing-card-fx'), function (card) {
            createTrigger({
                target: card,
                start: 'center bottom',
                toggleClass: 'is-animating'
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.timing__actions'), function (actions) {
            createTrigger({
                target: actions,
                start: 'center bottom',
                toggleClass: 'is-inview'
            });
        });
    }

    function conceptScene() {
        var section = document.getElementById('concept');
        var visuals = section ? section.querySelectorAll('.js-concept-visual-fx') : null;
        var contentNodes = section ? section.querySelectorAll('.concept-contents .wp-block-heading, .concept-contents p') : null;
        var revealConcept;

        if (!section || !visuals || !visuals.length) {
            return;
        }

        revealConcept = function () {
            addClassList(visuals, 'is-inview');
            addClassList(contentNodes, 'is-animating');
        };

        if (prefersReducedMotion()) {
            revealConcept();
            return;
        }

        if (window.gsap && window.ScrollTrigger) {
            window.gsap.registerPlugin(window.ScrollTrigger);
            window.gsap.set(visuals, {
                yPercent: 45,
                opacity: 0
            });

            window.gsap.to(visuals, {
                yPercent: 0,
                opacity: 1,
                duration: 1.35,
                stagger: 0.35,
                ease: 'sine.out',
                scrollTrigger: {
                    trigger: section,
                    start: 'center bottom',
                    end: 'bottom bottom',
                    once: true,
                    onEnter: revealConcept
                },
                onStart: revealConcept
            });
            return;
        }

        createTrigger({
            target: section,
            rootMargin: '0px 0px -38% 0px',
            onEnter: revealConcept
        });
    }

    function prideScene() {
        var section = document.getElementById('pride');
        var aside = document.getElementById('pride-aside');

        if (!section) {
            return;
        }

        Array.prototype.forEach.call(section.querySelectorAll('.js-pride-point-fx'), function (item) {
            createTrigger({
                target: item,
                start: 'center bottom',
                toggleClass: 'is-animating'
            });
        });

        createTrigger({
            target: aside,
            start: 'center bottom',
            toggleClass: 'is-inview'
        });
    }

    function menuScene() {
        Array.prototype.forEach.call(document.querySelectorAll('#menu .js-menu-item-fx'), function (item) {
            createTrigger({
                target: item,
                start: 'center bottom',
                toggleClass: 'is-animating'
            });
        });
    }

    function greetingScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.js-greeting'), function (section) {
            createTrigger({
                target: section,
                start: 'center bottom',
                toggleClass: 'is-inview'
            });
        });
    }

    function knowledgeScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.js-knowledge-card-fx'), function (card) {
            createTrigger({
                target: card,
                start: 'center bottom',
                toggleClass: 'is-animating'
            });
        });
    }

    function qaScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.qa-item'), function (item) {
            createTrigger({
                target: item,
                start: 'center bottom',
                toggleClass: 'is-inview'
            });
        });
    }

    function facilityScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.facility-card'), function (card) {
            createTrigger({
                target: card,
                start: 'center bottom',
                toggleClass: 'is-inview'
            });
        });
    }

    function shopInfoScene() {
        var body = document.querySelector('.shop-info__body');
        if (!body) {
            return;
        }

        createTrigger({
            target: body,
            start: 'center bottom',
            toggleClass: 'is-inview'
        });
    }

    function contactScene() {
        Array.prototype.forEach.call(document.querySelectorAll('.contact-catch, .contact-lead-block, .contact-form-block'), function (element) {
            createTrigger({
                target: element,
                start: 'center bottom',
                toggleClass: 'is-inview'
            });
        });
    }

    onReady(function () {
        initHeroScene();
        initNav();
        initScrollIndicator();
        initPageTop();
        initFloatingButtons();
    });

    onWindowLoad(function () {
        globalHeadlineAnimation();
        trustScene();
        timingScene();
        conceptScene();
        prideScene();
        menuScene();
        greetingScene();
        knowledgeScene();
        qaScene();
        facilityScene();
        shopInfoScene();
        contactScene();
    });
})();
