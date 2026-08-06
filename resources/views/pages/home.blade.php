@extends('layouts.app')

@section('content')

<section class="relative overflow-hidden bg-newman-navy text-white">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="absolute inset-0">
                    <img
                        src="{{ asset('images/hero-bg.webp') }}"
                        alt="Bali private tour with Newman Tour Guide"
                        class="hero-slide-img"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="bali-pattern absolute inset-0 opacity-20"></div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full min-h-[720px] w-[92%] max-w-7xl items-center justify-center pt-24 text-center">
    <div class="hero-content-block max-w-5xl">
                        <p class="hero-kicker mb-5 text-sm font-semibold uppercase tracking-[0.38em] text-newman-gold">
                            Newman Tour Bali
                        </p>

                        <h1 class="hero-title text-5xl font-extrabold uppercase leading-[0.98] tracking-[-0.05em] md:text-7xl lg:text-[92px]">
                            Discover Bali with Private Local Guide
                        </h1>

                        <p class="hero-description mx-auto mt-7 max-w-2xl text-base leading-8 text-white/78 md:text-lg">
                            Experience temples, beaches, rice terraces, and hidden local routes with a calm private trip made for your schedule.
                        </p>

                        <div class="hero-actions mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                                                <a
                            href="#booking"
                            class="bg-newman-gold px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-newman-navy transition duration-300 hover:bg-white"
                        >
                            Start Planning
                        </a>

                        <a
                            href="{{ route('tours') }}"
                            class="border border-white/35 px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-white transition duration-300 hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                        >
                            Browse Tours
                        </a>
                        </div>

                        <div class="hero-line mx-auto mt-16 h-14 w-px overflow-hidden bg-white/20 max-[391px]:hidden ">
                            <div class="scroll-line h-5 w-px bg-newman-gold"></div>
                        </div>

                        <p class="hero-meta mt-4 text-[11px] uppercase tracking-[0.32em] text-white/45 max-[391px]:hidden">
                            Private Tour · Transport · Custom Route
                        </p>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="absolute inset-0">
                    <img
                        src="{{ asset('images/hero-bg1.webp') }}"
                        alt="Private transport in Bali"
                        class="hero-slide-img"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="bali-pattern absolute inset-0 opacity-20"></div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full min-h-[720px] w-[92%] max-w-7xl items-center justify-center pt-24 text-center">
    <div class="hero-content-block max-w-5xl">
                        <p class="hero-kicker mb-5 text-sm font-semibold uppercase tracking-[0.38em] text-newman-gold">
    Private Transport, Arranged Around You
</p>

<h1 class="hero-title hero-title-transport text-5xl font-extrabold uppercase leading-[0.98] tracking-[-0.05em] md:text-7xl lg:text-[92px]">
    Comfortable Transport for Every Bali Journey
</h1>

<p class="hero-description hero-description-transport mx-auto mt-7 max-w-2xl text-base leading-8 text-white/78 md:text-lg">
    From couples and families to larger groups, Newman helps match your route,
    group size, and luggage with a suitable private vehicle.
</p>

<div class="hero-actions mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
    <a
        href="#vehicles"
        class="bg-newman-gold px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-newman-navy transition duration-300 hover:bg-white"
    >
        View Vehicle Options
    </a>

    <a
        href="{{ route('custom-trip.create') }}"
        class="border border-white/35 px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-white transition duration-300 hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
    >
        Plan Transport Request
    </a>
</div>

<div class="hero-line mx-auto mt-16 h-14 w-px overflow-hidden bg-white/20 max-[391px]:hidden">
    <div class="scroll-line h-5 w-px bg-newman-gold"></div>
</div>

<p class="hero-meta mt-4 text-[11px] uppercase tracking-[0.32em] text-white/45 max-[391px]:hidden">
    Small Groups · Larger Groups · Flexible Requests
</p>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="absolute inset-0">
                    <img
                        src="{{ asset('images/hero-bg2.webp') }}"
                        alt="Custom Bali experience"
                        class="hero-slide-img"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="bali-pattern absolute inset-0 opacity-20"></div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full min-h-[720px] w-[92%] max-w-7xl items-center justify-center pt-24 text-center">
    <div class="hero-content-block max-w-5xl">
                        <p class="hero-kicker mb-5 text-sm font-semibold uppercase tracking-[0.38em] text-newman-gold">
                            Custom Bali Experience
                        </p>

                        <h1 class="hero-title text-5xl font-extrabold uppercase leading-[0.98] tracking-[-0.05em] md:text-7xl lg:text-[92px]">
                            Your Route, Your Time, Your Bali Story
                        </h1>

                        <p class="hero-description mx-auto mt-7 max-w-2xl text-base leading-8 text-white/78 md:text-lg">
                            Tell us your destination list, group size, and travel date. We will help arrange a flexible route and suitable transport.
                        </p>

                        <div class="hero-actions mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                                                        <a
                                href="{{ route('custom-trip.create') }}"
                                class="bg-newman-gold px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-newman-navy transition duration-300 hover:bg-white"
                            >
                                Plan Custom Trip
                            </a>

                            <a
                                href="{{ route('contact') }}"
                                class="border border-white/35 px-8 py-4 text-sm font-bold uppercase tracking-[0.18em] text-white transition duration-300 hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                            >
                                Contact Newman
                            </a>
                        </div>

                        <div class="hero-line mx-auto mt-16 h-14 w-px overflow-hidden bg-white/20 max-[391px]:hidden">
                            <div class="scroll-line h-5 w-px bg-newman-gold"></div>
                        </div>

                        <p class="hero-meta mt-4 text-[11px] uppercase tracking-[0.32em] text-white/45 max-[391px]:hidden">
                            Flexible Route · Local Guide · Easy Request
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-pagination absolute bottom-11 left-1/2 z-20 -translate-x-1/2"></div>

        <button
            type="button"
            class="hero-prev hero-nav-btn absolute left-6 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur-md md:flex"
            aria-label="Previous slide"
        >
            <span>‹</span>
        </button>

        <button
            type="button"
            class="hero-next hero-nav-btn absolute right-6 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur-md md:flex"
            aria-label="Next slide"
        >
            <span>›</span>
        </button>
    </div>
