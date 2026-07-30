<x-layout title="Home Page">

    <header class="page-header">
        <h1>Welcome</h1>
    </header>

    <div style="margin-top: 20px">
        @if (session('success'))
            <p style="font-size: 30px ;color: #4f46e5">{{ session('success') }}</p>
       @endif
    </div>

</x-layout>
