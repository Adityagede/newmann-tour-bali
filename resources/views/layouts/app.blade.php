<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Newman Tour Bali' }}</title>
     <meta
        name="description"
        content="{{ $metaDescription ?? 'Explore Bali with Newman Tour Bali. Discover private Bali tours, flexible itineraries, local experiences, and comfortable travel with a trusted local guide.' }}"
    >
     <meta name="robots" content="index, follow">
       <link
        rel="canonical"
        href="{{ $canonical ?? url()->current() }}"
    >

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
