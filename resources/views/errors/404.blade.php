<x-layout title="Page not found">
    <section class="relative overflow-hidden">
        <div class="grid-paper absolute inset-0 [mask-image:linear-gradient(to_bottom,black,transparent)]"></div>
        <div class="wrap relative py-24 text-center sm:py-32">
            <p class="font-display text-8xl font-semibold text-brand-600 sm:text-9xl">404</p>
            <h1 class="h-section mt-4">This page has left the party.</h1>
            <p class="lead mx-auto mt-4 max-w-md">We couldn&rsquo;t find that page. It may have moved, or the link may be out of date.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                <a href="{{ route('portfolio') }}" class="btn btn-light"><x-icon name="images" class="size-4" /> See our work</a>
            </div>
        </div>
    </section>
</x-layout>
