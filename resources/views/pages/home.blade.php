@extends('layouts.app')

@section('content')

<section class="relative overflow-hidden bg-newman-navy text-white">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="absolute inset-0">
                    <img
                        src="{{ asset('images/hero-bg.webp') }}"
                        alt="Bali private tour with Newman Tour Bali"
                        width="2268"
                        height="4032"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                        class="hero-slide-img hero-slide-img-gate"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full w-[calc(100%-2rem)] max-w-7xl items-center text-center lg:text-left">
                    <div class="hero-content-block w-full">
                        <p class="hero-kicker font-semibold uppercase text-newman-gold">
                            Newman Tour Bali
                        </p>

                        <h1 class="hero-title font-extrabold">
                            Discover Bali with Private Local Guide
                        </h1>

                        <p class="hero-description mx-auto text-white/82 lg:mx-0">
                            Experience temples, beaches, rice terraces, and hidden local routes with a calm private trip made for your schedule.
                        </p>

                        <div class="hero-actions flex flex-col items-center justify-center sm:flex-row lg:justify-start">
                            <a
                                href="#booking"
                                class="hero-primary-cta bg-newman-gold font-bold uppercase text-newman-navy transition duration-300 hover:bg-white"
                            >
                                Start Planning
                            </a>

                            <a
                                href="{{ route('tours') }}"
                                class="hero-secondary-cta font-bold uppercase text-white transition duration-300 hover:text-newman-gold"
                            >
                                Browse Tours
                            </a>
                        </div>

                        <p class="hero-meta uppercase text-white/55">
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
                        width="1376"
                        height="544"
                        loading="lazy"
                        fetchpriority="low"
                        decoding="async"
                        class="hero-slide-img hero-slide-img-coast"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full w-[calc(100%-2rem)] max-w-7xl items-center text-center lg:text-left">
                    <div class="hero-content-block w-full">
                        <p class="hero-kicker font-semibold uppercase text-newman-gold">
                            Private Transport, Arranged Around You
                        </p>

                        <h1 class="hero-title hero-title-transport font-extrabold">
                            Comfortable Transport for Every Bali Journey
                        </h1>

                        <p class="hero-description hero-description-transport mx-auto text-white/82 lg:mx-0">
                            From couples and families to larger groups, Newman helps match your route,
                            group size, and luggage with a suitable private vehicle.
                        </p>

                        <div class="hero-actions flex flex-col items-center justify-center sm:flex-row lg:justify-start">
                            <a
                                href="#vehicles"
                                class="hero-primary-cta bg-newman-gold font-bold uppercase text-newman-navy transition duration-300 hover:bg-white"
                            >
                                View Vehicle Options
                            </a>

                            <a
                                href="{{ route('custom-trip.create') }}"
                                class="hero-secondary-cta font-bold uppercase text-white transition duration-300 hover:text-newman-gold"
                            >
                                Plan Transport Request
                            </a>
                        </div>

                        <p class="hero-meta uppercase text-white/55">
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
                        width="3024"
                        height="4032"
                        loading="lazy"
                        fetchpriority="low"
                        decoding="async"
                        class="hero-slide-img hero-slide-img-temple"
                    >
                    <div class="hero-overlay absolute inset-0"></div>
                </div>

                <div class="hero-slide-content relative z-10 mx-auto flex h-full w-[calc(100%-2rem)] max-w-7xl items-center text-center lg:text-left">
                    <div class="hero-content-block w-full">
                        <p class="hero-kicker font-semibold uppercase text-newman-gold">
                            Custom Bali Experience
                        </p>

                        <h1 class="hero-title font-extrabold">
                            Your Route, Your Time, Your Bali Story
                        </h1>

                        <p class="hero-description mx-auto text-white/82 lg:mx-0">
                            Tell us your destination list, group size, and travel date. We will help arrange a flexible route and suitable transport.
                        </p>

                        <div class="hero-actions flex flex-col items-center justify-center sm:flex-row lg:justify-start">
                            <a
                                href="{{ route('custom-trip.create') }}"
                                class="hero-primary-cta bg-newman-gold font-bold uppercase text-newman-navy transition duration-300 hover:bg-white"
                            >
                                Plan Custom Trip
                            </a>

                            <a
                                href="{{ route('contact') }}"
                                class="hero-secondary-cta font-bold uppercase text-white transition duration-300 hover:text-newman-gold"
                            >
                                Contact Newman
                            </a>
                        </div>

                        <p class="hero-meta uppercase text-white/55">
                            Flexible Route · Local Guide · Easy Request
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-pagination absolute bottom-6 left-1/2 z-20 -translate-x-1/2 sm:bottom-8"></div>

        <button
            type="button"
            class="hero-prev hero-nav-btn absolute left-5 top-1/2 z-20 hidden h-10 w-10 -translate-y-1/2 items-center justify-center border border-white/35 text-white md:flex lg:left-7"
            aria-label="Previous slide"
        >
            <span>‹</span>
        </button>

        <button
            type="button"
            class="hero-next hero-nav-btn absolute right-5 top-1/2 z-20 hidden h-10 w-10 -translate-y-1/2 items-center justify-center border border-white/35 text-white md:flex lg:right-7"
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


