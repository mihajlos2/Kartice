<x-layout title="Home Page">
    <h1>Home Page</h1>

    @if (session('success'))
        <p class="size = 4">{{ session('success') }}</p>
    @endif

</x-layout>
