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

<section
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

            return 'https://wa.me/6287887243495?text=' + encodeURIComponent(text);
        }
    }"
    class="relative overflow-hidden bg-white py-16 sm:py-20 lg:py-24"
>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-newman-gold/30 to-transparent"></div>

    <div class="mx-auto grid w-[92%] max-w-7xl gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
        <div data-aos="fade-up" class="space-y-5">
            <div class="contact-card border border-gray-100 bg-newman-sand p-6 shadow-sm shadow-newman-navy/5 sm:p-7">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">
                    Quick Contact
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-4xl">
                    The easiest way is through WhatsApp.
                </h2>

                <p class="mt-4 text-base leading-8 text-gray-600">
                    Send your question, date, hotel area, or places you want to visit. It does not need to be perfect yet.
                </p>

                <a
                    href="https://wa.me/6287887243495"
                    target="_blank"
                    class="mt-7 inline-flex bg-newman-navy px-6 py-4 text-sm font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl"
                >
                    Chat on WhatsApp
                </a>
            </div>

            <div data-aos="fade-up" data-aos-delay="100" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
                <div class="contact-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-gold">
                        Based In
                    </p>

                    <h3 class="mt-3 text-2xl font-semibold text-newman-navy">
                        Bali, Indonesia
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        Private trips can start from common hotel areas around Bali based on route and availability.
                    </p>
                </div>

                <div class="contact-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-gold">
                        Best For
                    </p>

                    <h3 class="mt-3 text-2xl font-semibold text-newman-navy">
                        Simple trip questions
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        Ask about route ideas, pickup timing, custom plans, or which tour fits your day best.
                    </p>
                </div>
            </div>

            <div data-aos="fade-up" data-aos-delay="100" class="contact-photo-wrap contact-card overflow-hidden border border-gray-100 bg-white shadow-sm shadow-newman-navy/5">
                <img
                    src="{{ asset('images/gall14.jpeg') }}"
                    alt="Bali trip moment with Newman"
                    class="contact-photo h-64 w-full object-cover"
                >

                <div class="p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-newman-blue">
                        Travel Note
                    </p>

                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        A good Bali plan usually starts from one simple question: “Where do you want the day to feel slow, beautiful, and comfortable?”
                    </p>
                </div>
            </div>
        </div>

        <div data-aos="fade-up" data-aos-delay="120" class="contact-card border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7 lg:p-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Send A Message
                </p>

                <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                    Tell us what you need help with.
                </h2>

                <p class="mt-5 max-w-2xl text-base leading-8 text-gray-600">
                    This message will open WhatsApp with your details filled in, so it feels quick and natural.
                </p>
            </div>

            <form class="grid gap-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-newman-navy">
                            Name
                        </label>

                        <input
                            x-model="name"
                            type="text"
                            placeholder="Your name"
                            class="contact-input mt-2"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-newman-navy">
                            WhatsApp
                        </label>

                        <input
                            x-model="whatsapp"
                            type="text"
                            placeholder="+62..."
                            class="contact-input mt-2"
                        >
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        What do you want to ask?
                    </label>

                    <textarea
                        x-model="message"
                        rows="7"
                        placeholder="Example: Hi Newman, we will stay in Ubud for 2 days and want a relaxed private tour with rice terrace and waterfall."
                        class="contact-input mt-2 resize-none"
                    ></textarea>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="flex items-center justify-center bg-newman-navy px-6 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-1 hover:bg-newman-blue hover:shadow-xl"
                    >
                        Send via WhatsApp
                    </a>

                    <a
                        href="{{ route('custom-trip.create') }}"
                        class="border border-newman-navy/15 px-6 py-4 text-center text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:border-newman-gold hover:bg-newman-gold"
                    >
                        Booking Request
                    </a>
                </div>

                <p class="text-sm leading-7 text-gray-500">
                    Replace the WhatsApp number in this page with your father’s real number before publishing.
                </p>
            </form>
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-newman-sand/45 py-16 sm:py-20 lg:py-24">
    <div class="absolute inset-0 opacity-[0.05]">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto w-[92%] max-w-7xl">
        <div data-aos="fade-up" class="mb-10 max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                Before You Message
            </p>

            <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
                A few details help make the reply easier.
            </h2>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div data-aos="fade-up" class="contact-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5">
                <p class="text-3xl font-semibold text-newman-gold">01</p>
                <h3 class="mt-5 text-xl font-semibold text-newman-navy">
                    Your travel date
                </h3>
                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Mention the date or month of your trip so availability can be checked.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="90" class="contact-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5">
                <p class="text-3xl font-semibold text-newman-gold">02</p>
                <h3 class="mt-5 text-xl font-semibold text-newman-navy">
                    Places you like
                </h3>
                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Send any places you want to visit, even if the route is not fixed yet.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="180" class="contact-card border border-gray-100 bg-white p-6 shadow-sm shadow-newman-navy/5">
                <p class="text-3xl font-semibold text-newman-gold">03</p>
                <h3 class="mt-5 text-xl font-semibold text-newman-navy">
                    Your hotel area
                </h3>
                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Pickup area helps estimate route timing and the best order of stops.
                </p>
            </div>
        </div>

        <div data-aos="fade-up" class="mt-10 bg-newman-navy p-6 text-white sm:p-8 lg:flex lg:items-center lg:justify-between lg:gap-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Ready to plan more clearly?
                </p>

                <h3 class="mt-3 text-2xl font-semibold tracking-[-0.04em]">
                    Use the booking request page for full trip details.
                </h3>
            </div>

            <a
                href="{{ route('custom-trip.create') }}"
                class="mt-6 inline-flex bg-newman-gold px-6 py-4 text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-white lg:mt-0"
            >
                Open Booking Request
            </a>
        </div>
    </div>
</section>
@endsection