</section>

@php
    $newmanMarqueeItems = [
        'Prepared Tour Packages',
        'Flexible Custom Trips',
        'Private Transport Arranged',
        'Local Route Coordination',
        'Clear Pricing Before Request',
        'Personal Confirmation by Newman',
    ];
@endphp

<section
    class="newman-marquee relative z-10 overflow-hidden border-y border-white/10 bg-[#071f33] text-white"
    aria-label="Newman Tour Bali services"
>
    {{-- Soft edge fade --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-y-0 left-0 z-10 w-10 bg-gradient-to-r from-[#071f33] to-transparent sm:w-20"
    ></div>

    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-y-0 right-0 z-10 w-10 bg-gradient-to-l from-[#071f33] to-transparent sm:w-20"
    ></div>

    <div class="newman-marquee-viewport overflow-hidden">
        <div class="newman-marquee-track flex w-max items-center py-4 sm:py-[18px]">
            @for ($copy = 0; $copy < 2; $copy++)
                <div
                    class="newman-marquee-group flex shrink-0 items-center"
                    @if ($copy === 1)
                        aria-hidden="true"
                    @endif
                >
                    @foreach ($newmanMarqueeItems as $item)
                        <div class="newman-marquee-item">
                            <span
                                aria-hidden="true"
                                class="newman-marquee-dot"
                            ></span>

                            <span>
                                {{ $item }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</section>


<section id="about-guide" class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="absolute left-0 top-0 h-full w-full opacity-[0.04]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="grid gap-12 lg:grid-cols-[0.92fr_1.08fr] lg:items-center">
            <div data-aos="fade-up" class="order-2 lg:order-1">
                <div class="relative">
                    <div class="overflow-hidden bg-newman-sand shadow-2xl shadow-newman-navy/10">
                        <img
                            src="{{ asset('images/owner.jpg') }}"
                            alt="Newman local Bali guide"
                            class="h-[420px] w-full object-cover sm:h-[520px] lg:h-[640px]"
                        >
                    </div>

                    <div class="absolute -bottom-6 left-5 right-5 bg-newman-navy p-5 text-white shadow-2xl sm:left-auto sm:right-8 sm:w-[320px]">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-newman-gold">
                            Local Guide
                        </p>

                        <p class="mt-3 text-xl font-semibold leading-snug">
                            Guided with care, patience, and real Bali experience.
                        </p>
                    </div>

                    <div class="absolute -right-4 top-8 hidden h-24 w-24 border border-newman-gold/40 lg:block"></div>
                    <div class="absolute -left-4 bottom-16 hidden h-32 w-32 bg-newman-sand lg:block"></div>
                </div>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="order-1 lg:order-2">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Meet Your Local Guide
                </p>

                <h2 class="mt-5 max-w-3xl text-4xl font-semibold leading-[1.05] tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                    A Bali trip that feels personal, calm, and well guided.
                </h2>

                <div class="mt-7 space-y-5 text-base leading-8 text-gray-600 sm:text-lg">
                    <p>
                        Newman Tour Guide is handled by a local Bali guide who understands how to make a trip feel comfortable, flexible, and not rushed.
                    </p>

                    <p>
                        From temple visits, rice terraces, beaches, waterfalls, local villages, to private transport around Bali, every route can be adjusted based on your time, group size, and travel style.
                    </p>
                </div>

                <div class="mt-9 grid gap-4 sm:grid-cols-3">
                    <div class="border border-gray-100 bg-newman-sand p-5">
                        <p class="text-2xl font-semibold text-newman-navy">01</p>
                        <h3 class="mt-4 font-semibold text-newman-navy">Local Route</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            Help you choose places that fit your day.
                        </p>
                    </div>

                    <div class="border border-gray-100 bg-white p-5 shadow-lg shadow-newman-navy/5">
                        <p class="text-2xl font-semibold text-newman-blue">02</p>
                        <h3 class="mt-4 font-semibold text-newman-navy">Private Trip</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            No mixed group. More calm and flexible.
                        </p>
                    </div>

                    <div class="border border-gray-100 bg-newman-navy p-5 text-white">
                        <p class="text-2xl font-semibold text-newman-gold">03</p>
                        <h3 class="mt-4 font-semibold">Transport Ready</h3>
                        <p class="mt-2 text-sm leading-6 text-white/60">
                            Avanza, Hiace, or custom car option.
                        </p>
                    </div>
                </div>

                <div class="mt-10 border-l border-newman-gold/50 pl-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-newman-blue">
                        Simple Travel Style
                    </p>

                    <p class="mt-3 max-w-2xl text-lg leading-8 text-newman-navy">
                        “Tell us where you want to go, and we will help arrange the route, timing, and car that fits your Bali plan.”
                    </p>
                </div>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                        

                    <a
                        href="{{ route('about') }}"
                        class="border border-newman-navy/15 px-7 py-4 text-center text-sm font-bold uppercase tracking-[0.18em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                    >
                        Show More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>



<section id="tours" class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="tour-card-overlay absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="mx-auto w-[92%] max-w-7xl">
        <div class=" mb-1 flex flex-col gap-6 sm:mb-1 lg:flex-row lg:items-end lg:justify-between">
            <div data-aos="fade-up" class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Popular Bali Experiences
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                    Go beyond the usual Bali trip.
                </h2>

                <p class="mt-5 max-w-2xl text-base leading-8 text-gray-600">
                    A few favorite private routes for travelers who want temples, beaches, rice terraces, waterfalls, and comfortable transport in one calm Bali experience.
                </p>
            </div>

            
        </div>

      
         <section class="relative overflow-hidden bg-white py-20 sm:py-24">
    <div class="mx-auto w-[92%] max-w-7xl">
        {{-- Section heading tetap gunakan heading Newman milikmu --}}

        <div class="mt-12 grid items-stretch gap-7  md:grid-cols-2 xl:grid-cols-3">
            @foreach ($popularTours as $tour)
                <div class="h-full">
                    <x-tour-card :tour="$tour"/>
                </div>
            @endforeach
        </div>
    </div>
</section>

    <div data-aos="fade-up" data-aos-delay="120" class="flex justify-center pb-6">
                <a
                    href="{{ route('tours') }}"
                    class="group inline-flex items-center justify-center gap-3 border border-newman-navy/15 px-6 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                >
                    Explore More Tours
                    <span class="transition duration-300 group-hover:translate-x-1">→</span>
                </a>
            </div>


             <div data-aos="fade-up" class="mt-8 border border-newman-gold/25 bg-newman-sand p-5 sm:p-7 lg:flex lg:items-center lg:justify-between lg:gap-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-blue">
                    Custom Bali Route
                </p>

                <h3 class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-newman-navy sm:text-3xl">
                    Already have your own destination list?
                </h3>

                <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                    Send your plan, date, group size, and preferred vehicle. Newman Tour Guide will help arrange the route and timing.
                </p>
            </div>

            <a
                href="{{ route('custom-trip.create') }}"
                class="mt-6 inline-flex bg-newman-gold px-6 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-white lg:mt-0"
            >
                Build Custom Trip
            </a>
        </div>
    </div>


    {{-- =========================================================
     A DAY WITH NEWMAN
     Personal local-guide story section
     ========================================================= --}}

<section
    id="day-with-newman"
    class="relative overflow-hidden  py-16 sm:py-20 lg:py-28"
>
    {{-- Quiet decorative line --}}
    <div
        aria-hidden="true"
        class="absolute left-0 top-0 h-px w-full bg-newman-navy/8"
    ></div>

    <div class="relative mx-auto w-[calc(100%-2rem)] max-w-7xl sm:w-[92%]">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,1.05fr)_minmax(420px,0.95fr)] lg:items-center lg:gap-16 xl:gap-24">

            {{-- Travel imagery --}}
            <div
                data-aos="fade-up"
                class="relative min-w-0"
            >
                <div class="relative">
                    <div class="overflow-hidden bg-newman-sand">
                        <img
                            src="{{ asset('images/owner.jpg') }}"
                            alt="Newman guiding guests during a private Bali journey"
                            loading="lazy"
                            decoding="async"
                            class="h-[430px] w-full object-cover object-center sm:h-[560px] lg:h-[620px]"
                        >
                    </div>

                    {{-- Small real travel moment --}}
                    <div
                        class="relative -mt-14 ml-auto w-[76%] border-[8px] border-[#f7f3eb] bg-white sm:-mt-24 sm:w-[64%] lg:absolute lg:-bottom-14 lg:-right-8 lg:mt-0 lg:w-[58%]"
                    >
                        <div class="overflow-hidden">
                            <img
                                src="{{ asset('images/img-review.jpeg') }}"
                                alt="A relaxed guest moment during a Bali trip"
                                loading="lazy"
                                decoding="async"
                                class="aspect-[4/3] h-full w-full object-cover"
                            >
                        </div>

                        <div class="bg-white px-4 py-4 sm:px-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                                Along the way
                            </p>

                            <p class="mt-2 text-sm leading-6 text-newman-navy">
                                Enough time for photographs, small stops,
                                and moments that were not written into the plan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Image location caption --}}
                <div class="mt-5 flex items-center gap-3 lg:mt-6">
                    <span class="h-px w-10 bg-newman-gold"></span>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-newman-navy/55">
                        Private routes around Bali
                    </p>
                </div>
            </div>

            {{-- Story --}}
            <div
                data-aos="fade-up"
                data-aos-delay="120"
                class="min-w-0 lg:pb-8"
            >
                <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-newman-gold">
                    A day with Newman
                </p>

                <h2 class="mt-5 max-w-2xl text-4xl font-semibold leading-[1.08] tracking-[-0.045em] text-newman-navy sm:text-5xl lg:text-[58px]">
                    A Bali day with time to actually enjoy it.
                </h2>

                <p class="mt-7 max-w-xl text-base leading-8 text-gray-600 sm:text-[17px]">
                    A private journey does not have to be a race between
                    destinations. We begin from your confirmed pickup area,
                    follow a route that makes sense for the day, and leave
                    enough room to enjoy each place at a comfortable pace.
                </p>

                <p class="mt-5 max-w-xl text-base leading-8 text-gray-600 sm:text-[17px]">
                    Traffic, weather, ceremonies, and how your group feels can
                    shape the final order. Newman stays in touch, adjusts where
                    needed, and helps keep the day simple.
                </p>

                {{-- Natural journey notes --}}
                <div class="mt-9 border-y border-newman-navy/10">
                    <div class="grid sm:grid-cols-3">
                        <div class="py-5 sm:pr-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Start
                            </p>

                            <p class="mt-2 text-sm leading-6 text-newman-navy">
                                Pickup arranged from the confirmed area.
                            </p>
                        </div>

                        <div class="border-t border-newman-navy/10 py-5 sm:border-l sm:border-t-0 sm:px-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Along the route
                            </p>

                            <p class="mt-2 text-sm leading-6 text-newman-navy">
                                Stops arranged in a sensible order.
                            </p>
                        </div>

                        <div class="border-t border-newman-navy/10 py-5 sm:border-l sm:border-t-0 sm:pl-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Your pace
                            </p>

                            <p class="mt-2 text-sm leading-6 text-newman-navy">
                                Time for food, photos, and quieter moments.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Personal note --}}
                <blockquote class="mt-8 border-l-2 border-newman-gold pl-5">
                    <p class="max-w-xl text-lg leading-8 text-newman-navy">
                        “Tell me the places you are interested in. I will help
                        shape a route that feels comfortable for your day.”
                    </p>

                    <footer class="mt-3 text-xs font-semibold uppercase tracking-[0.15em] text-newman-blue">
                        Newman · Local Bali guide
                    </footer>
                </blockquote>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">

                    <a
                        href="{{ route('gallery') }}"
                        class="inline-flex min-h-13 items-center justify-center border border-newman-navy/15 px-7 py-4 text-center text-xs font-bold uppercase tracking-[0.15em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:bg-newman-gold"
                    >
                        See Guest Moments
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>        
        

       
</section>

