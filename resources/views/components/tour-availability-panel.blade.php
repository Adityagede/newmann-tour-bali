@props([
    'tour',
    'preview' => false,
])

@php

    
    /*
    |--------------------------------------------------------------------------
    | Public promotional price
    |--------------------------------------------------------------------------
    */

    $promotion = data_get(
        $tour,
        'promotion',
        []
    );

    $promotionActive = (bool) data_get(
        $promotion,
        'active',
        false
    );

    $promotionLabel = trim(
        (string) data_get(
            $promotion,
            'label',
            ''
        )
    );

    $promotionName = trim(
        (string) data_get(
            $promotion,
            'name',
            'Limited offer'
        )
    );

    $baseAdultPriceText = data_get(
        $promotion,
        'base_adult_price_text'
    );

    $promotionalAdultPriceText = data_get(
        $promotion,
        'adult_price_text'
    );

    $savingAdultText = data_get(
        $promotion,
        'saving_adult_text'
    );

    $promotionalChildPriceText = data_get(
        $promotion,
        'child_price_text'
    );

    $displayChildPriceText = $promotionActive
        ? (
            $promotionalChildPriceText
            ?: ($tour['child_price_text'] ?? null)
        )
        : ($tour['child_price_text'] ?? null);


    /*
    |--------------------------------------------------------------------------
    | Tour Package V2 booking defaults
    |--------------------------------------------------------------------------
    */

    $maxGuests = $tour['booking_max_guests']
        ?? $tour['max_guests']
        ?? null;

    $minimumGuests = max(
        1,
        (int) (
            $tour['booking_min_guests']
            ?? $tour['min_guests']
            ?? 1
        )
    );

    $defaultLanguage = $tour['available_languages'][0]
        ?? null;



    $languages = $tour['available_languages']
        ?? [];

    if (!is_array($languages)) {
        $languages = [];
    }

    $languages = collect($languages)
        ->filter(
            fn ($language) =>
                is_string($language)
                && trim($language) !== ''
        )
        ->map(
            fn ($language) => trim($language)
        )
        ->unique(
            fn ($language) => strtolower($language)
        )
        ->values()
        ->all();

    $defaultLanguage = $languages[0]
        ?? '';

    $availabilityEndpoint = $preview
        ? route(
            'admin.tour-packages.preview.availability',
            [
                'tourPackage' => $tour['id'],
            ]
        )
        : route(
            'tours.availability',
            [
                'slug' => $tour['slug'],
            ]
        );
@endphp


<aside 
    id="tour-booking-panel-{{ $tour['id'] }}"
    data-tour-booking-panel
    x-data="{
     endpoint: @js(route('tours.availability', $tour['slug'])),
       
     csrfToken: @js(csrf_token()),

    adults: {{ $minimumGuests }},
    children: 0,
    infants: 0,

    date: '',
    language: @js($defaultLanguage),
    languages: @js($languages),

    participantsOpen: false,
    loading: false,
    error: '',
    result: null,

    minGuests: {{ $minimumGuests }},

    maxGuests: {{ $maxGuests
        ? (int) $maxGuests
        : 'null' }},

    get totalGuests() {
        return this.adults
            + this.children
            + this.infants;
    },

    get participantLabel() {
        const adultLabel =
            `${this.adults} ${this.adults === 1
                ? 'adult'
                : 'adults'}`;

        const childLabel =
            `${this.children} ${this.children === 1
                ? 'child'
                : 'children'}`;

        const infantLabel =
            `${this.infants} ${this.infants === 1
                ? 'infant'
                : 'infants'}`;

        return `${adultLabel} · ${childLabel} · ${infantLabel}`;
    },

    get availableOptions() {
        const options =
            this.result?.availability?.options;

        return Array.isArray(options)
            ? options
            : [];
    },

    get firstAvailableOption() {
        return this.availableOptions[0]
            ?? null;
    },

    get selectedPricing() {
        return this.firstAvailableOption?.pricing
            ?? this.result?.availability?.pricing
            ?? this.result?.pricing
            ?? null;
    },

    
    get transportRecommendation() {
    const raw =
        this.result
            ?.availability
            ?.recommended_transport
        ?? this.result
            ?.recommended_transport
        ?? null;

    if (!raw) {
        return null;
    }

    /*
     * Beberapa konfigurasi service mungkin hanya
     * mengembalikan nama kendaraan sebagai string.
     */
    if (typeof raw === 'string') {
        return {
            label: raw,
            type: null,
            capacity: null,
            quantity: 1,
            reason: null,
            totalPassengers:
                this.result
                    ?.selection
                    ?.total_participants
                ?? this.totalGuests,
        };
    }

    if (
        typeof raw !== 'object'
        || Array.isArray(raw)
    ) {
        return null;
    }

    return {
        label:
            raw.label
            ?? raw.vehicle_label
            ?? raw.vehicle_name
            ?? raw.name
            ?? raw.title
            ?? 'Recommended transport',

        type:
            raw.vehicle_type
            ?? raw.type
            ?? raw.key
            ?? raw.vehicle_key
            ?? null,

        capacity:
            raw.capacity
            ?? raw.passenger_capacity
            ?? raw.max_passengers
            ?? raw.capacity_per_vehicle
            ?? null,

        quantity:
            raw.quantity
            ?? raw.vehicles_required
            ?? raw.vehicle_count
            ?? raw.units
            ?? 1,

        reason:
            raw.reason
            ?? raw.explanation
            ?? raw.message
            ?? raw.description
            ?? null,

        totalPassengers:
            raw.total_passengers
            ?? raw.total_participants
            ?? this.result
                ?.selection
                ?.total_participants
            ?? this.totalGuests,
    };
},

