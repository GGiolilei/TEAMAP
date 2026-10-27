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
                --black-2: #3A3B3A;
                --text: #242524;
                --text-2: #747674;
                --muted: #9A9C9A;
                --bubble-me: #E9E9E7;
                --bubble-other: #F4F4F2;
                --border: #E1E1DE;
                --hover: #EEEEEC;
                --selected: #E5E5E2;
                --btn: #252625;
                --success: #4CAF72;
                --success-bg: #E8F5EC;
                --error: #D85C5C;
                --error-bg: #FBEAEA;
                --warning: #D9A441;
                --warning-bg: #FBF3DD;
                --info: #4E83C4;
                --info-bg: #EAF2FA;
                --unread: #35A866;
                --mention: #D98245;
            }

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
        <div class="flex min-h-screen flex-col">

            <nav class="sticky top-0 z-50 border-b border-(--border) bg-(--surface)/85 backdrop-blur-md">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">

                        <div class="flex items-center gap-6">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                                <span class="grid h-9 w-9 place-items-center rounded-xl border border-(--border) bg-(--bg)">
                                    <img src="{{ asset('image/teamapio.png') }}" alt="" class="h-5 w-5 object-contain">
                                </span>
                                <span class="text-base font-extrabold tracking-tight text-(--black)">TeaMap.IO</span>
                            </a>

                            <div class="hidden space-x-1 sm:flex">
                                <a href="{{ route('dashboard') }}"
                                   class="rounded-lg px-4 py-2 text-sm font-medium transition
                                   {{ request()->routeIs('dashboard') ? 'bg-(--selected) text-(--black)' : 'text-(--text-2) hover:bg-(--hover) hover:text-(--text)' }}">
                                    Dashboard
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden items-center gap-3 sm:flex">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-(--bubble-me) text-sm font-semibold text-(--black)">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="leading-tight">
                                    <div class="text-sm font-semibold text-(--text)">{{ Auth::user()->name }}</div>
                                    <div class="flex items-center gap-1.5 text-xs text-(--text-2)">
                                        <span class="h-1.5 w-1.5 rounded-full bg-(--success)"></span>
                                        Verified Member
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('profile.edit') }}" title="Account Settings"
                               class="rounded-xl border border-(--border) bg-(--surface) p-2 text-(--text-2) transition hover:bg-(--hover) hover:text-(--text)">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-xl border border-(--border) bg-(--surface) px-3 py-2 text-xs font-semibold text-(--text-2) transition hover:border-(--error)/30 hover:bg-(--error-bg) hover:text-(--error)">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>