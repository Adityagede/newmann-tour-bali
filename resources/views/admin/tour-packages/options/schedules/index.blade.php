@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Operating Schedules
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourOption->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Product:
                <strong>{{ $tourPackage->title }}</strong>.
                Manage weekly operating days and starting times
                used by the Availability Service.
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
                    'admin.tour-packages.options.schedules.create',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add schedule
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                All schedules
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $schedules->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Active schedules
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeCount }}
            </p>
        </div>
    </div>

    @if ($schedules->isEmpty())
        <section class="flex min-h-80 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center">
            <div>
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No operating schedules yet
                </h2>

                <p class="mt-3 max-w-lg text-sm leading-7 text-gray-500">
                    Add at least one active day and starting time
                    before activating this Tour Option.
                </p>
            </div>
        </section>
    @else
        <div class="space-y-8">
            @foreach ($schedulesByDay as $dayNumber => $daySchedules)
                <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                    <div class="border-b border-gray-100 pb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                            Operating day
                        </p>

                        <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $days[(int) $dayNumber]
                                ?? 'Unknown day' }}
                        </h2>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($daySchedules as $schedule)
                            <article class="grid gap-5 border border-newman-navy/10 p-5 lg:grid-cols-[minmax(0,1fr)_230px]">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-xl font-semibold text-newman-navy">
                                            {{ substr(
                                                (string) $schedule->start_time,
                                                0,
                                                5
                                            ) }}

                                            @if ($schedule->end_time)
                                                –
                                                {{ substr(
                                                    (string) $schedule->end_time,
                                                    0,
                                                    5
                                                ) }}
                                            @endif
                                        </h3>

                                        <span
                                            class="px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em]
                                                {{ $schedule->is_active
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-gray-100 text-gray-600' }}"
                                        >
                                            {{ $schedule->is_active
                                                ? 'Active'
                                                : 'Inactive' }}
                                        </span>
                                    </div>

                                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                        <div class="bg-newman-sand/60 p-4">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                                Capacity
                                            </p>

                                            <p class="mt-2 text-sm font-semibold text-newman-navy">
                                                {{ $schedule->capacity
                                                    ?? 'Option limit' }}
                                            </p>
                                        </div>

                                        <div class="bg-newman-sand/60 p-4">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                                Cutoff
                                            </p>

                                            <p class="mt-2 text-sm font-semibold text-newman-navy">
                                                {{ $schedule->booking_cutoff_hours }}
                                                hours
                                            </p>
                                        </div>

                                        <div class="bg-newman-sand/60 p-4">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                                Validity
                                            </p>

                                            <p class="mt-2 text-sm font-semibold text-newman-navy">
                                                @if (
                                                    $schedule->available_from
                                                    || $schedule->available_until
                                                )
                                                    {{ $schedule->available_from
                                                        ? $schedule->available_from->format('Y-m-d')
                                                        : 'Open' }}
                                                    –
                                                    {{ $schedule->available_until
                                                        ? $schedule->available_until->format('Y-m-d')
                                                        : 'Open' }}
                                                @else
                                                    Ongoing
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid content-start grid-cols-2 gap-2">
                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.schedules.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionSchedule' => $schedule,
                                            ]
                                        ) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="direction"
                                            value="up"
                                        >

                                        <button
                                            type="submit"
                                            @disabled($loop->first)
                                            class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy disabled:opacity-30"
                                        >
                                            Earlier
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.schedules.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionSchedule' => $schedule,
                                            ]
                                        ) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="direction"
                                            value="down"
                                        >

                                        <button
                                            type="submit"
                                            @disabled($loop->last)
                                            class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy disabled:opacity-30"
                                        >
                                            Later
                                        </button>
                                    </form>

                                    <a
                                        href="{{ route(
                                            'admin.tour-packages.options.schedules.edit',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionSchedule' => $schedule,
                                            ]
                                        ) }}"
                                        class="flex items-center justify-center border border-newman-gold bg-newman-sand px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.schedules.destroy',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionSchedule' => $schedule,
                                            ]
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove this operating schedule?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-red-700"
                                        >
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
@endsection