get hasTransportRecommendation() {
    return Boolean(
        this.transportRecommendation
    );
},

get transportCapacityLabel() {
    const transport =
        this.transportRecommendation;

    if (
        !transport
        || transport.capacity === null
        || transport.capacity === undefined
    ) {
        return 'Confirmed after request';
    }

    const capacity =
        Number(transport.capacity);

    if (!Number.isFinite(capacity)) {
        return transport.capacity;
    }

    return `${capacity} ${
        capacity === 1
            ? 'passenger'
            : 'passengers'
    } per vehicle`;
},

get transportQuantityLabel() {
    const transport =
        this.transportRecommendation;

    if (!transport) {
        return '';
    }

    const quantity =
        Number(transport.quantity ?? 1);

    if (!Number.isFinite(quantity)) {
        return transport.quantity;
    }

    return `${quantity} ${
        quantity === 1
            ? 'vehicle'
            : 'vehicles'
    }`;
},


  resetResult() {
    this.result = null;
    this.error = '';

    window.dispatchEvent(
        new CustomEvent(
            'tour-availability-cleared'
        )
    );
},

    increase(type) {
        if (
            ![
                'adults',
                'children',
                'infants',
            ].includes(type)
        ) {
            return;
        }

        if (
            this.maxGuests
            && this.totalGuests >= this.maxGuests
        ) {
            return;
        }

        this[type]++;

        this.resetResult();
    },

    decrease(type) {
        if (
            ![
                'adults',
                'children',
                'infants',
            ].includes(type)
        ) {
            return;
        }

        const minimum =
            type === 'adults'
                ? 1
                : 0;

        if (this[type] <= minimum) {
            return;
        }

        this[type]--;

        this.resetResult();
    },

    async checkAvailability() {
        this.error = '';
        this.result = null;

        if (!this.date) {
            this.error =
                'Please select a travel date.';

            return;
        }

        if (
            this.totalGuests < this.minGuests
        ) {
            this.error =
                `This option requires at least ${this.minGuests} participants.`;

            return;
        }

        if (
            this.maxGuests
            && this.totalGuests > this.maxGuests
        ) {
            this.error =
                `This option allows a maximum of ${this.maxGuests} participants.`;

            return;
        }

        if (
            this.languages.length > 0
            && !this.language
        ) {
            this.error =
                'Please select a language.';

            return;
        }

        this.loading = true;

        try {
            const response = await fetch(
                this.endpoint,
                {
                    method: 'POST',

                    headers: {
                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            this.csrfToken,
                    },

                    body: JSON.stringify({
                        travel_date:
                            this.date,

                        language:
                            this.language || null,

                        participants: {
                            adult:
                                this.adults,

                            child:
                                this.children,

                            infant:
                                this.infants,
                        },
                    }),
                }
            );

            const responseText =
                await response.text();

            let data = {};

            try {
                data = responseText
                    ? JSON.parse(responseText)
                    : {};
            } catch (parseError) {
                throw new Error(
                    `The server returned an invalid response (${response.status}).`
                );
            }

            if (!response.ok) {
                const validationMessage =
                    data.errors
                        ? Object.values(
                            data.errors
                        ).flat()[0]
                        : null;

                throw new Error(
                    validationMessage
                    || data.message
                    || 'Availability could not be checked.'
                );
            }

            const options = Array.isArray(data?.options)
                ? data.options
                : [];

            const firstOption = options[0] ?? null;

            data.pricing = data.pricing
                ?? firstOption?.pricing
                ?? null;

            this.result = data;

        window.dispatchEvent(
    new CustomEvent(
        'tour-availability-updated',
        {
            detail: data,
        }
    )
);

        } catch (error) {
            this.error =
                error?.message
                || 'Availability could not be checked.';
        } finally {
            this.loading = false;
        }
    },

   
}"
class="scroll-mt-24 md:scroll-mt-28 lg:sticky lg:top-28"
>



    <div class="overflow-hidden rounded-[22px] border border-newman-navy/10 bg-white shadow-[0_24px_70px_rgba(8,36,58,0.12)]">
        @if ($preview)
    <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-xs font-semibold leading-5 text-amber-800 sm:px-6">
        Admin preview. This Tour Product is still hidden
        from public tour listings.
    </div>
