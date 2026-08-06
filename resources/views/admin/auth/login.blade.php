<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Newman</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-newman-navy text-white antialiased">
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-16">
        <div class="absolute inset-0 opacity-[0.08]">
            <div class="h-full w-full bali-pattern"></div>
        </div>

        <div class="relative w-full max-w-md border border-white/10 bg-white p-6 text-newman-navy shadow-2xl shadow-black/20 sm:p-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                    Newman Admin
                </p>

                <h1 class="mt-4 text-3xl font-semibold tracking-[-0.04em]">
                    Login dashboard
                </h1>

                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Masuk untuk melihat booking request yang masuk dari website.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="grid gap-5">
                @csrf

                <div>
                    <label class="text-sm font-semibold">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="booking-input mt-2"
                        placeholder="admin@newman.test"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="booking-input mt-2"
                        placeholder="Admin password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="bg-newman-navy px-6 py-4 text-sm font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
                >
                    Login
                </button>
            </form>
        </div>
    </section>
</body>
</html>