<section id="about-guide" class="guide-section relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="guide-pattern absolute left-0 top-0 h-full w-full opacity-[0.04]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="guide-layout grid gap-12 lg:grid-cols-[0.92fr_1.08fr] lg:items-center">
            <div data-aos="fade-up" data-aos-delay="120" class="guide-intro">
                <p class="guide-kicker text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Meet Your Local Guide
                </p>

                <h2 class="guide-heading mt-5 max-w-3xl text-4xl font-semibold leading-[1.05] tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                    A Bali trip that feels personal, calm, and well guided.
                </h2>
            </div>

            <div data-aos="fade-up" class="guide-media">
                <div class="relative">
                    <div class="guide-photo-frame overflow-hidden bg-newman-sand shadow-2xl shadow-newman-navy/10">
                        <img
                            src="{{ asset('images/owner.jpg') }}"
                            alt="Newman local Bali guide"
                            width="1600"
                            height="1578"
                            loading="lazy"
                            decoding="async"
                            class="guide-photo h-[420px] w-full object-cover sm:h-[520px] lg:h-[640px]"
                        >
                    </div>

                    <div class="guide-photo-note absolute -bottom-6 left-5 right-5 bg-newman-navy p-5 text-white shadow-2xl sm:left-auto sm:right-8 sm:w-[320px]">
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

            <div data-aos="fade-up" data-aos-delay="120" class="guide-story">
                <div class="guide-copy mt-7 space-y-5 text-base leading-8 text-gray-600 sm:text-lg">
                    <p>
                        Newman Tour Bali is handled by a local Bali guide who understands how to make a trip feel comfortable, flexible, and not rushed.
                    </p>

                    <p>
                        From temple visits, rice terraces, beaches, waterfalls, local villages, to private transport around Bali, every route can be adjusted based on your time, group size, and travel style.
                    </p>
                </div>

                <div class="guide-values mt-9 grid gap-4 sm:grid-cols-3">
                    <div class="guide-value guide-value-sand border border-gray-100 bg-newman-sand p-5">
                        <p class="guide-value-number text-2xl font-semibold text-newman-navy">01</p>
                        <h3 class="guide-value-title mt-4 font-semibold text-newman-navy">Local Route</h3>
                        <p class="guide-value-copy mt-2 text-sm leading-6 text-gray-600">
                            Help you choose places that fit your day.
                        </p>
                    </div>

                    <div class="guide-value guide-value-white border border-gray-100 bg-white p-5 shadow-lg shadow-newman-navy/5">
                        <p class="guide-value-number text-2xl font-semibold text-newman-blue">02</p>
                        <h3 class="guide-value-title mt-4 font-semibold text-newman-navy">Private Trip</h3>
                        <p class="guide-value-copy mt-2 text-sm leading-6 text-gray-600">
                            No mixed group. More calm and flexible.
                        </p>
                    </div>

                    <div class="guide-value guide-value-navy border border-gray-100 bg-newman-navy p-5 text-white">
                        <p class="guide-value-number text-2xl font-semibold text-newman-gold">03</p>
                        <h3 class="guide-value-title mt-4 font-semibold">Transport Ready</h3>
                        <p class="guide-value-copy mt-2 text-sm leading-6 text-white/60">
                            Avanza, Hiace, or custom car option.
                        </p>
                    </div>
                </div>

                <div class="guide-quote mt-10 border-l border-newman-gold/50 pl-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-newman-blue">
                        Simple Travel Style
                    </p>

                    <p class="mt-3 max-w-2xl text-lg leading-8 text-newman-navy">
                        “Tell us where you want to go, and we will help arrange the route, timing, and car that fits your Bali plan.”
                    </p>
                </div>

                <div class="guide-actions mt-10 flex flex-col gap-4 sm:flex-row">
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



