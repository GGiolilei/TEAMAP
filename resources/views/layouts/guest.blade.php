<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                letter-spacing: -0.01em;
            }
        </style>
    </head>
    <body class="text-[#242524] antialiased selection:bg-[#202120] selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 bg-[#F7F7F5]">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white border border-[#E1E1DE] shadow-sm overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-[#9A9C9A]">
                © {{ date('Y') }} {{ config('app.name') }}
            </p>
        </div>
    </body>
</html>