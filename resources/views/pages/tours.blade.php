@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-newman-navy pt-32 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-bg1.webp') }}"
            alt="Bali private tour packages"
            width="1376"
            height="544"
            loading="eager"
            fetchpriority="high"
            decoding="async"
            class="h-full w-full object-cover opacity-55"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/70 to-newman-navy/35"></div>
    </div>

    <div class="absolute inset-0 opacity-20">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl pb-20 sm:pb-24 lg:pb-28">
        <div class="max-w-4xl" data-aos="fade-up">
            <p class="text-xs font-bold uppercase tracking-[0.38em] text-newman-gold">
                Bali Tour Packages
            </p>

            <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                Private Bali routes for calm, flexible, and personal travel days.
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-white/70 sm:text-lg">
                Choose a ready route or build your own Bali day with Newman. Every tour can be adjusted with private transport, flexible timing, and local guidance.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="140" class="mt-10 grid gap-3 sm:grid-cols-3 lg:max-w-3xl">
            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Private</p>
                <p class="mt-1 text-sm text-white/60">No mixed group tour</p>
            </div>

            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Flexible</p>
                <p class="mt-1 text-sm text-white/60">Route can be adjusted</p>
            </div>

            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Local</p>
                <p class="mt-1 text-sm text-white/60">Guided with Bali care</p>
            </div>
        </div>
    </div>
</section>

<section
    id="tours"
    x-data="{ activeCategory: 'all' }"
    class="tour-experiences relative overflow-hidden bg-white py-16 sm:py-20 lg:py-24"
>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="mx-auto w-[92%] max-w-7xl">
        <div class="mb-9 grid gap-6 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div data-aos="fade-up">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Explore Routes
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                    Find the Bali trip that fits your day.
                </h2>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="lg:ml-auto lg:max-w-2xl">
                <p class="text-base leading-8 text-gray-600">
                    Start with a popular route, then adjust the timing, destination stops, and vehicle option based on your group.
                </p>
            </div>
        </div>


        <div
    data-aos="fade-up"
    data-aos-delay="180"
    class="-mx-4 overflow-x-auto px-4 pb-4 sm:mx-0 sm:px-0"
>
    <div class="flex min-w-max gap-3">
        <button
            type="button"
            @click="activeCategory = 'all'"
            :class="
                activeCategory === 'all'
                    ? 'bg-newman-navy text-white'
                    : 'bg-newman-sand text-newman-navy hover:bg-newman-gold'
            "
            class="px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] transition duration-300"
        >
            All Tours
        </button>

        @foreach ($categories as $category)
            <button
                type="button"
                @click="activeCategory = '{{ $category['key'] }}'"
                :class="
                    activeCategory === '{{ $category['key'] }}'
                        ? 'bg-newman-navy text-white'
                        : 'bg-newman-sand text-newman-navy hover:bg-newman-gold'
                "
                class="px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] transition duration-300"
            >
                {{ $category['label'] }}
            </button>
        @endforeach
    </div>
</div>

        <div class="mt-12 pb-8">
            <div class="tour-experiences-grid-shell mx-auto w-[92%] max-w-7xl">
                <div class="tour-experiences-grid grid items-stretch gap-7 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($tours as $tour)
                        <x-tour-card
                            :tour="$tour"
                            :filterable="true"
                        />
                    @empty
                        <div class="md:col-span-2 xl:col-span-3">
                            <div class="border border-newman-navy/10 bg-newman-sand px-6 py-12 text-center">
                                <h3 class="text-xl font-semibold text-newman-navy">
                                    No tours are available yet
                                </h3>

                                <p class="mt-3 text-sm leading-7 text-gray-600">
                                    Contact Newman to arrange a private Bali route.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</section>

<section class="relative overflow-hidden bg-newman-sand py-16 sm:py-20 lg:py-24">
    <div class="absolute inset-0 opacity-[0.06]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto grid w-[92%] max-w-7xl gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                Not sure which route to choose?
            </p>

            <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                Tell Newman your plan and build a custom Bali day.
            </h2>

            <p class="mt-5 max-w-2xl text-base leading-8 text-gray-600">
                Share your destination list, hotel area, trip date, number of guests, and vehicle preference. Newman can help arrange a route that feels natural and not rushed.
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="border border-gray-100 p-4">
                    <p class="text-2xl font-semibold text-newman-gold">01</p>
                    <p class="mt-3 text-sm font-semibold text-newman-navy">Send your route</p>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Places you want to visit.</p>
                </div>

                <div class="border border-gray-100 p-4">
                    <p class="text-2xl font-semibold text-newman-gold">02</p>
                    <p class="mt-3 text-sm font-semibold text-newman-navy">Choose vehicle</p>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Avanza, Hiace, or another car.</p>
                </div>

                <div class="border border-gray-100 p-4">
                    <p class="text-2xl font-semibold text-newman-gold">03</p>
                    <p class="mt-3 text-sm font-semibold text-newman-navy">Confirm by WhatsApp</p>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Finalize timing and details.</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
             <a
    href="{{ route('custom-trip.create') }}"
    class="bg-newman-gold px-6 py-4 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
>
    Build Custom Trip
</a>

               
            </div>
        </div>
    </div>
</section>
@endsection
