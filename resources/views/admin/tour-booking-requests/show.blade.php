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

    $tour = is_array(
        $tourBookingRequest->tour_snapshot
    )
        ? $tourBookingRequest->tour_snapshot
        : [];

    $option = is_array(
        $tourBookingRequest->option_snapshot
    )
        ? $tourBookingRequest->option_snapshot
        : [];

    $selection = is_array(
        $tourBookingRequest->selection_snapshot
    )
        ? $tourBookingRequest->selection_snapshot
        : [];

    $items = is_array(
        $tourBookingRequest->items_snapshot
    )
        ? $tourBookingRequest->items_snapshot
        : [];

    $included = is_array(
        $items['included'] ?? null
    )
        ? $items['included']
        : [];

    $excluded = is_array(
        $items['excluded'] ?? null
    )
        ? $items['excluded']
        : [];

    $transport =
        $tourBookingRequest->transport_snapshot;

    $transportLabel = is_string($transport)
        ? $transport
        : (
            data_get($transport, 'label')
            ?? data_get($transport, 'vehicle_name')
            ?? data_get($transport, 'name')
            ?? 'Confirmed manually'
        );

    $transportCapacity =
        is_array($transport)
            ? (
                data_get($transport, 'capacity')
                ?? data_get($transport, 'passenger_capacity')
            )
            : null;

    $tourTitle =
        $tourBookingRequest->tourPackage?->title
        ?? ($tour['title'] ?? 'Unavailable tour');

    $optionTitle =
        $tourBookingRequest->tourOption?->title
        ?? ($option['title'] ?? 'Unavailable option');

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

    $customerWhatsapp = preg_replace(
        '/\D+/',
        '',
        $tourBookingRequest->guest_whatsapp
    );

    $message = implode("\n", [
        'Hello ' . $tourBookingRequest->guest_name
            . ', thank you for your Tour Booking Request with Newman Tour Guide.',

        '',

        'Booking Reference: '
            . $tourBookingRequest->booking_reference,

        'Tour: ' . $tourTitle,
        'Option: ' . $optionTitle,

        'Travel Date: '
            . (
                $tourBookingRequest->travel_date?->format('d M Y')
                ?? '-'
            ),

        'Starting Time: '
            . substr(
                (string) $tourBookingRequest->starting_time,
                0,
                5
            ),

        'Participants: '
            . $tourBookingRequest->total_participants,

        'Estimated Total: '
            . $money(
                $tourBookingRequest->estimated_total,
                $tourBookingRequest->currency
            ),

        '',

        'We would like to confirm your availability and trip details.',
    ]);

    $whatsappUrl = $customerWhatsapp
        ? 'https://wa.me/'
            . $customerWhatsapp
            . '?text='
            . urlencode($message)
        : null;

    $statusClass =
        $statusClasses[
            $tourBookingRequest->status
        ]
        ?? 'bg-gray-100 text-gray-700';
@endphp

