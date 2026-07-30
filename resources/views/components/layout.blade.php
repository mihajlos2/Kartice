<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>{{ $title }}</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }

        .registe {
            margin-left: auto;
            display: flex;
            gap: 12px;
        }

        .registe > a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.5s;
        }

        .registe > a:hover {
            color: #1c1c1a;
            background-color: white;
        }

        .main-navigation  {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 16px 40px;
            background-color: #1c1c1a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .main-navigation  > a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.5s;
        }

        .main-navigation  > a:hover {
            color: #1c1c1a;
            background-color: white;
        }
        .auth-button {
            width: 100%;
            padding: 11px 16px;
            border: 0;
            border-radius: 7px;
            background-color: #1c1c1a;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        main {
            padding: 40px;
        }
    </style>
</head>

<body>

<nav class="main-navigation">
    <a href="/">Home</a>
    @auth
        <a href="{{ route('packs.create') }}">Create Pack</a>
        <a href="{{ route('packs.index') }}">Show Packs</a>
        <div class="registe">
            <form action="/logout" method="POST">
                @csrf
                @method('DELETE')
                <button class="auth-button" type="submit">
                    Log out
                </button>
            </form>
        </div>
    @endauth

    @guest
        <div class="registe">
            <a href="/login"  >Log in</a>
            <a href="/register"  >Register</a>
        </div>
    @endguest

</nav>

<main>
    {{ $slot }}
</main>

</body>
</html>
