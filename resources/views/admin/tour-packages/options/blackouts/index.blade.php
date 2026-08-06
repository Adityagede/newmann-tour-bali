@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Blackout Dates
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourOption->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Product:
                <strong>{{ $tourPackage->title }}</strong>.
                Temporarily close an entire date or one scheduled
                departure without deleting its recurring schedule.
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
                    'admin.tour-packages.options.blackouts.create',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add blackout
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-4">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                All records
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $blackouts->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Active
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Upcoming
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $upcomingCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Past
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $pastCount }}
            </p>
        </div>
    </div>

    @if ($blackouts->isEmpty())
        <section class="flex min-h-80 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center">
            <div>
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No blackout dates
                </h2>

                <p class="mt-3 max-w-lg text-sm leading-7 text-gray-500">
                    The weekly schedules remain available until a
                    matching active blackout is added.
                </p>
            </div>
        </section>
    @else
        <div class="space-y-4">
            @foreach ($blackouts as $blackout)
                @php
                    $isPast = $blackout
                        ->blackout_date
                        ->startOfDay()
                        ->lt($today);

                    $statusLabel = !$blackout->is_active
                        ? 'Inactive'
                        : (
                            $isPast
                                ? 'Past'
                                : 'Upcoming'
                        );
                @endphp

                <article class="grid gap-5 border border-gray-100 bg-white p-5 shadow-sm lg:grid-cols-[minmax(0,1fr)_230px] sm:p-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-semibold text-newman-navy">
                                {{ $blackout->blackout_date->format('l, d F Y') }}
                            </h2>

                            <span class="bg-newman-gold px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                {{ $blackout->blocks_entire_day
                                    ? 'Entire day'
                                    : 'Specific time' }}
                            </span>

                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em]
                                    {{ $statusLabel === 'Upcoming'
                                        ? 'bg-red-100 text-red-700'
                                        : (
                                            $statusLabel === 'Inactive'
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-gray-100 text-gray-500'
                                        ) }}"
                            >
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <p class="mt-4 text-2xl font-semibold text-newman-navy">
                            @if ($blackout->blocks_entire_day)
                                All starting times blocked
                            @else
                                Starting at
                                {{ substr(
                                    (string) $blackout->start_time,
                                    0,
                                    5
                                ) }}
                            @endif
                        </p>

                        @if ($blackout->reason)
                            <div class="mt-5 bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Reason
                                </p>

                                <p class="mt-2 text-sm leading-7 text-newman-navy">
                                    {{ $blackout->reason }}
                                </p>
                            </div>
                        @endif

                        @if ($blackout->internal_note)
                            <div class="mt-3 border border-amber-200 bg-amber-50 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-amber-700">
                                    Internal note
                                </p>

                                <p class="mt-2 text-sm leading-7 text-amber-900">
                                    {{ $blackout->internal_note }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="grid content-start gap-2">
                        <a
                            href="{{ route(
                                'admin.tour-packages.options.blackouts.edit',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                    'tourOptionBlackoutDate' => $blackout,
                                ]
                            ) }}"
                            class="flex items-center justify-center border border-newman-gold bg-newman-sand px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                        >
                            Edit blackout
                        </a>

                        <form
                            action="{{ route(
                                'admin.tour-packages.options.blackouts.destroy',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                    'tourOptionBlackoutDate' => $blackout,
                                ]
                            ) }}"
                            method="POST"
                            onsubmit="return confirm('Remove this blackout date?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-red-700"
                            >
                                Remove blackout
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection