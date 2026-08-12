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
                        Newman Tour Guide is handled by a local Bali guide who understands how to make a trip feel comfortable, flexible, and not rushed.
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

@php
    $homepageFaqColumns = [
        [
            [
                'question' => 'Are Newman’s Bali tours private?',
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