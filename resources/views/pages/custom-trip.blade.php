@extends('layouts.app')

@section('content')
@php
    $initialVehicle = old(
        'selected_vehicle',
        $selectedVehicle ?? 'Not sure, please recommend'
    );

    $stepOneFields = [
        'trip_date',
        'people_count',
        'pickup_area',
        'message',
    ];

    $stepTwoFields = [
        'selected_vehicle',
        'custom_vehicle',
    ];

    $stepThreeFields = [
        'name',
        'whatsapp',
        'email',
    ];

    $initialStep = 1;

    if ($errors->hasAny($stepOneFields)) {
        $initialStep = 1;
    } elseif ($errors->hasAny($stepTwoFields)) {
        $initialStep = 2;
    } elseif ($errors->hasAny($stepThreeFields)) {
        $initialStep = 3;
    }

    $vehicleCards = [
        [
            'value' => 'Not sure, please recommend',
            'eyebrow' => 'Newman recommendation',
            'title' => 'Let Newman choose the best fit',
            'capacity' => 'Any group size',
            'description' => 'Choose this when you prefer Newman to review the group, luggage, and route before suggesting transport.',
        ],
        [
            'value' => 'Toyota Avanza',
            'eyebrow' => 'Private car preference',
            'title' => 'Toyota Avanza',
            'capacity' => 'Best starting point for 1–5 guests',
            'description' => 'A practical preference for couples, families, and smaller private groups with normal luggage.',
        ],
        [
            'value' => 'Toyota Hiace',
            'eyebrow' => 'Passenger van preference',
            'title' => 'Toyota Hiace',
            'capacity' => 'Best starting point for 6–12 guests',
            'description' => 'A spacious preference for family groups, office trips, and guests needing more cabin space.',
        ],
        [
            'value' => 'Another Car',
            'eyebrow' => 'Special transport request',
            'title' => 'Another vehicle arrangement',
            'capacity' => 'Large groups or special requirements',
            'description' => 'Use this for larger groups, multiple vehicles, accessibility needs, or another specific request.',
        ],
    ];
@endphp

