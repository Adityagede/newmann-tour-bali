@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Tour Option Discounts
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourOption->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Product:
                <strong>{{ $tourPackage->title }}</strong>.
                Only one currently valid discount with the highest
                priority is used for each price calculation.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ route(
                    'admin.tour-packages.options.edit',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
            >
                Back to option
            </a>

            <a
                href="{{ route(
                    'admin.tour-packages.options.discounts.create',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add discount
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                All discounts
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $discounts->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Active records
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Currently valid
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $currentCount }}
            </p>
        </div>
    </div>

    @if ($discounts->isEmpty())
        <section class="flex min-h-80 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center">
            <div>
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No discounts yet
                </h2>

                <p class="mt-3 max-w-lg text-sm leading-7 text-gray-500">
                    Base prices remain valid without discounts.
                    Add an offer only when its value and period
                    have been approved.
                </p>
            </div>
        </section>
    @else
        <div class="space-y-4">
            @foreach ($discounts as $discount)
                @php
                    $isScheduled =
                        $discount->is_active
                        && $discount->starts_at
                        && $discount->starts_at->gt($now);

                    $isExpired =
                        $discount->ends_at
                        && $discount->ends_at->lt($now);

                    $isCurrent =
                        $discount->is_active
                        && !$isScheduled
                        && !$isExpired;

                    $targets = is_array(
                        $discount->participant_types
                    )
                        ? $discount->participant_types
                        : [];
                @endphp

                <article class="grid gap-5 border border-gray-100 bg-white p-5 shadow-sm lg:grid-cols-[minmax(0,1fr)_230px] sm:p-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-semibold text-newman-navy">
                                {{ $discount->label }}
                            </h2>

                            <span class="bg-newman-gold px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                Priority {{ $discount->priority }}
                            </span>

                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em]
                                    {{ $isCurrent
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : (
                                            $isScheduled
                                                ? 'bg-blue-100 text-blue-700'
                                                : (
                                                    $isExpired
                                                        ? 'bg-gray-100 text-gray-500'
                                                        : 'bg-amber-100 text-amber-700'
                                                )
                                        ) }}"
                            >
                                @if ($isCurrent)
                                    Current
                                @elseif ($isScheduled)
                                    Scheduled
                                @elseif ($isExpired)
                                    Expired
                                @else
                                    Inactive
                                @endif
                            </span>
                        </div>

                        <p class="mt-4 text-2xl font-semibold text-newman-navy">
                            @if ($discount->discount_type === 'percentage')
                                {{ $discount->discount_value }}%
                            @else
                                IDR {{ number_format(
                                    $discount->discount_value,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                                per participant
                            @endif
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2">
                            @if ($targets === [])
                                <span class="border border-newman-navy/10 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                    All paid participants
                                </span>
                            @else
                                @foreach ($targets as $target)
                                    <span class="border border-newman-navy/10 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                        {{ $participantTypes[$target]
                                            ?? ucfirst($target) }}
                                    </span>
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Starts
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $discount->starts_at
                                        ? $discount->starts_at->format('Y-m-d H:i')
                                        : 'Immediately' }}
                                </p>
                            </div>

                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Ends
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $discount->ends_at
                                        ? $discount->ends_at->format('Y-m-d H:i')
                                        : 'No ending date' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid content-start gap-2">
                        <a
                            href="{{ route(
                                'admin.tour-packages.options.discounts.edit',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                    'tourOptionDiscount' => $discount,
                                ]
                            ) }}"
                            class="flex items-center justify-center border border-newman-gold bg-newman-sand px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                        >
                            Edit discount
                        </a>

                        <form
                            action="{{ route(
                                'admin.tour-packages.options.discounts.destroy',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                    'tourOptionDiscount' => $discount,
                                ]
                            ) }}"
                            method="POST"
                            onsubmit="return confirm('Remove this discount?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-red-700"
                            >
                                Remove discount
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection