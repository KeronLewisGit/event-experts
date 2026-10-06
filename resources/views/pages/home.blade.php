@php
    $site = config('site');
    $feed = [
        ['feed-1.jpg', 'DbEhTtOlLP2'], ['feed-2.jpg', 'DaikPKPDnuK'], ['feed-8.jpg', 'DZ1DYYIRau1'], ['feed-4.jpg', 'Cg26b22uTpz'],
        ['feed-5.jpg', 'CKPC5lyF9Ar'], ['feed-6.jpg', 'CJ7Gc24lW2i'], ['feed-3.jpg', 'CECPLKXBv1_'], ['feed-7.jpg', 'B7V4LLHp-vv'],
    ];
@endphp
<x-layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink text-white">
        <div class="grid-ink absolute inset-0 opacity-50"></div>
        <div class="absolute -top-48 -left-40 size-[38rem] rounded-full bg-brand-600/20 blur-3xl"></div>

        <div class="wrap relative grid items-center gap-12 pt-12 pb-16 sm:pt-16 sm:pb-24 lg:grid-cols-12">
            <div class="lg:col-span-6">
                <p class="eyebrow !text-brand-300">Luxury weddings &middot; Event decor &middot; Corporate events</p>
                <h1 class="mt-6 font-display text-6xl leading-[0.95] font-semibold sm:text-7xl lg:text-[5.5rem]">
                    You dream it.<br>
                    <span class="text-brand-300 italic">We make it happen.</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-white/70">
                    {{ $site['intro'] }}
                </p>

                <div class="mt-8 max-w-xl rounded-3xl bg-white/5 p-5 ring-1 ring-white/10 sm:p-6">
                    <x-date-check :booked="$booked" dark />
                </div>

                <ul class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-sm text-white/70">
                    <li class="flex items-center gap-2"><x-icon name="gem" class="size-[18px] text-brand-300" /> {{ $site['years'] }} years of experience</li>
                    <li class="flex items-center gap-2"><x-icon name="warehouse" class="size-[18px] text-brand-300" /> Our own warehouse and crew</li>
                    <li class="flex items-center gap-2"><x-icon name="message-circle" class="size-[18px] text-brand-300" /> Call or WhatsApp, any time</li>
                </ul>
            </div>

            {{-- Photo collage --}}
            <div class="relative lg:col-span-6" data-reveal style="--reveal-delay: 150ms">
                <div class="grid grid-cols-5 items-end gap-3 sm:gap-4">
                    <div class="col-span-3 aspect-[3/4] overflow-hidden rounded-t-full rounded-b-3xl ring-1 ring-white/15">
                        <img src="{{ asset('images/portfolio/sime-christian-1.jpg') }}" alt="A couple beneath a ceiling of suspended white florals" class="size-full object-cover" fetchpriority="high">
                    </div>
                    <div class="col-span-2 space-y-3 sm:space-y-4">
                        <div class="aspect-square overflow-hidden rounded-3xl ring-1 ring-white/15">
                            <img src="{{ asset('images/portfolio/dillon-kristal-1.jpg') }}" alt="A floral head table with a monogram backdrop" class="size-full object-cover">
                        </div>
                        <div class="aspect-[4/5] overflow-hidden rounded-3xl ring-1 ring-white/15">
                            <img src="{{ asset('images/portfolio/first-dance-on-the-clouds-2.jpg') }}" alt="A first dance on a cloud-covered floor beneath hanging florals" class="size-full object-cover">
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-5 left-4 hidden rounded-2xl bg-white px-5 py-3 text-ink shadow-lift sm:block animate-float">
                    <p class="text-[10px] font-semibold tracking-widest text-muted uppercase">Sime &amp; Christian &middot; Hyatt Regency</p>
                    <p class="font-display text-xl font-semibold">Our biggest floral overhead yet</p>
                </div>
            </div>
        </div>
        <div class="gilt h-px w-full"></div>
    </section>

    {{-- Stats --}}
    <section class="border-b border-line bg-white">
        <dl class="wrap grid grid-cols-2 gap-y-8 py-10 text-center lg:grid-cols-4">
            @foreach ([
                [$site['years'], '', 'years of experience'],
                [(int) round($site['social']['facebook_followers'] / 1000), 'K', 'followers on Facebook'],
                [(int) round($site['social']['instagram_followers'] / 1000), 'K', 'followers on Instagram'],
                [count($site['services']), '', 'services under one roof'],
            ] as [$number, $suffix, $label])
                <div>
                    <dd class="font-display text-5xl font-semibold text-brand-700 sm:text-6xl"><span x-data="countUp({{ $number }})" x-text="value">{{ $number }}</span>{{ $suffix }}</dd>
                    <dt class="mt-1 text-sm text-muted">{{ $label }}</dt>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Services --}}
    <section class="section">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div class="max-w-2xl">
                    <p class="eyebrow">What we do</p>
                    <h2 class="h-section mt-4">Design, decor and rentals for the days that matter.</h2>
                </div>
                <a href="{{ route('services') }}" class="link-arrow">All services <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($site['services'] as $slug => $service)
                    <a href="{{ route('service', $slug) }}" data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms"
                        @class(['group relative flex min-h-80 flex-col justify-end overflow-hidden rounded-3xl bg-ink p-6 text-white shadow-card transition hover:-translate-y-1 hover:shadow-lift', 'sm:col-span-2 lg:col-span-1' => $loop->last, 'lg:row-span-2' => $loop->first])>
                        <img src="{{ asset($service['image']) }}" alt="" class="absolute inset-0 size-full object-cover opacity-70 transition duration-700 group-hover:scale-105 group-hover:opacity-60" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>
                        <div class="relative">
                            <span class="grid size-11 place-items-center rounded-2xl bg-white/15 backdrop-blur"><x-icon :name="$service['icon']" /></span>
                            <p class="mt-4 text-[11px] font-semibold tracking-[0.16em] text-brand-200 uppercase">{{ $service['eyebrow'] }}</p>
                            <h3 class="mt-1 font-display text-3xl font-semibold">{{ $service['name'] }}</h3>
                            <p class="mt-1.5 text-sm text-white/75">{{ $service['summary'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured work --}}
    <section class="section border-y border-line bg-white">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div class="max-w-2xl">
                    <p class="eyebrow">Recent work</p>
                    <h2 class="h-section mt-4">Empty spaces, transformed.</h2>
                    <p class="lead mt-4">A few of the weddings and events we have designed and built, with the teams we built them with.</p>
                </div>
                <a href="{{ route('portfolio') }}" class="btn btn-dark">View the portfolio <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $project)
                    <x-project-card :project="$project" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Process --}}
    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4" data-reveal>
                <p class="eyebrow">How it works</p>
                <h2 class="h-section mt-4">Great events start behind the scenes.</h2>
                <p class="lead mt-4">Nothing is done by one person alone. It takes a team, and months of planning before the first flower goes up.</p>
                <a href="{{ route('about') }}" class="link-arrow mt-6">Meet the company <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <ol class="grid gap-5 sm:grid-cols-2 lg:col-span-8">
                @foreach ($site['process'] as [$icon, $title, $text])
                    <li class="card" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <div class="flex items-center justify-between">
                            <span class="icon-tile"><x-icon :name="$icon" /></span>
                            <span class="font-display text-5xl font-semibold text-brand-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="h-card mt-5">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Client review --}}
    <section class="relative overflow-hidden bg-ink py-16 text-white sm:py-24">
        <div class="grid-ink absolute inset-0 opacity-50"></div>
        <figure class="wrap relative max-w-4xl text-center" data-reveal>
            <x-icon name="quote" class="mx-auto size-10 text-brand-400" />
            <blockquote class="mt-6 font-display text-3xl leading-snug font-medium italic sm:text-4xl">
                &ldquo;{{ $site['review']['quote'] }}&rdquo;
            </blockquote>
            <figcaption class="mt-6 text-sm font-semibold tracking-[0.16em] text-brand-300 uppercase">{{ $site['review']['source'] }}</figcaption>
        </figure>
    </section>

    {{-- Collaborators --}}
    <section class="overflow-hidden border-b border-line bg-white py-10" aria-label="Planners, venues and suppliers we work alongside">
        <p class="wrap mb-6 text-center text-xs font-semibold tracking-[0.2em] text-muted uppercase">Working alongside Trinidad and Tobago&rsquo;s leading planners, venues and suppliers</p>
        <div class="flex w-max animate-marquee gap-12 pr-12">
            @foreach ([...$site['collaborators'], ...$site['collaborators']] as $name)
                <span class="flex items-center gap-12 font-display text-2xl font-medium whitespace-nowrap text-ink/70" @if ($loop->index >= count($site['collaborators'])) aria-hidden="true" @endif>
                    {{ $name }} <span class="size-1.5 rounded-full bg-brand-400"></span>
                </span>
            @endforeach
        </div>
    </section>

    {{-- Instagram --}}
    <section class="section">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div>
                    <p class="eyebrow">{{ $site['social']['instagram_handle'] }}</p>
                    <h2 class="h-section mt-4">Follow the work as it happens.</h2>
                </div>
                <a href="{{ $site['links']['instagram'] }}" target="_blank" rel="noopener" class="btn btn-light">Follow on Instagram <x-icon name="arrow-up-right" class="size-4" /></a>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                @foreach ($feed as [$image, $code])
                    <a href="https://www.instagram.com/p/{{ $code }}/" target="_blank" rel="noopener" class="group relative block aspect-square overflow-hidden rounded-2xl bg-paper-deep" data-reveal style="--reveal-delay: {{ ($loop->index % 4) * 60 }}ms" aria-label="View this post on Instagram">
                        <img src="{{ asset('images/'.$image) }}" alt="" class="size-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                        <span class="absolute inset-0 grid place-items-center bg-ink/0 text-white opacity-0 transition group-hover:bg-ink/40 group-hover:opacity-100"><x-icon name="arrow-up-right" class="size-6" /></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layout>
