<x-admin title="Portfolio">
    <x-slot:actions>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary !py-2.5"><x-icon name="plus" class="size-4" /> Add an event</a>
    </x-slot:actions>

    <p class="max-w-2xl text-sm text-muted">Events shown on the <a href="{{ route('portfolio') }}" target="_blank" class="link">Portfolio page</a>. Featured events also appear on the home page.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($projects as $project)
            <a href="{{ route('admin.projects.edit', $project) }}" class="card group flex gap-4 !p-4 transition hover:shadow-lift">
                <div class="size-24 shrink-0 overflow-hidden rounded-2xl bg-paper-deep">
                    @if ($project->cover)
                        <img src="{{ asset($project->cover) }}" alt="" class="size-full object-cover" loading="lazy">
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-display text-xl font-semibold group-hover:text-brand-700">{{ $project->title }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ $project->categoryLabel() }} @if ($project->dateLabel()) &middot; {{ $project->dateLabel() }} @endif &middot; {{ count($project->images()) }} {{ Str::plural('photo', count($project->images())) }}</p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @if ($project->is_published)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Live</span>
                        @else
                            <span class="rounded-full bg-paper px-2.5 py-1 text-xs font-semibold text-muted ring-1 ring-line">Draft</span>
                        @endif
                        @if ($project->is_featured)
                            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">Featured</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <p class="card text-center text-sm text-muted sm:col-span-2 xl:col-span-3">No events yet. Add your first one to start the portfolio.</p>
        @endforelse
    </div>
</x-admin>
