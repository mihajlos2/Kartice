<x-layout title="Home Page" :full-width="true" :overlay-navigation="true">
    <section
        class="min-h-dvh bg-cover bg-center bg-no-repeat px-6 pb-10 pt-32 sm:px-10 sm:pt-24"
        style="background-image: url('{{ asset('images/Img12_(Windows_7).jpg') }}')"
    >
        <header class="page-header">
            <h1 class="text-white">Welcome</h1>
        </header>

        @if (session('success'))
            <p class="mt-5 text-3xl text-white text-shadow-lg">{{ session('success') }}</p>
        @endif
    </section>
</x-layout>