@endif
        <div class="border-b border-newman-navy/10 px-6 py-6 sm:px-7">
    <div class="flex min-w-0 items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                Plan this tour
            </p>

            @if ($promotionActive)
                <p class="mt-4 text-[10px] font-bold uppercase tracking-[0.15em] text-newman-blue">
                    {{ $promotionName }}
                </p>

                @if ($baseAdultPriceText)
                    <p class="mt-2 text-sm leading-none text-gray-400 line-through">
                        {{ $baseAdultPriceText }}
                    </p>
                @endif

                <div class="mt-2 flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-1">
                    <p class="break-words text-2xl font-bold leading-none tracking-[-0.03em] text-newman-navy">
                        {{ $promotionalAdultPriceText }}
                    </p>

                    <span class="text-xs text-gray-500">
                        per Person
                    </span>
                </div>

                @if ($displayChildPriceText)
                    <p class="mt-3 text-sm text-gray-500">
                        Children from {{ $displayChildPriceText }}
                    </p>
                @endif

                @if ($savingAdultText)
                    <p class="mt-2 text-xs font-semibold text-newman-gold">
                        Save {{ $savingAdultText }} per person
                    </p>
                @endif

                <p class="mt-2 text-[11px] leading-5 text-gray-400">
                    Final price is recalculated after selecting the
                    travel date and participants.
                </p>
            @else
                <div class="mt-4 flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-1">
                    <p class="break-words text-2xl font-bold leading-none tracking-[-0.03em] text-newman-navy">
                        {{ $tour['price_text'] }}
                    </p>

                    @if (($tour['price_suffix'] ?? '') !== '')
                        <span class="text-xs text-gray-500">
                            {{ $tour['price_suffix'] }}
                        </span>
                    @endif
                </div>

                @if ($displayChildPriceText)
                    <p class="mt-3 text-sm text-gray-500">
                        Children from {{ $displayChildPriceText }}
                    </p>
                @endif
            @endif
        </div>

        @if ($promotionActive && $promotionLabel !== '')
            <span class="shrink-0 rounded-lg bg-newman-gold px-3 py-2 text-[10px] font-bold uppercase leading-4 tracking-[0.12em] text-newman-navy">
                {{ $promotionLabel }}
            </span>
        @endif
    </div>
</div>

        <div class="space-y-4 p-5 sm:p-6">
            {{-- Participants --}}
            <div class="relative">
                <label class="text-sm font-semibold text-newman-navy">
                    Participants
                </label>

                <button
                    type="button"
                    @click="participantsOpen = !participantsOpen"
                    class="mt-2 flex min-h-14 w-full items-center justify-between rounded-xl border border-newman-navy/10 bg-newman-sand/70 px-4 text-left text-sm font-semibold text-newman-navy transition hover:border-newman-gold/70"
                >
                    <span x-text="participantLabel"></span>
                    <span class="text-lg" :class="participantsOpen ? 'rotate-180' : ''">⌄</span>
                </button>

                <div
                    x-cloak
                    x-show="participantsOpen"
                    x-transition.origin.top
                    @click.outside="participantsOpen = false"
                    class="absolute inset-x-0 top-full z-30 mt-2 rounded-2xl border border-newman-navy/10 bg-white p-4 shadow-[0_20px_60px_rgba(8,36,58,0.16)]"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-newman-navy">Adults</p>
                            <p class="mt-1 text-xs text-gray-500">Age 12+</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="decrease('adults')"
                                :disabled="adults <= 1"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
                            >
                                −
                            </button>

                            <span class="w-6 text-center font-semibold text-newman-navy" x-text="adults"></span>

                            <button
                                type="button"
                                @click="increase('adults')"
                                :disabled="maxGuests && totalGuests >= maxGuests"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="my-4 h-px bg-newman-navy/10"></div>

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-newman-navy">Children</p>
                            <p class="mt-1 text-xs text-gray-500">Age 3–11</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="decrease('children')"
                                :disabled="children <= 0"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
                            >
                                −
                            </button>

                            <span class="w-6 text-center font-semibold text-newman-navy" x-text="children"></span>

                            <button
                                type="button"
                                @click="increase('children')"
                                :disabled="maxGuests && totalGuests >= maxGuests"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="my-4 h-px bg-newman-navy/10"></div>

