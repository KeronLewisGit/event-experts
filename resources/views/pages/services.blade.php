@php
    $services = config('site.services');
@endphp
<x-layout title="Services" description="Luxury wedding decor, micro weddings, corporate and state events, parties and event rentals from Event Experts Ltd in Trinidad and Tobago.">
    <x-page-hero eyebrow="Services" title="Decor, management and rentals."
        lead="One company for the design, the build and the pieces themselves. Choose where you would like to start.">
        <a href="{{ route('plan') }}" class="btn btn-primary">Plan your event <x-icon name="arrow-right" class="size-4" /></a>
        <a href="{{ route('portfolio') }}" class="btn btn-onDark">See our work</a>
    </x-page-hero>

    <section class="section">
        <div class="wrap space-y-6">
            @foreach ($services as $slug => $service)
                <article class="grid overflow-hidden rounded-[2rem] bg-white shadow-card ring-1 ring-line/70 lg:grid-cols-2" data-reveal>
                    <div @class(['aspect-[16/10] overflow-hidden bg-paper-deep lg:aspect-auto lg:min-h-96', 'lg:order-2' => $loop->even])>
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['name'] }}" class="size-full object-cover" loading="lazy">
                    </div>
                    <div class="flex flex-col justify-center p-8 sm:p-12">
                        <p class="eyebrow">{{ $service['eyebrow'] }}</p>
                        <h2 class="h-section mt-4">{{ $service['name'] }}</h2>
                        <p class="lead mt-4">{{ $service['intro'] }}</p>
                        <ul class="mt-6 space-y-2.5 text-sm">
                            @foreach ($service['points'] as [$icon, $title])
                                <li class="flex items-center gap-3"><x-icon :name="$icon" class="size-[18px] shrink-0 text-brand-600" /> <span class="font-medium">{{ $title }}</span></li>
                            @endforeach
                        </ul>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('service', $slug) }}" class="btn btn-dark">Explore {{ Str::lower($service['name']) }} <x-icon name="arrow-right" class="size-4" /></a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <x-cta-band />
</x-layout>
