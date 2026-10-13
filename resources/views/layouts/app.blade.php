<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        // Inline Blade sections are already escaped; normalize before escaping each tag.
        $metadataValue = static fn (string $section, string $fallback): string => html_entity_decode(
            trim($__env->yieldContent($section, $fallback)),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $pageTitle = __($metadataValue('meta_title', $title ?? 'Newman Tour Bali'));
        $pageDescription = __($metadataValue('meta_description', $metaDescription ?? 'Explore Bali with Newman Tour Bali. Discover private Bali tours, flexible itineraries, local experiences, and comfortable travel with a local guide.'));
        $pageCanonical = $metadataValue('meta_canonical', $canonical ?? url()->current());
        $pageRobots = $metadataValue('meta_robots', 'index, follow');
        $pageImage = $metadataValue('meta_image', asset('images/hero-bg.webp'));
    @endphp

    @if (! request()->routeIs('admin.*') && ! str_contains($pageRobots, 'noindex'))
        @include('components.google-analytics')
    @endif

    <title>{{ $pageTitle }}</title>
    <meta
        name="description"
        content="{{ $pageDescription }}"
    >
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $pageCanonical }}">
    @if (! request()->routeIs('admin.*') && ! str_contains($pageRobots, 'noindex'))
        @foreach (['en', 'id'] as $language)
            <link rel="alternate" hreflang="{{ $language }}" href="{{ \App\Http\Middleware\SetLocale::languageUrl(url()->current(), $language) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ \App\Http\Middleware\SetLocale::languageUrl(url()->current(), 'en') }}">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Newman Tour Bali">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon-64.png') }}">
@stack('styles')
    @php
        // Only small dynamic labels go to JavaScript; page copy stays server-rendered.
        $uiLabelKeys = [
            'Read more', 'Read less', 'Go to slide', 'Another Car', 'Not sure, please recommend',
            'English', 'Indonesian', 'adult', 'adults', 'child', 'children', 'infant', 'infants',
            'guest', 'guests', 'passenger', 'passengers', 'vehicle', 'vehicles',
            'stop', 'stops', 'hour', 'hours', 'min', 'mins', 'verified rating', 'verified ratings', 'guests hosted',
            'per adult', 'per child', 'per infant', 'Private tour', 'Full-day tour',
            'Flexible / not selected', 'To be confirmed', 'Not selected',
            'Pickup can be arranged', 'Vehicle arranged by Newman', 'Confirmation through WhatsApp',
            'Recommended transport', 'Transport recommendation', 'Arranged personally by Newman',
        ];
        $uiLabels = app()->getLocale() === 'id'
            ? array_combine($uiLabelKeys, array_map(fn ($key) => __($key), $uiLabelKeys))
            : [];
    @endphp
    <script>window.newmanUiLabels = @js($uiLabels);</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site bg-white text-newman-dark antialiased">
    @include('components.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

@stack('scripts')

<x-floating-whatsapp
    :tour="$tour ?? null"
/>
</body>
</html>