<div class="flex items-center justify-between gap-4">
    <div>
        <p class="font-semibold text-newman-navy">
            Infants
        </p>

        <p class="mt-1 text-xs text-gray-500">
            Age 0–2
        </p>
    </div>

    <div class="flex items-center gap-3">
        <button
            type="button"
            @click="decrease('infants')"
            :disabled="infants <= 0"
            class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
        >
            −
        </button>

        <span
            class="w-6 text-center font-semibold text-newman-navy"
            x-text="infants"
        ></span>

        <button
            type="button"
            @click="increase('infants')"
            :disabled="maxGuests && totalGuests >= maxGuests"
            class="flex h-10 w-10 items-center justify-center rounded-full border border-newman-navy/15 text-lg text-newman-navy disabled:cursor-not-allowed disabled:opacity-30"
        >
            +
        </button>
    </div>
</div>

                    @if ($maxGuests)
                        <p class="mt-4 rounded-lg bg-newman-sand px-3 py-2 text-xs leading-5 text-gray-600">
                            Maximum {{ $maxGuests }} guests for this tour.
                        </p>
                    @endif

                    <button
                        type="button"
                        @click="participantsOpen = false"
                        class="mt-4 w-full rounded-xl bg-newman-navy px-4 py-3 text-sm font-semibold text-white transition hover:bg-newman-blue"
                    >
                        Apply participants
                    </button>
                </div>
            </div>

            {{-- Date --}}
            <div>
                <label for="tour-date-{{ $tour['id'] }}" class="text-sm font-semibold text-newman-navy">
                    Travel date
                </label>

                <input
                    id="tour-date-{{ $tour['id'] }}"
                    x-model="date"
                    @change="resetResult()"
                    type="date"
                    min="{{ now()->toDateString() }}"
                    class="booking-input mt-2 min-h-14 rounded-xl border-newman-navy/10 bg-newman-sand/70"
                >
            </div>


            <div>
    <label
        for="tour-language-{{ $tour['id'] }}"
        class="text-sm font-semibold text-newman-navy"
    >
        Language
    </label>

    @if ($languages !== [])
        <select
            id="tour-language-{{ $tour['id'] }}"
            x-model="language"
            @change="resetResult()"
            class="booking-input booking-select mt-2 min-h-14 rounded-xl border-newman-navy/10 bg-newman-sand/70"
        >
            @foreach ($languages as $languageOption)
                <option value="{{ $languageOption }}">
                    {{ $languageOption }}
                </option>
            @endforeach
        </select>
    @else
        <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
            No language is configured for the active Tour Options.
        </div>
    @endif
</div>


            {{-- Availability button --}}
            <button
                type="button"
                @click="checkAvailability"
                :disabled="
                loading
                || (
                    languages.length > 0
                    && !language
                )
            "
                class="flex min-h-14 w-full items-center justify-center rounded-xl bg-newman-navy px-5 py-4 text-sm font-bold uppercase tracking-[0.12em] text-white transition hover:-translate-y-0.5 hover:bg-newman-blue disabled:cursor-wait disabled:opacity-60"
            >
                <span x-show="!loading">Check availability</span>
                <span x-show="loading">Checking...</span>
            </button>

            <p
                x-cloak
                x-show="error"
                x-text="error"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-red-700"
            ></p>

            {{-- Availability result --}}
            <div
                x-cloak
                x-show="result"
                x-transition
                class="rounded-2xl border border-newman-gold/35 bg-newman-sand/60 p-4"
            >
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                    Request summary
                </p>

                <p class="mt-3 font-semibold text-newman-navy" x-text="result?.tour?.title"></p>

                <div class="mt-3 space-y-1 text-sm leading-6 text-gray-600">
                    <p>
                        <span class="font-medium text-newman-navy">Date:</span>
                        <span x-text="result?.selection?.date_label"></span>
                    </p>
                    <p>
                        <span class="font-medium text-newman-navy">Participants:</span>
                        <span x-text="result?.selection?.participant_label"></span>
                    </p>

                    <p>
                        <span class="font-medium text-newman-navy">
                            Language:
                        </span>

                        <span
                            x-text="result?.selection?.language || '—'"
                        ></span>
                    </p>
                </div>

                {{-- Transport Recommendation --}}
