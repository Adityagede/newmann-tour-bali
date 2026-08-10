@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-newman-navy pt-32 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-contact.webp') }}"
            alt="Contact Newman Tour Guide in Bali"
            class="h-full w-full object-cover opacity-55"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-newman-navy via-newman-navy/76 to-newman-navy/30"></div>
    </div>

    <div class="absolute inset-0 opacity-20">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl pb-20 sm:pb-24 lg:pb-28">
        <div data-aos="fade-up" class="max-w-4xl">
            <a
                href="{{ route('home') }}"
                class="inline-flex border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/80 backdrop-blur-md transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
            >
                ← Back to home
            </a>

            <p class="mt-8 text-xs font-bold uppercase tracking-[0.38em] text-newman-gold">
                Contact Newman
            </p>

            <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                Ask anything about your Bali trip.
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-white/70 sm:text-lg">
                Have a question about routes, timing, pickup area, or a custom Bali day? Send a simple message and Newman will help you plan it calmly.
            </p>
        </div>
    </div>
</section>

<div
    x-data="{
        name: '',
        whatsapp: '',
        message: '',
        get whatsappUrl() {
            const text = [
                'Hello Newman Tour Guide, I want to ask about a Bali trip.',
                '',
                'Name: ' + (this.name || '-'),
                'WhatsApp: ' + (this.whatsapp || '-'),
                'Message: ' + (this.message || '-'),
                '',
                'Please help me with the details. Thank you.'
            ].join('\n');

            return 'https://wa.me/62XXXXXXXXXXX?text=' + encodeURIComponent(text);
        }
    }"
>
    <section class="relative overflow-hidden bg-white py-14 sm:py-18 lg:py-24">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

        <div class="mx-auto grid w-[92%] max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:gap-16">
            <div data-aos="fade-up" class="max-w-xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Plan with a local
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-4xl lg:text-5xl">
                    Tell Newman how you want your Bali days to feel.
                </h2>

                <p class="mt-5 text-base leading-8 text-gray-600">
                    You do not need a finished itinerary before you message. Share the places you are curious about, where you are staying, and the pace that suits you. Newman can help shape the route from there.
                </p>

                <div class="mt-8 border-y border-newman-navy/10">
                    @foreach ([
                        'You are not sure which tour fits your time in Bali',
                        'You would like a private or custom route',
                        'You have questions about pickup or travel time',
                        'You are travelling with children or family',
                        'You want a local recommendation before booking',
                    ] as $reason)
                        <div class="flex gap-4 border-b border-newman-navy/10 py-3.5 last:border-b-0">
                            <span class="mt-0.5 text-xs font-bold text-newman-gold">
                                {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <p class="text-sm leading-6 text-newman-navy">{{ $reason }}</p>
                        </div>
                    @endforeach
                </div>

                <a
                    href="https://wa.me/62XXXXXXXXXXX"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-7 inline-flex min-h-12 items-center justify-center bg-newman-navy px-6 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                >
                    Chat with Newman on WhatsApp
                </a>
            </div>

            <figure data-aos="fade-up" data-aos-delay="100" class="relative min-w-0">
                <div class="overflow-hidden rounded-[18px] bg-newman-sand">
                    <img
                        src="{{ asset('images/owner.jpg') }}"
                        alt="Newman sharing a local Bali travel moment"
                        class="h-[420px] w-full object-cover object-center sm:h-[540px] lg:h-[620px]"
                    >
                </div>

                <figcaption class="relative -mt-12 ml-4 max-w-[calc(100%-2rem)] border-l-2 border-newman-gold bg-newman-navy px-5 py-4 text-white sm:ml-8 sm:max-w-md sm:px-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                        A note from the road
                    </p>
                    <p class="mt-2 text-sm leading-6 text-white/75">
                        The best route is not always the one with the most stops. It is the one that gives you time to enjoy the places you came to see.
                    </p>
                </figcaption>
            </figure>
        </div>
    </section>

    <section class="border-y border-newman-navy/10 bg-newman-sand/45 py-14 sm:py-18 lg:py-22">
        <div class="mx-auto grid w-[92%] max-w-7xl gap-10 lg:grid-cols-[0.82fr_1.18fr] lg:gap-16">
            <div data-aos="fade-up" class="max-w-lg">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">
                    Start with a simple message
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-4xl">
                    Share what you know. The rest can be worked out together.
                </h2>

                <p class="mt-5 text-base leading-8 text-gray-600">
                    A travel date, your hotel area, and a few places you like are enough for Newman to understand the shape of your day.
                </p>

                <dl class="mt-8 space-y-5 border-l border-newman-gold/60 pl-5">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.14em] text-newman-navy">Travel date</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">An exact date or the month you expect to visit.</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.14em] text-newman-navy">Hotel area</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">This helps Newman plan pickup and a natural order of stops.</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.14em] text-newman-navy">Your group</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">Let Newman know if you are travelling as a couple, family, or group.</dd>
                    </div>
                </dl>
            </div>

            <div data-aos="fade-up" data-aos-delay="100" class="border border-newman-navy/10 bg-white p-5 sm:p-7 lg:p-8">
                <div class="border-b border-newman-navy/10 pb-6">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-gold">
                        Message Newman
                    </p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-newman-navy sm:text-3xl">
                        What would you like help planning?
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        Your answers open as a prepared WhatsApp message. You can edit it before sending.
                    </p>
                </div>

                <form class="mt-6 grid gap-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="contact-name" class="text-sm font-semibold text-newman-navy">Name</label>
                            <input
                                id="contact-name"
                                x-model="name"
                                type="text"
                                autocomplete="name"
                                placeholder="Your name"
                                class="contact-input mt-2"
                            >
                        </div>

                        <div>
                            <label for="contact-whatsapp" class="text-sm font-semibold text-newman-navy">WhatsApp</label>
                            <input
                                id="contact-whatsapp"
                                x-model="whatsapp"
                                type="tel"
                                autocomplete="tel"
                                placeholder="+62..."
                                class="contact-input mt-2"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="contact-message" class="text-sm font-semibold text-newman-navy">
                            Tell Newman about your trip
                        </label>
                        <textarea
                            id="contact-message"
                            x-model="message"
                            rows="6"
                            placeholder="For example: We are staying in Ubud for two days and would like a relaxed private tour with a rice terrace and waterfall."
                            class="contact-input mt-2 resize-none"
                        ></textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex min-h-12 flex-1 items-center justify-center bg-newman-navy px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-newman-blue focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                        >
                            Continue in WhatsApp
                        </a>

                        <a
                            href="{{ route('custom-trip.create') }}"
                            class="flex min-h-12 flex-1 items-center justify-center border border-newman-navy/15 px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold focus:outline-none focus:ring-2 focus:ring-newman-gold focus:ring-offset-2"
                        >
                            Plan a custom trip
                        </a>
                    </div>

                    <p class="text-xs leading-5 text-gray-500">
                        Replace the WhatsApp number on this page with Newman’s real number before publishing.
                    </p>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
