<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F7F7F5] text-[#242524] antialiased min-h-screen flex flex-col">

        {{-- NAV --}}
        <header class="w-full bg-white border-b border-[#E1E1DE]">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('image/Teamapio.png') }}" alt="Teamapio" class="h-8 w-auto">
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="px-4 py-2 text-sm font-medium bg-[#252625] text-white rounded-lg hover:bg-[#3A3B3A] transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-4 py-2 text-sm font-medium text-[#3A3B3A] rounded-lg hover:bg-[#EEEEEC] transition">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-4 py-2 text-sm font-medium bg-[#252625] text-white rounded-lg hover:bg-[#3A3B3A] transition">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        {{-- HERO --}}
        <main class="flex-1">
            <section class="max-w-6xl mx-auto px-6 pt-20 pb-16 grid lg:grid-cols-2 gap-14 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#747674] bg-white border border-[#E1E1DE] px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#202120]"></span>
                        Lorem ipsum dolor
                    </span>

                    <h1 class="mt-6 text-4xl sm:text-5xl font-bold tracking-tight leading-[1.1] text-[#202120]">
                        Lorem ipsum dolor sit amet consectetur.
                    </h1>

                    <p class="mt-5 text-base leading-relaxed text-[#747674] max-w-lg">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="px-6 py-3 text-sm font-semibold bg-[#252625] text-white rounded-xl hover:bg-[#3A3B3A] transition">
                                Go to Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-6 py-3 text-sm font-semibold bg-[#252625] text-white rounded-xl hover:bg-[#3A3B3A] transition">
                                    Get Started
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                   class="px-6 py-3 text-sm font-semibold text-[#3A3B3A] bg-white border border-[#E1E1DE] rounded-xl hover:bg-[#EEEEEC] transition">
                                    Log in
                                </a>
                            @endif
                        @endauth
                    </div>

                    <div class="mt-10 flex items-center gap-8 text-sm">
                        <div>
                            <p class="text-2xl font-bold text-[#202120]">Lorem</p>
                            <p class="text-[#9A9C9A] text-xs mt-0.5">ipsum dolor</p>
                        </div>
                        <div class="w-px h-8 bg-[#E1E1DE]"></div>
                        <div>
                            <p class="text-2xl font-bold text-[#202120]">Dolor</p>
                            <p class="text-[#9A9C9A] text-xs mt-0.5">sit amet</p>
                        </div>
                        <div class="w-px h-8 bg-[#E1E1DE]"></div>
                        <div>
                            <p class="text-2xl font-bold text-[#202120]">Amet</p>
                            <p class="text-[#9A9C9A] text-xs mt-0.5">consectetur</p>
                        </div>
                    </div>
                </div>

                {{-- CHAT PREVIEW CARD --}}
                <div class="relative">
                    <div class="bg-white border border-[#E1E1DE] rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-[#E1E1DE] flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#E5E5E2] flex items-center justify-center text-xs font-bold text-[#3A3B3A]">LI</div>
                                <div>
                                    <p class="text-sm font-semibold text-[#242524]">Lorem Ipsum Lobby</p>
                                    <p class="text-xs text-[#9A9C9A]">dolor sit · amet · elit</p>
                                </div>
                            </div>
                            <span class="text-xs font-medium text-[#747674] bg-[#F4F4F2] px-2.5 py-1 rounded-full">3 / 5</span>
                        </div>

                        <div class="p-5 space-y-3 bg-[#F7F7F5]">
                            <div class="flex">
                                <div class="max-w-[80%] bg-[#F4F4F2] border border-[#E1E1DE] text-sm text-[#242524] px-4 py-2.5 rounded-2xl rounded-bl-md">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <div class="max-w-[80%] bg-[#E9E9E7] text-sm text-[#242524] px-4 py-2.5 rounded-2xl rounded-br-md">
                                    Sed do eiusmod tempor incididunt ut labore.
                                </div>
                            </div>
                            <div class="flex">
                                <div class="max-w-[80%] bg-[#F4F4F2] border border-[#E1E1DE] text-sm text-[#242524] px-4 py-2.5 rounded-2xl rounded-bl-md">
                                    Ut enim ad minim veniam, quis nostrud exercitation.
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-3.5 border-t border-[#E1E1DE] bg-white flex items-center gap-3">
                            <div class="flex-1 text-sm text-[#9A9C9A] bg-[#F7F7F5] border border-[#E1E1DE] rounded-xl px-4 py-2.5">
                                Lorem ipsum dolor...
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-[#252625] flex items-center justify-center text-white text-sm">→</div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- FEATURES --}}
            <section class="max-w-6xl mx-auto px-6 pb-24">
                <div class="max-w-xl">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#9A9C9A]">Lorem ipsum</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-[#202120]">
                        Dolor sit amet consectetur adipiscing.
                    </h2>
                </div>

                <div class="mt-10 grid md:grid-cols-3 gap-5">
                    @foreach ([
                        ['01', 'Lorem Ipsum'],
                        ['02', 'Dolor Sit Amet'],
                        ['03', 'Consectetur Elit'],
                    ] as [$num, $title])
                        <div class="bg-white border border-[#E1E1DE] rounded-2xl p-6 hover:bg-[#EEEEEC] transition">
                            <span class="text-xs font-bold text-[#9A9C9A]">{{ $num }}</span>
                            <h3 class="mt-4 text-base font-semibold text-[#242524]">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-[#747674]">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- CTA BAND --}}
            <section class="max-w-6xl mx-auto px-6 pb-24">
                <div class="bg-[#202120] rounded-3xl px-8 py-14 sm:px-14 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                    <div class="max-w-md">
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                            Lorem ipsum dolor sit amet.
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-[#9A9C9A]">
                            Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
                        </p>
                    </div>
                    @guest
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="shrink-0 px-6 py-3 text-sm font-semibold bg-white text-[#202120] rounded-xl hover:bg-[#EEEEEC] transition text-center">
                                Get Started
                            </a>
                        @endif
                    @else
                        <a href="{{ url('/dashboard') }}"
                           class="shrink-0 px-6 py-3 text-sm font-semibold bg-white text-[#202120] rounded-xl hover:bg-[#EEEEEC] transition text-center">
                            Go to Dashboard
                        </a>
                    @endguest
                </div>
            </section>
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white border-t border-[#E1E1DE]">
            <div class="max-w-6xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <img src="{{ asset('image/Teamapio.png') }}" alt="Teamapio" class="h-6 w-auto opacity-80">
                <p class="text-xs text-[#9A9C9A]">
                    © {{ date('Y') }} {{ config('app.name') }}. Lorem ipsum dolor sit amet.
                </p>
            </div>
        </footer>

    </body>
</html>