<section id="tours" class="tour-experiences relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="tour-experiences-rule absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="tour-experiences-shell mx-auto w-[92%] max-w-7xl">
        <div class="tour-experiences-header mb-1 flex flex-col gap-6 sm:mb-1 lg:flex-row lg:items-end lg:justify-between">
            <div data-aos="fade-up" class="tour-experiences-intro max-w-3xl">
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

      
         <section data-aos="fade-up" data-aos-delay="120" class="tour-experiences-list relative overflow-hidden bg-white py-20 sm:py-24">
    <div class="tour-experiences-grid-shell mx-auto w-[92%] max-w-7xl">
        {{-- Section heading tetap gunakan heading Newman milikmu --}}

        <div class="tour-experiences-grid mt-12 grid items-stretch gap-7 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($popularTours as $tour)
                <div class="tour-experiences-item h-full">
                    <x-tour-card :tour="$tour"/>
                </div>
            @endforeach
        </div>
    </div>
</section>

    <div data-aos="fade-up" data-aos-delay="120" class="flex justify-center pb-6">
                <a
                    href="{{ route('tours') }}"
                    class="group inline-flex items-center justify-center gap-3 border border-newman-navy/15 px-3 py-3 text-xs md:px-5 md:4  font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
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
                    Send your plan, date, group size, and preferred vehicle. Newman Tour Bali will help arrange the route and timing.
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
                            loading="lazy"
                            decoding="async"
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
                            For bigger groups or special requests, choose another car option and tell us your group size. Newman Tour Bali will help recommend the suitable vehicle.
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
    class="why-newman-section relative overflow-hidden"
