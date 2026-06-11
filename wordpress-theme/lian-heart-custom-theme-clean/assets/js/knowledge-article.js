(function () {
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

    function createTrigger(target, className, options) {
        if (!target) {
            return;
        }

        var settings = options || {};
        var start = settings.start || 'center bottom';
        var rootMargin = settings.rootMargin || '0px 0px -25% 0px';

        if (prefersReducedMotion()) {
            addClass(target, className);
            return;
        }

        if (window.gsap && window.ScrollTrigger) {
            window.gsap.registerPlugin(window.ScrollTrigger);
            window.gsap.to(target, {
                scrollTrigger: {
                    trigger: target,
                    start: start,
                    toggleClass: className,
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
                addClass(entry.target, className);
            });
        }, { rootMargin: rootMargin });

        observer.observe(target);
    }

    onWindowLoad(function () {
        Array.prototype.forEach.call(document.querySelectorAll('.js-knowledge-article-fx'), function (element) {
            createTrigger(element, 'is-inview');
        });

        Array.prototype.forEach.call(document.querySelectorAll('.js-knowledge-article-content-fx'), function (element) {
            createTrigger(element, 'is-inview', {
                start: 'top 88%',
                rootMargin: '0px 0px -8% 0px'
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.js-knowledge-article-cta-fx, .js-knowledge-article-related-fx'), function (element) {
            createTrigger(element, 'is-inview', {
                start: 'top 85%',
                rootMargin: '0px 0px -12% 0px'
            });
        });
    });
})();
