@extends('layouts.app')

@section('content')
@php
    $values = [
        [
            'number' => '01',
            'title' => 'Calm and patient guidance',
            'description' => 'A good Bali trip does not need to feel rushed. Newman helps guests enjoy the route with enough time for photos, rest, and small local stops.',
        ],
        [
            'number' => '02',
            'title' => 'Local route understanding',
            'description' => 'Every area in Bali has its own rhythm. The route can be arranged based on time, weather, traffic, and what guests actually want to enjoy.',
        ],
        [
            'number' => '03',
            'title' => 'Personal trip experience',
            'description' => 'Newman Tour Guide is not made to feel like a big agency. It is a more personal way to explore Bali with simple communication and local care.',
        ],
    ];

    $storyNotes = [
        [
            'title' => 'Before the trip',
            'text' => 'Guests can share their plan, hotel area, date, and places they want to visit.',
        ],
        [
            'title' => 'During the day',
            'text' => 'The route can stay flexible, with time for photo stops, local food, or rest when needed.',
        ],
        [
            'title' => 'After the trip',
            'text' => 'Guests leave with real travel memories, not just a checklist of places.',
        ],
    ];
@endphp

<section class="relative overflow-hidden bg-newman-navy pt-32 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/owner.jpg') }}"
            alt="Newman Tour Guide in Bali"
            class="h-full w-full object-cover opacity-55"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/76 to-newman-navy/30"></div>
    </div>

    <div class="absolute inset-0 opacity-20">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl pb-20 sm:pb-24 lg:pb-28">
        <div data-aos="fade-up" class="max-w-4xl">
            <a
                href="{{ route('home') }}"
                class="inline-flex border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/80 backdrop-blur-md transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
            >
                ← Back to home
            </a>

            <p class="mt-8 text-xs font-bold uppercase tracking-[0.38em] text-newman-gold">
                About Newman
            </p>

            <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                A local Bali guide who helps the trip feel personal.
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-white/70 sm:text-lg">
                Newman Tour Guide is built around simple, warm, and flexible Bali travel. The focus is not only visiting places, but making the day feel comfortable, natural, and easy to enjoy.
            </p>
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="mx-auto grid w-[92%] max-w-7xl gap-12 lg:grid-cols-[0.92fr_1.08fr] lg:items-center">
        <div data-aos="fade-up" class="order-2 lg:order-1">
            <div class="relative">
                <div class="about-photo-card bg-newman-sand shadow-2xl shadow-newman-navy/10">
                    <img
                        src="{{ asset('images/whyimg1.jpeg') }}"
                        alt="Newman local Bali guide"
                        class="about-photo h-[440px] w-full object-cover sm:h-[560px] lg:h-[650px]"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-newman-navy/55 via-transparent to-transparent"></div>

                    <div class="absolute bottom-5 left-5 right-5 bg-white/92 p-5 shadow-xl backdrop-blur-md sm:left-6 sm:right-auto sm:w-[340px]">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-blue">
                            Local Guide
                        </p>

                        <p class="mt-3 text-xl font-semibold leading-snug text-newman-navy">
                            Simple guidance, flexible timing, and a calm way to explore Bali.
                        </p>
                    </div>
                </div>

                <div class="absolute -right-4 -top-7 hidden w-[230px] overflow-hidden border-[10px] border-white bg-newman-sand shadow-2xl shadow-newman-navy/10 lg:block">
                    <img
                        src="{{ asset('images/whyimg1.jpeg') }}"
                        alt="Bali guest moment with Newman"
                        class="about-photo h-[260px] w-full object-cover"
                    >
                </div>

                <div class="absolute -left-5 top-14 hidden h-28 w-28 border border-newman-gold/45 lg:block"></div>
            </div>
        </div>

        <div data-aos="fade-up" data-aos-delay="120" class="order-1 lg:order-2">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                The Story
            </p>

            <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl lg:text-6xl">
                Made for travelers who want Bali to feel warm, not rushed.
            </h2>

            <div class="mt-7 space-y-5 text-base leading-8 text-gray-600 sm:text-lg">
                <p>
                    Newman Tour Guide started from a simple idea: helping guests experience Bali in a way that feels easy, friendly, and personal.
                </p>

                <p>
                    Some travelers want temples and culture. Some want beaches and sunset. Some only want a quiet route, good views, and enough time to enjoy the day. Newman helps shape the trip around that kind of feeling.
                </p>

                <p>
                    This website is made to share that experience more clearly, so guests can see the routes, moments, reviews, and send a request before starting their Bali day.
                </p>
            </div>

            <div class="mt-9 border-l border-newman-gold/55 pl-6">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-newman-blue">
                    Travel style
                </p>

                <p class="mt-3 max-w-2xl text-lg leading-8 text-newman-navy">
                    “A trip should feel comfortable enough for guests to enjoy the place, the road, and the small moments in between.”
                </p>
            </div>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('custom-trip.create') }}"
                    class="bg-newman-navy px-7 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl"
                >
                    Plan Your Trip
                </a>

                <a
                    href="{{ route('gallery') }}"
                    class="border border-newman-navy/15 px-7 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                >
                    See Guest Moments
                </a>
            </div>
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-newman-sand/45 py-20 sm:py-24 lg:py-28">
    <div class="absolute inset-0 opacity-[0.05]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="mb-10 grid gap-6 lg:mb-14 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div data-aos="fade-up">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                    Why It Feels Different
                </p>

                <h2 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                    More personal than a fixed tour schedule.
                </h2>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="max-w-xl lg:ml-auto">
                <p class="text-base leading-8 text-gray-600">
                    The experience is built around real communication, local understanding, and the kind of travel day that can breathe.
                </p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($values as $value)
                <article
                    data-aos="fade-up"
                    data-aos-delay="{{ $loop->index * 90 }}"
                    class="about-story-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5 sm:p-7"
                >
                    <p class="text-4xl font-semibold tracking-[-0.05em] text-newman-gold">
                        {{ $value['number'] }}
                    </p>

                    <div class="about-line mt-5 h-12 w-px"></div>

                    <h3 class="mt-5 text-2xl font-semibold leading-tight tracking-[-0.03em] text-newman-navy">
                        {{ $value['title'] }}
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-gray-600">
                        {{ $value['description'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
    <div class="mx-auto grid w-[92%] max-w-7xl gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
        <div data-aos="fade-up">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                A Day With Newman
            </p>

            <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                From a simple message to a real Bali memory.
            </h2>

            <p class="mt-6 max-w-xl text-base leading-8 text-gray-600">
                The process is made simple so guests can focus on the trip, not the complicated planning.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="120" class="space-y-5">
            @foreach ($storyNotes as $note)
                <article class="grid gap-4 border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:grid-cols-[150px_1fr] sm:p-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-newman-blue">
                            {{ $note['title'] }}
                        </p>
                    </div>

                    <p class="text-sm leading-7 text-gray-600">
                        {{ $note['text'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-newman-navy py-20 text-white sm:py-24 lg:py-28">
    <div class="absolute inset-0 opacity-[0.08]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto grid w-[92%] max-w-7xl gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
        <div data-aos="fade-up">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Start Your Bali Plan
            </p>

            <h2 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight tracking-[-0.04em] sm:text-5xl">
                Tell Newman what kind of Bali day you want.
            </h2>

            <p class="mt-5 max-w-2xl text-base leading-8 text-white/62">
                Share your date, group size, hotel area, and places you want to visit. The route can be shaped from there.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="120" class="flex flex-col gap-3 sm:flex-row lg:flex-col">
            <a
                href="{{ route('custom-trip.create') }}"
                class="bg-newman-gold px-7 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-xl"
            >
                Send Booking Request
            </a>

            <a
                href="{{ route('tours') }}"
                class="border border-white/18 px-7 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
            >
                View Tours
            </a>
        </div>
    </div>
</section>
@endsection