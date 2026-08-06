@php
    $isEdit = isset($tourOptionSchedule)
        && $tourOptionSchedule instanceof \App\Models\TourOptionSchedule
        && $tourOptionSchedule->exists;

    $formAction = $isEdit
        ? route(
            'admin.tour-packages.options.schedules.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionSchedule' =>
                    $tourOptionSchedule,
            ]
        )
        : route(
            'admin.tour-packages.options.schedules.store',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        );

    $timeValue = static function (
        mixed $value
    ): string {
        if ($value === null || $value === '') {
            return '';
        }

        return substr((string) $value, 0, 5);
    };

    $dateValue = static function (
        mixed $value
    ): string {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return substr((string) $value, 0, 10);
    };

    $currentDay = old(
        'day_of_week',
        $isEdit
            ? $tourOptionSchedule->day_of_week
            : 1
    );

    $startTime = old(
        'start_time',
        $isEdit
            ? $timeValue(
                $tourOptionSchedule->start_time
            )
            : '06:00'
    );

    $endTime = old(
        'end_time',
        $isEdit
            ? $timeValue(
                $tourOptionSchedule->end_time
            )
            : ''
    );

    $availableFrom = old(
        'available_from',
        $isEdit
            ? $dateValue(
                $tourOptionSchedule->available_from
            )
            : ''
    );

    $availableUntil = old(
        'available_until',
        $isEdit
            ? $dateValue(
                $tourOptionSchedule->available_until
            )
            : ''
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit
                ? 'Edit Operating Schedule'
                : 'Add Operating Schedule' }}
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $tourOption->title }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            Product:
            <strong>{{ $tourPackage->title }}</strong>.
            Set the operating day, starting time, capacity,
            validity period, and booking cutoff for this option.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.options.schedules.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to schedules
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the schedule data.
        </p>

        <ul class="mt-3 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $formAction }}"
    method="POST"
    class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <main class="space-y-6">
        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Day and time
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Departure schedule
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-3">
                <div>
                    <label
                        for="day_of_week"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Operating day
                    </label>

                    <select
                        id="day_of_week"
                        name="day_of_week"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($days as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    (string) $currentDay
                                    === (string) $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="start_time"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Starting time
                    </label>

                    <input
                        id="start_time"
                        type="time"
                        name="start_time"
                        required
                        value="{{ $startTime }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label
                        for="end_time"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Ending time
                    </label>

                    <input
                        id="end_time"
                        type="time"
                        name="end_time"
                        value="{{ $endTime }}"
                        class="booking-input mt-2"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Optional and used as schedule information.
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Validity period
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Date availability
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="available_from"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Available from
                    </label>

                    <input
                        id="available_from"
                        type="date"
                        name="available_from"
                        value="{{ $availableFrom }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label
                        for="available_until"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Available until
                    </label>

                    <input
                        id="available_until"
                        type="date"
                        name="available_until"
                        value="{{ $availableUntil }}"
                        class="booking-input mt-2"
                    >
                </div>
            </div>

            <p class="mt-3 text-xs leading-6 text-gray-500">
                Leave both dates empty for an ongoing weekly schedule.
                Blackout dates will be managed separately.
            </p>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Capacity and cutoff
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Booking boundaries
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="capacity"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Schedule capacity
                    </label>

                    <input
                        id="capacity"
                        type="number"
                        name="capacity"
                        min="1"
                        max="1000"
                        value="{{ old(
                            'capacity',
                            $isEdit
                                ? $tourOptionSchedule->capacity
                                : $tourOption->max_guests
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="12"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Adult, Child, and Infant all count toward capacity.
                    </p>
                </div>

                <div>
                    <label
                        for="booking_cutoff_hours"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Booking cutoff in hours
                    </label>

                    <input
                        id="booking_cutoff_hours"
                        type="number"
                        name="booking_cutoff_hours"
                        min="0"
                        max="720"
                        required
                        value="{{ old(
                            'booking_cutoff_hours',
                            $isEdit
                                ? $tourOptionSchedule
                                    ->booking_cutoff_hours
                                : 12
                        ) }}"
                        class="booking-input mt-2"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Example: 12 closes booking twelve hours
                        before departure.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="bg-newman-navy p-5 text-white sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Schedule status
            </p>

            <div class="mt-6 space-y-4">
                <input
                    type="hidden"
                    name="is_active"
                    value="0"
                >

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
                                    ? (bool) $tourOptionSchedule
                                        ->is_active
                                    : true
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Active schedule
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Only active schedules are considered by
                            the Availability Service.
                        </span>
                    </span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    {{ $isEdit
                        ? 'Update schedule'
                        : 'Add schedule' }}
                </button>
            </div>
        </section>

        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Capacity note
            </p>

            <p class="mt-3 text-xs leading-6 text-gray-600">
                This checks whether one requested group exceeds the
                configured limit. Reserved seats from other bookings
                will be deducted after booking snapshots are connected.
            </p>
        </section>
    </aside>
</form>