<template
    x-if="
        result
        && hasTransportRecommendation
    "
>
    <section class="mt-5 overflow-hidden rounded-2xl border border-newman-gold/35 bg-white">
        <div class="border-b border-newman-navy/10 bg-newman-navy px-4 py-4 text-white">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                Transport recommendation
            </p>

            <div class="mt-3 flex items-start justify-between gap-4">
                <div>
                    <h3
                        class="text-lg font-semibold"
                        x-text="transportRecommendation.label"
                    ></h3>

                    <p
                        x-show="transportRecommendation.type"
                        class="mt-1 text-xs uppercase tracking-[0.12em] text-white/55"
                        x-text="transportRecommendation.type"
                    ></p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-newman-gold/40 bg-white/5 text-newman-gold">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        class="h-6 w-6"
                        aria-hidden="true"
                    >
                        <path d="M3 13.5V10l2-5h12l2 5v3.5"/>
                        <path d="M5 13.5h14v4H5z"/>
                        <path d="M7 17.5v1.5M17 17.5v1.5"/>
                        <circle cx="7.5" cy="13.5" r="1"/>
                        <circle cx="16.5" cy="13.5" r="1"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-newman-sand/70 p-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Capacity
                    </p>

                    <p
                        class="mt-2 text-sm font-semibold text-newman-navy"
                        x-text="transportCapacityLabel"
                    ></p>
                </div>

                <div class="rounded-xl bg-newman-sand/70 p-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Vehicles
                    </p>

                    <p
                        class="mt-2 text-sm font-semibold text-newman-navy"
                        x-text="transportQuantityLabel"
                    ></p>
                </div>
            </div>

            <div class="mt-3 rounded-xl border border-newman-gold/25 bg-newman-gold/5 p-3">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-newman-gold">
                    Based on your group
                </p>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Recommended for

                    <strong
                        class="text-newman-navy"
                        x-text="transportRecommendation.totalPassengers"
                    ></strong>

                    <span
                        x-text="
                            Number(
                                transportRecommendation.totalPassengers
                            ) === 1
                                ? 'passenger'
                                : 'passengers'
                        "
                    ></span>.
                </p>
            </div>

            <p
                x-show="transportRecommendation.reason"
                class="mt-3 text-sm leading-6 text-gray-600"
                x-text="transportRecommendation.reason"
            ></p>

            <p class="mt-3 text-xs leading-5 text-gray-500">
                Adults, children, and infants all count toward
                transport capacity. Final vehicle allocation is
                confirmed with Newman before the trip.
            </p>

            <p
    x-show="!result?.available"
    class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800"
>
    This transport recommendation is based on the
    selected group size. The selected date currently
    has no available Tour Option.
</p>
        </div>
    </section>
</template>


<template
    x-if="
        result
        && !hasTransportRecommendation
    "
>
    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">
            Transport confirmation
        </p>

        <p class="mt-2 text-sm leading-6 text-amber-900">
            Transport will be confirmed manually for this
            participant selection.
        </p>
    </div>
</template>

                

                <template x-if="result?.pricing?.formatted_total">
                    <div class="mt-4 border-t border-newman-navy/10 pt-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-gray-500">
                            Estimated total
                        </p>
                        <p class="mt-1 text-2xl font-bold tracking-[-0.03em] text-newman-navy" x-text="result.pricing.formatted_total"></p>
                    </div>
                </template>

                <template x-if="!result?.pricing?.formatted_total">
                    <p class="mt-4 border-t border-newman-navy/10 pt-4 text-sm leading-6 text-gray-600">
                        Newman will confirm the final price for this request.
                    </p>
                </template>


                <p class="mt-3 text-xs leading-5 text-gray-500" x-text="result?.message"></p>

            </div>

            {{-- Trust notes --}}
            <div class="space-y-3 border-t border-newman-navy/10 pt-5">
                <div class="flex gap-3 text-sm leading-6 text-gray-600">
                    <span class="mt-0.5 text-newman-gold">✓</span>
                    <p>Private transport and route details are confirmed directly with Newman.</p>
                </div>

                <div class="flex gap-3 text-sm leading-6 text-gray-600">
                    <span class="mt-0.5 text-newman-gold">✓</span>
                    <p>No online payment is required at this stage.</p>
                </div>
            </div>
        </div>
    </div>
</aside>
