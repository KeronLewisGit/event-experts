@php
    $categories = collect(config('site.categories'))->only($projects->pluck('category')->unique());
@endphp
<x-layout title="Portfolio" description="Weddings, corporate events and celebrations designed and built by Event Experts Ltd.">
    <x-page-hero eyebrow="Portfolio" title="Our work, event by event."
        lead="Real weddings and events from across Trinidad and Tobago, with the planners, venues and suppliers we built them with." />

    <section class="section" x-data="{ category: 'all' }">
        <div class="wrap">
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter by type of event">
                <button type="button" class="chip !px-4 !py-2 !text-sm transition" :class="category === 'all' && '!bg-ink !text-white !ring-ink'" @click="category = 'all'" :aria-pressed="category === 'all'">All work <span class="opacity-60">{{ $projects->count() }}</span></button>
                @foreach ($categories as $key => $label)
                    <button type="button" class="chip !px-4 !py-2 !text-sm transition" :class="category === '{{ $key }}' && '!bg-ink !text-white !ring-ink'" @click="category = '{{ $key }}'" :aria-pressed="category === '{{ $key }}'">{{ $label }} <span class="opacity-60">{{ $projects->where('category', $key)->count() }}</span></button>
                @endforeach
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $project)
                    <x-project-card :project="$project" x-show="category === 'all' || category === '{{ $project->category }}'" x-transition.opacity.duration.300ms />
                @empty
                    <p class="text-muted sm:col-span-2 lg:col-span-3">New work is on its way. Follow us on Instagram in the meantime.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-cta-band title="Yours could be next." />
</x-layout>
