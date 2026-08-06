@props([
    'tour',
    'filterable' => false,
])

@php
    $card = \App\Support\TourViewData::make($tour);

    $detailUrl = $card['slug'] !== ''
        ? route('tours.detail', $card['slug'])
        : '#';

    $placeholderUrl = asset('images/tour-placeholder.jpg');

    $thirdBenefit = $card['cancellation_text'] !== ''
        ? $card['cancellation_text']
        : $card['confirmation_text'];

    /*
    |--------------------------------------------------------------------------
    | Active card promotion
    |--------------------------------------------------------------------------
    */

    $promotion = \App\Support\TourCardPromotion::make(
        $tour
    );

    $promotionalChildPrice = $promotion['child_price_text']
        ?: $card['child_price_text'];
@endphp

<article
    @if ($filterable)
        x-show="activeCategory === 'all' || activeCategory === '{{ $card['category_key'] }}'"
        x-transition.opacity.duration.250ms
    @endif
    class="tour-card group flex h-full min-h-[700px] flex-col overflow-hidden rounded-[20px] border border-newman-navy/10 bg-white shadow-[0_14px_40px_rgba(8,36,58,0.08)] transition duration-300 hover:-translate-y-1 hover:border-newman-gold/50 hover:shadow-[0_20px_50px_rgba(8,36,58,0.12)]"
>
    {{-- Tour Image --}}
    <a
        href="{{ $detailUrl }}"
        class="relative block aspect-[4/3] shrink-0 overflow-hidden bg-newman-sand"
        aria-label="View {{ $card['title'] }}"
    >
        <img
            src="{{ $card['image_url'] }}"
            alt="{{ $card['title'] }}"
            loading="lazy"
            decoding="async"
            onerror="this.onerror=null;this.src='{{ $placeholderUrl }}';"
            class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.035]"
        >

        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy/60 via-transparent to-newman-navy/5"></div>

        {{-- Existing tour badge --}}
        <span
            class="absolute left-4 top-4 rounded-lg bg-white/95 px-4 py-2 text-[11px] font-semibold tracking-[0.08em] text-newman-navy shadow-sm backdrop-blur {{ $promotion['active'] ? 'max-w-[54%]' : 'max-w-[75%]' }}"
        >
            {{ $card['badge'] }}
        </span>

        {{-- Active discount badge --}}
        @if ($promotion['active'])
            <span
                class="absolute right-4 top-4 z-10 inline-flex max-w-[40%] items-center justify-center rounded-lg bg-newman-gold px-3 py-2 text-center text-[10px] font-bold uppercase leading-4 tracking-[0.12em] text-newman-navy shadow-lg shadow-newman-navy/20"
            >
                {{ $promotion['label'] }}
            </span>
        @endif

        <span class="absolute bottom-4 left-4 flex items-center gap-2 text-sm font-semibold text-white">
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"
                />

                <circle cx="12" cy="10" r="2.2"/>
            </svg>

            {{ $card['area'] }}
        </span>
    </a>

    {{-- Tour Content --}}
    <div class="flex flex-1 flex-col p-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-newman-gold">
            {{ $card['trip_type'] }}
        </p>

        <h3 class="mt-3 min-h-[66px] text-[22px] font-semibold leading-[1.42] tracking-[-0.025em] text-newman-navy">
            <a
                href="{{ $detailUrl }}"
                class="transition hover:text-newman-blue"
            >
                {{ $card['title'] }}
            </a>
        </h3>

        {{-- Rating --}}
        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-semibold text-newman-navy">
                <span class="text-newman-gold">★</span>
                {{ number_format($card['rating'], 1) }}
            </span>

            <span class="h-1 w-1 rounded-full bg-gray-300"></span>

            <span class="text-gray-500">
                {{ $card['review_text'] }}
            </span>
        </div>

        {{-- Tour Information --}}
        <div class="mt-5 min-h-[155px] space-y-4 border-t border-newman-navy/10 pt-5">
            <div class="flex items-start gap-3 text-sm">
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-newman-navy"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="8.5"/>

                    <path
                        stroke-linecap="round"
                        d="M12 7.5V12l3.2 2"
                    />
                </svg>

                <p class="font-semibold text-newman-navy">
                    {{ $card['duration'] }}
                </p>
            </div>

            <div class="flex items-start gap-3 text-sm">
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-newman-navy"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path
                        stroke-linejoin="round"
                        d="M5 16V9.8c0-.8.4-1.5 1.1-1.9L8 6.8h8l1.9 1.1c.7.4 1.1 1.1 1.1 1.9V16"
                    />

                    <path
                        stroke-linecap="round"
                        d="M4 16h16M7 16v2M17 16v2"
                    />

                    <circle cx="8" cy="13" r="1"/>
                    <circle cx="16" cy="13" r="1"/>
                </svg>

                <div>
                    <p class="font-semibold text-newman-navy">
                        {{ $card['pickup_text'] }}
                    </p>

                    <p class="mt-1 text-gray-500">
                        {{ $card['vehicle'] }}
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3 text-sm">
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-newman-gold"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="8.5"/>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m8.5 12 2.3 2.3 4.8-5"
                    />
                </svg>

                <p class="font-medium text-newman-navy">
                    {{ $thirdBenefit }}
                </p>
            </div>
        </div>

        {{-- Price --}}
        <div class="mt-auto border-t border-newman-navy/10 pt-6">
            @if ($promotion['active'])
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-newman-gold">
                        Limited offer
                    </p>

                    @if ($promotion['saving_adult_text'])
                        <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-newman-blue">
                            Save {{ $promotion['saving_adult_text'] }}
                        </p>
                    @endif
                </div>

                <p class="mt-2 text-sm leading-none text-gray-400 line-through">
                    {{ $promotion['base_adult_price_text'] }}
                </p>

                <div class="mt-2 flex flex-wrap items-end gap-x-2 gap-y-1">
                    <p class="text-[24px] font-bold leading-none tracking-[-0.03em] text-newman-navy">
                        {{ $promotion['adult_price_text'] }}
                    </p>

                    <p class="pb-0.5 text-xs text-gray-500">
                        per person
                    </p>
                </div>

                @if ($promotionalChildPrice)
                    <p class="mt-2 text-xs text-gray-500">
                        Children from {{ $promotionalChildPrice }}
                    </p>
                @endif

                <p class="mt-2 text-xs leading-5 text-gray-400">
                    Price confirmed after selecting date and participants.
                </p>
            @else
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">
                    {{ $card['price_label'] }}
                </p>

                <div class="mt-2 flex flex-wrap items-end gap-x-2 gap-y-1">
                    <p class="text-[24px] font-bold leading-none tracking-[-0.03em] text-newman-navy">
                        {{ $card['price_text'] }}
                    </p>

                    @if ($card['price_suffix'] !== '')
                        <p class="pb-0.5 text-xs text-gray-500">
                            {{ $card['price_suffix'] }}
                        </p>
                    @endif
                </div>

                @if ($card['child_price_text'])
                    <p class="mt-2 text-xs text-gray-500">
                        Children from {{ $card['child_price_text'] }}
                    </p>
                @endif

                @if ($card['price_note'] !== '')
                    <p class="mt-2 text-xs leading-5 text-gray-400">
                        {{ $card['price_note'] }}
                    </p>
                @endif
            @endif

            <a
                href="{{ $detailUrl }}"
                class="mt-5 flex min-h-12 w-full items-center justify-center rounded-lg bg-newman-navy px-5 py-3 text-center text-sm font-semibold text-white transition duration-200 hover:bg-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
            >
                Check availability
            </a>
        </div>
    </div>
</article>