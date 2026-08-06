@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Tour Options
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourPackage->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Create the bookable choices available inside this tour.
                Vehicle names should not be used as option titles.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ route(
                    'admin.tour-packages.edit',
                    $tourPackage
                ) }}"
                class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
            >
                Back to product
            </a>

            <a
                href="{{ route(
                    'admin.tour-packages.options.create',
                    $tourPackage
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add tour option
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

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                All options
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $options->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                Active
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                Draft
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $draftCount }}
            </p>
        </div>
    </div>

    @if ($options->isEmpty())
        <section class="flex min-h-80 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center">
            <div>
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No booking options yet
                </h2>

                <p class="mt-3 max-w-lg text-sm leading-7 text-gray-500">
                    Create the first draft option, then configure
                    participant prices, inclusions, schedules, and availability.
                </p>
            </div>
        </section>
    @else
        <div class="space-y-4">
            @foreach ($options as $tourOption)
                <article class="grid gap-5 border border-gray-100 bg-white p-5 shadow-sm lg:grid-cols-[minmax(0,1fr)_260px] sm:p-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-semibold text-newman-navy">
                                {{ $tourOption->title }}
                            </h2>

                            @if ($tourOption->is_default)
                                <span class="bg-newman-gold px-2 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-newman-navy">
                                    Default
                                </span>
                            @endif

                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-[0.14em]
                                    {{ $tourOption->status === 'active'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : (
                                            $tourOption->status === 'inactive'
                                                ? 'bg-gray-100 text-gray-600'
                                                : 'bg-amber-100 text-amber-700'
                                        ) }}"
                            >
                                {{ $tourOption->status }}
                            </span>
                        </div>

                        @if ($tourOption->short_description)
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-600">
                                {{ $tourOption->short_description }}
                            </p>
                        @endif

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                    Duration
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $tourOption->duration_minutes
                                        ? $tourOption->duration_minutes . ' minutes'
                                        : 'Not set' }}
                                </p>
                            </div>

                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                    Group
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $tourOption->min_guests }}
                                    –
                                    {{ $tourOption->max_guests ?? 'No limit' }}
                                </p>
                            </div>

                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                    Prices
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $tourOption->prices_count }}
                                </p>
                            </div>

                            <div class="bg-newman-sand/60 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                    Active schedules
                                </p>

                                <p class="mt-2 text-sm font-semibold text-newman-navy">
                                    {{ $tourOption->active_schedules_count }}
                                </p>
                            </div>
                        </div>

                        @if (is_array($tourOption->languages) && $tourOption->languages !== [])
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($tourOption->languages as $language)
                                    <span class="border border-newman-navy/10 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                        {{ $language }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="grid content-start grid-cols-2 gap-2">
                        <form
                            action="{{ route(
                                'admin.tour-packages.options.move',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
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
                                'admin.tour-packages.options.move',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
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
                                'admin.tour-packages.options.edit',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                ]
                            ) }}"
                            class="col-span-2 flex items-center justify-center bg-newman-navy px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
                        >
                            Edit option
                        </a>

                        <form
                            action="{{ route(
                                'admin.tour-packages.options.destroy',
                                [
                                    'tourPackage' => $tourPackage,
                                    'tourOption' => $tourOption,
                                ]
                            ) }}"
                            method="POST"
                            class="col-span-2"
                            onsubmit="return confirm('Archive this tour option?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-red-700 disabled:cursor-not-allowed disabled:opacity-40"
                                @disabled($tourOption->status === 'active')
                            >
                                Archive option
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection