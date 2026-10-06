@props(['project', 'tall' => false])
<a href="{{ route('portfolio.show', $project) }}" {{ $attributes->merge(['class' => 'group relative block overflow-hidden rounded-3xl bg-ink shadow-card ring-1 ring-line/70 transition hover:-translate-y-1 hover:shadow-lift']) }}>
    <div @class(['overflow-hidden', 'aspect-[3/4]' => $tall, 'aspect-[4/5]' => ! $tall])>
        @if ($project->cover)
            <img src="{{ asset($project->cover) }}" alt="{{ $project->title }}" class="size-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
        @endif
    </div>
    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink via-ink/75 to-transparent p-6 pt-24 text-white">
        <p class="flex flex-wrap items-center gap-2 text-[11px] font-semibold tracking-[0.16em] text-brand-200 uppercase">
            {{ $project->categoryLabel() }}
            @if ($project->dateLabel())
                <span class="text-white/40">&middot;</span> <span class="text-white/70">{{ $project->dateLabel() }}</span>
            @endif
        </p>
        <h3 class="mt-2 font-display text-3xl leading-tight font-semibold">{{ $project->title }}</h3>
        <p class="mt-1.5 line-clamp-2 text-sm text-white/70">{{ $project->summary }}</p>
        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-200 transition group-hover:gap-2.5">View the event <x-icon name="arrow-right" class="size-4" /></span>
    </div>
</a>
