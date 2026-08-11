@extends('layouts.app')

@section('content')
    @php
        $vehiclePreference = $customTripRequest->vehiclePreferenceLabel();
        $suggestedVehicle = $customTripRequest->suggestedVehicleLabel();

        $whatsappMessage = implode("\n", [
            'Hello Newman Tour Guide, I have submitted a custom trip request from the website.',
            '',
            'Reference: ' . $customTripRequest->booking_code,
            'Name: ' . $customTripRequest->name,
            'Trip Date: ' . ($customTripRequest->trip_date?->format('Y-m-d') ?? 'Flexible / not selected'),
            'Guests: ' . ($customTripRequest->people_count ?? '-'),
            'Vehicle Preference: ' . $vehiclePreference,
            'Pickup Area: ' . ($customTripRequest->pickup_area ?: 'To be confirmed'),
            '',
            'Please help review the route, vehicle arrangement, and quotation. Thank you.',
        ]);

        $whatsappUrl = 'https://wa.me/6287887243495?text=' . urlencode($whatsappMessage);
    @endphp

    <section
        class="relative min-h-screen overflow-x-hidden bg-newman-navy pt-28 text-white sm:pt-36 lg:pt-40"
    >
        {{-- Background decoration --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.08]">
            <div class="bali-pattern h-full w-full"></div>
        </div>

        <div
            class="pointer-events-none absolute -left-40 top-28 h-96 w-96 rounded-full bg-newman-gold/10 blur-3xl"
        ></div>

        <div
            class="pointer-events-none absolute -right-40 bottom-0 h-96 w-96 rounded-full bg-newman-blue/20 blur-3xl"
        ></div>

        {{-- Main container --}}
        <div
            class="relative mx-auto w-[calc(100%-2rem)] max-w-6xl pb-20 sm:w-[92%] sm:pb-24 lg:pb-28"
        >
            {{-- Main card --}}
            <div
                data-aos="fade-up"
                class="min-w-0 max-w-full overflow-hidden bg-white text-newman-navy shadow-2xl shadow-black/20"
            >
                {{-- Header --}}
                <div
                    class="min-w-0 overflow-hidden border-b border-gray-100 p-5 sm:p-8 lg:p-10"
                >
                    <div
                        class="flex min-w-0 flex-col gap-6 lg:flex-row lg:items-start lg:justify-between"
                    >
                        <div class="min-w-0 max-w-3xl">
                            <p
                                class="break-words text-xs font-bold uppercase tracking-[0.24em] text-newman-gold sm:tracking-[0.35em]"
                            >
                                Custom Trip Request Received
                            </p>

                            <h1
                                class="mt-4 break-words text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy [overflow-wrap:anywhere] sm:text-5xl lg:text-6xl"
                            >
                                Thank you, {{ $customTripRequest->name }}.
                            </h1>

                            <p
                                class="mt-5 break-words text-sm leading-7 text-gray-600 [overflow-wrap:anywhere] sm:text-base sm:leading-8"
                            >
                                Your request is now waiting for manual review.
                                Newman will check the route, group size, vehicle
                                preference, and availability before sending a
                                confirmation or quotation.
                            </p>
                        </div>

                        <div
                            class="w-full min-w-0 bg-newman-sand px-5 py-4 lg:w-auto lg:shrink-0 lg:text-right"
                        >
                            <p
                                class="text-xs font-bold uppercase tracking-[0.2em] text-newman-blue"
                            >
                                Status
                            </p>

                            <p
                                class="mt-2 break-words text-lg font-semibold capitalize text-newman-navy"
                            >
                                {{ $customTripRequest->status }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Content and next steps --}}
                <div
                    class="grid min-w-0 gap-0 lg:grid-cols-[minmax(0,1fr)_340px]"
                >
                    {{-- Request summary --}}
                    <div class="min-w-0 overflow-hidden p-5 sm:p-8 lg:p-10">
                        <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                            @foreach ([
                                ['Reference', $customTripRequest->booking_code],
                                [
                                    'Trip Date',
                                    $customTripRequest->trip_date?->format('d M Y')
                                        ?? 'Flexible / not selected',
                                ],
                                [
                                    'Guests',
                                    ($customTripRequest->people_count ?? '—') . ' guest(s)',
                                ],
                                [
                                    'Pickup Area',
                                    $customTripRequest->pickup_area ?: 'To be confirmed',
                                ],
                                ['Vehicle Preference', $vehiclePreference],
                                ['Suggested Fit', $suggestedVehicle],
                            ] as [$label, $value])
                                <div
                                    class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5"
                                >
                                    <p
                                        class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue"
                                    >
                                        {{ $label }}
                                    </p>

                                    <p
                                        class="mt-2 break-words font-semibold leading-6 text-newman-navy [overflow-wrap:anywhere]"
                                    >
                                        {{ $value }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Trip plan --}}
                        <div
                            class="mt-4 min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5"
                        >
                            <p
                                class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue"
                            >
                                Trip Plan
                            </p>

                            <p
                                class="mt-3 whitespace-pre-line break-words text-sm leading-7 text-newman-navy [overflow-wrap:anywhere]"
                            >
                                {{ $customTripRequest->message }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-6 grid min-w-0 gap-3 sm:grid-cols-2">
                            <a
                                href="{{ $whatsappUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex w-full min-w-0 items-center justify-center whitespace-normal break-words bg-newman-navy px-4 py-4 text-center text-xs font-bold uppercase leading-5 tracking-[0.08em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl sm:px-5 sm:text-sm sm:tracking-[0.12em]"
                            >
                                Continue via WhatsApp
                            </a>

                            <a
                                href="{{ route('tours') }}"
                                class="flex w-full min-w-0 items-center justify-center whitespace-normal break-words border border-newman-navy/15 px-4 py-4 text-center text-xs font-bold uppercase leading-5 tracking-[0.08em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold sm:px-5 sm:text-sm sm:tracking-[0.12em]"
                            >
                                Browse Tour Packages
                            </a>
                        </div>
                    </div>

                    {{-- What happens next --}}
                    <aside
                        class="min-w-0 overflow-hidden bg-newman-navy p-5 text-white sm:p-8 lg:p-10"
                    >
                        <p
                            class="break-words text-xs font-bold uppercase tracking-[0.24em] text-newman-gold sm:tracking-[0.3em]"
                        >
                            What happens next?
                        </p>

                        <div class="mt-6 space-y-5">
                            @foreach ([
                                [
                                    '01',
                                    'Review',
                                    'Newman reviews the route, date, group size, luggage, and vehicle preference.',
                                ],
                                [
                                    '02',
                                    'Contact',
                                    'The team contacts you through WhatsApp or email when clarification is needed.',
                                ],
                                [
                                    '03',
                                    'Confirm',
                                    'The itinerary, vehicle, availability, and quotation are confirmed manually.',
                                ],
                            ] as [$number, $title, $description])
                                <div
                                    class="min-w-0 overflow-hidden border border-white/10 bg-white/8 p-4"
                                >
                                    <p class="text-xl font-semibold text-newman-gold">
                                        {{ $number }}
                                    </p>

                                    <p class="mt-2 font-semibold">
                                        {{ $title }}
                                    </p>

                                    <p
                                        class="mt-2 break-words text-sm leading-6 text-white/60 [overflow-wrap:anywhere]"
                                    >
                                        {{ $description }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="mt-6 min-w-0 overflow-hidden border border-newman-gold/30 p-4"
                        >
                            <p
                                class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold"
                            >
                                No online payment
                            </p>

                            <p
                                class="mt-2 break-words text-sm leading-6 text-white/65 [overflow-wrap:anywhere]"
                            >
                                This page confirms receipt of a request only.
                                It is not a paid or instantly confirmed booking.
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
