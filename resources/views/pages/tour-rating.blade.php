@extends('layouts.app')

@section('content')
    @php
        $tourTitle = $booking->tourPackage?->title
            ?? data_get($booking->tour_snapshot, 'title')
            ?? 'Your Bali trip';

        $tourImage = \App\Support\TourViewData::imageUrl(
            $booking->tourPackage?->main_image
        );

        $ratingWasReceived = $rating !== null;
    @endphp

    <main class="min-h-screen bg-[#f6f1e7] pb-16 pt-28 text-newman-navy sm:pb-20 sm:pt-32 lg:pb-24 lg:pt-36">
        <div class="mx-auto w-[calc(100%-2rem)] max-w-6xl sm:w-[92%]">
            <div class="grid overflow-hidden border border-newman-navy/10 bg-white lg:grid-cols-[0.88fr_1.12fr]">
                <div class="relative min-h-64 overflow-hidden bg-newman-navy sm:min-h-80 lg:min-h-[620px]">
                    <img
                        src="{{ $tourImage }}"
                        alt="A moment from {{ $tourTitle }}"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-newman-navy/80 via-newman-navy/10 to-transparent"></div>

                    <div class="absolute inset-x-0 bottom-0 p-6 text-white sm:p-8 lg:p-10">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                            A moment from your Bali trip
                        </p>

                        <p class="mt-3 max-w-md text-2xl font-semibold leading-tight tracking-[-0.03em] sm:text-3xl">
                            {{ $tourTitle }}
                        </p>

                        <p class="mt-3 text-sm text-white/70">
                            {{ $booking->travel_date?->format('d F Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex min-w-0 items-center px-5 py-10 sm:px-10 sm:py-14 lg:px-14 lg:py-16">
                    <div class="w-full max-w-xl">
                        @if ($ratingWasReceived)
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                                Rating received
                            </p>

                            <h1 class="mt-4 text-4xl font-semibold leading-[1.05] tracking-[-0.045em] sm:text-5xl">
                                Thank you.
                            </h1>

                            <p class="mt-5 max-w-lg text-base leading-8 text-gray-600 sm:text-lg">
                                Your rating helps Newman keep every Bali trip personal, thoughtful and memorable.
                            </p>

                            <div class="mt-8 border-y border-newman-navy/10 py-6">
                                <p class="text-sm text-gray-500">Your verified rating</p>

                                <p class="mt-2 text-3xl font-semibold tracking-[-0.03em]">
                                    <span class="text-newman-gold" aria-hidden="true">★</span>
                                    {{ $rating->rating }}/5
                                </p>

                                @if ($rating->feedback)
                                    <div class="mt-6 border-l-2 border-newman-gold pl-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">
                                            Your private review
                                        </p>

                                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-newman-navy">
                                            {{ $rating->feedback }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <a
                                href="{{ route('tours') }}"
                                class="mt-8 inline-flex min-h-12 items-center justify-center border-b border-newman-navy pb-1 text-sm font-semibold text-newman-navy transition hover:border-newman-gold hover:text-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-4"
                            >
                                Explore more Bali journeys
                            </a>
                        @else
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-newman-gold">
                                Trip completed
                            </p>

                            <h1 class="mt-4 text-4xl font-semibold leading-[1.05] tracking-[-0.045em] sm:text-5xl">
                                How was your day with Newman?
                            </h1>

                            <p class="mt-5 max-w-lg text-base leading-8 text-gray-600">
                                Choose the stars that feel right. Your note is optional and stays private with Newman.
                            </p>

                            <form
                                method="POST"
                                action="{{ request()->fullUrl() }}"
                                class="mt-8"
                                x-data="{
                                    rating: Number('{{ old('rating', 0) }}') || 0,
                                    hoverRating: 0
                                }"
                            >
                                @csrf

                                <fieldset>
                                    <legend class="text-sm font-semibold text-newman-navy">
                                        Your star rating
                                    </legend>

                                    <div
                                        class="mt-3 flex w-fit gap-1 sm:gap-2"
                                        @mouseleave="hoverRating = 0"
                                    >
                                        @for ($star = 1; $star <= 5; $star++)
                                            <label
                                                for="rating-{{ $star }}"
                                                class="cursor-pointer"
                                                @mouseenter="hoverRating = {{ $star }}"
                                            >
                                                <input
                                                    id="rating-{{ $star }}"
                                                    type="radio"
                                                    name="rating"
                                                    value="{{ $star }}"
                                                    class="peer sr-only"
                                                    x-model.number="rating"
                                                    @checked((int) old('rating') === $star)
                                                >

                                                <span
                                                    class="flex h-12 w-12 items-center justify-center text-[2.35rem] leading-none transition-colors peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-newman-blue sm:h-14 sm:w-14 sm:text-[2.75rem]"
                                                    :class="(hoverRating || rating) >= {{ $star }}
                                                        ? 'text-newman-gold'
                                                        : 'text-newman-navy/20'"
                                                    aria-hidden="true"
                                                >★</span>

                                                <span class="sr-only">
                                                    {{ $star }} {{ $star === 1 ? 'star' : 'stars' }}
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                </fieldset>

                                @error('rating')
                                    <p class="mt-3 text-sm font-medium text-red-700" role="alert">
                                        {{ $message }}
                                    </p>
                                @enderror

                                <div class="mt-8">
                                    <label
                                        for="feedback"
                                        class="block text-sm font-semibold text-newman-navy"
                                    >
                                        Your private review for Newman
                                        <span class="font-normal text-gray-400">Optional</span>
                                    </label>

                                    <textarea
                                        id="feedback"
                                        name="feedback"
                                        rows="4"
                                        maxlength="1000"
                                        class="mt-3 w-full resize-y border border-newman-navy/15 bg-white px-4 py-4 text-base leading-7 text-newman-navy outline-none transition placeholder:text-gray-400 focus:border-newman-gold focus:ring-1 focus:ring-newman-gold"
                                        placeholder="Share what you enjoyed or what Newman could improve"
                                    >{{ old('feedback') }}</textarea>

                                    @error('feedback')
                                        <p class="mt-2 text-sm font-medium text-red-700" role="alert">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <button
                                    type="submit"
                                    class="mt-6 inline-flex min-h-12 w-full items-center justify-center bg-newman-navy px-7 py-3 text-sm font-semibold text-white transition hover:bg-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2 sm:w-auto"
                                >
                                    Send rating
                                </button>

                                <p class="mt-5 text-xs leading-6 text-gray-400">
                                    One verified rating is accepted for this completed booking. Private feedback is not published on the website.
                                </p>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