<section class="relative overflow-hidden bg-newman-navy pt-28 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-booking.webp') }}"
            alt="Plan a flexible private Bali trip with Newman"
            width="4081"
            height="6121"
            loading="eager"
            fetchpriority="high"
            decoding="async"
            class="h-full w-full object-cover opacity-50"
        >

        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/80 to-newman-navy/35"></div>
    </div>

    <div class="absolute inset-0 opacity-20">
        <div class="bali-pattern h-full w-full"></div>
    </div>

    <div
        class="relative mx-auto w-[calc(100%-2rem)] max-w-7xl pb-16 sm:w-[92%] sm:pb-24 lg:pb-28"
    >
        <div data-aos="fade-up" class="min-w-0 max-w-4xl">
            <a
                href="{{ route('home') }}"
                class="inline-flex border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-white/80 backdrop-blur-md transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
            >
                ← Back to home
            </a>

            <p class="mt-8 text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.38em]">
                Custom Trip Request V2
            </p>

            <h1 class="mt-5 break-words text-4xl font-semibold leading-tight tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                Plan the route first. Confirm the details with Newman.
            </h1>

            <p class="mt-6 max-w-3xl break-words text-base leading-8 text-white/72 sm:text-lg">
                Use this flow when you want a flexible itinerary rather than
                a published Tour Package. There is no website payment and no
                instant vehicle confirmation.
            </p>
        </div>

        <div
            data-aos="fade-up"
            data-aos-delay="120"
            class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-4"
        >
            @foreach ([
                ['01', 'Trip plan'],
                ['02', 'Vehicle preference'],
                ['03', 'Contact details'],
                ['04', 'Review request'],
            ] as [$number, $label])
                <div class="min-w-0 overflow-hidden border border-white/12 bg-white/10 p-4 backdrop-blur-md">
                    <p class="text-2xl font-semibold text-newman-gold">
                        {{ $number }}
                    </p>

                    <p class="mt-1 break-words text-sm leading-5 text-white/60">
                        {{ $label }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section
    x-data="{
        step: @js($initialStep),
        tripDate: @js(old('trip_date', '')),
        peopleCount: @js(old('people_count', '')),
        pickupArea: @js(old('pickup_area', '')),
        message: @js(old('message', '')),
        vehicle: @js($initialVehicle),
        customVehicle: @js(old('custom_vehicle', '')),
        name: @js(old('name', '')),
        whatsapp: @js(old('whatsapp', '')),
        email: @js(old('email', '')),
        vehicleRules: @js($vehicleRecommendations),

        get guestTotal() {
            const value = Number.parseInt(this.peopleCount, 10);
            return Number.isFinite(value) && value > 0 ? value : 0;
        },

        get recommendedVehicle() {
            if (this.guestTotal < 1) {
                return 'Enter the group size to see a recommendation';
            }

            const rule = this.vehicleRules.find((item) => {
                const aboveMinimum = this.guestTotal >= item.minimum;
                const belowMaximum = item.maximum === null || this.guestTotal <= item.maximum;
                return aboveMinimum && belowMaximum;
            });

            return rule ? rule.vehicle : 'Another Car';
        },

        get recommendedDescription() {
            if (this.guestTotal < 1) {
                return 'The final recommendation also considers luggage, child seats, accessibility, and availability.';
            }

            const rule = this.vehicleRules.find((item) => {
                const aboveMinimum = this.guestTotal >= item.minimum;
                const belowMaximum = item.maximum === null || this.guestTotal <= item.maximum;
                return aboveMinimum && belowMaximum;
            });

            return rule ? rule.description : 'Newman will review a larger transport arrangement.';
        },

        get vehicleText() {
            if (this.vehicle === 'Another Car') {
                return this.customVehicle || 'Another vehicle arrangement';
            }

            return this.vehicle || 'Not selected';
        },

        get vehicleNeedsAttention() {
            if (this.guestTotal < 1) {
                return false;
            }

            if (this.vehicle === 'Toyota Avanza' && this.guestTotal > 5) {
                return true;
            }

            if (this.vehicle === 'Toyota Hiace' && this.guestTotal > 12) {
                return true;
            }

            return false;
        },

        isRecommended(value) {
            return this.recommendedVehicle === value;
        },

        chooseVehicle(value) {
            this.vehicle = value;

            if (value !== 'Another Car') {
                this.customVehicle = '';
            }
        },

        validateScope(referenceName) {
            const scope = this.$refs[referenceName];

            if (!scope) {
                return true;
            }

            const fields = Array.from(
                scope.querySelectorAll('input, textarea, select')
            ).filter((field) => !field.disabled);

            for (const field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }

            return true;
        },

        validateVehicleStep() {
            if (!this.vehicle) {
                window.alert('Please choose a vehicle preference or ask Newman to recommend one.');
                return false;
            }

            if (this.vehicle === 'Another Car' && this.customVehicle.trim().length < 3) {
                window.alert('Please describe the transport arrangement you need.');
                this.$nextTick(() => this.$refs.customVehicle?.focus());
                return false;
            }

            return true;
        },

        nextStep() {
            let valid = true;

            if (this.step === 1) {
                valid = this.validateScope('stepOne');
            } else if (this.step === 2) {
                valid = this.validateVehicleStep();
            } else if (this.step === 3) {
                valid = this.validateScope('stepThree');
            }

            if (!valid) {
                return;
            }

            this.step = Math.min(4, this.step + 1);
            this.scrollToForm();
        },

        previousStep() {
            this.step = Math.max(1, this.step - 1);
            this.scrollToForm();
        },

        goBackTo(target) {
            if (target < this.step) {
                this.step = target;
                this.scrollToForm();
            }
        },

        submitRequest() {
            this.$refs.customTripForm.submit();
        },

        scrollToForm() {
            this.$nextTick(() => {
                this.$refs.formTop?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        }
    }"
   class="relative overflow-x-clip bg-white py-14 sm:py-20 lg:py-24"
>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div x-ref="formTop" class="scroll-mt-28"></div>

   <div
    class="mx-auto grid w-[calc(100%-2rem)] min-w-0 max-w-7xl gap-8 sm:w-[92%] lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start"
>
      <div
    data-aos="fade-up"
    class="min-w-0 overflow-hidden border border-gray-100 bg-white p-4 shadow-sm shadow-newman-navy/5 sm:p-7 lg:p-8"
>
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Custom Trip Builder
                </p>

               <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    @foreach (
        [
            1 => 'Plan',
            2 => 'Vehicle',
            3 => 'Contact',
            4 => 'Review',
        ] as $stepNumber => $stepLabel
    )
        <button
            type="button"
            @click="goBackTo({{ $stepNumber }})"
            :disabled="{{ $stepNumber }} > step"
            class="group min-w-0 border p-3 text-left transition duration-300 disabled:cursor-default"
            :class="
                step === {{ $stepNumber }}
                    ? 'border-newman-gold bg-newman-sand'
                    : (
                        step > {{ $stepNumber }}
                            ? 'border-newman-gold/30 bg-newman-gold/5'
                            : 'border-gray-200 bg-white'
                    )
            "
        >
            <span
                class="flex h-9 w-9 items-center justify-center border text-xs font-bold transition"
                :class="
                    step >= {{ $stepNumber }}
                        ? 'border-newman-gold bg-newman-gold text-newman-navy'
                        : 'border-gray-200 bg-white text-gray-400'
                "
            >
                {{ $stepNumber }}
            </span>

            <span
                class="mt-2 block break-words text-[10px] font-bold uppercase leading-4 tracking-[0.1em]"
                :class="
                    step >= {{ $stepNumber }}
                        ? 'text-newman-navy'
                        : 'text-gray-400'
                "
            >
                {{ $stepLabel }}
            </span>
        </button>
    @endforeach
</div>
            </div>

            @if ($errors->any())
                <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Please review the highlighted request information.</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                x-ref="customTripForm"
                action="{{ route('custom-trip.store') }}"
                method="POST"
            >
                @csrf

                <input type="hidden" name="selected_vehicle" :value="vehicle">

                <div x-show="step === 1" x-cloak x-transition.opacity x-ref="stepOne">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">Step 1</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-4xl">
                        Tell us the trip you want to build.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                        The route can still be adjusted after Newman contacts you. Give enough detail for an initial plan.
                    </p>

                    <div class="mt-7 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-newman-navy">
                                Preferred Trip Date
                                <span class="font-normal text-gray-400">(optional)</span>
                            </label>
                            <input
                                x-model="tripDate"
                                name="trip_date"
                                value="{{ old('trip_date') }}"
                                type="date"
                                min="{{ now()->toDateString() }}"
                                class="booking-input mt-2"
                            >
                            @error('trip_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-newman-navy">
                                Number of Guests
                            </label>
                            <input
                                x-model="peopleCount"
                                name="people_count"
                                value="{{ old('people_count') }}"
                                type="number"
                                min="1"
                                max="100"
                                required
                                placeholder="Example: 4"
                                class="booking-input mt-2"
                            >
                            @error('people_count')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="text-sm font-semibold text-newman-navy">
                            Pickup Area / Hotel
                            <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <input
                            x-model="pickupArea"
                            name="pickup_area"
                            value="{{ old('pickup_area') }}"
                            type="text"
                            maxlength="180"
                            placeholder="Example: Ubud hotel, Seminyak villa, or airport"
                            class="booking-input mt-2"
                        >
                        @error('pickup_area')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5">
                        <label class="text-sm font-semibold text-newman-navy">
                            Destination List & Trip Preferences
                        </label>
                        <textarea
                            x-model="message"
                            name="message"
                            rows="7"
                            required
                            minlength="20"
                            maxlength="1200"
                            class="booking-input mt-2 resize-none"
                            placeholder="Example: Ubud rice terrace, temple, waterfall, local lunch, relaxed pace, and enough time for photos."
                        >{{ old('message') }}</textarea>
                        <div class="mt-2 flex items-start justify-between gap-4 text-xs text-gray-400">
                            <p>Include destinations, pace, luggage, child seats, accessibility, or another special request.</p>
                            <p class="shrink-0"><span x-text="message.length"></span>/1200</p>
                        </div>
                        @error('message')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-show="step === 2" x-cloak x-transition.opacity>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">Step 2</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-4xl">
                        Choose a vehicle preference.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                        This is a preference, not a confirmed vehicle. Newman will confirm the final arrangement after reviewing the group and route.
                    </p>

                    <div class="mt-6 border border-newman-gold/30 bg-newman-sand p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-newman-gold">
                            Suggested starting point
                        </p>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xl font-semibold text-newman-navy" x-text="recommendedVehicle"></p>
                                <p class="mt-2 text-sm leading-6 text-gray-600" x-text="recommendedDescription"></p>
                            </div>
                            <button
                                x-show="guestTotal > 0"
                                type="button"
                                @click="chooseVehicle(recommendedVehicle)"
                                class="shrink-0 border border-newman-navy/15 bg-white px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
                            >
                                Use suggestion
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ($vehicleCards as $vehicleCard)
                            <button
                                type="button"
                                @click="chooseVehicle(@js($vehicleCard['value']))"
                                class="relative overflow-hidden border p-5 text-left transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:shadow-lg"
                                :class="vehicle === @js($vehicleCard['value'])
                                    ? 'border-newman-gold bg-newman-navy text-white shadow-lg'
                                    : 'border-gray-200 bg-white text-newman-navy'"
                            >
                                <span
                                    x-show="isRecommended(@js($vehicleCard['value']))"
                                    class="absolute right-3 top-3 bg-newman-gold px-2 py-1 text-[9px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                                >
                                    Suggested
                                </span>

                                <span
                                    class="text-[10px] font-bold uppercase tracking-[0.18em]"
                                    :class="vehicle === @js($vehicleCard['value']) ? 'text-newman-gold' : 'text-newman-blue'"
                                >
                                    {{ $vehicleCard['eyebrow'] }}
                                </span>

                                <span class="mt-3 block text-xl font-semibold">
                                    {{ $vehicleCard['title'] }}
                                </span>

                                <span
                                    class="mt-3 block text-xs font-bold uppercase tracking-[0.12em]"
                                    :class="vehicle === @js($vehicleCard['value']) ? 'text-white/70' : 'text-gray-500'"
                                >
                                    {{ $vehicleCard['capacity'] }}
                                </span>

                                <span
                                    class="mt-3 block text-sm leading-6"
                                    :class="vehicle === @js($vehicleCard['value']) ? 'text-white/70' : 'text-gray-600'"
                                >
                                    {{ $vehicleCard['description'] }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <div x-show="vehicle === 'Another Car'" x-cloak class="mt-5">
                        <label class="text-sm font-semibold text-newman-navy">
                            Describe the transport arrangement
                        </label>
                        <input
                            x-ref="customVehicle"
                            x-model="customVehicle"
                            name="custom_vehicle"
                            value="{{ old('custom_vehicle') }}"
                            type="text"
                            maxlength="180"
                            :required="vehicle === 'Another Car'"
                            placeholder="Example: transport for 18 guests, wheelchair access, or two vehicles"
                            class="booking-input mt-2"
                        >
                        @error('custom_vehicle')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        x-show="vehicleNeedsAttention"
                        x-cloak
                        class="mt-5 border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800"
                    >
                        Your current preference may be too small for the entered group. You can continue, but Newman will review and suggest another arrangement.
                    </div>

                    @error('selected_vehicle')
                        <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="step === 3" x-cloak x-transition.opacity x-ref="stepThree">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">Step 3</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-4xl">
                        How should Newman contact you?
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                        Use an active WhatsApp number. Newman will review the itinerary before confirming the route, transport, and final quotation.
                    </p>

                    <div class="mt-7 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-newman-navy">Full Name</label>
                            <input
                                x-model="name"
                                name="name"
                                value="{{ old('name') }}"
                                type="text"
                                required
                                maxlength="120"
                                autocomplete="name"
                                placeholder="Your name"
                                class="booking-input mt-2"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-newman-navy">WhatsApp</label>
                            <input
                                x-model="whatsapp"
                                name="whatsapp"
                                value="{{ old('whatsapp') }}"
                                type="text"
                                required
                                minlength="7"
                                maxlength="50"
                                autocomplete="tel"
                                placeholder="Your WhatsApp number"
                                class="booking-input mt-2"
                            >
                            @error('whatsapp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="text-sm font-semibold text-newman-navy">
                            Email
                            <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <input
                            x-model="email"
                            name="email"
                            value="{{ old('email') }}"
                            type="email"
                            maxlength="120"
                            autocomplete="email"
                            placeholder="your@email.com"
                            class="booking-input mt-2"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-show="step === 4" x-cloak x-transition.opacity>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">Step 4</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-4xl">
                        Review your custom trip request.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                        Submitting creates a pending request. It does not confirm availability and no payment is collected on the website.
                    </p>

                    <div class="mt-7 grid min-w-0 gap-4 sm:grid-cols-2">
    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Trip Date
        </p>

        <p
            class="mt-2 break-words font-semibold text-newman-navy [overflow-wrap:anywhere]"
            x-text="tripDate || 'Flexible / not selected'"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Guests
        </p>

        <p
            class="mt-2 break-words font-semibold text-newman-navy [overflow-wrap:anywhere]"
            x-text="peopleCount + (Number(peopleCount) === 1 ? ' guest' : ' guests')"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Vehicle Preference
        </p>

        <p
            class="mt-2 break-words font-semibold text-newman-navy [overflow-wrap:anywhere]"
            x-text="vehicleText"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Suggested Fit
        </p>

        <p
            class="mt-2 break-words font-semibold leading-6 text-newman-navy [overflow-wrap:anywhere]"
            x-text="recommendedVehicle"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:col-span-2 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Pickup Area
        </p>

        <p
            class="mt-2 break-words font-semibold text-newman-navy [overflow-wrap:anywhere]"
            x-text="pickupArea || 'To be confirmed'"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden bg-newman-sand p-4 sm:col-span-2 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Trip Plan
        </p>

        <p
            class="mt-2 whitespace-pre-line break-words text-sm leading-7 text-newman-navy [overflow-wrap:anywhere]"
            x-text="message"
        ></p>
    </div>

    <div class="min-w-0 overflow-hidden border border-gray-100 bg-white p-4 sm:col-span-2 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-newman-blue">
            Contact
        </p>

        <p
            class="mt-2 break-words font-semibold text-newman-navy [overflow-wrap:anywhere]"
            x-text="name"
        ></p>

        <p
            class="mt-1 break-words text-sm text-gray-600 [overflow-wrap:anywhere]"
            x-text="whatsapp"
        ></p>

        <p
            class="mt-1 break-words text-sm text-gray-500 [overflow-wrap:anywhere]"
            x-text="email || 'No email provided'"
        ></p>
    </div>
</div>
                    <div class="mt-6 min-w-0 overflow-hidden border border-newman-gold/30 bg-newman-navy p-4 text-white sm:p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold">
                            What happens next?
                        </p>

                        <p class="mt-3 break-words text-sm leading-7 text-white/70 [overflow-wrap:anywhere]">
                            Newman receives the request in Admin Custom Trips
                            and by SMTP, reviews the itinerary and vehicle fit,
                            then contacts you through WhatsApp or email.
                        </p>
                    </div>
                </div>
                {{-- End Step 4 --}}

                {{-- Shared navigation for all steps --}}
                <div
                    class="mt-8 grid min-w-0 gap-3 border-t border-gray-100 pt-6 sm:flex sm:items-center sm:justify-between"
                >
                    <button
                        x-show="step > 1"
                        x-cloak
                        type="button"
                        @click="previousStep()"
                        class="flex w-full min-w-0 items-center justify-center border border-newman-navy/15 bg-white px-4 py-4 text-center text-xs font-bold uppercase tracking-[0.12em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold sm:w-auto sm:px-6 sm:text-sm sm:tracking-[0.14em]"
                    >
                        Back
                    </button>

                    <div class="grid min-w-0 gap-3 sm:ml-auto">
                        <button
                            x-show="step < 4"
                            type="button"
                            @click="nextStep()"
                            class="flex w-full min-w-0 items-center justify-center bg-newman-navy px-4 py-4 text-center text-xs font-bold uppercase tracking-[0.12em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl sm:w-auto sm:px-7 sm:text-sm sm:tracking-[0.14em]"
                        >
                            Continue
                        </button>

                        <button
                            x-show="step === 4"
                            x-cloak
                            type="button"
                            @click="submitRequest()"
                            class="flex w-full min-w-0 items-center justify-center whitespace-normal break-words bg-newman-gold px-4 py-4 text-center text-xs font-bold uppercase leading-5 tracking-[0.08em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-newman-navy hover:text-white hover:shadow-xl sm:w-auto sm:px-7 sm:text-sm sm:tracking-[0.14em]"
                        >
                            Submit Custom Trip Request
                        </button>
                    </div>
                </div>
            </form>
                </div>



      <aside
    data-aos="fade-up"
    data-aos-delay="120"
    class="min-w-0 space-y-5 lg:sticky lg:top-28 lg:self-start"
>
            <div class="hidden min-w-0 overflow-hidden border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6 lg:block">
    <p class="break-words text-xs font-bold uppercase tracking-[0.24em] text-newman-gold">
        Request Progress
    </p>

    <div class="mt-6 space-y-3">
        @foreach (
            [
                1 => 'Trip plan',
                2 => 'Vehicle preference',
                3 => 'Contact details',
                4 => 'Review request',
            ] as $stepNumber => $stepLabel
        )
            <button
                type="button"
                @click="goBackTo({{ $stepNumber }})"
                :disabled="{{ $stepNumber }} > step"
                class="flex w-full min-w-0 items-center gap-3 border p-3 text-left transition disabled:cursor-default"
                :class="
                    step === {{ $stepNumber }}
                        ? 'border-newman-gold bg-white/12'
                        : 'border-white/10 bg-white/8'
                "
            >
                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center text-xs font-bold"
                    :class="
                        step >= {{ $stepNumber }}
                            ? 'bg-newman-gold text-newman-navy'
                            : 'bg-white/10 text-white/50'
                    "
                >
                    {{ $stepNumber }}
                </span>

                <span
                    class="min-w-0 break-words text-sm font-semibold leading-5"
                    :class="
                        step >= {{ $stepNumber }}
                            ? 'text-white'
                            : 'text-white/45'
                    "
                >
                    {{ $stepLabel }}
                </span>
            </button>
        @endforeach
    </div>
</div>

            <div class="border border-gray-100 bg-newman-sand p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">
                    Important
                </p>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-gray-600">
                    <li>• Vehicle selection is a preference.</li>
                    <li>• Availability is confirmed manually.</li>
                    <li>• A quotation is provided after review.</li>
                    <li>• No website payment is collected.</li>
                </ul>
            </div>

            <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Prefer a published tour?
                </p>
                <p class="mt-4 text-sm leading-7 text-gray-600">
                    Tour Packages use the separate V2 flow with availability, options, starting times, participant pricing, and an estimated total.
                </p>
               <a
    href="{{ route('tours') }}"
    class="mt-5 flex w-full min-w-0 items-center justify-center whitespace-normal break-words bg-newman-navy px-4 py-3 text-center text-xs font-bold uppercase leading-5 tracking-[0.12em] text-white transition hover:bg-newman-blue"
>
    Browse Tour Packages
</a>
            </div>
        </aside>
    </div>
</section >
@endsection
