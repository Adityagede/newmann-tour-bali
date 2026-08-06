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
@endphp

<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
        Separate Enquiry Flow
    </p>

    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                Custom trip requests
            </h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-500">
                Flexible itinerary requests from the Custom Trip V2 form. Vehicle selections are preferences and remain subject to manual confirmation.
            </p>
        </div>

        <a
            href="{{ route('admin.tour-booking-requests.index') }}"
            class="inline-flex min-h-11 items-center justify-center bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
        >
            Open V2 Bookings
        </a>
    </div>
</div>

<div class="mb-6 -mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
    <div class="flex min-w-max gap-3">
        <a
            href="{{ route('admin.custom-trip-requests.index', array_filter(['q' => $search])) }}"
            class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ ! $status ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
        >
            All ({{ $counts['all'] ?? 0 }})
        </a>

        @foreach ($availableStatuses as $item)
            <a
                href="{{ route('admin.custom-trip-requests.index', array_filter(['status' => $item, 'q' => $search])) }}"
                class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ $status === $item ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
            >
                {{ $item }} ({{ $counts[$item] ?? 0 }})
            </a>
        @endforeach
    </div>
</div>

<form action="{{ route('admin.custom-trip-requests.index') }}" method="GET" class="mb-6 grid gap-3 border border-newman-navy/10 bg-white p-4 sm:grid-cols-[1fr_auto]">
    @if ($status)
        <input type="hidden" name="status" value="{{ $status }}">
    @endif

    <input
        name="q"
        value="{{ $search }}"
        type="search"
        placeholder="Search reference, guest, WhatsApp, or email"
        class="booking-input"
    >

    <button
        type="submit"
        class="bg-newman-gold px-6 py-3 text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-newman-navy hover:text-white"
    >
        Search
    </button>
</form>

<section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-[0.18em] text-gray-400">
                    <th class="py-4 pr-4">Reference</th>
                    <th class="py-4 pr-4">Guest</th>
                    <th class="py-4 pr-4">Trip Date</th>
                    <th class="py-4 pr-4">Guests</th>
                    <th class="py-4 pr-4">Vehicle Preference</th>
                    <th class="py-4 pr-4">Status</th>
                    <th class="py-4 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($customTripRequests as $customTripRequest)
                    @php
                        $vehicleText = $customTripRequest->vehiclePreferenceLabel();
                        $suggestedVehicle = $customTripRequest->suggestedVehicleLabel();
                    @endphp

                    <tr class="border-b border-gray-100 align-top">
                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">{{ $customTripRequest->booking_code }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $customTripRequest->created_at?->format('d M Y, H:i') }}</p>
                        </td>

                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">{{ $customTripRequest->name }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $customTripRequest->whatsapp }}</p>
                        </td>

                        <td class="py-4 pr-4">{{ $customTripRequest->trip_date?->format('d M Y') ?? '—' }}</td>
                        <td class="py-4 pr-4">{{ $customTripRequest->people_count ?? '—' }}</td>
                        <td class="py-4 pr-4">
                            <p class="font-semibold text-newman-navy">{{ $vehicleText }}</p>
                            <p class="mt-1 max-w-[240px] text-xs leading-5 text-gray-400">Suggested: {{ $suggestedVehicle }}</p>
                        </td>

                        <td class="py-4 pr-4">
                            <span class="px-3 py-2 text-xs font-bold uppercase {{ $statusClasses[$customTripRequest->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $customTripRequest->status }}
                            </span>
                        </td>

                        <td class="py-4 text-right">
                            <a
                                href="{{ route('admin.custom-trip-requests.show', $customTripRequest) }}"
                                class="font-bold uppercase tracking-[0.14em] text-newman-blue hover:text-newman-gold"
                            >
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">
                            No custom trip request matches this filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $customTripRequests->links() }}</div>
</section>
@endsection