@php
    $vehicles = [
        [
            'name' => 'Toyota Avanza',
            'label' => 'Small Private Car',
            'badge' => 'Best for small group',
            'capacity' => '1–4 passengers',
            'comfort' => 'Private, simple, flexible',
            'image' => 'images/avanza.webp',
            'description' => 'A comfortable option for couples, small families, airport pickup, and full-day private tours around Bali.',
            'features' => ['Hotel pickup', 'Private trip', 'Flexible route'],
            'recommended' => false,
        ],
        [
            'name' => 'Toyota Hiace',
            'label' => 'Group Minibus',
            'badge' => 'Most comfortable for group',
            'capacity' => 'up to 10–14 passengers',
            'comfort' => 'Spacious, calm, group friendly',
            'image' => 'images/hiace.png',
            'description' => 'A spacious vehicle for family groups, office trips, airport transfers, and longer Bali day tours.',
            'features' => ['Group friendly', 'More luggage space', 'Full-day trip'],
            'recommended' => true,
        ],
    ];
@endphp

<section id="vehicles" class="relative overflow-hidden bg-newman-sand py-20 sm:py-24 lg:py-28">
    <div class="absolute inset-0 opacity-[0.06]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="mb-10 flex flex-col gap-6 sm:mb-12 lg:flex-row lg:items-end lg:justify-between">
            <div data-aos="fade-up" class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                    Private Transport
                    </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                    Choose the right car for your Bali route.
                </h2>

                <p class="mt-5 max-w-2xl text-base leading-8 text-gray-600">
                    Travel around Bali with private transport that fits your group. Choose Avanza, Hiace, or request another car option for a bigger group.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="lg:max-w-sm">
                <div class="border border-newman-gold/30 bg-white p-5 shadow-sm shadow-newman-navy/5">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-newman-gold">
                        Flexible Booking
                    </p>
                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        Select your vehicle when sending a booking request. Final details will be confirmed through WhatsApp.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_1fr_0.78fr]">
            @foreach ($vehicles as $vehicle)
                <article
                    data-aos="fade-up"
                    data-aos-delay="{{ $loop->index * 90 }}"
                    data-aos-duration="850"
                    class="vehicle-card group overflow-hidden border bg-white shadow-sm shadow-newman-navy/5 {{ $vehicle['recommended'] ? 'border-newman-gold/40' : 'border-gray-100' }}"
                >
                    <div class="relative h-64 overflow-hidden bg-white sm:h-72">
                        <img
                            src="{{ asset($vehicle['image']) }}"
                            alt="{{ $vehicle['name'] }}"
                            class="vehicle-image h-full w-full object-cover"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy/50 via-transparent to-transparent"></div>

                        <div class="vehicle-badge absolute left-4 top-4 bg-newman-navy px-3 py-2 text-[11px] font-bold uppercase tracking-[0.14em] text-white shadow-lg">
                            {{ $vehicle['badge'] }}
                        </div>

                        @if ($vehicle['recommended'])
                            <div class="absolute bottom-4 left-4 bg-newman-gold px-4 py-2 text-[11px] font-bold uppercase tracking-[0.14em] text-newman-navy">
                                Recommended
                            </div>
                        @endif
                    </div>

                    <div class="p-5 sm:p-6">
                        <p class="text-sm font-semibold text-gray-500">
                            {{ $vehicle['label'] }}
                        </p>

                        <h3 class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-newman-navy">
                            {{ $vehicle['name'] }}
                        </h3>

                        <p class="mt-4 leading-7 text-gray-600">
                            {{ $vehicle['description'] }}
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="bg-newman-sand p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-blue">
                                    Capacity
                                </p>
                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $vehicle['capacity'] }}
                                </p>
                            </div>

                            <div class="bg-newman-sand p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-blue">
                                    Travel Feel
                                </p>
                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $vehicle['comfort'] }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach ($vehicle['features'] as $feature)
                                <span class="border border-gray-100 bg-white px-3 py-2 text-xs font-medium text-newman-navy shadow-sm">
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>

                        <a
                            href="{{ route('custom-trip.create', ['vehicle' => $vehicle['name']]) }}"
                            class="vehicle-cta mt-7 flex w-full items-center justify-center bg-newman-navy px-5 py-4 text-sm font-bold uppercase tracking-[0.14em] text-white transition duration-300"
                        >
                            Select {{ $vehicle['name'] }}
                        </a>
                    </div>
                </article>
            @endforeach

            <article
                data-aos="fade-up"
                data-aos-delay="180"
                data-aos-duration="850"
                class="vehicle-card relative overflow-hidden border border-newman-gold/30 bg-newman-navy p-6 text-white shadow-2xl shadow-newman-navy/15 sm:p-7"
            >
                <div class="absolute inset-0 opacity-20">
                    <div class="h-full w-full bali-pattern"></div>
                </div>

                <div class="relative flex h-full min-h-[520px] flex-col">
                    <div>
                        <div class="flex h-14 w-14 items-center justify-center bg-newman-gold text-2xl font-semibold text-newman-navy">
                            +
                        </div>

                        <p class="mt-8 text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                            Custom Option
                        </p>

                        <h3 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] sm:text-4xl">
                            Need another car?
                        </h3>

                        <p class="mt-5 leading-8 text-white/65">
                            For bigger groups or special requests, choose another car option and tell us your group size. Newman Tour Guide will help recommend the suitable vehicle.
                        </p>
                    </div>

                    <div class="mt-8 space-y-3">
                        <div class="border border-white/10 bg-white/8 p-4">
                            <p class="text-sm font-semibold">Bigger group</p>
                            <p class="mt-1 text-sm text-white/55">For more than standard Hiace capacity.</p>
                        </div>

                        <div class="border border-white/10 bg-white/8 p-4">
                            <p class="text-sm font-semibold">Not sure what to choose</p>
                            <p class="mt-1 text-sm text-white/55">Send your plan and let us recommend.</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-8">
                        <a
                            href="{{ route('custom-trip.create', ['vehicle' => 'Another Car']) }}"
                            class="flex w-full items-center justify-center bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.14em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-white"
                        >
                            Select Another Car
                        </a>

                        <p class="mt-4 text-center text-xs leading-6 text-white/45">
                            Final vehicle availability will be confirmed manually.
                        </p>
                    </div>
                </div>
            </article>
        </div>

        <div data-aos="fade-up" class="mt-8 grid gap-4 border border-newman-gold/25 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6 lg:grid-cols-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-newman-blue">
                    01 · Choose Tour
                </p>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Pick one of the Bali tour routes or request a custom destination list.
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-newman-blue">
                    02 · Select Vehicle
                </p>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Choose Avanza, Hiace, another car, or let Newman recommend based on your group.
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-newman-blue">
                    03 · Confirm Details
                </p>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Booking request will be confirmed manually through WhatsApp before the trip.
                </p>
            </div>
        </div>
    </div>
