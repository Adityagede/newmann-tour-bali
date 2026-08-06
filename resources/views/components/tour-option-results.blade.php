@props([
    'tour',
    'preview' => false,
])

@php
    $reviewEndpoint = $preview
        ? route(
            'admin.tour-packages.preview.review.store',
            [
                'tourPackage' => $tour['id'],
            ]
        )
        : route(
            'tours.review.store',
            [
                'slug' => $tour['slug'],
            ]
        );
@endphp

<section
    id="available-tour-options"
    x-data="{

            handleOptionAction(option) {
    if (!this.isSelected(option)) {
        this.chooseOption(option);
        return;
    }

    if (!this.canContinue) {
        return;
    }

    this.$nextTick(() => {
        this.$refs.reviewForm.submit();
    });
},
    
        result: null,
        options: [],

        selectedOptionKey: null,
        selectedStartingTime: '',

        reset() {
            this.result = null;
            this.options = [];
            this.selectedOptionKey = null;
            this.selectedStartingTime = '';
        },

        loadResult(data) {
            this.reset();

            this.result = data;

            this.options = Array.isArray(
                data?.options
            )
                ? data.options
                : [];

            const defaultOption =
                this.options.find(
                    option =>
                        Boolean(
                            option.is_default
                        )
                )
                ?? this.options[0]
                ?? null;

            if (defaultOption) {
                this.chooseOption(
                    defaultOption
                );
            }

            window.setTimeout(() => {
                this.$el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }, 120);
        },

        optionKey(option) {
            return String(
                option?.option_id
                ?? option?.slug
                ?? option?.title
                ?? ''
            );
        },

        isSelected(option) {
            return this.selectedOptionKey
                === this.optionKey(option);
        },

        chooseOption(option) {
            this.selectedOptionKey =
                this.optionKey(option);

            const firstTime =
                Array.isArray(
                    option?.starting_times
                )
                    ? option
                        .starting_times[0]
                    : null;

            this.selectedStartingTime =
                firstTime?.value
                ?? '';

            this.publishSelection();
        },

        chooseStartingTime(
            option,
            time
        ) {
            this.selectedOptionKey =
                this.optionKey(option);

            this.selectedStartingTime =
                time?.value
                ?? '';

            this.publishSelection();
        },

        publishSelection() {
            window.dispatchEvent(
                new CustomEvent(
                    'tour-option-selected',
                    {
                        detail: {
                            option:
                                this.selectedOption,

                            starting_time:
                                this.selectedStartingTime,

                            request:
                                this.result,
                        },
                    }
                )
            );
        },

        continueToReview() {
    if (!this.canContinue) {
        return;
    }

    const detail = {
        option:
            this.selectedOption,

        starting_time:
            this.selectedStartingTime,

        request:
            this.result,
    };

    window.dispatchEvent(
        new CustomEvent(
            'tour-option-selected',
            {
                detail,
            }
        )
    );

    window.dispatchEvent(
        new CustomEvent(
            'tour-review-requested',
            {
                detail,
            }
        )
    );
},

        get selectedOption() {
            return this.options.find(
                option =>
                    this.isSelected(
                        option
                    )
            ) ?? null;
        },

        get hasOptions() {
            return this.options.length > 0;
        },

        get canContinue() {
            if (!this.selectedOption) {
                return false;
            }

            const times =
                this.selectedOption
                    ?.starting_times
                ?? [];

            return times.length === 0
                || this.selectedStartingTime
                    !== '';
        },
    }"
    @tour-availability-updated.window="
        loadResult($event.detail)
    "
    @tour-availability-cleared.window="
        reset()
    "
    x-cloak
    x-show="result"
    class="scroll-mt-28 border-y border-newman-navy/10 bg-newman-sand/35 py-14 sm:py-18"
