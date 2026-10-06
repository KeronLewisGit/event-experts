@php
    $site = config('site');
@endphp
<x-layout title="About us" description="Event Experts Ltd is an event decor, management and rental company in Chaguanas with 20 years of experience across Trinidad and Tobago.">
    <x-page-hero eyebrow="About us" title="Twenty years of making it happen."
        :lead="$site['intro']" image="images/about-crew.jpg" image-alt="The Event Experts crew setting up a floral installation">
        <a href="{{ route('portfolio') }}" class="btn btn-primary">See our work <x-icon name="arrow-right" class="size-4" /></a>
        <a href="{{ route('careers') }}" class="btn btn-onDark">Join the team</a>
    </x-page-hero>

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-6" data-reveal>
                <p class="eyebrow">Our story</p>
                <h2 class="h-section mt-4">We love what we do, and we think it shows.</h2>
                <div class="prose-site mt-6">
                    <p>Event Experts Ltd designs and builds weddings, corporate events and celebrations across Trinidad and Tobago. Couples know us for floral overheads, stage design and custom glass tables. Institutions have trusted us with occasions such as the re-opening of the Red House and the first sitting of Parliament.</p>
                    <p>The work you see on the day is the last step. Behind every event are months of planning, ordering, designing and prepping in our {{ $site['warehouse'] }} warehouse, and a crew that builds it, then takes it all down again.</p>
                    <p>We take pride in knowing that when you decide to tie the knot, we will stand by you throughout, until that dream wedding is made a reality.</p>
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:col-span-6">
                @foreach ([
                    ['gem', 'Design led', 'The bride and groom know what they want. We guide them along to create decor that works in the room.'],
                    ['warehouse', 'Our own stock', 'Tables, backdrops and decor pieces come from our own warehouse, not a catalogue.'],
                    ['users', 'A real team', 'Nothing is done by one person alone. Designers, florists and crew work as one.'],
                    ['handshake', 'Good company', 'We work alongside the country’s leading planners, venues, lighting and photography teams.'],
                ] as [$icon, $title, $text])
                    <div class="card" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <span class="icon-tile"><x-icon :name="$icon" /></span>
                        <h3 class="h-card mt-5">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section border-y border-line bg-white">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div>
                    <p class="eyebrow">Recent work</p>
                    <h2 class="h-section mt-4">Making magic, one event at a time.</h2>
                </div>
                <a href="{{ route('portfolio') }}" class="link-arrow">Full portfolio <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" tall data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms" />
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layout>
