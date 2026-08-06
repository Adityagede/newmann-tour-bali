@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Tour Roadmap
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourPackage->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Manage the shared route used as the main roadmap for this
                tour product. Option-specific routes will be supported later.
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
                    'admin.tour-packages.roadmap.create',
                    $tourPackage
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add roadmap stop
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
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                Total stops
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $stops->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                Active stops
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">
                Map locations
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $mapCount }}
            </p>
        </div>
    </div>

    @if ($stops->isEmpty())
        <section class="flex min-h-80 items-center justify-center border border-dashed border-newman-navy/20 bg-white p-8 text-center">
            <div>
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No roadmap stops yet
                </h2>

                <p class="mt-3 max-w-lg text-sm leading-7 text-gray-500">
                    Add pickup, destinations, activities, meals, and the
                    final return after the real route has been confirmed.
                </p>
            </div>
        </section>
    @else
        <div class="space-y-8">
            @foreach ($stopsByDay as $dayNumber => $dayStops)
                <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                    <div class="border-b border-gray-100 pb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                            Day {{ $dayNumber }}
                        </p>

                        <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $dayStops->count() }}
                            {{ $dayStops->count() === 1 ? 'stop' : 'stops' }}
                        </h2>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($dayStops as $tourStop)
                            <article class="grid gap-5 border border-newman-navy/10 p-5 lg:grid-cols-[110px_minmax(0,1fr)_230px]">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-gold">
                                        {{ $stopTypes[$tourStop->stop_type]
                                            ?? ucfirst($tourStop->stop_type) }}
                                    </p>

                                    <p class="mt-2 text-sm font-semibold text-newman-navy">
                                        {{ $tourStop->time_label
                                            ?: (
                                                $tourStop->scheduled_time
                                                    ? substr(
                                                        (string) $tourStop->scheduled_time,
                                                        0,
                                                        5
                                                    )
                                                    : 'Flexible'
                                            ) }}
                                    </p>
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-xl font-semibold text-newman-navy">
                                            {{ $tourStop->title }}
                                        </h3>

                                        @unless ($tourStop->is_active)
                                            <span class="bg-gray-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                                Inactive
                                            </span>
                                        @endunless

                                        @if ($tourStop->show_on_map)
                                            <span class="bg-newman-sand px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                                Map
                                            </span>
                                        @endif
                                    </div>

                                    @if ($tourStop->location_name)
                                        <p class="mt-2 text-sm font-medium text-newman-blue">
                                            {{ $tourStop->location_name }}
                                        </p>
                                    @endif

                                    @if ($tourStop->description)
                                        <p class="mt-3 text-sm leading-7 text-gray-600">
                                            {{ $tourStop->description }}
                                        </p>
                                    @endif

                                    @if ($tourStop->duration_minutes)
                                        <p class="mt-3 text-xs text-gray-500">
                                            Duration:
                                            {{ $tourStop->duration_minutes }}
                                            minutes
                                        </p>
                                    @endif
                                </div>

                                <div class="grid content-start grid-cols-2 gap-2">
                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.roadmap.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourStop' => $tourStop,
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
                                            'admin.tour-packages.roadmap.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourStop' => $tourStop,
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
                                            'admin.tour-packages.roadmap.edit',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourStop' => $tourStop,
                                            ]
                                        ) }}"
                                        class="flex items-center justify-center border border-newman-gold bg-newman-sand px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.roadmap.destroy',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourStop' => $tourStop,
                                            ]
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove this roadmap stop?')"
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