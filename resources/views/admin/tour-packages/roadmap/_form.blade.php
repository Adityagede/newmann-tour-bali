@php
    $isEdit = isset($tourStop)
        && $tourStop instanceof \App\Models\TourStop
        && $tourStop->exists;

    $action = $isEdit
        ? route(
            'admin.tour-packages.roadmap.update',
            [
                'tourPackage' => $tourPackage,
                'tourStop' => $tourStop,
            ]
        )
        : route(
            'admin.tour-packages.roadmap.store',
            $tourPackage
        );

    $scheduledTime = old(
        'scheduled_time',
        $isEdit && $tourStop->scheduled_time
            ? substr(
                (string) $tourStop->scheduled_time,
                0,
                5
            )
            : ''
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit ? 'Edit Roadmap Stop' : 'Add Roadmap Stop' }}
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $tourPackage->title }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            This stop belongs to the shared product roadmap
            and can be used by every booking option.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.roadmap.index',
            $tourPackage
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to roadmap
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the roadmap data.
        </p>

        <ul class="mt-3 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $action }}"
    method="POST"
    class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <main class="space-y-6">
        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Stop information
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Day number
                    </label>

                    <input
                        type="number"
                        name="day_number"
                        min="1"
                        max="30"
                        required
                        value="{{ old(
                            'day_number',
                            $isEdit
                                ? $tourStop->day_number
                                : 1
                        ) }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Stop type
                    </label>

                    <select
                        name="stop_type"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($stopTypes as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'stop_type',
                                        $isEdit
                                            ? $tourStop->stop_type
                                            : 'destination'
                                    ) === $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-newman-navy">
                        Stop title
                    </label>

                    <input
                        type="text"
                        name="title"
                        maxlength="180"
                        required
                        value="{{ old(
                            'title',
                            $isEdit
                                ? $tourStop->title
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Hotel pickup"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-newman-navy">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        maxlength="3000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Explain what happens at this stop."
                    >{{ old(
                        'description',
                        $isEdit
                            ? $tourStop->description
                            : ''
                    ) }}</textarea>
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Timing
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Scheduled time
                    </label>

                    <input
                        type="time"
                        name="scheduled_time"
                        value="{{ $scheduledTime }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Time label
                    </label>

                    <input
                        type="text"
                        name="time_label"
                        maxlength="80"
                        value="{{ old(
                            'time_label',
                            $isEdit
                                ? $tourStop->time_label
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Early morning"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Duration in minutes
                    </label>

                    <input
                        type="number"
                        name="duration_minutes"
                        min="0"
                        max="1440"
                        value="{{ old(
                            'duration_minutes',
                            $isEdit
                                ? $tourStop->duration_minutes
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="60"
                    >
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Location and map
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Location name
                    </label>

                    <input
                        type="text"
                        name="location_name"
                        maxlength="180"
                        value="{{ old(
                            'location_name',
                            $isEdit
                                ? $tourStop->location_name
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Address
                    </label>

                    <input
                        type="text"
                        name="address"
                        maxlength="500"
                        value="{{ old(
                            'address',
                            $isEdit
                                ? $tourStop->address
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Latitude
                    </label>

                    <input
                        type="number"
                        step="0.0000001"
                        name="latitude"
                        value="{{ old(
                            'latitude',
                            $isEdit
                                ? $tourStop->latitude
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="-8.409518"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Longitude
                    </label>

                    <input
                        type="number"
                        step="0.0000001"
                        name="longitude"
                        value="{{ old(
                            'longitude',
                            $isEdit
                                ? $tourStop->longitude
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="115.188919"
                    >
                </div>
            </div>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="bg-newman-navy p-5 text-white sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Visibility
            </p>

            <div class="mt-6 space-y-4">
                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_active',
                                $isEdit
                                    ? (bool) $tourStop->is_active
                                    : true
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Active stop
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Inactive stops remain stored but should not appear publicly.
                        </span>
                    </span>
                </label>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="show_on_map"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'show_on_map',
                                $isEdit
                                    ? (bool) $tourStop->show_on_map
                                    : false
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Show on map
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Latitude and longitude are required when enabled.
                        </span>
                    </span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    {{ $isEdit
                        ? 'Update roadmap stop'
                        : 'Add roadmap stop' }}
                </button>
            </div>
        </section>

        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Map safety
            </p>

            <p class="mt-3 text-xs leading-6 text-gray-600">
                Only enable the map when the coordinates have been checked.
                Pickup locations such as a guest’s hotel normally should not
                use a fixed map coordinate.
            </p>
        </section>
    </aside>
</form>