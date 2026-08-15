@props([
    'tour',
])

<section
    id="review-your-trip"
    x-data="{
        visible: false,
        selection: null,
        ready: false,

        customer: {
            full_name: '',
            whatsapp: '',
            email: '',
            pickup_address: '',
            special_requests: '',
            agree: false,
        },

        errors: {},

        captureSelection(detail) {
            this.selection = detail;
            this.ready = false;
            this.errors = {};
        },

        openReview(detail) {
            this.captureSelection(detail);
            this.visible = true;

            this.$nextTick(() => {
                this.$el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            });
        },

        get requestData() {
            return this.selection?.request
                ?? null;
        },

        get selectedOption() {
            return this.selection?.option
                ?? null;
        },

        get startingTime() {
            return this.selection
                ?.starting_time
                || 'Not selected';
        },

        get requestSelection() {
            return this.requestData
                ?.selection
                ?? {};
        },

        get participants() {
            return this.requestSelection
                ?.participants
                ?? {};
        },

        get participantLabel() {
            return this.requestSelection
                ?.participant_label
                ?? '—';
        },

        get travelDate() {
            return this.requestSelection
                ?.date_label
                ?? this.requestSelection
                    ?.travel_date
                ?? '—';
        },

        get language() {
            return this.requestSelection
                ?.language
                ?? '—';
        },

        get transport() {
            return this.requestData
                ?.recommended_transport
                ?? this.requestData
                    ?.availability
                    ?.recommended_transport
                ?? null;
        },

        get transportLabel() {
            const transport =
                this.transport;

            if (!transport) {
                return 'Confirmed after request';
            }

            if (typeof transport === 'string') {
                return transport;
            }

            return transport.label
                ?? transport.vehicle_label
                ?? transport.vehicle_name
                ?? transport.name
                ?? transport.title
                ?? 'Recommended transport';
        },

        get transportQuantity() {
            const transport =
                this.transport;

            if (
                !transport
                || typeof transport
                    !== 'object'
            ) {
                return null;
            }

            return transport.quantity
                ?? transport.vehicles_required
                ?? transport.vehicle_count
                ?? transport.units
                ?? null;
        },

        get pricing() {
            return this.selectedOption
                ?.pricing
                ?? null;
        },

        get estimatedTotal() {
            return this.pricing
                ?.formatted_estimated_total
                ?? 'Price confirmation required';
        },

        get baseTotal() {
            return this.pricing
                ?.formatted_base_total
                ?? null;
        },

        get discountAmount() {
            return this.pricing
                ?.formatted_discount
                ?? null;
        },

        validateCustomer() {
            this.errors = {};

            const name =
                this.customer.full_name.trim();

            const whatsapp =
                this.customer.whatsapp.trim();

            const pickup =
                this.customer.pickup_address.trim();

            const email =
                this.customer.email.trim();

            if (!name) {
                this.errors.full_name =
                    'Please enter the guest name.';
            }

            if (!whatsapp) {
                this.errors.whatsapp =
                    'Please enter a WhatsApp number.';
            }

            if (!pickup) {
                this.errors.pickup_address =
                    'Please enter the pickup location.';
            }

            if (
                email
                && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/
                    .test(email)
            ) {
                this.errors.email =
                    'Please enter a valid email address.';
            }

            if (!this.customer.agree) {
                this.errors.agree =
                    'Please confirm that the trip details are correct.';
            }

            return Object.keys(
                this.errors
            ).length === 0;
        },

        prepareBookingRequest() {
            this.ready =
                this.validateCustomer();

            if (!this.ready) {
                return;
            }

            window.dispatchEvent(
                new CustomEvent(
                    'tour-booking-request-prepared',
                    {
                        detail:
                            this.bookingPayload,
                    }
                )
            );

            this.$nextTick(() => {
                this.$refs.readyMessage
                    ?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    });
            });
        },

        get bookingPayload() {
            return {
                tour_package_id:
                    @js($tour['id']),

                tour_slug:
                    @js($tour['slug']),

                tour_title:
                    @js($tour['title']),

                tour_option_id:
                    this.selectedOption
                        ?.option_id
                    ?? null,

                tour_option_title:
                    this.selectedOption
                        ?.title
                    ?? null,

                travel_date:
                    this.requestSelection
                        ?.travel_date
                    ?? null,

                starting_time:
                    this.selection
                        ?.starting_time
                    ?? null,

                language:
                    this.requestSelection
                        ?.language
                    ?? null,

                participants:
                    this.participants,

                recommended_transport:
                    this.transport,

                pricing:
                    this.pricing,

                customer: {
                    full_name:
                        this.customer
                            .full_name
                            .trim(),

                    whatsapp:
                        this.customer
                            .whatsapp
                            .trim(),

                    email:
                        this.customer
                            .email
                            .trim()
                        || null,

                    pickup_address:
                        this.customer
                            .pickup_address
                            .trim(),

                    special_requests:
                        this.customer
                            .special_requests
                            .trim()
                        || null,
                },
            };
        },
    }"
    @tour-option-selected.window="
        captureSelection($event.detail)
    "
    @tour-review-requested.window="
        openReview($event.detail)
    "
    x-cloak
    x-show="visible"
    class="scroll-mt-28 border-y border-newman-navy/10 bg-white py-14 sm:py-20"
