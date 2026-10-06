@props(['title'])
@php
    $current = Route::currentRouteName();
    $newCount = \App\Models\Enquiry::where('status', 'new')->count();
    $links = [
        ['admin.dashboard', 'Dashboard', 'layout-dashboard', ['admin.dashboard']],
        ['admin.enquiries', 'Enquiries', 'inbox', ['admin.enquiries', 'admin.enquiry']],
        ['admin.dates', 'Calendar', 'calendar-days', ['admin.dates']],
        ['admin.projects', 'Portfolio', 'images', ['admin.projects', 'admin.projects.create', 'admin.projects.edit']],
        ['admin.vacancies', 'Careers', 'briefcase', ['admin.vacancies']],
        ['admin.announcements', 'Announcements', 'megaphone', ['admin.announcements']],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · Event Experts Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen lg:flex">
    <aside class="flex shrink-0 flex-col bg-ink text-white lg:sticky lg:top-0 lg:h-screen lg:w-64">
        <div class="flex items-center justify-between gap-3 p-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/mark-light.png') }}" alt="Event Experts Ltd" class="h-8 w-auto">
                <span class="font-display text-base font-semibold tracking-[0.08em] whitespace-nowrap uppercase">Event Experts</span>
            </a>
            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[10px] font-semibold tracking-widest uppercase">Admin</span>
        </div>
        <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:flex-1 lg:flex-col lg:pb-0" aria-label="Admin">
            @foreach ($links as [$route, $label, $icon, $active])
                <a href="{{ route($route) }}" @class([
                    'flex shrink-0 items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition',
                    'bg-white text-ink' => in_array($current, $active),
                    'text-white/70 hover:bg-white/10 hover:text-white' => ! in_array($current, $active),
                ])>
                    <x-icon :name="$icon" class="size-[18px]" /> {{ $label }}
                    @if ($route === 'admin.enquiries' && $newCount)
                        <span class="ml-auto rounded-full bg-brand-600 px-2 py-0.5 text-[11px] font-bold text-white">{{ $newCount }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="hidden space-y-1 border-t border-white/10 p-3 lg:block">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white">
                <x-icon name="external-link" class="size-[18px]" /> View website
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white">
                    <x-icon name="log-out" class="size-[18px]" /> Sign out
                </button>
            </form>
            <p class="px-3.5 pt-2 text-xs text-white/40">{{ auth()->user()->name }}</p>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="flex flex-wrap items-center justify-between gap-4 border-b border-line bg-white px-5 py-5 sm:px-8">
            <h1 class="font-display text-3xl font-semibold">{{ $title }}</h1>
            <div class="flex items-center gap-2">
                {{ $actions ?? '' }}
                <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">
                    @csrf
                    <button class="btn btn-light !py-2">Sign out</button>
                </form>
            </div>
        </header>
        <main class="p-5 sm:p-8">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 ring-1 ring-emerald-200" role="status">
                    <x-icon name="check" class="size-4 text-emerald-600" /> {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-900 ring-1 ring-rose-200" role="alert">
                    <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
