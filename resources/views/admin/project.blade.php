@php
    $editing = $project->exists;
    $images = $project->images();
@endphp
<x-admin :title="$editing ? $project->title : 'Add an event'">
    <x-slot:actions>
        @if ($editing)
            <a href="{{ route('portfolio.show', $project) }}" target="_blank" class="btn btn-light !py-2.5">View on website <x-icon name="arrow-up-right" class="size-4" /></a>
        @endif
        <a href="{{ route('admin.projects') }}" class="btn btn-light !py-2.5"><x-icon name="arrow-left" class="size-4" /> Portfolio</a>
    </x-slot:actions>

    <form method="POST" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="card">
                <h2 class="font-display text-2xl font-semibold">The event</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <x-field name="title" label="Title" required :value="$project->title" placeholder="e.g. Dillon & Kristal" class="sm:col-span-2" />
                    <x-field name="category" type="select" label="Type of event" required :options="config('site.categories')" :value="$project->category" placeholder="Choose one" />
                    <x-field name="event_date" type="date" label="Event date" :value="$project->event_date?->toDateString()" hint="Only the year is shown on the website." />
                    <x-field name="venue" label="Venue" :value="$project->venue" placeholder="e.g. Hyatt Regency Trinidad" class="sm:col-span-2" />
                    <x-field name="summary" label="One-line summary" required :value="$project->summary" maxlength="255" class="sm:col-span-2" hint="Shown on the portfolio card." />
                    <x-field name="body" type="textarea" label="The story" :value="$project->body" rows="7" class="sm:col-span-2" hint="Leave a blank line between paragraphs." />
                </div>
            </section>

            <section class="card">
                <h2 class="font-display text-2xl font-semibold">Photos</h2>
                @if ($images)
                    <p class="mt-1 text-sm text-muted">The first photo is the cover. Tick a photo to remove it when you save.</p>
                    <div class="mt-5 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($images as $path)
                            <label class="group relative block cursor-pointer overflow-hidden rounded-2xl bg-paper-deep ring-1 ring-line">
                                <img src="{{ asset($path) }}" alt="" class="aspect-square w-full object-cover transition peer-checked:opacity-40" loading="lazy">
                                @if ($loop->first)
                                    <span class="absolute top-2 left-2 rounded-full bg-ink px-2 py-0.5 text-[10px] font-semibold text-white uppercase">Cover</span>
                                @endif
                                <span class="absolute right-2 bottom-2 flex items-center gap-1.5 rounded-full bg-white/95 px-2 py-1 text-[11px] font-semibold shadow-sm">
                                    <input type="checkbox" name="remove[]" value="{{ $path }}" class="size-3.5 accent-rose-600"> Remove
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="block" x-data="fileField()">
                        <span class="label">{{ $images ? 'Replace the cover' : 'Cover photo' }}</span>
                        <span class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-line bg-paper p-4 text-sm transition hover:border-ink/40">
                            <x-icon name="upload" class="size-5 shrink-0 text-brand-700" />
                            <span class="min-w-0 truncate font-semibold" x-text="name || 'Choose a photo'">Choose a photo</span>
                            <input type="file" name="cover_file" class="sr-only" accept=".jpg,.jpeg,.png,.webp" @change="pick($event)">
                        </span>
                    </label>
                    <label class="block" x-data="fileField()">
                        <span class="label">Add gallery photos</span>
                        <span class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-line bg-paper p-4 text-sm transition hover:border-ink/40">
                            <x-icon name="images" class="size-5 shrink-0 text-brand-700" />
                            <span class="min-w-0 truncate font-semibold" x-text="name || 'Choose up to 12 photos'">Choose up to 12 photos</span>
                            <input type="file" name="photos[]" multiple class="sr-only" accept=".jpg,.jpeg,.png,.webp" @change="pick($event)">
                        </span>
                    </label>
                </div>
                <p class="hint">JPG, PNG or WebP, up to 6 MB each.</p>
            </section>
        </div>

        <div class="space-y-6">
            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Publishing</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="is_published" value="1" class="mt-0.5 size-4 rounded accent-brand-600" @checked(old('is_published', $project->is_published))>
                        <span><strong class="block font-semibold">Show on the website</strong><span class="text-muted">Untick to keep it as a draft.</span></span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="mt-0.5 size-4 rounded accent-brand-600" @checked(old('is_featured', $project->is_featured))>
                        <span><strong class="block font-semibold">Feature on the home page</strong><span class="text-muted">The six newest featured events are shown.</span></span>
                    </label>
                </div>
                <button class="btn btn-primary mt-6 w-full">{{ $editing ? 'Save changes' : 'Add to portfolio' }}</button>
            </section>

            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">The team</h2>
                <x-field name="credits" type="textarea" :value="$project->creditsText()" rows="6" class="mt-4"
                    placeholder="Planning: Events by Tricia&#10;Photography: Awesome Photography" hint="One per line, as Role: Name." />
                <x-field name="instagram_url" type="url" label="Instagram post" :value="$project->instagram_url" placeholder="https://www.instagram.com/p/…" class="mt-4" />
            </section>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="mt-6" onsubmit="return confirm('Remove “{{ $project->title }}” from the portfolio? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-ghost !text-rose-700"><x-icon name="trash-2" class="size-4" /> Delete this event</button>
        </form>
    @endif
</x-admin>
