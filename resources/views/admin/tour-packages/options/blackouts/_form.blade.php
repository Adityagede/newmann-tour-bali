@php
    $isEdit = isset($tourOptionBlackoutDate)
        && $tourOptionBlackoutDate instanceof \App\Models\TourOptionBlackoutDate
        && $tourOptionBlackoutDate->exists;

    $formAction = $isEdit
        ? route(
            'admin.tour-packages.options.blackouts.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionBlackoutDate' =>
                    $tourOptionBlackoutDate,
            ]
        )
        : route(
            'admin.tour-packages.options.blackouts.store',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        );

    $dateValue = old(
        'blackout_date',
        $isEdit && $tourOptionBlackoutDate->blackout_date
            ? $tourOptionBlackoutDate
                ->blackout_date
                ->format('Y-m-d')
            : ''
    );

    $timeValue = old(
        'start_time',
        $isEdit && $tourOptionBlackoutDate->start_time
            ? substr(
                (string) $tourOptionBlackoutDate->start_time,
                0,
                5
            )
            : ''
    );

    $blocksEntireDay = filter_var(
        old(
            'blocks_entire_day',
            $isEdit
                ? (bool) $tourOptionBlackoutDate
                    ->blocks_entire_day
                : true
        ),
        FILTER_VALIDATE_BOOLEAN
    );

    $isActive = filter_var(
        old(
            'is_active',
            $isEdit
                ? (bool) $tourOptionBlackoutDate->is_active
                : true
        ),
        FILTER_VALIDATE_BOOLEAN
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit
                ? 'Edit Blackout Date'
                : 'Add Blackout Date' }}
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $tourOption->title }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            Product:
            <strong>{{ $tourPackage->title }}</strong>.
            Close an entire date or one scheduled starting time
            without deleting the recurring operating schedule.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.options.blackouts.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to blackouts
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the blackout data.
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
                    Date and scope
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Block availability
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="blackout_date"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Blackout date
                    </label>

                    <input
                        id="blackout_date"
                        type="date"
                        name="blackout_date"
                        required
                        value="{{ $dateValue }}"
                        class="booking-input mt-2"
                    >
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
                        value="{{ $timeValue }}"
                        class="booking-input mt-2"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Required only for a specific-time blackout.
                        Ignored when the entire day is blocked.
                    </p>
                </div>
            </div>

            <input
                type="hidden"
                name="blocks_entire_day"
                value="0"
            >

            <label class="mt-6 flex items-start gap-3 border border-newman-gold/30 bg-newman-sand p-4">
                <input
                    type="checkbox"
                    name="blocks_entire_day"
                    value="1"
                    class="mt-1 h-4 w-4 accent-newman-gold"
                    @checked($blocksEntireDay)
                >

                <span>
                    <span class="block text-sm font-semibold text-newman-navy">
                        Block the entire day
                    </span>

                    <span class="mt-1 block text-xs leading-5 text-gray-500">
                        Every starting time belonging to this option
                        will be unavailable on the selected date.
                    </span>
                </span>
            </label>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Guest-facing reason
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Availability explanation
                </h2>
            </div>

            <div class="mt-6">
                <label
                    for="reason"
                    class="text-sm font-semibold text-newman-navy"
                >
                    Reason
                </label>

                <textarea
                    id="reason"
                    name="reason"
                    rows="4"
                    maxlength="500"
                    class="booking-input mt-2 resize-y"
                    placeholder="Example: Not operating on this date."
                >{{ old(
                    'reason',
                    $isEdit
                        ? $tourOptionBlackoutDate->reason
                        : ''
                ) }}</textarea>

                <p class="mt-2 text-xs leading-6 text-gray-500">
                    Keep this neutral and factual. Detailed private
                    operational information belongs in the internal note.
                </p>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Internal administration
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Private note
                </h2>
            </div>

            <div class="mt-6">
                <label
                    for="internal_note"
                    class="text-sm font-semibold text-newman-navy"
                >
                    Internal note
                </label>

                <textarea
                    id="internal_note"
                    name="internal_note"
                    rows="6"
                    maxlength="2000"
                    class="booking-input mt-2 resize-y"
                    placeholder="Only visible to Newman admin."
                >{{ old(
                    'internal_note',
                    $isEdit
                        ? $tourOptionBlackoutDate->internal_note
                        : ''
                ) }}</textarea>
            </div>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="bg-newman-navy p-5 text-white sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Blackout status
            </p>

            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <label class="mt-6 flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="mt-1 h-4 w-4 accent-newman-gold"
                    @checked($isActive)
                >

                <span>
                    <span class="block text-sm font-semibold">
                        Active blackout
                    </span>

                    <span class="mt-1 block text-xs leading-5 text-white/55">
                        Inactive blackouts remain stored but are
                        ignored by the Availability Service.
                    </span>
                </span>
            </label>

            <button
                type="submit"
                class="mt-5 w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
            >
                {{ $isEdit
                    ? 'Update blackout'
                    : 'Add blackout' }}
            </button>
        </section>

        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Operating rule
            </p>

            <div class="mt-4 space-y-3 text-xs leading-6 text-gray-600">
                <p>
                    Full-day blackout overrides every starting time
                    on the selected date.
                </p>

                <p>
                    A specific-time blackout must match an active
                    schedule for that weekday and time.
                </p>

                <p>
                    Deleting a blackout does not delete the weekly
                    operating schedule.
                </p>
            </div>
        </section>
    </aside>
</form>