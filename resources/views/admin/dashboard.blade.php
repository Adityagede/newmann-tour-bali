@extends('admin.layouts.app')

@section('content')
@php
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'contacted' => 'bg-blue-100 text-blue-700',
        'confirmed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'completed' => 'bg-newman-sand text-newman-navy',
    ];

    $money = static function (
        ?int $amount,
        string $currency = 'IDR'
    ): string {
        if ($amount === null) {
            return '—';
        }

        return strtoupper($currency)
            . ' '
            . number_format(
                $amount,
                0,
                ',',
                '.'
            );
    };

    $statCards = [
        [
            'label' => 'Total',
            'value' => $stats['total'] ?? 0,
            'class' => 'text-newman-navy',
        ],
        [
            'label' => 'Pending',
            'value' => $stats['pending'] ?? 0,
            'class' => 'text-newman-gold',
        ],
        [
            'label' => 'Contacted',
            'value' => $stats['contacted'] ?? 0,
            'class' => 'text-newman-blue',
        ],
        [
            'label' => 'Confirmed',
            'value' => $stats['confirmed'] ?? 0,
            'class' => 'text-green-700',
        ],
        [
            'label' => 'Cancelled',
            'value' => $stats['cancelled'] ?? 0,
            'class' => 'text-red-700',
        ],
        [
            'label' => 'Completed',
            'value' => $stats['completed'] ?? 0,
            'class' => 'text-newman-navy',
        ],
    ];
@endphp

<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
        Tour Package V2
    </p>

    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                Booking overview
            </h1>

            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-500">
                Live overview of requests submitted through the Tour Package V2
                availability, review, and booking-success flow.
            </p>
        </div>

        <a
            href="{{ route('admin.tour-booking-requests.index') }}"
            class="inline-flex min-h-11 items-center justify-center bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
        >
            View all bookings
        </a>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($statCards as $card)
        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5">
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
                {{ $card['label'] }}
            </p>

            <p class="mt-3 text-4xl font-semibold {{ $card['class'] }}">
                {{ $card['value'] }}
            </p>
        </div>
    @endforeach
</div>


<section class="mt-8 border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Tour Package health</p>
            <h2 class="mt-2 text-2xl font-semibold text-newman-navy">V2 configuration overview</h2>
            <p class="mt-2 max-w-3xl text-sm leading-7 text-gray-500">
                These counters show whether active products have the default option, participant pricing, and operating schedules required by the public booking-request flow.
            </p>
        </div>
        <a href="{{ route('admin.tour-packages.index') }}" class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue">Manage tour packages</a>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Active products', 'value' => $tourHealth['active'] ?? 0],
            ['label' => 'Draft products', 'value' => $tourHealth['draft'] ?? 0],
            ['label' => 'No default option', 'value' => $tourHealth['without_default_option'] ?? 0],
            ['label' => 'Options without price', 'value' => $tourHealth['options_without_price'] ?? 0],
            ['label' => 'Options without schedule', 'value' => $tourHealth['options_without_schedule'] ?? 0],
            ['label' => 'Inactive products', 'value' => $tourHealth['inactive'] ?? 0],
        ] as $healthCard)
            <div class="border border-gray-100 bg-newman-sand/40 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-500">{{ $healthCard['label'] }}</p>
                <p class="mt-2 text-3xl font-semibold text-newman-navy">{{ $healthCard['value'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="mt-8 border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Latest V2 bookings
            </p>

            <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                Recent booking requests
            </h2>
        </div>

        <a
            href="{{ route('admin.tour-booking-requests.index') }}"
            class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
        >
            View all
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-[0.18em] text-gray-400">
                    <th class="py-4 pr-4">Reference</th>
                    <th class="py-4 pr-4">Guest</th>
                    <th class="py-4 pr-4">Tour / option</th>
                    <th class="py-4 pr-4">Travel date</th>
                    <th class="py-4 pr-4">Guests</th>
                    <th class="py-4 pr-4">Total</th>
                    <th class="py-4 pr-4">Status</th>
                    <th class="py-4 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($latestBookings as $booking)
                    @php
                        $tourTitle = $booking->tourPackage?->title
                            ?? data_get(
                                $booking->tour_snapshot,
                                'title',
                                'Unavailable tour'
                            );

                        $optionTitle = $booking->tourOption?->title
                            ?? data_get(
                                $booking->option_snapshot,
                                'title',
                                'Unavailable option'
                            );
                    @endphp

                    <tr class="border-b border-gray-100 align-top">
                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">
                                {{ $booking->booking_reference }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ ($booking->requested_at ?? $booking->created_at)?->format('d M Y, H:i') ?? '—' }}
                            </p>
                        </td>

                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">
                                {{ $booking->guest_name }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $booking->guest_whatsapp }}
                            </p>
                        </td>

                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">
                                {{ $tourTitle }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $optionTitle }}
                            </p>
                        </td>

                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">
                                {{ $booking->travel_date?->format('d M Y') ?? '—' }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ substr((string) $booking->starting_time, 0, 5) }}
                                · {{ $booking->language }}
                            </p>
                        </td>

                        <td class="py-4 pr-4 font-semibold text-newman-navy">
                            {{ $booking->total_participants }}
                        </td>

                        <td class="py-4 pr-4 font-semibold text-newman-navy">
                            {{ $money(
                                $booking->estimated_total,
                                $booking->currency
                            ) }}
                        </td>

                        <td class="py-4 pr-4">
                            <span class="px-3 py-2 text-xs font-bold uppercase {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $booking->status }}
                            </span>
                        </td>

                        <td class="py-4 text-right">
                            <a
                                href="{{ route(
                                    'admin.tour-booking-requests.show',
                                    $booking
                                ) }}"
                                class="font-bold uppercase tracking-[0.14em] text-newman-blue hover:text-newman-gold"
                            >
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            No Tour Booking Request V2 has been submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="mt-6 border border-newman-navy/10 bg-newman-sand p-5 sm:p-6">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">
                Separate custom itinerary flow
            </p>
            <h2 class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                Custom Trip Requests
            </h2>
            <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600">
                These requests are not package bookings. They remain separate so Tour Package V2 totals and pricing stay accurate.
            </p>
        </div>

        <div class="grid min-w-[260px] grid-cols-2 gap-3">
            <div class="bg-white p-4 text-center">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">Total</p>
                <p class="mt-2 text-3xl font-semibold text-newman-navy">{{ $customTripStats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-4 text-center">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">Pending</p>
                <p class="mt-2 text-3xl font-semibold text-newman-gold">{{ $customTripStats['pending'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <a
        href="{{ route('admin.custom-trip-requests.index') }}"
        class="mt-5 inline-flex bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
    >
        Open Custom Trips
    </a>
</section>
@endsection


@push('scripts')
<script>
    (() => {
        const refreshEveryMs = 15000;
        let remaining = refreshEveryMs / 1000;

        const badge = document.createElement('div');
        badge.className = 'fixed bottom-4 right-4 z-50 border border-newman-navy/10 bg-white px-3 py-2 text-xs font-semibold text-gray-500 shadow-lg';
        document.body.appendChild(badge);

        const render = () => {
            badge.textContent = `Dashboard refresh in ${remaining}s`;
        };

        render();
        window.setInterval(() => {
            remaining -= 1;
            if (remaining <= 0) {
                window.location.reload();
                return;
            }
            render();
        }, 1000);
    })();
</script>
@endpush
