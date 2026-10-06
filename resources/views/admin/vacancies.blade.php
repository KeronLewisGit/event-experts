<x-admin title="Careers">
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card !p-0 xl:col-span-2">
            <div class="p-6 pb-4">
                <h2 class="font-display text-2xl font-semibold">Vacancies</h2>
                <p class="mt-1 text-sm text-muted">Open roles appear on the <a href="{{ route('careers') }}" target="_blank" class="link">Careers page</a>. Applications arrive in <a href="{{ route('admin.enquiries', ['type' => 'career']) }}" class="link">Enquiries</a> with the resume attached.</p>
            </div>
            <ul class="divide-y divide-line border-t border-line">
                @forelse ($vacancies as $vacancy)
                    <li class="flex flex-wrap items-center gap-4 px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold">{{ $vacancy->title }}</p>
                            <p class="mt-0.5 text-xs text-muted">{{ $vacancy->location ?: 'No location' }} &middot; {{ count($vacancy->requirementList()) }} {{ Str::plural('requirement', count($vacancy->requirementList())) }}</p>
                        </div>
                        @if ($vacancy->is_open)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Open</span>
                        @else
                            <span class="rounded-full bg-paper px-2.5 py-1 text-xs font-semibold text-muted ring-1 ring-line">Closed</span>
                        @endif
                        <form method="POST" action="{{ route('admin.vacancies.toggle', $vacancy) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-light !px-3.5 !py-2">{{ $vacancy->is_open ? 'Close' : 'Re-open' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.vacancies.destroy', $vacancy) }}" onsubmit="return confirm('Remove this vacancy?')">
                            @csrf
                            @method('DELETE')
                            <button class="grid size-9 place-items-center rounded-full text-muted transition hover:bg-rose-50 hover:text-rose-700" aria-label="Remove vacancy"><x-icon name="trash-2" class="size-4" /></button>
                        </form>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-muted">No vacancies yet.</li>
                @endforelse
            </ul>
        </section>

        <section class="card !p-6">
            <h2 class="font-display text-2xl font-semibold">New vacancy</h2>
            <form method="POST" action="{{ route('admin.vacancies.store') }}" class="mt-4 space-y-4">
                @csrf
                <label class="block"><span class="label">Job title</span><input type="text" name="title" value="{{ old('title') }}" maxlength="120" class="input" placeholder="e.g. Event staff" required></label>
                <label class="block"><span class="label">Location</span><input type="text" name="location" value="{{ old('location') }}" maxlength="120" class="input" placeholder="{{ config('site.warehouse') }}"></label>
                <label class="block"><span class="label">Short description</span><textarea name="summary" rows="2" maxlength="255" class="input">{{ old('summary') }}</textarea></label>
                <label class="block"><span class="label">Requirements</span><textarea name="requirements" rows="5" class="input" placeholder="One per line">{{ old('requirements') }}</textarea></label>
                <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4" /> Publish vacancy</button>
            </form>
        </section>
    </div>
</x-admin>
