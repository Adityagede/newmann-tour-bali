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
        $pageTitle = $metadataValue('meta_title', $title ?? 'Newman Tour Bali');
        $pageDescription = $metadataValue('meta_description', $metaDescription ?? 'Explore Bali with Newman Tour Bali. Discover private Bali tours, flexible itineraries, local experiences, and comfortable travel with a local guide.');
        $pageCanonical = $metadataValue('meta_canonical', $canonical ?? url()->current());
        $pageRobots = $metadataValue('meta_robots', 'index, follow');
        $pageImage = $metadataValue('meta_image', asset('images/hero-bg.webp'));
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta
        name="description"
        content="{{ $pageDescription }}"
    >
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $pageCanonical }}">

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


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-newman-dark antialiased">
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
