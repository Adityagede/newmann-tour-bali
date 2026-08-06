@extends('layouts.app')

@section('content')
    @php
        $tour =
            $booking->tour_snapshot
            ?? [];

        $option =
            $booking->option_snapshot
            ?? [];

        $selection =
            $booking->selection_snapshot
            ?? [];

        $transport =
            $booking->transport_snapshot;

        $transportLabel = is_string(
            $transport
        )
            ? $transport
            : (
                $transport['label']
                ?? $transport['vehicle_name']
                ?? $transport['name']
                ?? 'Confirmed by Newman'
            );

        $money = static fn (
            int $amount
        ): string =>
            $booking->currency
            . ' '
            . number_format(
                $amount,
                0,
                ',',
                '.'
            );
    @endphp

    <main class="min-h-[75vh] bg-newman-sand/35 py-14 sm:py-20">
        <div class="mx-auto w-[92%] max-w-5xl">
            <section class="overflow-hidden rounded-[30px] border border-newman-gold/35 bg-white shadow-[0_24px_70px_rgba(8,36,58,0.11)]">
                <header class="bg-newman-navy px-6 py-10 text-center text-white sm:px-10 sm:py-14">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-newman-gold/50 bg-newman-gold text-3xl text-newman-navy">
                        ✓
                    </div>

                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                        Booking request received
                    </p>

                    <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-semibold tracking-[-0.045em] sm:text-6xl">
                        Your Bali trip request is now pending confirmation.
                    </h1>

                    <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-white/65">
                        Newman will review the selected option,
                        schedule, pickup details, and transport
                        arrangement before confirming the request.
                    </p>
                </header>

                <div class="p-6 sm:p-10">
                    <div class="rounded-2xl border border-newman-gold/35 bg-newman-sand/45 p-5 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">
                            Booking reference
                        </p>

                        <p class="mt-3 text-2xl font-bold tracking-[0.08em] text-newman-navy sm:text-3xl">
                            {{ $booking->booking_reference }}
                        </p>

                        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">
                            Status: Pending confirmation
                        </p>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-newman-sand/55 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Tour
                            </p>

                            <p class="mt-2 font-semibold text-newman-navy">
                                {{ $tour['title'] ?? 'Tour request' }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $option['title'] ?? 'Selected Tour Option' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-newman-sand/55 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Travel schedule
                            </p>

                            <p class="mt-2 font-semibold text-newman-navy">
                                {{ $booking->travel_date?->format('d M Y') }}
                                ·
                                {{ substr((string) $booking->starting_time, 0, 5) }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $booking->language }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-newman-sand/55 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Participants
                            </p>

                            <p class="mt-2 font-semibold text-newman-navy">
                                {{ $selection['participant_label']
                                    ?? (
                                        $booking->total_participants
                                        . ' participants'
                                    ) }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $transportLabel }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-newman-navy p-5 text-white">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-newman-gold">
                                Estimated total
                            </p>

                            <p class="mt-2 text-2xl font-bold">
                                {{ $money(
                                    (int) $booking->estimated_total
                                ) }}
                            </p>

                            @if ($booking->discount_amount > 0)
                                <p class="mt-1 text-xs text-newman-gold">
                                    Discount:
                                    {{ $money(
                                        (int) $booking->discount_amount
                                    ) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl border border-newman-navy/10 p-5 sm:p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-gold">
                            What happens next?
                        </p>

                        <div class="mt-4 grid gap-4 text-sm leading-7 text-gray-600 sm:grid-cols-3">
                            <div>
                                <strong class="block text-newman-navy">
                                    1. Newman reviews
                                </strong>

                                Availability and trip details
                                are checked manually.
                            </div>

                            <div>
                                <strong class="block text-newman-navy">
                                    2. We contact you
                                </strong>

                                Confirmation is sent through
                                the contact details provided.
                            </div>

                            <div>
                                <strong class="block text-newman-navy">
                                    3. Trip confirmation
                                </strong>

                                Your request remains Pending
                                until Newman confirms it.
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <a
                            href="{{ route('tours') }}"
                            class="flex min-h-12 items-center justify-center rounded-xl bg-newman-navy px-7 py-3 text-xs font-bold uppercase tracking-[0.13em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
                        >
                            Explore other tours
                        </a>

                        <a
                            href="{{ route('home') }}"
                            class="flex min-h-12 items-center justify-center rounded-xl border border-newman-navy/15 bg-white px-7 py-3 text-xs font-bold uppercase tracking-[0.13em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-sand"
                        >
                            Back to home
                        </a>
                    </div>

                    <p class="mt-7 text-center text-xs leading-5 text-gray-500">
                        Keep your booking reference for future
                        communication. This page does not represent
                        a completed payment or final booking confirmation.
                    </p>
                </div>
            </section>
        </div>
    </main>
@endsection