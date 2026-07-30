<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<nav class="main-navigation">
    <a href="/">Home</a>

    @auth
        <a href="{{ route('packs.create') }}">Create Pack</a>
        <a href="{{ route('packs.index') }}">Show Packs</a>

        <div class="navigation-actions">
            <form action="/logout" method="POST">
                @csrf
                @method('DELETE')

                <button class="navigation-button" type="submit">
                    Log out
                </button>
            </form>
        </div>
    @endauth

    @guest
        <div class="navigation-actions">
            <a href="/login">Log in</a>
            <a href="/register">Register</a>
        </div>
    @endguest
</nav>

<main class="page-content">
    {{ $slot }}
</main>

</body>
</html>
