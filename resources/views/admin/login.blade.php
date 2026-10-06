<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Staff sign in · Event Experts Ltd</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-ink p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <img src="{{ asset('images/portfolio/a-white-wedding-1.jpg') }}" alt="" class="absolute inset-0 size-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-ink/30"></div>
        <img src="{{ asset('images/logo-light.png') }}" alt="Event Experts Ltd" class="relative h-24 w-auto self-start">
        <div class="relative">
            <p class="font-display text-2xl font-semibold text-brand-300 italic">{{ config('site.tagline') }}</p>
            <h1 class="mt-3 font-display text-6xl leading-none font-semibold">Every enquiry,<br>one inbox.</h1>
            <p class="mt-4 max-w-md text-white/60">Quote requests, rental enquiries, job applications and messages from the website, plus your booking calendar, portfolio and site announcements.</p>
        </div>
        <div class="gilt relative h-px w-40"></div>
    </div>

    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-sm">
            <x-logo class="mb-10 lg:hidden" />
            <h2 class="font-display text-4xl font-semibold">Staff sign in</h2>
            <p class="mt-2 text-sm text-muted">Sign in to manage the website.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-4">
                @csrf
                <x-field name="email" type="email" label="Email" required autofocus autocomplete="username" :value="app()->isLocal() ? 'admin@eventexpertstt.com' : null" />
                <x-field name="password" type="password" label="Password" required autocomplete="current-password" />
                <label class="flex items-center gap-2.5 text-sm">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-brand-600"> Keep me signed in
                </label>
                <button type="submit" class="btn btn-primary w-full !py-3.5 !text-base">Sign in</button>
            </form>

            @if (app()->isLocal())
                <div class="mt-6 rounded-2xl bg-white p-4 text-sm ring-1 ring-line">
                    <p class="font-semibold">Demo access</p>
                    <p class="mt-1 text-muted">Email <code class="font-mono text-ink">admin@eventexpertstt.com</code><br>Password <code class="font-mono text-ink">events-demo-2026</code></p>
                    <p class="mt-2 text-xs text-muted">Shown in the local environment only.</p>
                </div>
            @endif

            <a href="{{ route('home') }}" class="link-arrow mt-8"><x-icon name="arrow-left" class="size-4" /> Back to website</a>
        </div>
    </div>
</body>
</html>
