<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TeaMap') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg: #F7F7F5;
                --surface: #FFFFFF;
                --black: #202120;
                --text: #242524;
                --text-2: #747674;
                --muted: #9A9C9A;
                --border: #E1E1DE;
                --selected: #E5E5E2;
            }

            /* Force light: no OS dark-mode override */
            :root { color-scheme: light; }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                letter-spacing: -0.01em;
                background: var(--bg);
                color: var(--text);
            }

            ::selection { background: var(--black); color: #fff; }
        </style>
    </head>
    <body class="antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

            <div class="w-full max-w-md rounded-3xl border border-(--border) bg-(--surface) px-6 py-8 shadow-[0_30px_60px_-30px_rgba(32,33,32,.2)] sm:px-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-(--muted)">
                &copy; {{ date('Y') }} {{ config('app.name', 'TeaMap') }}
            </p>
        </div>
    </body>
</html>