>
    <div class="mx-auto w-[92%] max-w-7xl">
        {{-- Header --}}
        <div class="border-b border-newman-navy/10 pb-8">
            <p class="text-xs font-bold uppercase tracking-[0.32em] text-newman-gold">
                Review your trip
            </p>

            <h2 class="mt-3 max-w-4xl text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                Check every detail before sending your request.
            </h2>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Newman will review availability and confirm the
                final arrangement before the booking request is
                accepted.
            </p>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
            {{-- Trip summary --}}
            <div class="space-y-6">
                <section class="overflow-hidden rounded-[26px] border border-newman-navy/10 bg-white shadow-[0_18px_55px_rgba(8,36,58,0.08)]">
                    <div class="bg-newman-navy p-6 text-white sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                            Selected experience
                        </p>

                        <h3
                            class="mt-3 text-2xl font-semibold tracking-[-0.03em]"
                            x-text="selectedOption?.title"
                        ></h3>

                        <p class="mt-2 text-sm text-white/65">
                            {{ $tour['title'] }}
                        </p>
                    </div>

                    <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-7">
                        <div class="rounded-2xl bg-newman-sand/70 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Travel date
                            </p>

                            <p
                                class="mt-2 font-semibold text-newman-navy"
                                x-text="travelDate"
                            ></p>
                        </div>

                        <div class="rounded-2xl bg-newman-sand/70 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Starting time
                            </p>

                            <p
                                class="mt-2 font-semibold text-newman-navy"
                                x-text="startingTime"
                            ></p>
                        </div>

                        <div class="rounded-2xl bg-newman-sand/70 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Participants
                            </p>

                            <p
                                class="mt-2 font-semibold text-newman-navy"
                                x-text="participantLabel"
                            ></p>
                        </div>

                        <div class="rounded-2xl bg-newman-sand/70 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                                Language
                            </p>

                            <p
                                class="mt-2 font-semibold text-newman-navy"
                                x-text="language"
                            ></p>
                        </div>
                    </div>
                </section>

                {{-- Transport --}}
                <section class="rounded-[24px] border border-newman-gold/35 bg-newman-sand/35 p-5 sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                        Transport recommendation
                    </p>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3
                                class="text-xl font-semibold text-newman-navy"
                                x-text="transportLabel"
                            ></h3>

                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                Adults, children, and infants are
                                counted toward transport capacity.
                            </p>
                        </div>

                        <p
                            x-show="transportQuantity"
                            class="rounded-full bg-newman-navy px-4 py-2 text-xs font-bold uppercase tracking-[0.12em] text-white"
                        >
                            <span
                                x-text="transportQuantity"
                            ></span>

                            <span
                                x-text="
                                    Number(
                                        transportQuantity
                                    ) === 1
                                        ? 'vehicle'
                                        : 'vehicles'
                                "
                            ></span>
                        </p>
                    </div>
                </section>

                {{-- Pricing --}}
                <section class="rounded-[24px] bg-newman-navy p-6 text-white sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                                Estimated total
                            </p>

                            <p
                                x-show="discountAmount"
                                class="mt-3 text-sm text-white/45 line-through"
                                x-text="baseTotal"
                            ></p>

                            <p
                                class="mt-2 text-3xl font-bold tracking-[-0.04em]"
                                x-text="estimatedTotal"
                            ></p>

                            <p
                                x-show="discountAmount"
                                class="mt-2 text-xs font-semibold text-newman-gold"
                            >
                                Discount:
                                <span
                                    x-text="discountAmount"
                                ></span>
                            </p>
                        </div>

                        <p class="max-w-sm text-xs leading-6 text-white/55">
                            This estimate is generated by the Newman
                            Pricing Service. Final confirmation is
                            completed before acceptance.
                        </p>
                    </div>
                </section>
            </div>

            {{-- Customer details --}}
            <aside class="rounded-[26px] border border-newman-navy/10 bg-newman-sand/35 p-5 shadow-[0_18px_55px_rgba(8,36,58,0.08)] sm:p-7">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                    Guest details
                </p>

                <h3 class="mt-3 text-2xl font-semibold text-newman-navy">
                    Who should Newman contact?
                </h3>

                <div class="mt-6 space-y-5">
                    <div>
                        <label
                            for="review-full-name"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            Full name
                        </label>

                        <input
                            id="review-full-name"
                            type="text"
                            x-model="customer.full_name"
                            @input="
                                ready = false;
                                delete errors.full_name;
                            "
                            autocomplete="name"
                            class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-white"
                            placeholder="Guest name"
                        >

                        <p
                            x-show="errors.full_name"
                            x-text="errors.full_name"
                            class="mt-2 text-xs text-red-600"
                        ></p>
                    </div>

                    <div>
                        <label
                            for="review-whatsapp"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            WhatsApp number
                        </label>

                        <input
                            id="review-whatsapp"
                            type="tel"
                            x-model="customer.whatsapp"
                            @input="
                                ready = false;
                                delete errors.whatsapp;
                            "
                            autocomplete="tel"
                            class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-white"
                            placeholder="Your WhatsApp number"
                        >

                        <p
                            x-show="errors.whatsapp"
                            x-text="errors.whatsapp"
                            class="mt-2 text-xs text-red-600"
                        ></p>
                    </div>

                    <div>
                        <label
                            for="review-email"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            Email
                            <span class="font-normal text-gray-400">
                                (optional)
                            </span>
                        </label>

                        <input
                            id="review-email"
                            type="email"
                            x-model="customer.email"
                            @input="
                                ready = false;
                                delete errors.email;
                            "
                            autocomplete="email"
                            class="booking-input mt-2 min-h-13 rounded-xl border-newman-navy/10 bg-white"
                            placeholder="guest@example.com"
                        >

                        <p
                            x-show="errors.email"
                            x-text="errors.email"
                            class="mt-2 text-xs text-red-600"
                        ></p>
                    </div>

                    <div>
                        <label
                            for="review-pickup"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            Pickup location
                        </label>

                        <textarea
                            id="review-pickup"
                            x-model="customer.pickup_address"
                            @input="
                                ready = false;
                                delete errors.pickup_address;
                            "
                            rows="3"
                            class="booking-input mt-2 rounded-xl border-newman-navy/10 bg-white"
                            placeholder="Hotel, villa, or confirmed meeting location"
                        ></textarea>

                        <p
                            x-show="errors.pickup_address"
                            x-text="errors.pickup_address"
                            class="mt-2 text-xs text-red-600"
                        ></p>
                    </div>

                    <div>
                        <label
                            for="review-notes"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            Special requests
                            <span class="font-normal text-gray-400">
                                (optional)
                            </span>
                        </label>

                        <textarea
                            id="review-notes"
                            x-model="customer.special_requests"
                            @input="ready = false"
                            rows="4"
                            class="booking-input mt-2 rounded-xl border-newman-navy/10 bg-white"
                            placeholder="Accessibility, luggage, dietary notes, or other requests"
                        ></textarea>
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-newman-navy/10 bg-white p-4">
                        <input
                            type="checkbox"
                            x-model="customer.agree"
                            @change="
                                ready = false;
                                delete errors.agree;
                            "
                            class="mt-1 rounded border-newman-navy/20 text-newman-gold focus:ring-newman-gold"
                        >

                        <span class="text-sm leading-6 text-gray-600">
                            I have reviewed the selected tour,
                            date, participants, language, starting
                            time, and estimated price.
                        </span>
                    </label>

                    <p
                        x-show="errors.agree"
                        x-text="errors.agree"
                        class="text-xs text-red-600"
                    ></p>

                    <button
                        type="button"
                        @click="prepareBookingRequest()"
                        class="flex min-h-14 w-full items-center justify-center rounded-xl bg-newman-gold px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-navy hover:text-white"
                    >
                        Continue to final request
                    </button>

                    <p class="text-xs leading-5 text-gray-500">
                        No online payment is required. Submit this booking
                        request and Newman will verify the selected schedule,
                        then contact you with final confirmation.
                    </p>

                    <div
                        x-ref="readyMessage"
                        x-cloak
                        x-show="ready"
                        x-transition
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                    >
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">
                            Review complete
                        </p>

                        <p class="mt-2 text-sm leading-6 text-emerald-800">
                            The trip and guest information are ready
                            to be submitted as a Booking Request.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
