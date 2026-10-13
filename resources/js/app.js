import Alpine from 'alpinejs';

import AOS from 'aos';
import 'aos/dist/aos.css';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

window.Alpine = Alpine;
// Dynamic UI labels use the same Laravel dictionary as Blade.
Alpine.store('language', {
    text(value) {
        if (typeof value !== 'string' || document.documentElement.lang !== 'id') return value;
        const labels = window.newmanUiLabels || {};
        if (labels[value] !== undefined) return labels[value];
        return value.replace(/\b(\d+)\s+(verified ratings?|guests hosted|adults?|children|child|infants?|guests?|passengers?|vehicles?|stops?|hours?|mins?)\b/g,
            (_, count, word) => `${count} ${labels[word] ?? word}`);
    },
});
Alpine.start();

try {
    AOS.init({
        duration: 900,
        once: true,
        offset: 90,
        easing: 'ease-out-cubic',
    });

    document.documentElement.classList.add('aos-initialized');
} catch (error) {
    console.error('AOS initialization failed:', error);
}

document.addEventListener('DOMContentLoaded', async () => {
    const swiperSelector = [
        '.hero-swiper',
        '.tour-swiper',
        '.review-swiper',
        '.tour-detail-swiper',
    ].join(',');

    if (!document.querySelector(swiperSelector)) {
        return;
    }

    let Swiper;
    let Navigation;
    let Pagination;
    let Autoplay;
    let EffectFade;

    try {
        ({
            Swiper,
            Navigation,
            Pagination,
            Autoplay,
            EffectFade,
        } = await import('./swiper-runtime.js'));
    } catch (error) {
        console.error('Swiper initialization failed:', error);
        return;
    }

    const heroSwiperEl = document.querySelector('.hero-swiper');

    if (heroSwiperEl) {
        const heroSwiper = new Swiper(heroSwiperEl, {
            modules: [Navigation, Pagination, Autoplay, EffectFade],
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            loop: false,
            rewind: true,
            speed: 1200,
            autoplay: {
                delay: 5200,
                disableOnInteraction: false,
                pauseOnMouseEnter: false,
            },
            pagination: {
                el: '.hero-pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    return `<button class="${className}" aria-label="${Alpine.store('language').text('Go to slide')} ${index + 1}"></button>`;
                },
            },
            navigation: {
                nextEl: '.hero-next',
                prevEl: '.hero-prev',
            },
            observer: true,
            observeParents: true,
            watchSlidesProgress: true,
            on: {
                init(swiper) {
                    swiper.autoplay.start();
                },
                slideChangeTransitionStart(swiper) {
                    swiper.slides.forEach((slide) => {
                        slide.classList.remove('is-hero-active');
                    });

                    const activeSlide = swiper.slides[swiper.activeIndex];

                    if (activeSlide) {
                        activeSlide.classList.add('is-hero-active');
                    }
                },
            },
        });

        const activeSlide = heroSwiper.slides[heroSwiper.activeIndex];

        if (activeSlide) {
            activeSlide.classList.add('is-hero-active');
        }
    }

    const tourSwiperEl = document.querySelector('.tour-swiper');

    if (tourSwiperEl) {
        new Swiper(tourSwiperEl, {
            modules: [Navigation, Pagination, Autoplay],
            slidesPerView: 1.12,
            spaceBetween: 18,
            loop: true,
            speed: 850,
            autoplay: {
                delay: 3600,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.tour-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.tour-next',
                prevEl: '.tour-prev',
            },
            breakpoints: {
                768: {
                    slidesPerView: 2.1,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    }

    const reviewSwiperEl = document.querySelector('#reviews .review-swiper');

if (reviewSwiperEl) {
    const reviewSwiper = new Swiper(reviewSwiperEl, {
        modules: [Navigation, Pagination, Autoplay],
        autoHeight: true,
        slidesPerView: 1.08,
        spaceBetween: 18,
        loop: true,
        speed: 850,
        autoplay: {
            delay: 4200,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.review-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.review-next',
            prevEl: '.review-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1.4,
                spaceBetween: 20,
            },
            744: {
                slidesPerView: 2,
                spaceBetween: 22,
            },
            // The tablet slider shares its row with the review summary.
            1280: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
        },
    });

    const reviewCards = Array.from(reviewSwiperEl.querySelectorAll('.trip-review-card'));
    const reviewMessageStates = new WeakMap();
    const reducedReviewMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const updateReviewHeight = () => reviewSwiper.updateAutoHeight(0);
    const updateReviewAutoplay = () => {
        const hasExpandedReview = reviewCards.some((card) =>
            card.querySelector('[data-review-toggle]').getAttribute('aria-expanded') === 'true'
        );

        if (hasExpandedReview) {
            reviewSwiper.autoplay.stop();
        } else if (!reviewSwiper.autoplay.running) {
            reviewSwiper.autoplay.start();
        }
    };

    const refreshReview = (card) => {
        const message = card.querySelector('[data-review-message]');
        const button = card.querySelector('[data-review-toggle]');
        const wasExpanded = button.getAttribute('aria-expanded') === 'true';
        const state = reviewMessageStates.get(message) ?? {};

        state.animation?.cancel();
        state.animation = null;
        delete message.dataset.animating;
        message.dataset.collapsed = 'false';
        state.expandedHeight = message.scrollHeight;

        message.dataset.collapsed = 'true';
        state.collapsedHeight = message.getBoundingClientRect().height;
        const isTruncated = state.expandedHeight > state.collapsedHeight + 1;
        const isExpanded = isTruncated && wasExpanded;

        reviewMessageStates.set(message, state);
        message.dataset.collapsed = String(isTruncated && !isExpanded);
        button.hidden = !isTruncated;
        button.setAttribute('aria-expanded', String(isExpanded));
        button.textContent = Alpine.store('language').text(isExpanded ? 'Read less' : 'Read more');
    };

    reviewCards.forEach((card) => {
        const message = card.querySelector('[data-review-message]');
        const button = card.querySelector('[data-review-toggle]');

        refreshReview(card);
        button.addEventListener('click', () => {
            const isExpanded = button.getAttribute('aria-expanded') !== 'true';
            const state = reviewMessageStates.get(message);
            const startHeight = message.getBoundingClientRect().height;
            const endHeight = isExpanded ? state.expandedHeight : state.collapsedHeight;

            // Capture the current height before cancellation so rapid clicks reverse smoothly.
            state.animation?.cancel();
            state.animation = null;
            button.setAttribute('aria-expanded', String(isExpanded));
            button.textContent = Alpine.store('language').text(isExpanded ? 'Read less' : 'Read more');
            updateReviewAutoplay();

            if (reducedReviewMotion.matches || Math.abs(startHeight - endHeight) < 1) {
                delete message.dataset.animating;
                message.dataset.collapsed = String(!isExpanded);
                updateReviewHeight();
                return;
            }

            message.dataset.collapsed = 'false';
            message.dataset.animating = 'true';
            const animation = message.animate([
                { height: `${startHeight}px` },
                { height: `${endHeight}px` },
            ], {
                duration: 320,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            });
            state.animation = animation;
            animation.onfinish = () => {
                if (state.animation !== animation) return;
                message.dataset.collapsed = String(!isExpanded);
                delete message.dataset.animating;
                state.animation = null;
                animation.cancel();
                updateReviewHeight();
            };
            updateReviewHeight();
        });
    });

    // Recheck actual line overflow when card widths change at slider breakpoints.
    const reviewWidths = new WeakMap();
    const reviewResizeObserver = new ResizeObserver((entries) => {
        let widthChanged = false;

        entries.forEach(({ target, contentRect }) => {
            // The wrapper follows each animated height change, keeping the footer visible.
            updateReviewHeight();
            if (reviewWidths.get(target) === contentRect.width) {
                return;
            }

            reviewWidths.set(target, contentRect.width);
            refreshReview(target.closest('.trip-review-card'));
            widthChanged = true;
        });

        if (widthChanged) {
            updateReviewHeight();
            updateReviewAutoplay();
        }
    });

    reviewCards.forEach((card) => reviewResizeObserver.observe(card.querySelector('[data-review-message]')));
    document.fonts.ready.then(() => {
        reviewCards.forEach(refreshReview);
        updateReviewHeight();
        updateReviewAutoplay();
    });
    updateReviewHeight();
}

const tourDetailSwiperEl = document.querySelector('.tour-detail-swiper');

if (tourDetailSwiperEl) {
    new Swiper(tourDetailSwiperEl, {
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 1.12,
        spaceBetween: 16,
        loop: true,
        speed: 850,
        autoplay: {
            delay: 3600,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.tour-detail-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.tour-detail-next',
            prevEl: '.tour-detail-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1.6,
                spaceBetween: 18,
            },
            768: {
                slidesPerView: 2.2,
                spaceBetween: 20,
            },
            1180: {
                slidesPerView: 3,
                spaceBetween: 22,
            },
        },
    });
}
});

// Progressive disclosure affects mobile itinerary presentation only.
document.addEventListener('DOMContentLoaded', () => {
    const mobile = window.matchMedia('(max-width: 767px)');
    const rows = [...document.querySelectorAll('.public-site .newman-roadmap-row')]
        .filter((row) => row.querySelector('.newman-roadmap-summary'));

    const syncAccessibility = () => {
        rows.forEach((row) => {
            if (mobile.matches) {
                row.setAttribute('aria-expanded', String(row.classList.contains('is-mobile-expanded')));
                row.setAttribute('aria-controls', row.querySelector('.newman-roadmap-summary').id);
            } else {
                row.removeAttribute('aria-expanded');
                row.removeAttribute('aria-controls');
            }
        });
    };

    rows.forEach((row) => {
        row.classList.add('has-mobile-description');
        row.addEventListener('click', () => {
            if (!mobile.matches) return;
            row.classList.toggle('is-mobile-expanded');
            syncAccessibility();
        });
    });

    mobile.addEventListener('change', syncAccessibility);
    syncAccessibility();
});
