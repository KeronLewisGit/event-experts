<x-admin :title="$enquiry->reference">
    <x-slot:actions>
        <a href="{{ route('admin.enquiries') }}" class="btn btn-light !py-2.5"><x-icon name="arrow-left" class="size-4" /> All enquiries</a>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card xl:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="icon-tile"><x-icon :name="$enquiry->typeIcon()" /></span>
                    <div>
                        <p class="text-sm text-muted">{{ $enquiry->typeLabel() }} &middot; received {{ $enquiry->created_at->format('j F Y, g:ia') }}</p>
                        <h2 class="font-display text-3xl font-semibold">{{ $enquiry->summary }}</h2>
                    </div>
                </div>
                <x-status-badge :enquiry="$enquiry" />
            </div>

            @if ($enquiry->is_sample)
                <p class="mt-5 rounded-xl bg-paper px-4 py-3 text-sm text-muted ring-1 ring-line">This is sample data seeded for the demo. Real submissions from the website appear alongside it.</p>
            @endif

            <dl class="mt-6 divide-y divide-line border-t border-line text-sm">
                @foreach ($enquiry->details() as $label => $value)
                    <div class="grid gap-1 py-3.5 sm:grid-cols-3">
                        <dt class="text-muted">{{ $label }}</dt>
                        <dd class="font-medium whitespace-pre-line sm:col-span-2">{{ $value }}</dd>
                    </div>
                @endforeach
                @if ($enquiry->attachment_path)
                    <div class="grid gap-1 py-3.5 sm:grid-cols-3">
                        <dt class="text-muted">Attachment</dt>
                        <dd class="sm:col-span-2">
                            <a href="{{ route('admin.enquiry.attachment', $enquiry) }}" class="inline-flex items-center gap-2 rounded-full bg-paper px-3.5 py-1.5 font-semibold ring-1 ring-line transition hover:ring-ink/40">
                                <x-icon name="paperclip" class="size-4" /> {{ $enquiry->attachment_name }}
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </section>

        <div class="space-y-6">
            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Customer</h2>
                <p class="mt-3 font-semibold">{{ $enquiry->name }}</p>
                <div class="mt-3 space-y-2 text-sm">
                    <a href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: your '.Str::lower($enquiry->typeLabel()).' ('.$enquiry->reference.')') }}" class="flex items-center gap-2.5 hover:text-brand-700"><x-icon name="mail" class="size-4 text-muted" /> {{ $enquiry->email }}</a>
                    @if ($enquiry->phone)
                        <a href="tel:{{ $enquiry->phone }}" class="flex items-center gap-2.5 hover:text-brand-700"><x-icon name="phone" class="size-4 text-muted" /> {{ $enquiry->phone }}</a>
                    @endif
                </div>
                @if ($enquiry->whatsappUrl())
                    <a href="{{ $enquiry->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-4 w-full"><x-icon name="message-circle" class="size-4" /> Reply on WhatsApp</a>
                @endif
            </section>

            @if ($eventDate)
                <section class="card !p-6">
                    <h2 class="font-display text-2xl font-semibold">Event date</h2>
                    <p class="mt-2 text-sm font-semibold">{{ $eventDate->format('l j F Y') }}</p>
                    @if ($bookedDate)
                        <p class="mt-2 flex items-center gap-2 text-sm text-muted"><x-icon name="calendar-x" class="size-4 text-rose-600" /> Marked “{{ Str::lower($bookedDate->statusLabel()) }}” on the calendar.</p>
                    @elseif ($eventDate->isFuture())
                        <p class="mt-2 text-sm text-muted">Open on the website calendar.</p>
                        <form method="POST" action="{{ route('admin.dates.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="date" value="{{ $eventDate->toDateString() }}">
                            <input type="hidden" name="status" value="booked">
                            <input type="hidden" name="note" value="{{ $enquiry->reference }} · {{ $enquiry->name }}">
                            <button class="btn btn-light w-full"><x-icon name="calendar-x" class="size-4" /> Mark this date as booked</button>
                        </form>
                    @endif
                </section>
            @endif

            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Follow up</h2>
                <form method="POST" action="{{ route('admin.enquiry.update', $enquiry) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')
                    <label class="block">
                        <span class="label">Status</span>
                        <select name="status" class="input">
                            @foreach (\App\Models\Enquiry::STATUSES as $status => $label)
                                <option value="{{ $status }}" @selected($enquiry->status === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="label">Internal notes</span>
                        <textarea name="notes" rows="4" class="input" placeholder="Visible to staff only">{{ $enquiry->notes }}</textarea>
                    </label>
                    <button class="btn btn-primary w-full">Save</button>
                </form>
            </section>

            <form method="POST" action="{{ route('admin.enquiry.destroy', $enquiry) }}" onsubmit="return confirm('Delete {{ $enquiry->reference }}? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-ghost w-full !text-rose-700"><x-icon name="trash-2" class="size-4" /> Delete enquiry</button>
            </form>
        </div>
    </div>
</x-admin>
