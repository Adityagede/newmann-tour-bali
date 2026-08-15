@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
        Guest Feedback
    </p>

    <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
        Ratings &amp; Reviews
    </h1>

    <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-500">
        Verified guest feedback from completed Newman trips.
    </p>
</div>

<div class="grid gap-4 sm:grid-cols-3">
    <section class="border border-newman-navy/10 bg-white p-5 sm:p-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">
            Average Rating
        </p>

        <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy">
            @if ($summary['average_rating'] !== null)
                {{ number_format($summary['average_rating'], 1) }}
                <span class="text-base font-normal text-gray-400">/ 5</span>
            @else
                <span class="text-gray-400">—</span>
            @endif
        </p>
    </section>

    <section class="border border-newman-navy/10 bg-white p-5 sm:p-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">
            Verified Reviews
        </p>

        <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy">
            {{ number_format($summary['verified_reviews']) }}
        </p>
    </section>

    <section class="border border-newman-navy/10 bg-white p-5 sm:p-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">
            Tours Reviewed
        </p>

        <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy">
            {{ number_format($summary['tours_reviewed']) }}
        </p>
    </section>
</div>

<form
    method="GET"
    action="{{ route('admin.ratings.index') }}"
    class="mt-6 border border-newman-navy/10 bg-white p-4 sm:p-5"
>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_minmax(180px,0.7fr)_150px_160px_auto] xl:items-end">
        <div>
            <label for="rating-search" class="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                Search
            </label>

            <input
                id="rating-search"
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Guest, booking code, or tour"
                class="mt-2 min-h-11 w-full border border-newman-navy/15 bg-white px-3 py-2 text-sm text-newman-navy outline-none transition placeholder:text-gray-400 focus:border-newman-gold focus:ring-1 focus:ring-newman-gold"
            >
        </div>

        <div>
            <label for="rating-tour" class="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                Tour Package
            </label>

            <select
                id="rating-tour"
                name="tour"
                class="mt-2 min-h-11 w-full border border-newman-navy/15 bg-white px-3 py-2 text-sm text-newman-navy outline-none transition focus:border-newman-gold focus:ring-1 focus:ring-newman-gold"
            >
                <option value="">All tours</option>

                @foreach ($tourPackages as $tourPackage)
                    <option value="{{ $tourPackage->id }}" @selected($tourId === $tourPackage->id)>
                        {{ $tourPackage->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="rating-score" class="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                Rating
            </label>

            <select
                id="rating-score"
                name="rating"
                class="mt-2 min-h-11 w-full border border-newman-navy/15 bg-white px-3 py-2 text-sm text-newman-navy outline-none transition focus:border-newman-gold focus:ring-1 focus:ring-newman-gold"
            >
                <option value="">All ratings</option>

                @for ($star = 5; $star >= 1; $star--)
                    <option value="{{ $star }}" @selected($ratingValue === $star)>
                        {{ $star }} {{ $star === 1 ? 'star' : 'stars' }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <label for="rating-sort" class="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                Sort
            </label>

            <select
                id="rating-sort"
                name="sort"
                class="mt-2 min-h-11 w-full border border-newman-navy/15 bg-white px-3 py-2 text-sm text-newman-navy outline-none transition focus:border-newman-gold focus:ring-1 focus:ring-newman-gold"
            >
                <option value="newest" @selected($sort === 'newest')>Newest</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                <option value="highest" @selected($sort === 'highest')>Highest</option>
                <option value="lowest" @selected($sort === 'lowest')>Lowest</option>
            </select>
        </div>

        <div class="flex gap-2 md:col-span-2 xl:col-span-1">
            <button
                type="submit"
                class="inline-flex min-h-11 flex-1 items-center justify-center bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-newman-gold hover:text-newman-navy focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
            >
                Apply
            </button>

            @if ($search !== '' || $tourId !== null || $ratingValue !== null || $sort !== 'newest')
                <a
                    href="{{ route('admin.ratings.index') }}"
                    class="inline-flex min-h-11 items-center justify-center border border-newman-navy/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-sand focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                >
                    Clear
                </a>
            @endif
        </div>
    </div>
</form>

<div class="mt-6 space-y-4">
    @forelse ($ratings as $rating)
        @php
            $booking = $rating->bookingRequest;
            $tour = $rating->tourPackage;
            $score = (int) $rating->rating;
        @endphp

        <article class="border border-newman-navy/10 bg-white p-5 sm:p-6">
            <div class="grid gap-5 lg:grid-cols-[150px_minmax(0,1fr)_auto] lg:items-start">
                <div>
                    <p class="text-xl tracking-[0.08em] text-newman-gold" aria-hidden="true">
                        {{ str_repeat('★', $score) }}{{ str_repeat('☆', 5 - $score) }}
                    </p>

                    <p class="mt-2 text-sm font-semibold text-newman-navy">
                        {{ number_format($score, 1) }} out of 5
                    </p>

                    <p class="mt-2 inline-flex bg-green-50 px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-green-700">
                        Verified
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-gold">
                        {{ $tour->title }}
                    </p>

                    <h2 class="mt-2 text-xl font-semibold tracking-[-0.025em] text-newman-navy">
                        {{ $booking->guest_name }}
                    </h2>

                    <dl class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500">
                        <div>
                            <dt class="sr-only">Booking code</dt>
                            <dd>{{ $booking->booking_reference }}</dd>
                        </div>

                        <div>
                            <dt class="sr-only">Trip date</dt>
                            <dd>Trip {{ $booking->travel_date?->format('d M Y') ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt class="sr-only">Submitted date</dt>
                            <dd>Submitted {{ $rating->created_at?->format('d M Y') ?? '—' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 border-l-2 border-newman-gold pl-4">
                        @if ($rating->feedback)
                            <p class="text-sm leading-7 text-newman-navy">
                                {{ \Illuminate\Support\Str::limit($rating->feedback, 180) }}
                            </p>
                        @else
                            <p class="text-sm italic leading-7 text-gray-400">
                                No written feedback.
                            </p>
                        @endif
                    </div>
                </div>

                <a
                    href="{{ route('admin.ratings.show', $rating) }}"
                    class="inline-flex min-h-11 items-center justify-center border border-newman-navy/15 px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                >
                    View Review
                </a>
            </div>
        </article>
    @empty
        <section class="border border-newman-navy/10 bg-white px-6 py-14 text-center sm:py-16">
            <p class="text-xl font-semibold tracking-[-0.025em] text-newman-navy">
                No verified ratings yet.
            </p>

            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-gray-500">
                Guest feedback will appear here after completed trips receive a rating.
            </p>
        </section>
    @endforelse
</div>

@if ($ratings->hasPages())
    <div class="mt-6">
        {{ $ratings->links() }}
    </div>
@endif
@endsection
