@php
    $site = config('site');
    $bag = 'quote';
    $errorBag = $errors->getBag($bag);
    $failed = $errorBag->any();
    $old = fn (string $key, $default = null) => $failed ? old($key, $default) : $default;
    $requested = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) request('date')) ? request('date') : '';
    // Re-open the step that holds the first error.
    $stepFields = [['event_type', 'event_date', 'date_flexible', 'venue', 'guests'], ['needs', 'vision', 'attachment'], ['name', 'email', 'phone', 'contact_method', 'heard']];
    $start = 0;
    foreach ($stepFields as $index => $fields) {
        if ($errorBag->hasAny($fields)) { $start = $index; break; }
    }
@endphp
<x-layout title="Plan your event" description="Check your date and tell Event Experts Ltd about your wedding or event to receive ideas and a quote.">
    <x-page-hero eyebrow="Plan your event" title="Tell us about your day."
        lead="Three short steps. We will come back to you with ideas and a quote, by WhatsApp, phone or email, whichever you prefer." />

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <form id="form-quote" method="POST" action="{{ route('enquiries.store', 'quote') }}" enctype="multipart/form-data"
                    class="card scroll-mt-28" x-data="stepper(3, {{ $start }})">
                    @csrf
                    <x-form-status type="quote" title="We have your event details." message="Thank you. A member of our team will be in touch to talk through your ideas." />

                    {{-- Progress --}}
                    <ol class="mb-8 grid grid-cols-3 gap-2 text-xs font-semibold">
                        @foreach (['The event', 'Your vision', 'Your details'] as $label)
                            <li>
                                <button type="button" class="block w-full text-left" @click="{{ $loop->index }} < step && go({{ $loop->index }})" :class="{{ $loop->index }} < step ? 'cursor-pointer' : 'cursor-default'">
                                    <span class="block h-1 rounded-full transition" :class="step >= {{ $loop->index }} ? 'bg-brand-600' : 'bg-line'"></span>
                                    <span class="mt-2 block" :class="step >= {{ $loop->index }} ? 'text-ink' : 'text-muted'">{{ $loop->iteration }}. {{ $label }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ol>

                    {{-- Step 1 --}}
                    <div data-step="0" x-show="step === 0">
                        <h2 class="h-card">What are we celebrating?</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <x-field name="event_type" type="select" label="Type of event" :bag="$bag" required :options="$site['event_types']" placeholder="Choose one" class="sm:col-span-2" />

                            <div class="sm:col-span-2" x-data="dateChecker(@js((object) $booked), @js($old('event_date', $requested)))">
                                <label for="quote-event_date" class="label">Event date</label>
                                <input type="date" id="quote-event_date" name="event_date" x-model="date" :min="today" class="input sm:max-w-xs {{ $errorBag->has('event_date') ? 'input-error' : '' }}">
                                @if ($errorBag->has('event_date'))
                                    <p class="field-error">{{ $errorBag->first('event_date') }}</p>
                                @endif
                                <div class="mt-2 text-sm" aria-live="polite">
                                    <p x-cloak x-show="status === 'open'" class="flex items-center gap-2 font-medium text-emerald-700"><x-icon name="calendar-check" class="size-4 shrink-0" /> <span><span x-text="label"></span> is open on our calendar.</span></p>
                                    <p x-cloak x-show="status === 'limited'" class="flex items-center gap-2 font-medium text-amber-700"><x-icon name="calendar-days" class="size-4 shrink-0" /> <span><span x-text="label"></span> has limited availability. Send your details and we will confirm.</span></p>
                                    <p x-cloak x-show="status === 'booked'" class="flex items-center gap-2 font-medium text-rose-700"><x-icon name="calendar-x" class="size-4 shrink-0" /> <span><span x-text="label"></span> is fully booked. You can still send your details with another date in mind.</span></p>
                                    <p x-cloak x-show="status === 'past'" class="text-muted">Choose a date in the future.</p>
                                </div>
                                <label class="mt-3 flex items-center gap-2.5 text-sm">
                                    <input type="hidden" name="date_flexible" value="0">
                                    <input type="checkbox" name="date_flexible" value="1" class="size-4 rounded accent-brand-600" @checked($old('date_flexible'))> My date is flexible or not set yet
                                </label>
                            </div>

                            <x-field name="venue" label="Venue or area" :bag="$bag" placeholder="e.g. Hyatt Regency, or at home in Chaguanas" />
                            <x-field name="guests" type="select" label="Number of guests" :bag="$bag" required :options="$site['guest_ranges']" placeholder="Choose a range" />
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div data-step="1" x-show="step === 1" x-cloak>
                        <h2 class="h-card">What would you like us to do?</h2>
                        <fieldset class="mt-5">
                            <legend class="label">Choose all that apply <span class="text-brand-600" aria-hidden="true">*</span></legend>
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($site['needs'] as $need)
                                    <label class="option">
                                        <input type="checkbox" name="needs[]" value="{{ $need }}" class="peer sr-only" @checked(in_array($need, (array) $old('needs', [])))>
                                        <span><x-icon name="check" class="size-4 shrink-0 text-brand-600 opacity-0 transition [input:checked+span>&]:opacity-100" /> {{ $need }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if ($errorBag->has('needs'))
                                <p class="field-error">{{ $errorBag->first('needs') }}</p>
                            @endif
                        </fieldset>

                        <x-field name="vision" type="textarea" label="Describe your vision" :bag="$bag" class="mt-4" rows="5"
                            placeholder="Colours, flowers, a theme, something you saw at one of our events…" />

                        <div class="mt-4" x-data="fileField()">
                            <span class="label">Inspiration photo <span class="font-normal text-muted">(optional)</span></span>
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-dashed border-line bg-paper p-4 transition hover:border-ink/40">
                                <span class="icon-tile"><x-icon name="upload" /></span>
                                <span class="min-w-0 text-sm">
                                    <span class="block font-semibold" x-text="name || 'Upload a photo or PDF'">Upload a photo or PDF</span>
                                    <span class="block text-xs text-muted">JPG, PNG, WebP or PDF, up to {{ \App\Support\Content::uploadLimitMb() }} MB</span>
                                </span>
                                <input type="file" name="attachment" class="sr-only" accept=".jpg,.jpeg,.png,.webp,.pdf" @change="pick($event)">
                            </label>
                            @if ($errorBag->has('attachment'))
                                <p class="field-error">{{ $errorBag->first('attachment') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div data-step="2" x-show="step === 2" x-cloak>
                        <h2 class="h-card">How do we reach you?</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <x-field name="name" label="Your name" :bag="$bag" required autocomplete="name" />
                            <x-field name="phone" type="tel" label="Phone or WhatsApp" :bag="$bag" required autocomplete="tel" placeholder="868-000-0000" />
                            <x-field name="email" type="email" label="Email" :bag="$bag" required autocomplete="email" />
                            <x-field name="contact_method" type="select" label="Best way to reach you" :bag="$bag" required :options="$site['contact_methods']" value="WhatsApp" />
                            <x-field name="heard" type="select" label="How did you hear about us?" :bag="$bag" :options="$site['heard']" placeholder="Optional" class="sm:col-span-2" />
                        </div>
                    </div>

                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-6">
                        <button type="button" class="btn btn-light" x-show="step > 0" x-cloak @click="go(step - 1)"><x-icon name="arrow-left" class="size-4" /> Back</button>
                        <span x-show="step === 0"></span>
                        <button type="button" class="btn btn-dark" x-show="step < steps - 1" @click="next()">Continue <x-icon name="arrow-right" class="size-4" /></button>
                        <button type="submit" class="btn btn-primary" x-show="step === steps - 1" x-cloak>Send my event details <x-icon name="send" class="size-4" /></button>
                    </div>
                </form>
            </div>

            <aside class="space-y-5 lg:col-span-4">
                <div class="rounded-3xl bg-ink p-6 text-white sm:p-8" data-reveal>
                    <p class="text-xs font-semibold tracking-[0.16em] text-white/50 uppercase">Prefer to talk?</p>
                    <p class="mt-3 font-display text-3xl font-semibold">Call or WhatsApp us.</p>
                    <p class="mt-2 text-sm text-white/60">We do not take enquiries by direct message on Instagram or Facebook.</p>
                    <div class="mt-5 space-y-2">
                        <a href="{{ \App\Support\Content::whatsapp('Hi Event Experts, I would like a quote for my event.') }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><x-icon name="message-circle" class="size-4" /> WhatsApp {{ $site['phone'] }}</a>
                        <a href="tel:{{ $site['phone_alt_href'] }}" class="btn btn-onDark w-full"><x-icon name="phone" class="size-4" /> {{ $site['phone_alt'] }}</a>
                    </div>
                </div>
                <div class="card" data-reveal style="--reveal-delay: 100ms">
                    <h2 class="h-card">What happens next</h2>
                    <ol class="mt-4 space-y-4 text-sm">
                        @foreach ($site['process'] as [$icon, $title, $text])
                            <li class="flex gap-3">
                                <x-icon :name="$icon" class="mt-0.5 size-[18px] shrink-0 text-brand-600" />
                                <span><strong class="block font-semibold">{{ $title }}</strong><span class="text-muted">{{ $text }}</span></span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
