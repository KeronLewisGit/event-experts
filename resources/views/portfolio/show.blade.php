@php
    $images = $project->images();
@endphp
<x-layout :title="$project->title" :description="$project->summary">
    <article x-data="gallery(@js(array_map(fn ($path) => asset($path), $images)))" @keydown.escape.window="open = false" @keydown.arrow-right.window="open && move(1)" @keydown.arrow-left.window="open && move(-1)">
        <header class="relative overflow-hidden bg-ink text-white">
            <div class="grid-ink absolute inset-0 opacity-50"></div>
            <div class="wrap relative max-w-5xl py-14 sm:py-20">
                <nav class="mb-6 flex items-center gap-2 text-xs font-medium text-white/50" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white">Home</a><span>/</span>
                    <a href="{{ route('portfolio') }}" class="hover:text-white">Portfolio</a>
                </nav>
                <p class="flex flex-wrap items-center gap-3 text-xs font-semibold text-white/60">
                    <span class="rounded-full bg-brand-600 px-2.5 py-1 text-white">{{ $project->categoryLabel() }}</span>
                    @if ($project->venue)<span class="flex items-center gap-1.5"><x-icon name="map-pin" class="size-3.5" /> {{ $project->venue }}</span>@endif
                    @if ($project->dateLabel())<span>{{ $project->dateLabel() }}</span>@endif
                    @unless ($project->is_published)<span class="rounded-full bg-white/15 px-2.5 py-1 text-white">Draft, visible to staff only</span>@endunless
                </p>
                <h1 class="h-display mt-5">{{ $project->title }}</h1>
                <p class="mt-5 max-w-2xl text-xl leading-relaxed text-white/75">{{ $project->summary }}</p>
            </div>
        </header>

        <div class="wrap max-w-5xl py-12 sm:py-16">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    @if ($project->body)
                        <div class="prose-site">
                            @foreach (preg_split('/\R{2,}/', trim($project->body)) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('plan') }}" class="btn btn-primary">Plan something like this <x-icon name="arrow-right" class="size-4" /></a>
                        @if ($project->instagram_url)
                            <a href="{{ $project->instagram_url }}" target="_blank" rel="noopener" class="btn btn-light">View on Instagram <x-icon name="arrow-up-right" class="size-4" /></a>
                        @endif
                    </div>
                </div>
                @if ($project->credits)
                    <aside class="lg:col-span-5">
                        <div class="card bg-paper">
                            <h2 class="font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase">The team</h2>
                            <dl class="mt-4 divide-y divide-line text-sm">
                                <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Decor and design</dt><dd class="text-right font-semibold">{{ config('site.name') }}</dd></div>
                                @foreach ($project->credits as [$role, $name])
                                    <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">{{ $role }}</dt><dd class="text-right font-semibold">{{ $name }}</dd></div>
                                @endforeach
                            </dl>
                        </div>
                    </aside>
                @endif
            </div>

            @if ($images)
                <div class="mt-12 columns-2 gap-4 lg:columns-3">
                    @foreach ($images as $path)
                        <button type="button" class="group mb-4 block w-full overflow-hidden rounded-2xl bg-paper-deep ring-1 ring-line" @click="show({{ $loop->index }})" aria-label="Open photo {{ $loop->iteration }} of {{ count($images) }}">
                            <img src="{{ asset($path) }}" alt="{{ $project->title }}, photo {{ $loop->iteration }}" class="w-full transition duration-700 group-hover:scale-[1.03]" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Lightbox --}}
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink/95 p-4" role="dialog" aria-modal="true" aria-label="Photo viewer" @click.self="open = false">
            <img :src="images[index]" alt="" class="max-h-full max-w-full rounded-xl object-contain">
            <button type="button" class="absolute top-4 right-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="open = false" aria-label="Close"><x-icon name="x" /></button>
            <template x-if="images.length > 1">
                <div>
                    <button type="button" class="absolute top-1/2 left-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(-1)" aria-label="Previous photo"><x-icon name="chevron-left" /></button>
                    <button type="button" class="absolute top-1/2 right-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(1)" aria-label="Next photo"><x-icon name="chevron-right" /></button>
                    <p class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white" x-text="(index + 1) + ' / ' + images.length"></p>
                </div>
            </template>
        </div>
    </article>

    @if ($more->isNotEmpty())
        <section class="section border-t border-line bg-white">
            <div class="wrap">
                <h2 class="h-section">More from the portfolio</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($more as $other)
                        <x-project-card :project="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