>
    <div class="mx-auto w-[92%] max-w-7xl">
        <div class="flex flex-col gap-5 border-b border-newman-navy/10 pb-7 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.32em] text-newman-gold">
                    Available tour options
                </p>

                <h2 class="mt-3 max-w-3xl text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                    Choose how you want to experience this tour.
                </h2>

                <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                    Prices and starting times are calculated
                    from your selected date, language, and
                    participant group.
                </p>
            </div>

            <div
                class="rounded-2xl border border-newman-gold/30 bg-white px-5 py-4"
            >
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                    Your selection
                </p>

                <p
                    class="mt-2 text-sm font-semibold text-newman-navy"
                    x-text="result?.selection?.date_label"
                ></p>

                <p
                    class="mt-1 text-xs leading-5 text-gray-500"
                    x-text="result?.selection?.participant_label"
                ></p>
            </div>
        </div>

        {{-- No available option --}}
        <template x-if="!hasOptions">
            <div class="mt-8 rounded-[24px] border border-amber-200 bg-amber-50 p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700">
                    No option for this selection
                </p>

                <h3 class="mt-3 text-2xl font-semibold text-newman-navy">
                    Try another operating date.
                </h3>

                <p
                    class="mt-3 max-w-2xl text-sm leading-7 text-amber-900"
                    x-text="
                        result?.message
                        || 'No Tour Options are available for this date.'
                    "
                ></p>

                <p class="mt-3 text-xs leading-5 text-amber-800">
                    Your transport recommendation may still be
                    displayed because it is based on group size,
                    while Tour Option availability also depends
                    on schedule, language, blackout dates, and
                    booking cutoff.
                </p>
            </div>
        </template>

        {{-- Option cards --}}
        <div
            x-show="hasOptions"
            class="mt-8 grid gap-6 xl:grid-cols-2"
        >
            <template
                x-for="option in options"
                :key="optionKey(option)"
            >
                <article
                    class="overflow-hidden rounded-[26px] border bg-white shadow-[0_18px_55px_rgba(8,36,58,0.08)] transition duration-300"
                    :class="isSelected(option)
                        ? 'border-newman-gold ring-2 ring-newman-gold/20'
                        : 'border-newman-navy/10 hover:border-newman-gold/60'"
                >
                    <div class="border-b border-newman-navy/10 p-5 sm:p-7">
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        x-show="option.is_default"
                                        class="rounded-full bg-newman-gold px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                                    >
                                        Recommended
                                    </span>

                                    <span
                                        x-show="option.is_all_inclusive"
                                        class="rounded-full bg-newman-navy px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-white"
                                    >
                                        All-inclusive
                                    </span>
                                </div>

                                <h3
                                    class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-newman-navy"
                                    x-text="option.title"
                                ></h3>

                                <p
                                    x-show="option.short_description"
                                    class="mt-3 text-sm leading-7 text-gray-600"
                                    x-text="option.short_description"
                                ></p>
                            </div>

                            <button
                                type="button"
                                @click="chooseOption(option)"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 transition"
                                :class="isSelected(option)
                                    ? 'border-newman-gold bg-newman-gold text-newman-navy'
                                    : 'border-newman-navy/15 bg-white text-transparent'"
                                :aria-label="`Choose ${option.title}`"
                            >
                                ✓
                            </button>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-newman-sand/70 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Duration
                                </p>

                                <p
                                    class="mt-2 text-sm font-semibold text-newman-navy"
                                    x-text="option.duration_label || 'Flexible'"
                                ></p>
                            </div>

                            <div class="rounded-xl bg-newman-sand/70 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Pickup
                                </p>

                                <p
                                    class="mt-2 text-sm font-semibold text-newman-navy"
                                    x-text="option.pickup_label || 'Confirmed later'"
                                ></p>
                            </div>

                            <div class="rounded-xl bg-newman-sand/70 p-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                                    Language
                                </p>

                                <p
                                    class="mt-2 text-sm font-semibold text-newman-navy"
                                    x-text="
                                        Array.isArray(option.languages)
                                            ? option.languages.join(', ')
                                            : '—'
                                    "
                                ></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 p-5 sm:p-7 lg:grid-cols-2">
                        {{-- Included --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Included
                            </p>

                            <div class="mt-4 space-y-3">
                                <template
                                    x-for="item in option.included"
                                    :key="`included-${item.label}`"
                                >
                                    <div class="flex gap-3">
                                        <span class="mt-0.5 text-newman-gold">
                                            ✓
                                        </span>

                                        <div>
                                            <p
                                                class="text-sm font-medium text-newman-navy"
                                                x-text="item.label"
                                            ></p>

                                            <p
                                                x-show="item.details"
                                                class="mt-1 text-xs leading-5 text-gray-500"
                                                x-text="item.details"
                                            ></p>
                                        </div>
                                    </div>
                                </template>

                                <p
                                    x-show="!option.included?.length"
                                    class="text-sm text-gray-500"
                                >
                                    Inclusions will be confirmed
                                    before booking.
                                </p>
                            </div>
                        </div>

                        {{-- Excluded --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                                Not included
                            </p>

                            <div class="mt-4 space-y-3">
                                <template
                                    x-for="item in option.excluded"
                                    :key="`excluded-${item.label}`"
                                >
                                    <div class="flex gap-3">
                                        <span class="mt-0.5 text-gray-400">
                                            —
                                        </span>

                                        <div>
                                            <p
                                                class="text-sm font-medium text-newman-navy"
                                                x-text="item.label"
                                            ></p>

                                            <p
                                                x-show="item.details"
                                                class="mt-1 text-xs leading-5 text-gray-500"
                                                x-text="item.details"
                                            ></p>
                                        </div>
                                    </div>
                                </template>

                                <p
                                    x-show="!option.excluded?.length"
                                    class="text-sm text-gray-500"
                                >
                                    No exclusions have been listed.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Starting times --}}
                    <div class="border-t border-newman-navy/10 px-5 py-5 sm:px-7">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                            Starting time
                        </p>

                        <div
                            x-show="option.starting_times?.length"
                            class="mt-4 flex flex-wrap gap-2"
                        >
                            <template
                                x-for="time in option.starting_times"
                                :key="`${optionKey(option)}-${time.value}`"
                            >
                                <button
                                    type="button"
                                    @click="
                                        chooseStartingTime(
                                            option,
                                            time
                                        )
                                    "
                                    class="min-h-11 rounded-full border px-5 py-2 text-sm font-semibold transition"
                                    :class="
                                        isSelected(option)
                                        && selectedStartingTime
                                            === time.value
                                            ? 'border-newman-navy bg-newman-navy text-white'
                                            : 'border-newman-navy/15 bg-white text-newman-navy hover:border-newman-gold hover:bg-newman-sand'
                                    "
                                    x-text="time.label"
                                ></button>
                            </template>
                        </div>

                        <div
                            x-show="!option.starting_times?.length"
                            class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800"
                        >
                            No selectable starting time was returned
                            for this option.
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div class="border-t border-newman-navy/10 bg-newman-navy p-5 text-white sm:p-7">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                    Estimated total
                                </p>

                                <template
                                    x-if="option.pricing?.formatted_estimated_total"
                                >
                                    <div>
                                        <p
                                            x-show="
                                                option.pricing
                                                    ?.discount_amount
                                                > 0
                                            "
                                            class="mt-3 text-sm text-white/50 line-through"
                                            x-text="
                                                option.pricing
                                                    .formatted_base_total
                                            "
                                        ></p>

                                        <p
                                            class="mt-1 text-3xl font-bold tracking-[-0.04em]"
                                            x-text="
                                                option.pricing
                                                    .formatted_estimated_total
                                            "
                                        ></p>

                                        <p
                                            x-show="
                                                option.pricing
                                                    ?.formatted_discount
                                            "
                                            class="mt-2 text-xs font-semibold text-newman-gold"
                                        >
                                            Save
                                            <span
                                                x-text="
                                                    option.pricing
                                                        .formatted_discount
                                                "
                                            ></span>
                                        </p>
                                    </div>
                                </template>

                                <p
                                    x-show="
                                        !option.pricing
                                            ?.formatted_estimated_total
                                    "
                                    class="mt-3 text-lg font-semibold"
                                >
                                    Price confirmation required
                                </p>
                            </div>

                            <button
    type="button"
    @click="handleOptionAction(option)"
    :disabled="
        isSelected(option)
        && !canContinue
    "
    class="min-h-12 rounded-xl px-7 py-3 text-xs font-bold uppercase tracking-[0.13em] transition"
    :class="isSelected(option)
        ? 'bg-newman-gold text-newman-navy'
        : 'border border-white/25 bg-white/5 text-white hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy'"
    x-text="
        isSelected(option)
            ? (
                canContinue
                    ? 'Continue'
                    : 'Choose a time'
            )
            : 'Select'
    "
></button>
                        </div>

                        <p class="mt-4 text-xs leading-5 text-white/55">
                            Estimated price is calculated by the
                            Newman pricing service. Final confirmation
                            is completed before the booking request
                            is accepted.
                        </p>
                    </div>
                </article>
            </template>
        </div>
    </div>

    <form
    x-ref="reviewForm"
    method="POST"
    action="{{ $reviewEndpoint }}"
    class="hidden"
>
    @csrf

    <input
        type="hidden"
        name="tour_option_id"
        :value="
            selectedOption?.option_id
            ?? ''
        "
    >

    <input
        type="hidden"
        name="travel_date"
        :value="
            result?.selection?.travel_date
            ?? ''
        "
    >

    <input
        type="hidden"
        name="starting_time"
        :value="selectedStartingTime"
    >

    <input
        type="hidden"
        name="language"
        :value="
            result?.selection?.language
            ?? ''
        "
    >

    <input
        type="hidden"
        name="adults"
        :value="
            result
                ?.selection
                ?.participants
                ?.adult
            ?? 0
        "
    >

    <input
        type="hidden"
        name="children"
        :value="
            result
                ?.selection
                ?.participants
                ?.child
            ?? 0
        "
    >

    <input
        type="hidden"
        name="infants"
        :value="
            result
                ?.selection
                ?.participants
                ?.infant
            ?? 0
        "
    >
</form>
</section>