<div class="mb-6">
    <a
        href="{{ route(
            'admin.tour-booking-requests.index'
        ) }}"
        class="text-xs font-bold uppercase tracking-[0.16em] text-newman-navy hover:text-newman-gold"
    >
        ← Back to Tour Booking Requests
    </a>

    <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Tour Package V2
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourBookingRequest->booking_reference }}
            </h1>

            <p class="mt-3 text-sm text-gray-500">
                Requested
                {{ (
                    $tourBookingRequest->requested_at
                    ?? $tourBookingRequest->created_at
                )?->format('d M Y, H:i') ?? '—' }}
            </p>
        </div>

        <span class="inline-flex self-start px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] {{ $statusClass }}">
            {{ $tourBookingRequest->status }}
        </span>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-start">
    <div class="space-y-6">
        <section class="border border-newman-navy/10 bg-white">
            <div class="border-b border-newman-navy/10 bg-newman-navy px-6 py-5 text-white">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                    Guest information
                </p>

                <h2 class="mt-2 text-2xl font-semibold">
                    {{ $tourBookingRequest->guest_name }}
                </h2>
            </div>

            <div class="grid gap-5 p-6 sm:grid-cols-2">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        WhatsApp
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->guest_whatsapp }}
                    </p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Email
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->guest_email ?: '—' }}
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Pickup location
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">
                        {{ $tourBookingRequest->pickup_address }}
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Special requests
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">
                        {{ $tourBookingRequest->special_requests ?: 'No special requests.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-newman-navy/10 bg-white p-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                Trip selection
            </p>

            <h2 class="mt-3 text-2xl font-semibold text-newman-navy">
                {{ $tourTitle }}
            </h2>

            <p class="mt-1 text-sm font-semibold text-gray-500">
                {{ $optionTitle }}
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Travel date
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->travel_date?->format('d M Y') ?? '—' }}
                    </p>
                </div>

                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Starting time
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ substr(
                            (string) $tourBookingRequest->starting_time,
                            0,
                            5
                        ) }}
                    </p>
                </div>

                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Language
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->language }}
                    </p>
                </div>

                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Adults
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->adult_count }}
                    </p>
                </div>

                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Children
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->child_count }}
                    </p>
                </div>

                <div class="bg-newman-sand/45 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                        Infants
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tourBookingRequest->infant_count }}
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-newman-gold/30 bg-white p-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                Transport recommendation
            </p>

            <h2 class="mt-3 text-2xl font-semibold text-newman-navy">
                {{ $transportLabel }}
            </h2>

            @if ($transportCapacity)
                <p class="mt-2 text-sm text-gray-500">
                    Recommended capacity:
                    {{ $transportCapacity }}
                </p>
            @endif
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="border border-green-200 bg-white p-6">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-green-700">
                    Included
                </p>

                <div class="mt-4 space-y-3">
                    @forelse ($included as $item)
                        <div class="border-b border-newman-navy/10 pb-3 last:border-0 last:pb-0">
                            <p class="font-semibold text-newman-navy">
                                {{ data_get($item, 'label', 'Included item') }}
                            </p>

                            @if (data_get($item, 'details'))
                                <p class="mt-1 text-sm leading-6 text-gray-500">
                                    {{ data_get($item, 'details') }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            No included-item snapshot.
                        </p>
                    @endforelse
                </div>
            </section>

            <section class="border border-red-200 bg-white p-6">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-red-700">
                    Excluded
                </p>

                <div class="mt-4 space-y-3">
                    @forelse ($excluded as $item)
                        <div class="border-b border-newman-navy/10 pb-3 last:border-0 last:pb-0">
                            <p class="font-semibold text-newman-navy">
                                {{ data_get($item, 'label', 'Excluded item') }}
                            </p>

                            @if (data_get($item, 'details'))
                                <p class="mt-1 text-sm leading-6 text-gray-500">
                                    {{ data_get($item, 'details') }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            No excluded-item snapshot.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <aside class="space-y-6 xl:sticky xl:top-6">
        <section class="bg-newman-navy p-6 text-white">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                Pricing snapshot
            </p>

            <div class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-white/60">
                        Base total
                    </span>

                    <strong>
                        {{ $money(
                            $tourBookingRequest->base_total,
                            $tourBookingRequest->currency
                        ) }}
                    </strong>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <span class="text-white/60">
                        Discount
                    </span>

                    <strong class="text-newman-gold">
                        {{ $money(
                            $tourBookingRequest->discount_amount,
                            $tourBookingRequest->currency
                        ) }}
                    </strong>
                </div>

                <div class="border-t border-white/15 pt-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-white/50">
                        Estimated total
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-[-0.03em]">
                        {{ $money(
                            $tourBookingRequest->estimated_total,
                            $tourBookingRequest->currency
                        ) }}
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-newman-navy/10 bg-white p-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-gold">
                Update status
            </p>

            <form
                method="POST"
                action="{{ route(
                    'admin.tour-booking-requests.status.update',
                    $tourBookingRequest
                ) }}"
                class="mt-4 space-y-4"
            >
                @csrf
                @method('PATCH')

                <select
                    name="status"
                    class="w-full border border-newman-navy/15 bg-white px-4 py-3 text-sm font-semibold text-newman-navy outline-none transition focus:border-newman-gold"
                >
                    @foreach ($availableStatuses as $item)
                        <option
                            value="{{ $item }}"
                            @selected(
                                $tourBookingRequest->status === $item
                            )
                        >
                            {{ ucfirst($item) }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="min-h-12 w-full bg-newman-gold px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:bg-newman-navy hover:text-white"
                >
                    Update status
                </button>
            </form>
        </section>

        <section class="border border-newman-navy/10 bg-white p-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-gold">
                Contact guest
            </p>

            @if ($whatsappUrl)
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 flex min-h-12 w-full items-center justify-center bg-green-600 px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-green-700"
                >
                    Open WhatsApp
                </a>
            @else
                <p class="mt-3 text-sm text-gray-500">
                    No valid WhatsApp number available.
                </p>
            @endif
        </section>

        <section class="border border-newman-navy/10 bg-white p-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                Internal identifiers
            </p>

            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">
                        Request ID
                    </dt>

                    <dd class="font-semibold text-newman-navy">
                        {{ $tourBookingRequest->id }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">
                        Tour Product ID
                    </dt>

                    <dd class="font-semibold text-newman-navy">
                        {{ $tourBookingRequest->tour_package_id ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">
                        Tour Option ID
                    </dt>

                    <dd class="font-semibold text-newman-navy">
                        {{ $tourBookingRequest->tour_option_id ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">
                        Source
                    </dt>

                    <dd class="font-semibold text-newman-navy">
                        {{ $tourBookingRequest->source }}
                    </dd>
                </div>
            </dl>
        </section>
    </aside>
</div>
@endsection