@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-newman-navy pt-32 text-white sm:pt-36 lg:pt-40">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-contact.webp') }}"
            alt="Contact Newman Tour Bali"
            width="6240"
            height="4160"
            loading="eager"
            fetchpriority="high"
            decoding="async"
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
                'Hello Newman Tour Bali, I want to ask about a Bali trip.',
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

    <div class="mx-auto grid w-[calc(100%-2rem)] max-w-7xl gap-8 sm:w-[92%] sm:gap-10 xl:grid-cols-[0.82fr_1.18fr] xl:items-start xl:gap-12">
        <div data-aos="fade-up" class="min-w-0 space-y-5">
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

            <div data-aos="fade-up" data-aos-delay="100" class="grid gap-5 md:grid-cols-2 xl:grid-cols-1">
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
                    width="1280"
                    height="960"
                    loading="lazy"
                    decoding="async"
                    class="contact-photo h-60 w-full object-cover sm:h-72 xl:h-64"
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

        <div data-aos="fade-up" data-aos-delay="120" class="contact-card min-w-0 border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7 xl:p-8">
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
                    Your details stay in the WhatsApp message and are only sent when you choose to continue.
                </p>
            </form>
        </div>
    </div>
</section>

<section
    id="location"
    aria-labelledby="contact-location-heading"
    class="relative overflow-hidden bg-newman-navy py-16 text-white sm:py-20 lg:py-24"
>
    <div class="absolute inset-0 opacity-[0.055]" aria-hidden="true">
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div class="relative mx-auto grid w-[calc(100%-2rem)] max-w-7xl gap-10 sm:w-[92%] sm:gap-12 xl:grid-cols-[0.68fr_1.32fr] xl:items-center xl:gap-16">
        <div data-aos="fade-up" class="max-w-xl">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Find Newman in Bali
            </p>

            <h2
                id="contact-location-heading"
                class="mt-4 text-3xl font-semibold leading-tight tracking-[-0.04em] sm:text-5xl"
            >
                A local place behind every Bali journey.
            </h2>

            <p class="mt-5 text-base leading-8 text-white/68 sm:text-lg">
                This is Newman&rsquo;s public location in Bali. Use the map when you need directions, then share your hotel or pickup area so the day can be planned naturally around your route.
            </p>

            <div class="mt-8 space-y-5 border-t border-white/12 pt-6">
                <div class="grid gap-1 sm:grid-cols-[8rem_1fr] sm:gap-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-newman-gold">
                        Based in
                    </p>
                    <p class="leading-7 text-white/78">
                        Bali, Indonesia
                    </p>
                </div>

                <div class="grid gap-1 sm:grid-cols-[8rem_1fr] sm:gap-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-newman-gold">
                        Trip pickup
                    </p>
                    <p class="leading-7 text-white/78">
                        Hotel pickup details are confirmed personally for each route.
                    </p>
                </div>
            </div>

            <a
                href="https://www.google.com/maps/search/?api=1&amp;query=-8.530035366967237%2C115.34035468088022"
                target="_blank"
                rel="noopener noreferrer"
                class="group mt-8 inline-flex items-center gap-3 border-b border-newman-gold/70 pb-2 text-sm font-bold uppercase tracking-[0.16em] text-newman-gold transition hover:border-white hover:text-white"
            >
                Open in Google Maps
                <span aria-hidden="true" class="transition group-hover:translate-x-1">&rarr;</span>
            </a>
        </div>

        <div data-aos="fade-up" data-aos-delay="100" class="min-w-0">
            <div class="relative">
                <div
                    aria-hidden="true"
                    class="absolute -left-3 -top-3 h-16 w-16 border-l border-t border-newman-gold/70 sm:-left-4 sm:-top-4 sm:h-24 sm:w-24"
                ></div>

                <div class="relative h-[340px] overflow-hidden border border-white/12 bg-white/5 shadow-2xl shadow-black/20 sm:h-[440px] xl:h-[520px]">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d483.7783814522578!2d115.34035468088022!3d-8.530035366967237!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zOMKwMzEnNDYuNyJTIDExNcKwMjAnMjUuNyJF!5e1!3m2!1sid!2sid!4v1771117949086!5m2!1sid!2sid"
                        title="Newman Tour Bali public location on Google Maps"
                        class="absolute inset-0 h-full w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>

            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/52">
                The map shows the public location you provided. Tour pickup and meeting details can still be arranged around your stay.
            </p>
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

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
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

        <div data-aos="fade-up" class="mt-10 bg-newman-navy p-6 text-white sm:p-8 xl:flex xl:items-center xl:justify-between xl:gap-8">
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
                class="mt-6 inline-flex bg-newman-gold px-6 py-4 text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-1 hover:bg-white xl:mt-0"
            >
                Open Booking Request
            </a>
        </div>
    </div>
</section>
@endsection
