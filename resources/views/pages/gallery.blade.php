@extends('layouts.app')

@section('content')

<section class="relative overflow-hidden bg-newman-navy pt-32 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-gallery.webp') }}"
            alt="Guest moments with Newman Tour Bali"
            width="7090"
            height="4732"
            loading="eager"
            fetchpriority="high"
            decoding="async"
            class="h-full w-full object-cover opacity-55"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/75 to-newman-navy/35"></div>
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
                Newman Guest Gallery
            </p>

            <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                Real travel moments with guests in Bali.
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-white/70 sm:text-lg">
                A collection of simple, warm, and personal memories from private Bali trips with Newman Tour Bali.
            </p>
        </div>

        <div data-aos="fade-up" data-aos-delay="140" class="mt-10 grid gap-3 sm:grid-cols-3 lg:max-w-3xl">
            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Private</p>
                <p class="mt-1 text-sm text-white/60">Guest trip moments</p>
            </div>

            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Bali</p>
                <p class="mt-1 text-sm text-white/60">Routes and memories</p>
            </div>

            <div class="border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                <p class="text-2xl font-semibold text-newman-gold">Local</p>
                <p class="mt-1 text-sm text-white/60">Guided with care</p>
            </div>
        </div>
    </div>
</section>

<section
    x-data="{ activeCategory: 'all' }"
    class="relative overflow-hidden bg-newman-sand/45 py-16 sm:py-20 lg:py-24"
>
    <div class="absolute inset-0 opacity-[0.05]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div class="mb-10 grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div data-aos="fade-up">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                    Photo Stories
                </p>

                <h2 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                    Not just places, but people and moments.
                </h2>
            </div>

            <div data-aos="fade-up" data-aos-delay="120" class="max-w-xl lg:ml-auto">
                <p class="text-base leading-8 text-gray-600">
                    These photos are meant to show the real side of Newman Tour Bali: guests, routes, transport, photo stops, and comfortable travel days around Bali.
                </p>
            </div>
        </div>

       <div
    data-aos="fade-up"
    data-aos-delay="160"
    class="-mx-4 overflow-x-auto px-4 pb-3 sm:mx-0 sm:px-0"
>
    <div class="flex min-w-max gap-2 sm:min-w-0 sm:flex-wrap sm:gap-3">
        <button
            type="button"
            @click="activeCategory = 'all'"
            :class="
                activeCategory === 'all'
                    ? 'border-newman-navy bg-newman-navy text-white'
                    : 'border-newman-navy/10 bg-white text-newman-navy hover:border-newman-gold hover:bg-newman-sand'
            "
            class="shrink-0 border px-5 py-3 text-[11px] font-bold uppercase tracking-[0.14em] transition duration-300"
        >
            All Moments
        </button>

        @foreach ($categories as $category)
            <button
                type="button"
                @click="activeCategory = @js($category)"
                :class="
                    activeCategory === @js($category)
                        ? 'border-newman-navy bg-newman-navy text-white'
                        : 'border-newman-navy/10 bg-white text-newman-navy hover:border-newman-gold hover:bg-newman-sand'
                "
                class="shrink-0 border px-5 py-3 text-[11px] font-bold uppercase tracking-[0.14em] transition duration-300"
            >
                {{ ucwords(
                    str_replace(
                        ['-', '_'],
                        ' ',
                        $category
                    )
                ) }}
            </button>
        @endforeach
    </div>
</div>

      <div data-aos="fade-up" data-aos-delay="180" class="mt-10 grid min-w-0 gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($guestMoments as $moment)
        <article
            x-show="
                activeCategory === 'all'
                || activeCategory ===
                    @js($moment['category'])
            "
            x-transition:enter="
                transition
                duration-300
                ease-out
            "
            x-transition:enter-start="
                translate-y-3
                opacity-0
            "
            x-transition:enter-end="
                translate-y-0
                opacity-100
            "
            class="group min-w-0"
        >
            <figure
                class="relative aspect-[16/10] min-w-0 overflow-hidden bg-newman-sand shadow-sm transition duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-newman-navy/15"
            >
                <img
                    src="{{ asset($moment['image']) }}"
                    alt="{{ $moment['alt'] ?? $moment['title'] }}"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.035]"
                >

                {{-- Natural dark overlay --}}
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-t from-newman-navy/95 via-newman-navy/15 to-transparent"
                ></div>

                {{-- Category badge --}}
                <span
                    class="absolute left-4 top-4 bg-white/95 px-3 py-2 text-[9px] font-bold uppercase tracking-[0.14em] text-newman-navy shadow-lg backdrop-blur-sm sm:left-5 sm:top-5"
                >
                    {{ ucwords(
                        str_replace(
                            ['-', '_'],
                            ' ',
                            $moment['category']
                        )
                    ) }}
                </span>

                {{-- Content overlay --}}
                <figcaption
                    class="absolute inset-x-0 bottom-0 min-w-0 p-5 text-white sm:p-6"
                >
                    @if (! empty($moment['tag']))
                        <p
                            class="break-words text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold"
                        >
                            {{ $moment['tag'] }}
                        </p>
                    @endif

                    <h3
                        class="mt-2 break-words text-xl font-semibold leading-tight tracking-[-0.025em] sm:text-2xl"
                    >
                        {{ $moment['title'] }}
                    </h3>

                    @if (! empty($moment['caption']))
                        <p
                            class="mt-2 hidden break-words text-sm leading-6 text-white/65 sm:block"
                        >
                            {{ \Illuminate\Support\Str::limit(
                                $moment['caption'],
                                95
                            ) }}
                        </p>
                    @endif
                </figcaption>

                {{-- Small visual affordance --}}
                <span
                    aria-hidden="true"
                    class="absolute bottom-5 right-5 hidden h-10 w-10 translate-y-2 items-center justify-center rounded-full bg-white text-newman-navy opacity-0 shadow-lg transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 lg:flex"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-4 w-4"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 12h14M14 7l5 5-5 5"
                        />
                    </svg>
                </span>
            </figure>
        </article>
    @empty
        <div class="sm:col-span-2 xl:col-span-3">
            <div
                class="flex min-h-72 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center"
            >
                <div>
                    <p class="font-semibold text-newman-navy">
                        Guest moments will be added soon.
                    </p>

                    <p class="mt-2 text-sm leading-7 text-gray-500">
                        Active Gallery moments from the dashboard will
                        appear here.
                    </p>
                </div>
            </div>
        </div>
    @endforelse
</div>

        
    </div>
</section>
@endsection
