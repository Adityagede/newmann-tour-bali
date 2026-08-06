@extends('admin.layouts.app')

@section('content')
@php
    $vehicleText = $customTripRequest->vehiclePreferenceLabel();
    $suggestedVehicle = $customTripRequest->suggestedVehicleLabel();

    $customerWhatsapp = preg_replace('/\D+/', '', $customTripRequest->whatsapp);

    $message = implode("\n", [
        'Hello ' . $customTripRequest->name . ', thank you for your custom trip request with Newman Tour Guide.',
        '',
        'Reference: ' . $customTripRequest->booking_code,
        'Trip Date: ' . ($customTripRequest->trip_date?->format('Y-m-d') ?? '-'),
        'People: ' . ($customTripRequest->people_count ?? '-'),
        'Vehicle Preference: ' . $vehicleText,
        'Suggested Fit: ' . $suggestedVehicle,
        '',
        'We would like to review your route, vehicle preference, and trip details before confirmation.'
    ]);

    $whatsappUrl = $customerWhatsapp
        ? 'https://wa.me/' . $customerWhatsapp . '?text=' . urlencode($message)
        : null;

    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'contacted' => 'bg-blue-100 text-blue-700',
        'confirmed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'completed' => 'bg-newman-sand text-newman-navy',
    ];
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">Custom Trip Detail</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $customTripRequest->booking_code }}
        </h1>
        <div class="mt-4">
            <span class="inline-flex px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] {{ $statusClasses[$customTripRequest->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $customTripRequest->status }}
            </span>
        </div>
    </div>

    <a
        href="{{ route('admin.custom-trip-requests.index') }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to Custom Trips
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
        <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Guest Request</p>
            <h2 class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">Custom itinerary information</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([
                ['Name', $customTripRequest->name],
                ['WhatsApp', $customTripRequest->whatsapp],
                ['Email', $customTripRequest->email ?: '—'],
                ['Trip Date', $customTripRequest->trip_date?->format('d M Y') ?? '—'],
                ['Guests', $customTripRequest->people_count ?? '—'],
                ['Vehicle Preference', $vehicleText],
                ['Suggested Fit', $suggestedVehicle],
                ['Pickup Area', $customTripRequest->pickup_area ?: '—'],
                ['Request Type', 'Custom Bali Private Tour'],
            ] as [$label, $value])
                <div class="bg-newman-sand p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-newman-blue">{{ $label }}</p>
                    <p class="mt-2 font-semibold text-newman-navy">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 bg-newman-sand p-5">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-newman-blue">Destination List & Preferences</p>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-newman-navy">{{ $customTripRequest->message ?: '—' }}</p>
        </div>
    </section>

    <aside class="space-y-5">
        <div class="border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Admin Action</p>

            <form
                action="{{ route('admin.custom-trip-requests.status.update', $customTripRequest) }}"
                method="POST"
                class="mt-6 grid gap-4"
            >
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-sm font-semibold">Update Status</label>
                    <select name="status" class="booking-input booking-select mt-2">
                        @foreach ($availableStatuses as $status)
                            <option value="{{ $status }}" @selected($customTripRequest->status === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    Save Status
                </button>
            </form>

            @if ($whatsappUrl)
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    class="mt-3 flex w-full items-center justify-center border border-white/18 px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-white transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                >
                    Open WhatsApp
                </a>
            @endif

            <form
                action="{{ route('admin.custom-trip-requests.destroy', $customTripRequest) }}"
                method="POST"
                class="mt-3"
                onsubmit="return confirm('Delete custom trip request {{ $customTripRequest->booking_code }}? This cannot be undone.');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="flex w-full items-center justify-center border border-red-300 px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-red-200 transition hover:bg-red-500 hover:text-white"
                >
                    Delete Request
                </button>
            </form>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Created At</p>
            <p class="mt-3 text-sm leading-7 text-gray-600">{{ $customTripRequest->created_at?->format('d M Y, H:i') }}</p>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Last Updated</p>
            <p class="mt-3 text-sm leading-7 text-gray-600">{{ $customTripRequest->updated_at?->format('d M Y, H:i') }}</p>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">Flow Separation</p>
            <p class="mt-3 text-sm leading-7 text-gray-600">
                This request belongs to the Custom Trip V2 flow. The selected vehicle is a preference only; the route, transport arrangement, availability, and quotation must be confirmed manually.
            </p>
        </div>
    </aside>
</div>
@endsection
