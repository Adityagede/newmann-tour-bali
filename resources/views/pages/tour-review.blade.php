@extends('layouts.app')

@section('content')
    @php
        $tour = $review['tour'];
        $option = $review['option'];
        $selection = $review['selection'];
        $pricing = $review['pricing'];

        $currency = $pricing['currency']
            ?? 'IDR';

        $money = static fn (
            int $amount
        ): string =>
            $currency
            . ' '
            . number_format(
                $amount,
                0,
                ',',
                '.'
            );

        $transport =
            $review['recommended_transport']
            ?? null;

        $transportLabel = is_string(
            $transport
        )
            ? $transport
            : (
                $transport['label']
                ?? $transport['vehicle_name']
                ?? $transport['name']
                ?? 'Confirmed after request'
            );

        $backUrl = $isPreview
            ? route(
                'admin.tour-packages.preview',
                [
                    'tourPackage' =>
                        $tour['id'],
                ]
            )
            : route(
                'tours.detail',
                [
                    'slug' =>
                        $tour['slug'],
                ]
            );

            $bookingRequestEndpoint = route(
    'tours.booking-request.store',
    [
        'slug' => $tour['slug'],
    ]
);
    @endphp

    @if ($isPreview)
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-center text-xs font-semibold text-amber-800">
            Admin preview. This review has not created
            a Booking Request.
        </div>
    @endif

    <main class="bg-newman-sand/30 py-12 sm:py-16">
        <div class="mx-auto w-[92%] max-w-7xl">
            <a
                href="{{ $backUrl }}"
                class="text-xs font-bold uppercase tracking-[0.15em] text-newman-navy hover:text-newman-gold"
            >
                ← Back to tour options
            </a>

            <header class="mt-8 border-b border-newman-navy/10 pb-8">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Review your trip
                </p>

                <h1 class="mt-3 max-w-4xl text-4xl font-semibold tracking-[-0.045em] text-newman-navy sm:text-6xl">
                    Check every detail before sending
                    your request.
                </h1>

                <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                    Availability and price have been checked
                    again by Newman before opening this page.
                </p>
            </header>

            @if ($errors->any())
    <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-red-700">
            Please check the Booking Request.
        </p>

        <ul class="mt-3 space-y-2 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-[26px] border border-newman-navy/10 bg-white">
                        <div class="bg-newman-navy p-6 text-white sm:p-8">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Selected experience
                            </p>

                            <h2 class="mt-3 text-3xl font-semibold">
                                {{ $option['title'] }}
                            </h2>

                            <p class="mt-2 text-sm text-white/60">
                                {{ $tour['title'] }}
                            </p>
                        </div>

                        <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-7">
                            <div class="rounded-2xl bg-newman-sand/70 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                                    Travel date
                                </p>

                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $selection['date_label'] }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-newman-sand/70 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                                    Starting time
                                </p>

                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $selection['starting_time'] }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-newman-sand/70 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                                    Participants
                                </p>

                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $selection['participant_label'] }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-newman-sand/70 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.13em] text-gray-400">
                                    Language
                                </p>

                                <p class="mt-2 font-semibold text-newman-navy">
                                    {{ $selection['language'] }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[24px] border border-newman-gold/30 bg-white p-6 sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                            Transport recommendation
                        </p>

                        <h2 class="mt-3 text-2xl font-semibold text-newman-navy">
                            {{ $transportLabel }}
                        </h2>

                        <p class="mt-3 text-sm leading-7 text-gray-600">
                            Adults, children, and infants all
                            count toward transport capacity.
                            Final allocation is confirmed by Newman.
                        </p>
                    </section>

                    <section class="rounded-[24px] bg-newman-navy p-6 text-white sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                            Estimated total
                        </p>

                        @if (($pricing['discount_amount'] ?? 0) > 0)
                            <p class="mt-3 text-sm text-white/45 line-through">
                                {{ $money($pricing['base_total']) }}
                            </p>
                        @endif

                        <p class="mt-2 text-4xl font-bold tracking-[-0.04em]">
                            {{ $money($pricing['estimated_total']) }}
                        </p>

                        @if (($pricing['discount_amount'] ?? 0) > 0)
                            <p class="mt-2 text-xs font-semibold text-newman-gold">
                                You save
                                {{ $money($pricing['discount_amount']) }}
                            </p>
                        @endif

                        <p class="mt-5 text-xs leading-6 text-white/55">
                            The price was recalculated by the backend.
                            Final confirmation occurs before the request
                            is accepted.
                        </p>
                    </section>
                </div>

                <aside class="rounded-[26px] border border-newman-navy/10 bg-white p-6 shadow-[0_18px_55px_rgba(8,36,58,0.08)] sm:p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                        Guest details
                    </p>

                    <h2 class="mt-3 text-2xl font-semibold text-newman-navy">
                        Who should Newman contact?
                    </h2>


                        <form
    method="POST"
    action="{{ $bookingRequestEndpoint }}"
    class="mt-6 space-y-5"
>
    @csrf

                        <div>
                            <label
                                for="guest-full-name"
                                class="text-sm font-semibold text-newman-navy"
                            >
                                Full name
                            </label>

                            <input
                                id="guest-full-name"
                                type="text"
                                name="full_name"
                                value="{{ old ('full_name') }}"
                                class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-newman-sand/40"
                                autocomplete="name"

                            >
                        </div>

                        <div>
                            <label
                                for="guest-whatsapp"
                                class="text-sm font-semibold text-newman-navy"
                            >
                                WhatsApp number
                            </label>

                            <input
                                id="guest-whatsapp"
                                type="tel"
                                name="whatsapp"
                                value="{{ old('whatsapp') }}"
                                required
                                class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-newman-sand/40"
                                autocomplete="tel"
                                placeholder="Your WhatsApp number"
                            >
                        </div>

                        <div>
                            <label
                                for="guest-email"
                                class="text-sm font-semibold text-newman-navy"
                            >
                                Email
                                <span class="font-normal text-gray-400">
                                    (optional)
                                </span>
                            </label>

                            <input
                                id="guest-email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-newman-sand/40"
                                        autocomplete="email"
                            >
                        </div>

                        <div>
                            <label
                                for="guest-pickup"
                                class="text-sm font-semibold text-newman-navy"
                            >
                                Pickup location
                            </label>

                            <textarea
                                id="guest-pickup"
                                name="pickup_address"
                                rows="3"
                                required
                                class="booking-input mt-2 rounded-xl border-newman-navy/10 bg-newman-sand/40"
                            >{{ old('pickup_address') }}
                            </textarea>
                        </div>

                        <div>
                            <label
                                for="guest-notes"
                                class="text-sm font-semibold text-newman-navy"
                            >
                                Special requests
                                <span class="font-normal text-gray-400">
                                    (optional)
                                </span>
                            </label>

                            <textarea
                              id="guest-notes"
                                name="special_requests"
                                rows="4"
                                class="booking-input mt-2 rounded-xl border-newman-navy/10 bg-newman-sand/40"
                            >{{ old('special_requests') }}
                            </textarea>
                        </div>


                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-newman-navy/10 bg-newman-sand/40 p-4">
    <input
        type="checkbox"
        name="agreement"
        value="1"
        @checked(old('agreement'))
        required
        class="mt-1 rounded border-newman-navy/20 text-newman-gold focus:ring-newman-gold"
    >

    <span class="text-sm leading-6 text-gray-600">
        I have reviewed the selected tour,
        travel date, participants, language,
        starting time, pickup information,
        and estimated price.
    </span>
</label>

                        @if ($isPreview)
    <button
        type="button"
        disabled
        class="flex min-h-14 w-full items-center justify-center rounded-xl bg-newman-navy px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-white opacity-45"
    >
        Preview only
    </button>

    <p class="text-xs leading-5 text-amber-700">
        Admin Preview does not create a real
        Booking Request.
    </p>
@else
    <button
        type="submit"
        class="flex min-h-14 w-full items-center justify-center rounded-xl bg-newman-gold px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-navy hover:text-white"
    >
        Send booking request
    </button>

    <p class="text-xs leading-5 text-gray-500">
        Sending this form creates a Booking Request
        with status Pending. No online payment is
        required at this stage.
    </p>
@endif
                    </form>
                </aside>
            </div>
        </div>
    </main>
@endsection
