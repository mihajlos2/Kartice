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

        nav {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 16px 40px;
            background-color: #1c1c1a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        nav > a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.5s;
        }

        nav > a:hover {
            color: #1c1c1a;
            background-color: white;
        }

        main {
            padding: 40px;
        }
    </style>
</head>

<body>

<nav>
    <a href="/">Home</a>
    <a href="{{ route('packs.create') }}">Create Pack</a>
    <a href="{{ route('packs.index') }}">Show Packs</a>


    <div class="registe">
        <a href="login"  >Log in</a>
        <a href="register"  >Register</a>
    </div>
</nav>

<main>
    {{ $slot }}
</main>

</body>
</html>
