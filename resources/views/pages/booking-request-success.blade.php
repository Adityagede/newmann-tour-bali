@extends('layouts.app')

@section('content')
    @php
        $tour = $booking->tour_snapshot ?? [];
        $option = $booking->option_snapshot ?? [];
        $selection = $booking->selection_snapshot ?? [];
        $transport = $booking->transport_snapshot;

        $transportLabel = is_string($transport)
            ? $transport
            : (
                $transport['label']
                ?? $transport['vehicle_name']
                ?? $transport['name']
                ?? 'Arranged personally by Newman'
            );

        $money = static fn (int $amount): string =>
            $booking->currency
            . ' '
            . number_format($amount, 0, ',', '.');

        $participantLabel = $selection['participant_label']
            ?? $booking->total_participants
                . ' '
                . ($booking->total_participants === 1 ? 'guest' : 'guests');

        $statusLabel = ucfirst($booking->status);

        $statusClasses = [  
            'pending' => 'bg-amber-100 text-amber-800',
            'contacted' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-newman-sand text-newman-navy',
        ];

        $statusSummary = [
            'pending' => 'Newman will personally check your date, route and pickup details before confirming the trip.',
            'contacted' => 'Newman has started reviewing your request and will confirm the remaining trip details with you.',
            'confirmed' => 'Your trip has been confirmed. Keep this booking reference with your trip details.',
            'cancelled' => 'This booking request has been cancelled. Contact Newman if you need any clarification.',
            'completed' => 'This trip has been completed. Thank you for choosing Newman Tour Bali.',
        ][$booking->status] ?? 'Contact Newman if you need more information about this booking request.';

        $statusFooter = [
            'pending' => 'Your request is awaiting Newman’s confirmation.',
            'contacted' => 'Newman is in contact with you about this request.',
            'confirmed' => 'Your booking is confirmed.',
            'cancelled' => 'This booking request is cancelled.',
            'completed' => 'This booking is completed.',
        ][$booking->status] ?? 'Keep your booking reference when contacting Newman.';
    @endphp

    <main class="min-h-screen overflow-hidden bg-white pb-16 pt-28 text-newman-navy sm:pb-20 sm:pt-32 lg:pb-24 lg:pt-36">
        <div class="mx-auto w-[calc(100%-2rem)] max-w-6xl sm:w-[92%]">
            <header class="grid gap-8 border-b border-newman-navy/15 pb-10 sm:pb-12 lg:grid-cols-[minmax(0,1fr)_19rem] lg:items-end lg:gap-16">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-newman-gold text-lg font-bold text-newman-navy" aria-hidden="true">
                            ✓
                        </span>

                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                            Request received
                        </p>
                    </div>

                    <h1 class="mt-6 text-4xl font-semibold leading-[1.03] tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                        Your Bali day is taking shape.
                    </h1>

                    <p class="mt-6 max-w-2xl text-base leading-8 text-gray-600 sm:text-lg">
                        {{ $statusSummary }}
                    </p>
                </div>

                <div class="border-l-2 border-newman-gold pl-5 sm:pl-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Booking reference
                    </p>

                    <p class="mt-2 break-all font-mono text-xl font-semibold tracking-[0.04em] sm:text-2xl">
                        {{ $booking->booking_reference }}
                    </p>

                    <p class="mt-3 inline-flex px-3 py-1.5 text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabel }}
                    </p>
                </div>
            </header>

            <div class="grid gap-12 py-10 sm:py-14 lg:grid-cols-[minmax(0,1.25fr)_minmax(17rem,0.75fr)] lg:gap-16 lg:py-16">
                <section aria-labelledby="journey-summary-heading">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                        Your journey
                    </p>

                    <h2 id="journey-summary-heading" class="mt-3 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
                        {{ $tour['title'] ?? 'Private Bali tour' }}
                    </h2>

                    <p class="mt-2 text-base text-gray-500">
                        {{ $option['title'] ?? 'Selected tour experience' }}
                    </p>

                    <dl class="mt-9 divide-y divide-newman-navy/10 border-y border-newman-navy/10">
                        <div class="grid gap-2 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6">
                            <dt class="text-sm text-gray-500">Travel date</dt>
                            <dd class="font-semibold">
                                {{ $booking->travel_date?->format('d F Y') }}
                                <span class="font-normal text-gray-400">at</span>
                                {{ substr((string) $booking->starting_time, 0, 5) }}
                            </dd>
                        </div>

                        <div class="grid gap-2 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6">
                            <dt class="text-sm text-gray-500">Guests</dt>
                            <dd class="font-semibold">{{ $participantLabel }}</dd>
                        </div>

                        <div class="grid gap-2 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6">
                            <dt class="text-sm text-gray-500">Language</dt>
                            <dd class="font-semibold">{{ $booking->language }}</dd>
                        </div>

                        <div class="grid gap-2 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6">
                            <dt class="text-sm text-gray-500">Transport</dt>
                            <dd class="font-semibold">{{ $transportLabel }}</dd>
                        </div>

                        <div class="grid gap-2 py-5 sm:grid-cols-[10rem_1fr] sm:gap-6">
                            <dt class="text-sm text-gray-500">Estimated total</dt>
                            <dd>
                                <p class="text-2xl font-semibold tracking-[-0.03em]">
                                    {{ $money((int) $booking->estimated_total) }}
                                </p>

                                @if ($booking->discount_amount > 0)
                                    <p class="mt-1 text-sm text-newman-gold">
                                        Includes {{ $money((int) $booking->discount_amount) }} discount
                                    </p>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <p class="mt-5 text-xs leading-6 text-gray-500">
                        This is an estimate, not a completed payment or final confirmation. Keep your reference when contacting Newman.
                    </p>
                </section>

                <aside aria-labelledby="next-steps-heading" class="lg:border-l lg:border-newman-navy/10 lg:pl-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                        What happens next
                    </p>

                    <h2 id="next-steps-heading" class="mt-3 text-2xl font-semibold tracking-[-0.03em]">
                        A personal confirmation, not an automatic one.
                    </h2>

                    <ol class="mt-8 space-y-7">
                        <li class="grid grid-cols-[2rem_1fr] gap-4">
                            <span class="font-semibold text-newman-gold">01</span>
                            <div>
                                <h3 class="font-semibold">Newman checks your plan</h3>
                                <p class="mt-1 text-sm leading-6 text-gray-500">
                                    We review availability, timing and pickup details.
                                </p>
                            </div>
                        </li>

                        <li class="grid grid-cols-[2rem_1fr] gap-4">
                            <span class="font-semibold text-newman-gold">02</span>
                            <div>
                                <h3 class="font-semibold">We get in touch</h3>
                                <p class="mt-1 text-sm leading-6 text-gray-500">
                                    Your confirmation is sent using the contact details you provided.
                                </p>
                            </div>
                        </li>

                        <li class="grid grid-cols-[2rem_1fr] gap-4">
                            <span class="font-semibold text-newman-gold">03</span>
                            <div>
                                <h3 class="font-semibold">Your trip is confirmed</h3>
                                <p class="mt-1 text-sm leading-6 text-gray-500">
                                    Once everything is ready, your Bali day is officially set.
                                </p>
                            </div>
                        </li>
                    </ol>

                    <p class="mt-8 border-t border-newman-navy/10 pt-6 text-sm leading-6 text-gray-500">
                        Ratings are not collected at the booking-request stage. After the trip is completed, Newman can send you a secure private link to rate the experience and leave an optional private review.
                    </p>
                </aside>
            </div>

            <footer class="flex flex-col gap-4 border-t border-newman-navy/15 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-lg text-sm leading-6 text-gray-500">
                    {{ $statusFooter }}
                </p>

                <nav aria-label="Booking confirmation actions" class="flex flex-col gap-3 sm:flex-row">
                    <a
                        href="{{ route('tours') }}"
                        class="inline-flex min-h-12 items-center justify-center bg-newman-navy px-6 py-3 text-sm font-semibold text-white transition hover:bg-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                    >
                        Explore other tours
                    </a>

                    <a
                        href="{{ route('home') }}"
                        class="inline-flex min-h-12 items-center justify-center border border-newman-navy/20 px-6 py-3 text-sm font-semibold text-newman-navy transition hover:border-newman-gold focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                    >
                        Back to home
                    </a>
                </nav>
            </footer>
        </div>
    </main>
@endsection
