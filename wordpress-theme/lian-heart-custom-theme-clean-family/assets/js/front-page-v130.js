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
        }, 2100);
    }

    function scheduleMark(element, delay) {
        window.setTimeout(function () {
            markInview(element);
        }, delay || 0);
    }

    function createScrollScene(triggerElement, start, handler, rootMargin) {
        if (!triggerElement) {
            return;
        }

        if (window.gsap && window.ScrollTrigger) {
            window.gsap.registerPlugin(window.ScrollTrigger);
            window.ScrollTrigger.create({
                trigger: triggerElement,
                start: start,
                once: true,
                onEnter: handler
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                observer.unobserve(entry.target);
                handler();
            });
        }, { rootMargin: rootMargin || '0px 0px -30% 0px' });

        observer.observe(triggerElement);
    }

    function initHeroScene() {
        var heroCopy = document.querySelector('.js-hero-copy-fx');
        var sliderElement = document.getElementById('js-hero-swiper');
        var eyebrow = heroCopy ? heroCopy.querySelector('.mv__eyebrow') : null;
        var title = heroCopy ? heroCopy.querySelector('.mv__title') : null;
        var description = heroCopy ? heroCopy.querySelector('.mv__description') : null;
        var actions = heroCopy ? heroCopy.querySelector('.mv__actions') : null;

        function replayHeroCopy() {
            [eyebrow, title, description, actions].forEach(function (element) {
                if (!element) {
                    return;
                }
                element.classList.remove('is-inview');
                element.classList.remove('is-animating');
                void element.offsetWidth;
            });
            scheduleMark(eyebrow, 0);
            scheduleMark(title, 120);
            scheduleMark(description, 260);
            scheduleMark(actions, 380);
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

    function initConceptScene() {
        var section = document.getElementById('concept');
        if (!section) {
            return;
        }
        var visuals = Array.prototype.slice.call(section.querySelectorAll('.concept-visual'));
        var headline = section.querySelector('.wp-headline');
        var contents = section.querySelector('.concept-contents');
        var inner = section.querySelector('.concept-contents__inner');

        createScrollScene(section, 'center bottom', function () {
            scheduleMark(contents, 0);
            scheduleMark(visuals[1], 620);
            scheduleMark(visuals[0], 1180);
            scheduleMark(visuals[2], 1620);
            scheduleMark(headline, 2060);
            scheduleMark(inner, 2480);
        }, '0px 0px -12% 0px');
    }

    function initPrideScene() {
        var section = document.getElementById('pride');
        if (!section) {
            return;
        }
        var headline = section.querySelector('.wp-headline');
        var points = Array.prototype.slice.call(section.querySelectorAll('.pride-point'));
        var aside = section.querySelector('.pride-aside');

        createScrollScene(headline, 'center 92%', function () {
            scheduleMark(headline, 0);
        }, '0px 0px -10% 0px');

        points.forEach(function (point) {
            var figure = point.querySelector('.pride-point__fig-block');
            var body = point.querySelector('.pride-point__body');
            createScrollScene(point, 'center 92%', function () {
                scheduleMark(figure, 0);
                scheduleMark(body, 900);
            }, '0px 0px -10% 0px');
        });

        createScrollScene(aside, 'center 92%', function () {
            if (!aside) {
                return;
            }
            scheduleMark(aside.querySelector('.pride-aside__image'), 0);
            scheduleMark(aside.querySelector('.pride-aside__body'), 720);
        }, '0px 0px -10% 0px');
    }

    function initMenuScene() {
        var section = document.getElementById('menu');
        if (!section) {
            return;
        }
        var headline = section.querySelector('.wp-headline');
        var items = Array.prototype.slice.call(section.querySelectorAll('.menu-item'));

        createScrollScene(headline, 'center 92%', function () {
            scheduleMark(headline, 0);
        }, '0px 0px -10% 0px');

        items.forEach(function (item) {
            var figure = item.querySelector('.menu-item__fig');
            var body = item.querySelector('.menu-item__body');
            createScrollScene(item, 'center 92%', function () {
                scheduleMark(figure, 0);
                scheduleMark(body, 900);
            }, '0px 0px -10% 0px');
        });
    }

    function initGreetingScene() {
        var section = document.getElementById('greeting');
        if (!section) {
            return;
        }
        var headline = section.querySelector('.wp-headline');
        var body = section.querySelector('.greeting__body');
        var cover = body ? body.querySelector('.greeting-cover') : null;
        var guts = body ? body.querySelector('.greeting__guts') : null;

        createScrollScene(section, 'center bottom', function () {
            scheduleMark(headline, 0);
            scheduleMark(cover, 820);
            scheduleMark(guts, 1460);
        }, '0px 0px -12% 0px');
    }

    function initQaScene() {
        var section = document.getElementById('qa');
        if (!section) {
            return;
        }
        var headline = section.querySelector('.wp-headline');
        var items = Array.prototype.slice.call(section.querySelectorAll('.qa-item'));

        createScrollScene(section, 'top 82%', function () {
            scheduleMark(headline, 0);
            items.forEach(function (item, index) {
                scheduleMark(item, 180 + (index * 110));
            });
        }, '0px 0px -8% 0px');
    }

    function initFacilityScene() {
        var section = document.getElementById('facility');
        if (!section) {
            return;
        }
        var headline = section.querySelector('.wp-headline');
        var lead = section.querySelector('.section-lead');
        var items = Array.prototype.slice.call(section.querySelectorAll('.facility-card'));

        createScrollScene(section, 'center 92%', function () {
            scheduleMark(headline, 0);
            scheduleMark(lead, 420);
            items.forEach(function (item, index) {
                var base = 760 + (index * 320);
                scheduleMark(item.querySelector('.facility-card__image'), base);
                scheduleMark(item.querySelector('.facility-card__body'), base + 340);
            });
        }, '0px 0px -10% 0px');
    }

    function initShopInfoScene() {
        var section = document.getElementById('shop-info');
        if (!section) {
            return;
        }
        createScrollScene(section, 'top 82%', function () {
            scheduleMark(section.querySelector('.wp-headline'), 0);
            scheduleMark(section.querySelector('.shop-visual'), 340);
            scheduleMark(section.querySelector('.shop-info__guts'), 700);
        }, '0px 0px -8% 0px');
    }

    function initContactScene() {
        var section = document.getElementById('contact');
        if (!section) {
            return;
        }
        createScrollScene(section, 'top 82%', function () {
            scheduleMark(section.querySelector('.wp-headline'), 0);
            scheduleMark(section.querySelector('.contact-catch'), 420);
            scheduleMark(section.querySelector('.contact-lead-block'), 920);
            scheduleMark(section.querySelector('.contact-form-block'), 1260);
        }, '0px 0px -8% 0px');
    }

    function initScrollScenes() {
        initConceptScene();
        initPrideScene();
        initMenuScene();
        initGreetingScene();
        initQaScene();
        initFacilityScene();
        initShopInfoScene();
        initContactScene();
    }

    onReady(function () {
        initHeroScene();
        initNav();
        initScrollIndicator();
        initScrollScenes();
        initPageTop();
    });
}());




