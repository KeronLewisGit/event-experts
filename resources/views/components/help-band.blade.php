@props(['topic' => null, 'title' => 'Questions? Talk to our team.', 'text' => 'Send us a note and we will get back to you. For the quickest answer, call or WhatsApp. We do not take enquiries by direct message.'])
<section class="section border-t border-line bg-white" id="contact">
    <div class="wrap grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-5" data-reveal>
            <p class="eyebrow">Contact us</p>
            <h2 class="h-section mt-4">{{ $title }}</h2>
            <p class="lead mt-4">{{ $text }}</p>

            <dl class="mt-8 space-y-4 text-sm">
                <div class="flex items-center gap-4">
                    <span class="icon-tile"><x-icon name="phone" /></span>
                    <div>
                        <dt class="text-muted">Call or WhatsApp</dt>
                        <dd class="text-base font-semibold"><a href="tel:{{ config('site.phone_href') }}">{{ config('site.phone') }}</a> &middot; <a href="tel:{{ config('site.phone_alt_href') }}">{{ config('site.phone_alt') }}</a></dd>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="icon-tile"><x-icon name="map-pin" /></span>
                    <div>
                        <dt class="text-muted">Based in</dt>
                        <dd class="font-semibold">{{ config('site.location') }}</dd>
                    </div>
                </div>
            </dl>
        </div>
        <div class="lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
            <div class="card bg-paper">
                <x-contact-form :topic="$topic" compact />
            </div>
        </div>
    </div>
</section>
