@php
    $site = config('site');
@endphp
<x-layout title="Contact" description="Call, WhatsApp or message Event Experts Ltd in Chaguanas, Trinidad and Tobago.">
    <x-page-hero eyebrow="Contact us" title="Let’s talk about your event."
        lead="The quickest way to reach us is a call or a WhatsApp message. You can also send a note here and we will reply.">
        <a href="{{ \App\Support\Content::whatsapp('Hi Event Experts, I would like to ask about decor for my event.') }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><x-icon name="message-circle" class="size-4" /> WhatsApp {{ $site['phone'] }}</a>
        <a href="tel:{{ $site['phone_href'] }}" class="btn btn-onDark"><x-icon name="phone" class="size-4" /> Call us</a>
    </x-page-hero>

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7" data-reveal>
                <div class="card">
                    <h2 class="h-card">Send us a message</h2>
                    <p class="mt-1 mb-6 text-sm text-muted">Planning an event? The <a href="{{ route('plan') }}" class="link">event planner</a> collects everything we need for a quote.</p>
                    <x-contact-form :topic="request('topic')" />
                </div>
            </div>

            <aside class="space-y-5 lg:col-span-5" data-reveal style="--reveal-delay: 120ms">
                <div class="card">
                    <h2 class="h-card">Reach the team</h2>
                    <ul class="mt-4 divide-y divide-line text-sm">
                        @foreach ([
                            ['message-circle', 'WhatsApp', $site['phone'], \App\Support\Content::whatsapp()],
                            ['phone', 'Call', $site['phone'].' · '.$site['phone_alt'], 'tel:'.$site['phone_href']],
                            ['mail', 'Email', $site['email'], 'mailto:'.$site['email']],
                            ['map-pin', 'Based in', $site['location'], null],
                        ] as [$icon, $label, $value, $url])
                            <li class="flex items-center gap-4 py-3.5">
                                <x-icon :name="$icon" class="size-5 shrink-0 text-brand-600" />
                                <div class="min-w-0">
                                    <p class="text-muted">{{ $label }}</p>
                                    @if ($url)
                                        <a href="{{ $url }}" class="font-semibold break-words hover:text-brand-700">{{ $value }}</a>
                                    @else
                                        <p class="font-semibold">{{ $value }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-3xl bg-ink p-6 text-white sm:p-8">
                    <p class="text-xs font-semibold tracking-[0.16em] text-white/50 uppercase">Follow the work</p>
                    <p class="mt-3 font-display text-3xl font-semibold">New events, most weeks.</p>
                    <p class="mt-2 text-sm text-white/60">Please call or WhatsApp for enquiries. We do not answer direct messages.</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ $site['links']['instagram'] }}" target="_blank" rel="noopener" class="btn btn-onDark !py-2.5">Instagram <x-icon name="arrow-up-right" class="size-4" /></a>
                        <a href="{{ $site['links']['facebook'] }}" target="_blank" rel="noopener" class="btn btn-onDark !py-2.5">Facebook <x-icon name="arrow-up-right" class="size-4" /></a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="bg-white pb-16 sm:pb-24">
        <div class="wrap">
            <div class="overflow-hidden rounded-3xl ring-1 ring-line" data-reveal>
                <iframe title="Map of Chaguanas, Trinidad" class="h-96 w-full border-0 grayscale-[35%]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps?q=Chaguanas,+Trinidad+and+Tobago&z=12&output=embed"></iframe>
            </div>
        </div>
    </section>
</x-layout>