</section>


@php
    $whyItems = [
        [
            'number' => '01',
            'title' => 'Private route, not a rushed schedule',
            'description' => 'Your Bali trip can be adjusted based on your time, mood, and group condition. Stop longer when the place feels good, skip what does not fit.',
        ],
        [
            'number' => '02',
            'title' => 'Local guidance with comfortable transport',
            'description' => 'From hotel pickup to the last stop of the day, Newman helps arrange the route with Avanza, Hiace, or another car option when needed.',
        ],
        [
            'number' => '03',
            'title' => 'Easy planning through WhatsApp',
            'description' => 'Send your destination list, travel date, and group size. The details can be confirmed calmly before the trip starts.',
        ],
    ];

    $smallDetails = [
        'Hotel pickup',
        'Flexible timing',
        'Photo stops',
        'Local food tips',
    ];
@endphp



{{-- =========================================================
     WHY TRAVEL WITH NEWMAN
     Replaces the previous Why Choose Newman section only
     ========================================================= --}}

@php
    $newmanReasons = [
        [
            'icon' => 'guide',
            'title' => 'A local guide who keeps things easy',
            'description' => 'Ask questions, share what interests you, and enjoy the day with someone who understands Bali and the route.',
        ],
        [
            'icon' => 'vehicle',
            'title' => 'Private transport for your group',
            'description' => 'Travel with an Avanza, Hiace, or another vehicle arrangement that suits your group size and trip plan.',
        ],
        [
            'icon' => 'route',
            'title' => 'A route that can still breathe',
            'description' => 'The plan stays clear without feeling rigid. Stops can be reordered when traffic, weather, or your pace changes.',
        ],
        [
            'icon' => 'price',
            'title' => 'Clear pricing before you continue',
            'description' => 'See the available package price or request a quotation before the trip is personally confirmed by Newman.',
        ],
        [
            'icon' => 'family',
            'title' => 'Comfortable for different travel styles',
            'description' => 'Couples, families, and private groups can enjoy a pace that leaves room for rest, photographs, and meals.',
        ],
        [
            'icon' => 'heart',
            'title' => 'Small Bali moments matter too',
            'description' => 'A quiet road, a local food stop, or a place worth staying longer can become the best part of the day.',
        ],
    ];

    $newmanHighlights = [
        [
            'value' => '1–14',
            'label' => 'Guests in a private request',
        ],
        [
            'value' => '2 + custom',
            'label' => 'Vehicle arrangements',
        ],
        [
            'value' => 'Flexible',
            'label' => 'Route and timing',
        ],
        [
            'value' => 'Personal',
            'label' => 'WhatsApp confirmation',
        ],
    ];