>
    <div class="why-newman-container relative mx-auto w-[calc(100%-2rem)] max-w-7xl sm:w-[92%]">
        {{-- Section heading --}}
        <div
            data-aos="fade-up"
            class="why-newman-heading mx-auto max-w-3xl text-center"
        >
            <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-newman-gold">
                Why travel with Newman?
            </p>

            <h2 class="why-newman-title mt-5 font-semibold text-newman-navy">
                More than getting from one Bali stop to the next.
            </h2>

            <p class="why-newman-intro mx-auto mt-5 max-w-2xl text-gray-600">
                Newman helps arrange a private Bali day that feels clear,
                comfortable, and personal—without turning every hour into a
                strict schedule.
            </p>
        </div>

        {{-- Six reasons --}}
        <div data-aos="fade-up" data-aos-delay="100" class="why-reasons-grid">
            @foreach ($newmanReasons as $index => $reason)
                <article
                    data-aos="fade-up"
                    data-aos-delay="{{ ($index % 3) * 70 }}"
                    class="why-card group relative min-w-0"
                >
                    {{-- Icon --}}
                    <div class="why-card-icon flex shrink-0 items-center justify-center rounded-full text-newman-navy">
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

                    <div class="why-card-copy min-w-0">
                        <h3 class="why-card-title font-semibold text-newman-navy">
                            {{ $reason['title'] }}
                        </h3>

                        <p class="why-card-description text-gray-600">
                            {{ $reason['description'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Trust / service summary --}}
        <div
            data-aos="fade-up"
            class="why-newman-facts"
        >
            <div class="why-newman-facts-grid">
                @foreach ($newmanHighlights as $highlight)
                    <div class="why-newman-fact min-w-0 text-center">
                        <p class="why-newman-fact-value break-words font-semibold text-newman-navy">
                            {{ $highlight['value'] }}
                        </p>

                        <p class="why-newman-fact-label mx-auto mt-2 max-w-[170px] text-gray-600">
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
                        alt="Bali guest review highlight"
                        width="1280"
                        height="960"
                        loading="lazy"
                        decoding="async"
                        class="rounded-lg pb-4"
                    >

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white shadow-lg">
                        <img
                            src="{{ asset('logo/tripadvisor.png') }}"
                            alt="Tripadvisor"
                            width="512"
                            height="512"
                            loading="lazy"
                            decoding="async"
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
                       Newman Tour Bali
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
                    loading="lazy"
                    decoding="async"
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
    </div>
</section>

{{-- =========================================================
     BALI TRAVEL PHOTOGRAPHIC BREAK
     ========================================================= --}}
<section
    id="bali-travel-break"
    aria-labelledby="bali-travel-break-title"
    class="bg-white py-12 sm:py-16 lg:py-20"
>
    <div class="mx-auto w-[calc(100%-1.25rem)] max-w-7xl sm:w-[92%]">
        <figure
            data-aos="fade-up"
            class="relative isolate h-[340px] overflow-hidden bg-newman-navy shadow-[0_18px_50px_rgba(7,30,51,0.10)] min-[375px]:h-[360px] sm:h-[390px] md:h-[420px] lg:h-[460px]  "
        >
            <img
                src="{{ asset('images/wide-img.webp') }}"
                alt="Newman with guests during a Bali trip overlooking a mountain landscape"
                loading="lazy"
                decoding="async"
                class="absolute inset-0 h-full w-full object-cover object-[60%_48%] min-[375px]:object-[58%_48%] sm:object-[56%_48%] md:object-[52%_46%] lg:object-[50%_46%]"
            >

            <div
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-b from-newman-navy/70 via-newman-navy/15 to-newman-navy/45 md:bg-gradient-to-r md:from-newman-navy/72 md:via-newman-navy/20 md:to-transparent"
            ></div>

            <figcaption class="absolute inset-x-0 top-0 z-10 p-6 text-white sm:p-8 md:p-10 lg:p-12">
                <div class="max-w-[16rem] min-[375px]:max-w-[18rem] sm:max-w-sm md:max-w-md">
                    <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-newman-gold sm:text-[11px] sm:tracking-[0.32em]">
                        Along the way
                    </p>

                    <h2
                        id="bali-travel-break-title"
                        class="mt-3 text-[29px] font-semibold leading-[1.08] tracking-[-0.04em] text-white min-[375px]:text-[31px] sm:mt-4 sm:text-4xl md:text-[42px] lg:text-5xl"
                    >
                        Leave a little room for Bali.
                    </h2>

                    <p class="mt-4 max-w-md text-sm leading-6 text-white/82 sm:text-[15px] sm:leading-7 md:mt-5 md:text-base md:leading-7">
                        A clear plan, without turning every hour into a strict schedule.
                    </p>
                </div>
                <div>

                </div>
            </figcaption>

        </figure>
    </div>
</section>


@php
    $homepageFaqColumns = [
        [
            [
                'question' => 'Is Newman’s Bali tours private?',
                'answer' => 'Yes. Tour Packages and Custom Trips are arranged for your own couple, family, or group, so there is no mixed-group schedule. Timing and stops can be discussed around your day.',
            ],
            [
                'question' => 'Should I choose a Tour Package or a Custom Trip?',
                'answer' => 'Choose a Tour Package when a published route is already close to what you want. Choose a Custom Trip when you would rather build the destination list, preferred pace, pickup area, and vehicle from the beginning.',
            ],
            [
                'question' => 'Can the route and timing be adjusted?',
                'answer' => 'Yes. A published route is a useful starting point. Newman can review the stops, travel time, traffic, weather, and your group’s needs before the final confirmation so the day stays practical and does not feel rushed.',
            ],
            [
                'question' => 'Where can Newman pick us up?',
                'answer' => 'Private trips can start from common hotel areas around Bali. Share your hotel or pickup area first, then the exact pickup time and availability can be confirmed after Newman reviews the route.',
            ],
        ],
        [
            [
                'question' => 'Which vehicle should I choose?',
                'answer' => 'Avanza is a comfortable starting point for couples and small families, while Hiace gives groups more room. Another arrangement can be requested for bigger groups or special needs. Your selection is a preference; Newman confirms the final vehicle based on guests, luggage, child seats, accessibility, and availability.',
            ],
            [
                'question' => 'Is a booking request already confirmed?',
                'answer' => 'Not yet. Sending a request creates a pending request. Newman checks the route, availability, vehicle fit, and quotation before contacting you. No payment is collected on the website.',
            ],
            [
                'question' => 'What should I share before planning the trip?',
                'answer' => 'Send your preferred date, number of guests, hotel area, places you want to visit, and the pace you enjoy. It also helps to mention luggage, child seats, accessibility, or another special request early.',
            ],
            [
                'question' => 'How will Newman contact me after I send a request?',
                'answer' => 'Newman will follow up through the active WhatsApp number you provide; email is optional for a Custom Trip. This is where the timing, availability, vehicle, and final quotation are confirmed personally.',
            ],
        ],
    ];
@endphp

{{-- =========================================================
     FREQUENTLY ASKED QUESTIONS
     ========================================================= --}}
<section
    id="faq"
    aria-labelledby="faq-title"
    class="relative isolate overflow-hidden py-14 sm:py-16 md:py-20 lg:py-24"
>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-[0.035]">
        <div class="bali-pattern h-full w-full"></div>
    </div>

    <div class="relative mx-auto w-[calc(100%-1.5rem)] max-w-7xl sm:w-[92%]">
        <header data-aos="fade-up" class="mx-auto max-w-3xl text-center">
            <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-newman-gold sm:text-xs sm:tracking-[0.34em]">
                Frequently Asked Questions
            </p>

            <h2
                id="faq-title"
                class="mt-4 text-[30px] font-semibold leading-[1.12] tracking-[-0.04em] text-newman-navy sm:mt-5 sm:text-4xl md:text-[42px] lg:text-5xl"
            >
                Good to know before your Bali day.
            </h2>

            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-gray-600 sm:mt-5 sm:text-base sm:leading-8">
                Simple answers about private routes, pickup, transport, and what happens after you send a request.
            </p>

            <span aria-hidden="true" class="mx-auto mt-6 block h-px w-14 bg-newman-gold sm:mt-7 sm:w-16"></span>
        </header>

        <div class="mt-9 grid gap-x-12 md:mt-11 md:grid-cols-2 lg:mt-14 lg:gap-x-16">
            @foreach ($homepageFaqColumns as $column)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                    @foreach ($column as $faq)
                        @php
                            $faqId = 'homepage-faq-' . $loop->parent->index . '-' . $loop->index;
                        @endphp

                        <div class="relative border-b border-newman-navy/15">
                            <input
                                id="{{ $faqId }}"
                                type="checkbox"
                                aria-controls="{{ $faqId }}-answer"
                                class="peer sr-only"
                            >

                            <label
                                id="{{ $faqId }}-label"
                                for="{{ $faqId }}"
                                class="block cursor-pointer py-5 pr-10 text-left text-[15px] font-semibold leading-6 text-newman-navy transition-colors duration-300 hover:text-newman-blue peer-checked:text-newman-blue peer-focus-visible:text-newman-blue sm:py-6 sm:text-base"
                            >
                                {{ $faq['question'] }}
                            </label>

                            <span
                                aria-hidden="true"
                                class="pointer-events-none absolute right-0 top-[18px] flex h-6 w-6 items-center justify-center text-[25px] font-light leading-none text-newman-gold transition-transform duration-300 peer-checked:rotate-45 sm:top-[22px]"
                            >
                                +
                            </span>

                            <div
                                id="{{ $faqId }}-answer"
                                role="region"
                                aria-labelledby="{{ $faqId }}-label"
                                class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-500 ease-out peer-checked:grid-rows-[1fr] peer-checked:opacity-100"
                            >
                                <div class="min-h-0 overflow-hidden">
                                    <p class="max-w-xl pb-5 pr-9 text-sm leading-7 text-gray-600 sm:pb-6 sm:text-[15px] sm:leading-7">
                                        {{ $faq['answer'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
