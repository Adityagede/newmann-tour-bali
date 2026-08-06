import '../css/app.css'

import Alpine from 'alpinejs';

import AOS from 'aos';
import 'aos/dist/aos.css';

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

window.Alpine = Alpine;
Alpine.start();

AOS.init({
    duration: 900,
    once: true,
    offset: 90,
    easing: 'ease-out-cubic',
});

document.addEventListener('DOMContentLoaded', () => {
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
                    return `<button class="${className}" aria-label="Go to slide ${index + 1}"></button>`;
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

    const reviewSwiperEl = document.querySelector('.review-swiper');

if (reviewSwiperEl) {
    new Swiper(reviewSwiperEl, {
        modules: [Navigation, Pagination, Autoplay],
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
            768: {
                slidesPerView: 2,
                spaceBetween: 22,
            },
            1180: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
        },
    });
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