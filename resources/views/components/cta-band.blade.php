@props([
    'title' => 'Let’s make it happen.',
    'text' => 'Tell us your date, venue and guest count and we will come back to you with ideas and a quote. Prefer to talk? Call or WhatsApp us.',
])
<section class="wrap py-16 sm:py-20">
    <div class="relative overflow-hidden rounded-[2rem] bg-ink px-6 py-14 text-center text-white sm:px-16 sm:py-20" data-reveal>
        <div class="grid-ink absolute inset-0 opacity-50"></div>
        <div class="absolute -top-24 -left-24 size-72 rounded-full bg-brand-600/40 blur-3xl"></div>
        <div class="absolute -right-20 -bottom-28 size-80 rounded-full bg-brand-400/25 blur-3xl"></div>
        <div class="relative mx-auto max-w-2xl">
            <p class="font-display text-2xl font-semibold text-brand-300 italic">{{ config('site.tagline') }}</p>
            <h2 class="mt-3 font-display text-5xl leading-[1.05] font-semibold sm:text-6xl">{{ $title }}</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-white/75">{{ $text }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if ($slot->isNotEmpty())
                    {{ $slot }}
                @else
                    <a href="{{ route('plan') }}" class="btn btn-primary !px-6 !py-3.5 !text-base">Plan your event <x-icon name="arrow-right" class="size-4" /></a>
                    <a href="{{ \App\Support\Content::whatsapp('Hi Event Experts, I would like to ask about decor for my event.') }}" target="_blank" rel="noopener" class="btn btn-onDark !px-6 !py-3.5 !text-base"><x-icon name="message-circle" class="size-4" /> WhatsApp us</a>
                @endif
            </div>
        </div>
    </div>
</section>
