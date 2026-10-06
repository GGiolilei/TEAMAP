
@props(['hideNavbar' => false])

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
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                letter-spacing: -0.01em;
            }
        </style>

        <x-theme-script />
    </head>

    <body class="bg-[#F7F7F5] text-[#242524] antialiased selection:bg-[#202120] selection:text-white">
        <div class="min-h-screen flex flex-col">

            @unless($hideNavbar)
                <nav class="bg-white border-b border-[#E1E1DE] sticky top-0 z-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center gap-6">
                                <a href="{{ route('dashboard') }}" class="flex items-center">
                                    <img src="{{ asset('image/teamapio.png') }}"
                                         alt="{{ config('app.name', 'TeaMap') }}"
                                         class="h-8 w-auto object-contain">
                                </a>

                                <div class="hidden sm:flex space-x-1">
                                    <a href="{{ route('dashboard') }}"
                                       class="text-sm font-medium px-4 py-2 rounded-lg bg-[#E5E5E2] text-[#202120] hover:bg-[#EEEEEC] transition">
                                        Dashboard
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-semibold text-[#242524]">
                                        {{ Auth::user()->name }}
                                    </div>
                                    <div class="text-xs text-[#9A9C9A]">
                                        Verified Member
                                    </div>
                                </div>

                                <a href="{{ route('profile.edit') }}"
                                   class="p-2 bg-white hover:bg-[#EEEEEC] border border-[#E1E1DE] rounded-xl text-[#747674] hover:text-[#202120] transition"
                                   title="Account Settings">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs font-semibold text-[#747674] hover:text-[#202120] hover:bg-[#EEEEEC] transition bg-white border border-[#E1E1DE] px-3 py-2 rounded-xl">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>
            @endunless

            <main class="flex-1">
                {{ $slot }}
            </main>

        </div>
    </body>
</html>