@endphp

<section
    id="why-newman"
    class="relative overflow-hidden  py-16 sm:py-20 lg:py-28"
>
    {{-- Soft background accents --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute -left-32 top-24 h-80 w-80 rounded-full bg-newman-gold/[0.055] blur-3xl"
    ></div>

    <div
        aria-hidden="true"
        class="pointer-events-none absolute -right-32 bottom-16 h-80 w-80 rounded-full bg-newman-blue/[0.045] blur-3xl"
    ></div>

    <div class="relative mx-auto w-[calc(100%-2rem)] max-w-7xl sm:w-[92%]">
        {{-- Section heading --}}
        <div
            data-aos="fade-up"
            class="mx-auto max-w-3xl text-center"
        >
            <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-newman-gold">
                Why travel with Newman?
            </p>

            <h2 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.045em] text-newman-navy sm:text-5xl lg:text-[58px]">
                More than getting from one Bali stop to the next.
            </h2>

            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-gray-600 sm:text-[17px]">
                Newman helps arrange a private Bali day that feels clear,
                comfortable, and personal—without turning every hour into a
                strict schedule.
            </p>
        </div>

        {{-- Six reasons --}}
        <div class="mt-12 grid gap-5 md:grid-cols-2 lg:mt-16 lg:grid-cols-3 lg:gap-6">
            @foreach ($newmanReasons as $index => $reason)
                <article
                    data-aos="fade-up"
                    data-aos-delay="{{ ($index % 3) * 70 }}"
                    class="group relative flex min-w-0 flex-col items-center rounded-[24px] border border-newman-navy/8 bg-white px-6 py-8 text-center shadow-[0_14px_45px_rgba(8,36,58,0.055)] transition duration-300 hover:-translate-y-1 hover:border-newman-gold/40 hover:shadow-[0_20px_55px_rgba(8,36,58,0.09)] sm:min-h-[285px] sm:px-8 sm:py-9"
                >
                    {{-- Icon --}}
                    <div class="flex h-[72px] w-[72px] shrink-0 items-center justify-center rounded-full border border-newman-gold/20 bg-newman-gold/[0.11] text-newman-navy transition duration-300 group-hover:bg-newman-gold group-hover:text-newman-navy">
                        @switch($reason['icon'])
                            @case('guide')
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <circle cx="9" cy="7" r="3"></circle>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.5 20v-2.2A4.8 4.8 0 0 1 8.3 13h1.4a4.8 4.8 0 0 1 4.8 4.8V20"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        d="M16 8.5a3.1 3.1 0 0 1 0 5.8M18.5 6.5a5.8 5.8 0 0 1 0 9.8"
                                    ></path>
                                </svg>
                                @break

                            @case('vehicle')
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 16V9.8c0-.8.4-1.5 1.1-1.9L7.5 6.5h9l2.4 1.4c.7.4 1.1 1.1 1.1 1.9V16"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        d="M3 16h18M6.5 16v2M17.5 16v2"
                                    ></path>
                                    <circle cx="7.5" cy="13" r="1.2"></circle>
                                    <circle cx="16.5" cy="13" r="1.2"></circle>
                                </svg>
                                @break

                            @case('route')
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <circle cx="6" cy="6" r="2.2"></circle>
                                    <circle cx="18" cy="18" r="2.2"></circle>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.2 6H14a3 3 0 0 1 3 3v1a3 3 0 0 1-3 3h-4a3 3 0 0 0-3 3v0"
                                    ></path>
                                </svg>
                                @break

                            @case('price')
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 3v18M16 7.2c-.9-.8-2.2-1.2-3.6-1.2-2.1 0-3.8 1.1-3.8 2.8 0 4.3 7.8 2.1 7.8 6.3 0 1.7-1.7 2.9-4.1 2.9-1.6 0-3.1-.5-4.1-1.4"
                                    ></path>
                                </svg>
                                @break

                            @case('family')
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <circle cx="8" cy="7" r="2.7"></circle>
                                    <circle cx="16.5" cy="8.5" r="2.2"></circle>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.5 19v-1.8A4.2 4.2 0 0 1 7.7 13h.6a4.2 4.2 0 0 1 4.2 4.2V19M13 15a3.7 3.7 0 0 1 3.3-2h.4a3.8 3.8 0 0 1 3.8 3.8V19"
                                    ></path>
                                </svg>
                                @break

                            @default
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-8 w-8"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20.5 8.8c0 5.5-8.5 10.2-8.5 10.2S3.5 14.3 3.5 8.8A4.4 4.4 0 0 1 12 7.2a4.4 4.4 0 0 1 8.5 1.6Z"
                                    ></path>
                                </svg>
                        @endswitch
                    </div>

                    <h3 class="mt-6 text-xl font-semibold leading-7 tracking-[-0.025em] text-newman-navy">
                        {{ $reason['title'] }}
                    </h3>

                    <p class="mt-4 max-w-sm text-sm leading-7 text-gray-600">
                        {{ $reason['description'] }}
                    </p>

                    {{-- Small confirmation marker --}}
                    <div class="mt-auto pt-6">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-newman-navy text-newman-gold transition duration-300 group-hover:bg-newman-gold group-hover:text-newman-navy">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m7.5 12 3 3 6-7"
                                ></path>
                            </svg>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Trust / service summary --}}
        <div
            data-aos="fade-up"
            class="mt-10 overflow-hidden rounded-[24px] bg-gradient-to-r from-newman-navy via-[#123753] to-newman-navy px-6 py-8 text-white shadow-[0_18px_55px_rgba(8,36,58,0.14)] sm:px-8 lg:mt-14 lg:px-10"
        >
            <div class="grid grid-cols-2 gap-y-8 sm:grid-cols-4">
                @foreach ($newmanHighlights as $highlight)
                    <div
                        @class([
                            'min-w-0 text-center',
                            'border-l border-white/12' => ! $loop->first,
                            'max-sm:border-l-0' => $loop->iteration === 3,
                        ])
                    >
                        <p class="break-words text-2xl font-semibold tracking-[-0.035em] text-newman-gold sm:text-3xl lg:text-[34px]">
                            {{ $highlight['value'] }}
                        </p>

                        <p class="mx-auto mt-2 max-w-[170px] text-xs leading-5 text-white/65 sm:text-sm sm:leading-6">
                            {{ $highlight['label'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


@php
    $guestReviews = [
        [
            'name' => 'Maya & Daniel',
            'from' => 'Australia',
            'trip' => 'Ubud Culture Tour',
            'rating' => '5.0',
            'date' => 'Private day trip',
            'title' => 'A calm and beautiful way to see Bali',
            'message' => 'The trip felt relaxed from the beginning. We did not feel rushed, the route was flexible, and Newman helped us enjoy Ubud, rice terrace, local stops, and a waterfall in one comfortable day.',
            'initials' => 'MD',
        ],
        [
            'name' => 'Rina Family',
            'from' => 'Indonesia',
            'trip' => 'South Bali Sunset Tour',
            'rating' => '5.0',
            'date' => 'Family trip',
            'title' => 'Comfortable for family travel',
            'message' => 'We traveled with family and the car was comfortable. The timing was good, the beach stops were not too crowded, and the sunset plan was arranged nicely.',
            'initials' => 'RF',
        ],
        [
            'name' => 'Hiro Tanaka',
            'from' => 'Japan',
            'trip' => 'Kintamani Volcano Tour',
            'rating' => '4.9',
            'date' => 'Nature route',
            'title' => 'Simple, private, and well planned',
            'message' => 'The route was easy to follow and the day felt smooth. We liked the volcano view, coffee stop, and local roads. It felt more personal than a big group tour.',
            'initials' => 'HT',
        ],
        [
            'name' => 'Emma Wilson',
            'from' => 'United Kingdom',
            'trip' => 'Custom Bali Route',
            'rating' => '5.0',
            'date' => 'Custom trip',
            'title' => 'Great help for a custom Bali day',
            'message' => 'We sent our own destination list and Newman helped arrange the timing. The trip felt natural and easy, especially because we could adjust the plan during the day.',
            'initials' => 'EW',
        ],
    ];
@endphp

@php
    $guestReviews = [
        [
            'name' => 'Alexandra C',
            'from' => 'Saudi Arabia',
            'time' => '5 months ago',
            'trip' => 'Ubud Private Tour',
            'title' => 'Bali, visits and company💕',
            'message' => '2 days done with the nyuman team, the waterfalls, coffee factory, Virgin beach, the king’s temple, “Toto”, made us live his life, his culture, his country, an incredible wealth, the visit to the EAST was much longer in time to drive but also rewarding
Taking a guide is for me an indispensable asset in the success of such a trip
Thank you!',
            'initials' => 'A',
        ],
        [
            'name' => 'Benson C',
            'from' => 'Singapore',
            'time' => '7 months ago',
            'trip' => 'South Bali Sunset Trip',
            'title' => 'Amazing day and very comfortable trip',
            'message' => 'We had a family holiday with one toddler and Nyoman is the perfect guide to get you through that. He came highly recommended by a trusted friend and werent disappointed.
Our trip in Bali spanned Central Bali, Ubud, and Nusa Penida, all planned and catered for by Nyoman and his able team.
Throughout the planning, Nyoman was transparent, flexible, and good-intentioned and he knows Bali very well as well as the tourism and hospitality industry having been in the cruise-line & service industry for about a decade before embarking on being a tour guide.
Highly recommended',
            'initials' => 'M',
        ],
        [
            'name' => 'Archi D',
            'from' => 'Australia',
            'time' => '3 months ago',
            'trip' => 'Kintamani Volcano Route',
            'title' => 'Not only a guide',
            'message' => 'Nyoman is not only a driver or a guide. You get a great person who is extremely knowledgeable about Bali and Balinese culture. Nyoman himself has traveled round the world and has seen a lot of different cultures. Not a moment in our trip went being bored. He told us stories, gave us tips, took our photos and made us laugh with his great wit from time to time. He is a very flexible person and would show you around as per your need and schedule. He also took us to some offbeat locations, away from tourist crowds. His car was clean and kept water bottles for both of us every day as it was extremely hot outside. By the time we said goodbye, we had become very good friends. I hope to go back to Bali and travel again with him.',
            'initials' => 'A',
        ],
        [
            'name' => 'Queen H',
            'from' => 'Indonesia',
            'time' => '2 months ago',
            'trip' => 'Batur Jeep Moments',
            'title' => 'Memorable Bali Trip',
            'message' => 'NewMan is the Perfect Bali Driver, highly recommend.
This was our frist trip to Bali, NewMan with his charming smile made us feel Welcome.
He also took pictures for us, appreciate for that.
He explained us a lot of Balinese Culture & Balis History.
He made our trip Memorable.
I will definitely visit again to Bali',
            'initials' => 'R',
        ],
    ];
@endphp

<section id="reviews" class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="mx-auto w-[92%] max-w-7xl">
        <div data-aos="fade-up" class="mb-10 text-center sm:mb-14">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Guest Reviews
            </p>

            <h2 class="mt-4 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                What travelers say about Newman
            </h2>

            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-gray-600">
                Real travel-style feedback from guests who chose private Bali routes, flexible timing, and comfortable transport.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[0.75fr_1.25fr] lg:items-center">
            <aside data-aos="fade-up" class="mx-auto w-full max-w-sm text-center lg:mx-0">
                <div class="border border-gray-100 bg-newman-sand/70 p-7 shadow-sm shadow-newman-navy/5 sm:p-8">
                    <img
                     src="{{ asset('images/img-review.jpeg') }}"
                      alt="background-review"
                      class="pb-4 rounded-lg">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white shadow-lg">
                        <img
                            src="{{ asset('logo/tripadvisor.png') }}"
                            alt="Tripadvisor"
                            class="h-10 w-auto"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                        >
                        <span class="hidden text-sm font-bold text-newman-navy">
                            Tripadvisor
                        </span>
                    </div>

                    <h3 class="mt-6 text-2xl font-semibold text-newman-navy">
                        Freelance Bali Driver
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                       Newman Bali Tour & Transport
                    </p>

                    <div class="mt-5 flex justify-center gap-1.5">
                        <span class="trip-rating-dot"></span>
                        <span class="trip-rating-dot"></span>
                        <span class="trip-rating-dot"></span>
                        <span class="trip-rating-dot"></span>
                        <span class="trip-rating-dot"></span>
                    </div>

                    <p class="mt-4 text-lg font-semibold text-newman-navy">
                        5.0 average rating
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Based on guest trip feedback
                    </p>

                    <a
                        href="https://www.tripadvisor.com/Attraction_Review-g3187039-d12703454-Reviews-Freelance_Bali_Driver-Samplangan_Bali.html"
                        target="blank"
                        class="mt-7 inline-flex border border-newman-navy/15 bg-white px-6 py-3 text-sm font-bold text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                    >
                        Open Tripadvisor
                    </a>
                </div>

                <p class="mt-4 text-xs leading-6 text-gray-400">
                    Replace this with your real Tripadvisor or Google review link when the business profile is ready.
                </p>
            </aside>

            <div data-aos="fade-up" data-aos-delay="140" class="relative min-w-0">
                <div class="swiper review-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($guestReviews as $review)
                            <div class="swiper-slide h-auto">
                                <article class="trip-review-card flex h-full flex-col rounded-2xl border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-newman-blue text-lg font-bold text-white">
                                                {{ $review['initials'] }}
                                            </div>

                                            <div>
                                                <h3 class="font-semibold leading-tight text-newman-navy">
                                                    {{ $review['name'] }}
                                                </h3>

                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $review['time'] }} · {{ $review['from'] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white shadow-md">
                                            <span class="text-sm font-bold text-[#00aa6c]">TA</span>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex items-center gap-1.5">
                                        <span class="trip-rating-dot"></span>
                                        <span class="trip-rating-dot"></span>
                                        <span class="trip-rating-dot"></span>
                                        <span class="trip-rating-dot"></span>
                                        <span class="trip-rating-dot"></span>
                                    </div>

                                    <div class="mt-5">
                                        <p class="inline-flex bg-newman-sand px-3 py-2 text-xs font-semibold text-newman-navy">
                                            {{ $review['trip'] }}
                                        </p>

                                        <h4 class="mt-4 text-xl font-semibold leading-snug tracking-[-0.03em] text-newman-navy">
                                            {{ $review['title'] }}
                                        </h4>

                                        <p class="mt-3 line-clamp-4 text-sm leading-7 text-gray-600">
                                            {{ $review['message'] }}
                                        </p>
                                    </div>

                                    <div class="mt-auto pt-6">
                                        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                            <button type="button" class="text-sm font-semibold text-gray-500 transition hover:text-newman-blue">
                                                Read more
                                            </button>

                                            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-newman-gold">
                                                Verified trip
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <div class="review-pagination"></div>
                </div>

                <button
                    type="button"
                    class="review-prev review-nav-btn absolute -left-5 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-newman-navy shadow-lg lg:flex"
                    aria-label="Previous review"
                >
                    ‹
                </button>

                <button
                    type="button"
                    class="review-next review-nav-btn absolute -right-5 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-newman-navy shadow-lg lg:flex"
                    aria-label="Next review"
                >
                    ›
                </button>

                <div class="mt-5 flex justify-center gap-3 lg:hidden">
                    <button
                        type="button"
                        class="review-prev review-nav-btn flex h-11 w-11 items-center justify-center rounded-full border border-gray-100 bg-white text-newman-navy shadow-md"
                        aria-label="Previous review"
                    >
                        ‹
                    </button>

                    <button
                        type="button"
                        class="review-next review-nav-btn flex h-11 w-11 items-center justify-center rounded-full border border-gray-100 bg-white text-newman-navy shadow-md"
                        aria-label="Next review"
                    >
                        ›
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>



@php
    $moments = collect($guestMoments ?? []);

    $featuredMoment = $moments->first();
    $smallMoments = $moments->skip(1)->take(5);
@endphp

<section id="gallery" class="relative overflow-hidden bg-newman-sand/45 py-20 sm:py-24 lg:py-28">
    <div class="absolute inset-0 opacity-[0.05]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="mb-10 flex flex-col gap-6 lg:mb-14 lg:flex-row lg:items-end lg:justify-between">
            <div data-aos="fade-up" class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                    Guest Moments
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                    Real moments from Bali trips with Newman.
                </h2>

                <p class="mt-5 max-w-2xl text-base leading-8 text-gray-600">
                    A small collection of travel memories with guests: private routes, family trips, photo stops, comfortable transport, and calm Bali moments along the way.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="flex lg:justify-end">
                <a
                    href="{{ route('gallery') }}"
                    class="group inline-flex items-center justify-center gap-3 border border-newman-navy/15 bg-white px-6 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                >
                    Explore More
                    <span class="transition duration-300 group-hover:translate-x-1">→</span>
                </a>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1.05fr_0.95fr]">
            <article
                data-aos="fade-up"
                class="guest-gallery-card min-h-[540px] bg-newman-navy text-white shadow-2xl shadow-newman-navy/10"
            >
                <img
                    src="{{ asset($guestMoments[0]['image']) }}"
                    alt="{{ $guestMoments[0]['title'] }}"
                    class="guest-gallery-image absolute inset-0 h-full w-full object-cover opacity-85"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/35 to-transparent"></div>

                <div class="guest-gallery-tag absolute left-5 top-5 bg-newman-navy px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white shadow-lg">
                    {{ data_get(
                            $featuredMoment,
                            'tag',
                            data_get($featuredMoment, 'location', 'Bali')
                        ) }}
                </div>

                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7 lg:p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-gold">
                        {{ data_get($featuredMoment, 'location', 'Bali') }}
                    </p>

                    <h3 class="mt-3 max-w-2xl text-4xl font-semibold leading-tight tracking-[-0.04em] sm:text-5xl">
                        {{ data_get(
    $featuredMoment,
    'title',
    'Guest moment in Bali'
) }}
                    </h3>

                    <p class="mt-4 max-w-xl leading-8 text-white/72">
                        {{ data_get(
    $featuredMoment,
    'caption',
    data_get($featuredMoment, 'description', '')
) }}
                    </p>
                </div>
            </article>

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($smallMoments as $moment)
    @php
        $momentSize = data_get(
            $moment,
            'size',
            data_get($moment, 'display_size', 'regular')
        );

        $momentImage = data_get(
            $moment,
            'image',
            data_get(
                $moment,
                'image_path',
                'images/gallery-placeholder.jpg'
            )
        );

        $momentTitle = data_get(
            $moment,
            'title',
            'Guest moment in Bali'
        );

        $momentAlt = data_get(
            $moment,
            'alt',
            data_get($moment, 'alt_text', $momentTitle)
        );

        $momentTag = data_get(
            $moment,
            'tag',
            data_get($moment, 'location', 'Bali')
        );

        $momentLocation = data_get(
            $moment,
            'location',
            'Bali'
        );

        $momentCaption = data_get(
            $moment,
            'caption',
            data_get($moment, 'description', '')
        );
    @endphp

    <article
        data-aos="fade-up"
        data-aos-delay="{{ $loop->index * 70 }}"
        data-aos-duration="850"
        class="guest-gallery-card relative min-h-[250px] overflow-hidden bg-newman-navy text-white shadow-sm shadow-newman-navy/5
            {{ $momentSize === 'large' ? 'sm:col-span-2 lg:min-h-[360px]' : '' }}"
    >
        <img
            src="{{ asset($momentImage) }}"
            alt="{{ $momentAlt }}"
            loading="lazy"
            decoding="async"
            class="guest-gallery-image absolute inset-0 h-full w-full object-cover opacity-85"
        >

        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/30 to-transparent"></div>

        <div class="guest-gallery-tag absolute left-5 top-5 bg-newman-navy px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white">
            {{ $momentTag }}
        </div>

        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-newman-gold">
                {{ $momentLocation }}
            </p>

            <h3 class="mt-3 text-xl font-semibold leading-snug text-white sm:text-2xl">
                {{ $momentTitle }}
            </h3>

            @if ($momentCaption)
                <p class="mt-3 line-clamp-2 text-sm leading-7 text-white/70">
                    {{ $momentCaption }}
                </p>
            @endif
        </div>
    </article>
@endforeach
            </div>
        </div>

        <div data-aos="fade-up" class="mt-8 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6 lg:flex lg:items-center lg:justify-between lg:gap-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-blue">
                    Real documentation
                </p>

                <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">
                    Use real photos from your father’s trips with guests. This will make Newman Tour Guide feel more personal, trusted, and natural.
                </p>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row lg:mt-0">
                <a
                    href="{{ route('gallery') }}"
                    class="bg-newman-navy px-6 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl"
                >
                    View Full Gallery
                </a>

                <a
                    href="#booking"
                    class="border border-newman-navy/15 px-6 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                >
                    Plan Your Trip
                </a>
            </div>
        </div>
    </div>
</section>
@endsection