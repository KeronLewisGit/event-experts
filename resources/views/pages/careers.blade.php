@php
    $site = config('site');
    $bag = 'career';
    $errorBag = $errors->getBag($bag);
    $roles = [...$vacancies->pluck('title')->all(), 'Event staff', 'General application'];
@endphp
<x-layout title="Careers" description="Join the Event Experts team. See open roles and apply online.">
    <x-page-hero eyebrow="Careers" title="Join our team."
        lead="Great events start behind the scenes. We are always glad to hear from reliable, hands-on people who want to help build them.">
        <a href="#form-career" class="btn btn-primary">Apply now <x-icon name="arrow-right" class="size-4" /></a>
    </x-page-hero>

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="eyebrow">Open roles</p>
                <h2 class="h-section mt-4">{{ $vacancies->isEmpty() ? 'No open roles right now.' : 'We’re hiring.' }}</h2>

                <div class="mt-8 space-y-5">
                    @forelse ($vacancies as $vacancy)
                        <article class="card" data-reveal>
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-display text-3xl font-semibold">{{ $vacancy->title }}</h3>
                                    @if ($vacancy->location)
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-muted"><x-icon name="map-pin" class="size-4 text-brand-600" /> {{ $vacancy->location }}</p>
                                    @endif
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Open</span>
                            </div>
                            @if ($vacancy->summary)
                                <p class="mt-4 text-muted">{{ $vacancy->summary }}</p>
                            @endif
                            @if ($vacancy->requirementList())
                                <h4 class="mt-6 font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase">Requirements</h4>
                                <ul class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                                    @foreach ($vacancy->requirementList() as $requirement)
                                        <li class="flex items-start gap-2.5"><x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-600" /> {{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </article>
                    @empty
                        <p class="lead">You are still welcome to send a general application. We keep resumes on file for event staff roles.</p>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-5" data-reveal style="--reveal-delay: 120ms">
                <form id="form-career" method="POST" action="{{ route('enquiries.store', 'career') }}" enctype="multipart/form-data" class="card scroll-mt-28 bg-white lg:sticky lg:top-28">
                    @csrf
                    <h2 class="h-card">Apply online</h2>
                    <p class="mt-1 mb-6 text-sm text-muted">Attach your resume and we will be in touch if there is a fit.</p>
                    <x-form-status type="career" title="Application received." message="Thank you for your interest in joining the team." />
                    <div class="space-y-4">
                        <x-field name="role" type="select" label="Role" :bag="$bag" required :options="$roles" />
                        <x-field name="name" label="Full name" :bag="$bag" required autocomplete="name" />
                        <x-field name="phone" type="tel" label="Phone or WhatsApp" :bag="$bag" required autocomplete="tel" />
                        <x-field name="email" type="email" label="Email" :bag="$bag" required autocomplete="email" />
                        <x-field name="message" type="textarea" label="Tell us about your experience" :bag="$bag" rows="3" />
                        <div x-data="fileField()">
                            <span class="label">Resume <span class="text-brand-600" aria-hidden="true">*</span></span>
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-dashed border-line bg-paper p-4 transition hover:border-ink/40">
                                <span class="icon-tile"><x-icon name="upload" /></span>
                                <span class="min-w-0 text-sm">
                                    <span class="block font-semibold" x-text="name || 'Upload your resume'">Upload your resume</span>
                                    <span class="block text-xs text-muted">PDF or Word, up to {{ \App\Support\Content::uploadLimitMb() }} MB</span>
                                </span>
                                <input type="file" name="attachment" class="sr-only" accept=".pdf,.doc,.docx" required @change="pick($event)">
                            </label>
                            @if ($errorBag->has('attachment'))
                                <p class="field-error">{{ $errorBag->first('attachment') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                    <button type="submit" class="btn btn-primary mt-6 w-full">Send application <x-icon name="send" class="size-4" /></button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
