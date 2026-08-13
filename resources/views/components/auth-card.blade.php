@props(['maxWidth' => '440px'])

<section class="relative min-h-screen overflow-hidden px-4 py-10 sm:px-6"
         style="background-color: var(--c-page)">
    {{-- Background effects --}}
    <div class="auth-blob -left-40 top-10 h-[500px] w-[500px]"
         style="background-color: var(--c-blob-1)"></div>

    <div class="auth-blob -right-40 top-20 h-[460px] w-[460px]"
         style="background-color: var(--c-blob-2)"></div>

    <div class="auth-blob bottom-[-180px] left-[15%] h-[450px] w-[450px]"
         style="background-color: var(--c-blob-3)"></div>

    {{-- Card wrapper --}}
    <div class="relative z-10 flex min-h-[calc(100vh-5rem)] items-center justify-center">
        <div class="auth-card" style="max-width: {{ $maxWidth }}">
            {{ $slot }}
        </div>
    </div>
</section>

