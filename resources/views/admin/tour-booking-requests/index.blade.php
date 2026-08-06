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
@endphp

<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
        Tour Package V2
    </p>

    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                Tour booking requests
            </h1>

            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-500">
                Requests submitted through the Tour Package
                availability, option, review, and success flow.
            </p>
        </div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="inline-flex min-h-11 items-center justify-center border border-newman-navy/15 bg-white px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-sand"
        >
            Dashboard
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6 -mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
    <div class="flex min-w-max gap-3">
        <a
            href="{{ route('admin.tour-booking-requests.index', array_filter([
                'q' => $search,
            ])) }}"
            class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ ! $status ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
        >
            All
            <span class="ml-1 opacity-60">
                {{ $counts['all'] ?? 0 }}
            </span>
        </a>

        @foreach ($availableStatuses as $item)
            <a
                href="{{ route('admin.tour-booking-requests.index', array_filter([
                    'status' => $item,
                    'q' => $search,
                ])) }}"
                class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ $status === $item ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
            >
                {{ $item }}

                <span class="ml-1 opacity-60">
                    {{ $counts[$item] ?? 0 }}
                </span>
            </a>
        @endforeach
    </div>
</div>

<form
    method="GET"
    action="{{ route('admin.tour-booking-requests.index') }}"
    class="mb-6 grid gap-3 border border-newman-navy/10 bg-white p-4 sm:grid-cols-[minmax(0,1fr)_180px_auto]"
>
    <div>
        <label
            for="tour-booking-search"
            class="sr-only"
        >
            Search booking requests
        </label>

        <input
            id="tour-booking-search"
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Reference, guest, WhatsApp, or email"
            class="w-full border border-newman-navy/15 bg-newman-sand/25 px-4 py-3 text-sm text-newman-navy outline-none transition focus:border-newman-gold"
        >
    </div>

    <div>
        <label
            for="tour-booking-status"
            class="sr-only"
        >
            Status
        </label>

        <select
            id="tour-booking-status"
            name="status"
            class="w-full border border-newman-navy/15 bg-white px-4 py-3 text-sm text-newman-navy outline-none transition focus:border-newman-gold"
        >
            <option value="">
                All statuses
            </option>

            @foreach ($availableStatuses as $item)
                <option
                    value="{{ $item }}"
                    @selected($status === $item)
                >
                    {{ ucfirst($item) }}
                </option>
            @endforeach
        </select>
    </div>

    <button
        type="submit"
        class="min-h-11 bg-newman-navy px-6 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
    >
        Search
    </button>
</form>

<div class="overflow-hidden border border-newman-navy/10 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-[1100px] w-full text-left">
            <thead class="border-b border-newman-navy/10 bg-newman-sand/45">
                <tr class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                    <th class="px-5 py-4">
                        Reference
                    </th>

                    <th class="px-5 py-4">
                        Guest
                    </th>

                    <th class="px-5 py-4">
                        Tour and option
                    </th>

                    <th class="px-5 py-4">
                        Schedule
                    </th>

                    <th class="px-5 py-4">
                        Participants
                    </th>

                    <th class="px-5 py-4">
                        Estimated total
                    </th>

                    <th class="px-5 py-4">
                        Status
                    </th>

                    <th class="px-5 py-4 text-right">
                        Action
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-newman-navy/10">
                @forelse ($tourBookingRequests as $item)
                    @php
                        $tourTitle =
                            $item->tourPackage?->title
                            ?? data_get(
                                $item->tour_snapshot,
                                'title',
                                'Unavailable tour'
                            );

                        $optionTitle =
                            $item->tourOption?->title
                            ?? data_get(
                                $item->option_snapshot,
                                'title',
                                'Unavailable option'
                            );

                        $requestedMoment =
                            $item->requested_at
                            ?? $item->created_at;

                        $statusClass =
                            $statusClasses[$item->status]
                            ?? 'bg-gray-100 text-gray-700';
                    @endphp

                    <tr class="align-top transition hover:bg-newman-sand/20">
                        <td class="px-5 py-5">
                            <a
                                href="{{ route(
                                    'admin.tour-booking-requests.show',
                                    $item
                                ) }}"
                                class="font-bold text-newman-navy underline decoration-newman-gold/50 underline-offset-4 hover:text-newman-gold"
                            >
                                {{ $item->booking_reference }}
                            </a>

                            <p class="mt-2 text-xs text-gray-400">
                                {{ $requestedMoment?->format('d M Y, H:i') ?? '—' }}
                            </p>
                        </td>

                        <td class="px-5 py-5">
                            <p class="font-semibold text-newman-navy">
                                {{ $item->guest_name }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $item->guest_whatsapp }}
                            </p>

                            @if ($item->guest_email)
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $item->guest_email }}
                                </p>
                            @endif
                        </td>

                        <td class="px-5 py-5">
                            <p class="font-semibold text-newman-navy">
                                {{ $tourTitle }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $optionTitle }}
                            </p>
                        </td>

                        <td class="px-5 py-5">
                            <p class="font-semibold text-newman-navy">
                                {{ $item->travel_date?->format('d M Y') ?? '—' }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ substr(
                                    (string) $item->starting_time,
                                    0,
                                    5
                                ) }}
                                ·
                                {{ $item->language }}
                            </p>
                        </td>

                        <td class="px-5 py-5">
                            <p class="font-semibold text-newman-navy">
                                {{ $item->total_participants }}
                                participants
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $item->adult_count }} adult ·
                                {{ $item->child_count }} child ·
                                {{ $item->infant_count }} infant
                            </p>
                        </td>

                        <td class="px-5 py-5">
                            <p class="font-bold text-newman-navy">
                                {{ $money(
                                    $item->estimated_total,
                                    $item->currency
                                ) }}
                            </p>

                            @if ($item->discount_amount > 0)
                                <p class="mt-1 text-xs font-semibold text-green-700">
                                    Discount
                                    {{ $money(
                                        $item->discount_amount,
                                        $item->currency
                                    ) }}
                                </p>
                            @endif
                        </td>

                        <td class="px-5 py-5">
                            <span class="inline-flex px-3 py-2 text-[10px] font-bold uppercase tracking-[0.13em] {{ $statusClass }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <td class="px-5 py-5 text-right">
                            <a
                                href="{{ route(
                                    'admin.tour-booking-requests.show',
                                    $item
                                ) }}"
                                class="inline-flex min-h-10 items-center justify-center border border-newman-navy/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.13em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
                            >
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="8"
                            class="px-6 py-16 text-center"
                        >
                            <p class="text-lg font-semibold text-newman-navy">
                                No Tour Booking Requests found.
                            </p>

                            <p class="mt-2 text-sm text-gray-500">
                                Try another search or status filter.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($tourBookingRequests->hasPages())
    <div class="mt-6">
        {{ $tourBookingRequests->links() }}
    </div>
@endif
@endsection