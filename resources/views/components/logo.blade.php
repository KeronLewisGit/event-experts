@props(['dark' => false])
<span {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <img src="{{ asset($dark ? 'images/mark-light.png' : 'images/mark-dark.png') }}" alt="" width="290" height="193" class="h-9 w-auto sm:h-10">
    <span class="leading-none">
        <span class="block font-display text-[1.35rem] font-semibold tracking-[0.14em] uppercase">Event Experts <span class="text-[0.7em]">Ltd</span></span>
        <span class="mt-1 block text-[9px] font-medium tracking-[0.28em] uppercase opacity-60">{{ config('site.descriptor') }}</span>
    </span>
</span>
