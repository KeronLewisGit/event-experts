@php
    $site = config('site');
    $isRentals = $slug === 'rentals';
@endphp
<x-layout :title="$service['name']" :description="$service['summary'].' '.$service['intro']">
    <x-page-hero :eyebrow="$service['eyebrow']" :title="$service['name']" :lead="$service['intro']"
        :image="$service['image']" :image-alt="$service['name'].' by Event Experts Ltd'" :crumb="['Services', route('services')]">
        @if ($isRentals)
            <a href="#form-rental" class="btn btn-primary">Ask about rentals <x-icon name="arrow-right" class="size-4" /></a>
        @else
            <a href="{{ route('plan') }}" class="btn btn-primary">Plan your event <x-icon name="arrow-right" class="size-4" /></a>
        @endif
        <a href="{{ \App\Support\Content::whatsapp('Hi Event Experts, I would like to ask about '.Str::lower($service['name']).'.') }}" target="_blank" rel="noopener" class="btn btn-onDark"><x-icon name="message-circle" class="size-4" /> WhatsApp us</a>
    </x-page-hero>

    <section class="section">
        <div class="wrap">
            <div class="max-w-2xl" data-reveal>
                <p class="eyebrow">What is included</p>
                <h2 class="h-section mt-4">{{ $service['summary'] }}</h2>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(4, count($service['points'])) }}">
                @foreach ($service['points'] as [$icon, $title, $text])
                    <div class="card" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <span class="icon-tile"><x-icon :name="$icon" /></span>
                        <h3 class="h-card mt-5">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($projects->isNotEmpty())
        <section class="section border-y border-line bg-white">
            <div class="wrap">
                <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                    <div>
                        <p class="eyebrow">From the portfolio</p>
                        <h2 class="h-section mt-4">See it built.</h2>
                    </div>
                    <a href="{{ route('portfolio') }}" class="link-arrow">Full portfolio <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($isRentals)
        @php($bag = 'rental')
        <section class="section border-t border-line bg-white">
            <div class="wrap grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-5" data-reveal>
                    <p class="eyebrow">Rental enquiry</p>
                    <h2 class="h-section mt-4">Tell us what you need.</h2>
                    <p class="lead mt-4">List the pieces you are interested in and your date. We will confirm what is available from our {{ $site['warehouse'] }} warehouse and send a price.</p>
                    <p class="mt-6 flex items-center gap-3 text-sm"><span class="icon-tile"><x-icon name="phone" /></span> <span>Prefer to talk?<br><a href="tel:{{ $site['phone_href'] }}" class="text-base font-semibold">{{ $site['phone'] }}</a></span></p>
                </div>
                <div class="lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
                    <form id="form-rental" method="POST" action="{{ route('enquiries.store', 'rental') }}" class="card scroll-mt-28 bg-paper">
                        @csrf
                        <x-form-status type="rental" title="Rental enquiry sent." message="We will check our stock for your date and get back to you." />
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-field name="name" label="Name" :bag="$bag" required autocomplete="name" />
                            <x-field name="phone" type="tel" label="Phone or WhatsApp" :bag="$bag" required autocomplete="tel" />
                            <x-field name="email" type="email" label="Email" :bag="$bag" required autocomplete="email" />
                            <x-field name="event_date" type="date" label="Event date" :bag="$bag" hint="Leave empty if you are not sure yet." />
                            <x-field name="items" type="textarea" label="What would you like to rent?" :bag="$bag" required class="sm:col-span-2" rows="3" placeholder="e.g. grass wall, two glass tables, centrepieces for 12 tables" />
                            <x-field name="message" type="textarea" label="Anything else we should know?" :bag="$bag" class="sm:col-span-2" rows="3" />
                        </div>
                        <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                        <button type="submit" class="btn btn-primary mt-5">Send rental enquiry <x-icon name="arrow-right" class="size-4" /></button>
                    </form>
                </div>
            </div>
        </section>
    @else
        <x-help-band :topic="$service['topic']" :title="'Ask us about '.Str::lower($service['name']).'.'" />
    @endif
</x-layout>
