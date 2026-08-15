@extends('admin.layouts.app')

@section('content')
@php
    $booking = $rating->bookingRequest;
    $tour = $rating->tourPackage;
    $score = (int) $rating->rating;
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            Verified Trip Rating
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            Review Detail
        </h1>

        <p class="mt-3 text-sm leading-7 text-gray-500">
            Submitted for {{ $tour->title }}.
        </p>
    </div>

    <a
        href="{{ route('admin.ratings.index') }}"
        class="inline-flex min-h-11 items-center justify-center border border-newman-navy/15 bg-white px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
    >
        Back to Ratings
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-start">
    <section class="border border-newman-navy/10 bg-white p-5 sm:p-7">
        <div class="border-b border-newman-navy/10 pb-6">
            <p class="text-2xl tracking-[0.1em] text-newman-gold" aria-hidden="true">
                {{ str_repeat('★', $score) }}{{ str_repeat('☆', 5 - $score) }}
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <p class="text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    {{ number_format($score, 1) }} out of 5
                </p>

                <span class="bg-green-50 px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-green-700">
                    Verified
                </span>
            </div>
        </div>

        <div class="pt-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">
                Guest Feedback
            </p>

            @if ($rating->feedback)
                <p class="mt-4 whitespace-pre-line text-base leading-8 text-newman-navy">{{ $rating->feedback }}</p>
            @else
                <p class="mt-4 text-sm italic leading-7 text-gray-400">
                    No written feedback.
                </p>
            @endif
        </div>
    </section>

    <aside class="space-y-5">
        <section class="border border-newman-navy/10 bg-white p-5 sm:p-6">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                Trip Context
            </p>

            <dl class="mt-5 space-y-5 text-sm">
                <div>
                    <dt class="text-xs text-gray-400">Tour Package</dt>
                    <dd class="mt-1 font-semibold leading-6 text-newman-navy">{{ $tour->title }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-400">Guest</dt>
                    <dd class="mt-1 font-semibold text-newman-navy">{{ $booking->guest_name }}</dd>

                    @if ($booking->guest_email)
                        <dd class="mt-1 break-all text-gray-500">{{ $booking->guest_email }}</dd>
                    @endif

                    <dd class="mt-1 text-gray-500">{{ $booking->guest_whatsapp }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-400">Booking Code</dt>
                    <dd class="mt-1 font-semibold text-newman-navy">{{ $booking->booking_reference }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-400">Trip Date</dt>
                    <dd class="mt-1 font-semibold text-newman-navy">
                        {{ $booking->travel_date?->format('d F Y') ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-400">Submitted</dt>
                    <dd class="mt-1 font-semibold text-newman-navy">
                        {{ $rating->created_at?->format('d F Y, H:i') ?? '—' }}
                    </dd>
                </div>
            </dl>

            <a
                href="{{ route('admin.tour-booking-requests.show', $booking) }}#verified-trip-rating"
                class="mt-6 inline-flex min-h-11 w-full items-center justify-center bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-newman-gold hover:text-newman-navy focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
            >
                View Booking
            </a>
        </section>
    </aside>
</div>
@endsection
