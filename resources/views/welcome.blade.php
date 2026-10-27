<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: #F7F7F5;
            --surface: #FFFFFF;
            --black: #202120;
            --black-2: #3A3B3A;
            --text: #242524;
            --text-2: #747674;
            --text-muted: #9A9C9A;
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

        body { background: var(--bg); color: var(--text); }

        /* ---------- Splash ---------- */
        #splash {
            position: fixed; inset: 0; z-index: 100;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 28px;
            background: var(--bg);
            transition: opacity .6s ease, visibility .6s ease;
        }
        #splash.hide { opacity: 0; visibility: hidden; }
        #splash .logo-wrap {
            position: relative; width: 112px; height: 112px;
            display: grid; place-items: center;
            border-radius: 32px; background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px -20px rgba(32,33,32,.25);
            animation: pop .8s cubic-bezier(.2,.9,.3,1.2) both;
        }
        #splash .logo-wrap::before {
            content: ""; position: absolute; inset: -14px; border-radius: 42px;
            border: 1px solid var(--border);
            animation: ring 2s ease-out infinite;
        }
        #splash img { width: 64px; height: 64px; object-fit: contain; }
        #splash .name {
            font-weight: 600; letter-spacing: -.02em; font-size: 1.25rem; color: var(--black);
            animation: rise .7s .25s ease both;
        }
        #splash .bar {
            width: 120px; height: 3px; border-radius: 999px; background: var(--selected); overflow: hidden;
            animation: rise .7s .35s ease both;
        }
        #splash .bar i {
            display: block; height: 100%; width: 100%; background: var(--black);
            transform-origin: left; transform: scaleX(0);
            animation: load 1.6s .3s cubic-bezier(.6,0,.2,1) forwards;
        }
        @keyframes pop  { from { opacity: 0; transform: scale(.6) } to { opacity: 1; transform: scale(1) } }
        @keyframes rise { from { opacity: 0; transform: translateY(10px) } to { opacity: 1; transform: none } }
        @keyframes ring { 0% { opacity: .8; transform: scale(.92) } 100% { opacity: 0; transform: scale(1.25) } }
        @keyframes load { to { transform: scaleX(1) } }

        /* ---------- Page entrance ---------- */
        .fade-up { opacity: 0; transform: translateY(14px); animation: rise .7s ease forwards; }
        .d1 { animation-delay: .1s } .d2 { animation-delay: .2s } .d3 { animation-delay: .3s } .d4 { animation-delay: .4s }

        @media (prefers-reduced-motion: reduce) {
            #splash *, .fade-up { animation: none !important; opacity: 1; transform: none; }
        }
    </style>
</head>
<body class="min-h-screen antialiased">

    {{-- Splash screen (shows once per session) --}}
    <div id="splash">
        <div class="logo-wrap">
            {{-- Put your PNG at public/images/logo.png --}}
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div class="name">{{ config('app.name', 'Laravel') }}</div>
        <div class="bar"><i></i></div>
    </div>

    <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col px-6">

        {{-- Nav --}}
        <header class="flex items-center justify-between py-6 fade-up">
            <a href="/" class="flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded-xl border border-(--border) bg-(--surface)">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-5 w-5 object-contain">
                </span>
                <span class="text-sm font-semibold tracking-tight text-(--black)">{{ config('app.name', 'Laravel') }}</span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2 text-sm">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="rounded-lg bg-(--btn) px-4 py-2 font-medium text-white transition hover:bg-(--black-2)">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-lg px-4 py-2 font-medium text-(--text-2) transition hover:bg-(--hover) hover:text-(--text)">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="rounded-lg bg-(--btn) px-4 py-2 font-medium text-white transition hover:bg-(--black-2)">
                                Get started
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        {{-- Hero --}}
        <main class="flex flex-1 flex-col items-center justify-center gap-14 py-12 lg:flex-row lg:gap-16">

            <section class="max-w-xl text-center lg:text-left">
                <span class="fade-up d1 inline-flex items-center gap-2 rounded-full border border-(--border) bg-(--surface) px-3 py-1 text-xs font-medium text-(--text-2)">
                    <span class="h-1.5 w-1.5 rounded-full bg-(--success)"></span>
                    Teams online now
                </span>

                <h1 class="fade-up d2 mt-5 text-4xl font-semibold leading-[1.1] tracking-tight text-(--black) sm:text-5xl">
                    Your team's lobby,<br>
                    <span class="text-(--text-muted)">simple and clear.</span>
                </h1>

                <p class="fade-up d3 mt-5 text-base leading-7 text-(--text-2)">
                    Create a lobby, invite your people, and get things done together. No clutter. Just the space you need.
                </p>

                <div class="fade-up d4 mt-8 flex flex-col items-center gap-3 sm:flex-row lg:justify-start justify-center">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="w-full rounded-xl bg-(--btn) px-6 py-3 text-center text-sm font-medium text-white transition hover:bg-(--black-2) sm:w-auto">
                            Create your lobby
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="w-full rounded-xl border border-(--border) bg-(--surface) px-6 py-3 text-center text-sm font-medium text-(--text) transition hover:bg-(--hover) sm:w-auto">
                            I have an account
                        </a>
                    @endif
                </div>
            </section>

            {{-- Preview card --}}
            <section class="fade-up d3 w-full max-w-sm shrink-0">
                <div class="rounded-3xl border border-(--border) bg-(--surface) p-5 shadow-[0_30px_60px_-30px_rgba(32,33,32,.25)]">

                    <div class="flex items-center justify-between border-b border-(--border) pb-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-xl bg-(--selected) text-sm font-semibold text-(--black)">T</div>
                            <div>
                                <p class="text-sm font-semibold text-(--text)">Team Lobby</p>
                                <p class="text-xs text-(--text-muted)">4 members</p>
                            </div>
                        </div>
                        <span class="flex items-center gap-1.5 rounded-full bg-(--success-bg) px-2.5 py-1 text-xs font-medium text-(--success)">
                            <span class="h-1.5 w-1.5 rounded-full bg-(--success)"></span> Online
                        </span>
                    </div>

                    <div class="space-y-3 py-5 text-sm">
                        <div class="max-w-[80%] rounded-2xl rounded-tl-md bg-(--bubble-other) px-4 py-2.5 text-(--text)">
                            Ready when you are 👋
                        </div>
                        <div class="ml-auto max-w-[80%] rounded-2xl rounded-tr-md bg-(--bubble-me) px-4 py-2.5 text-(--text)">
                            Joining the lobby now
                        </div>
                        <div class="max-w-[80%] rounded-2xl rounded-tl-md bg-(--bubble-other) px-4 py-2.5 text-(--text)">
                            <span class="font-medium text-(--mention)">@you</span> check the new map
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 border-t border-(--border) pt-4 text-xs font-medium">
                        <span class="rounded-full bg-(--info-bg) px-3 py-1 text-(--info)">Map</span>
                        <span class="rounded-full bg-(--warning-bg) px-3 py-1 text-(--warning)">Pending</span>
                        <span class="rounded-full bg-(--error-bg) px-3 py-1 text-(--error)">Offline</span>
                    </div>
                </div>
            </section>
        </main>

        {{-- Features --}}
        <section class="fade-up d4 grid gap-4 pb-12 sm:grid-cols-3">
            @foreach ([
                ['Lobbies', 'Spin up a space for any team in seconds.'],
                ['Collaboration', 'Everyone sees the same thing, live.'],
                ['Clean by design', 'Calm colors. Zero noise.'],
            ] as [$title, $text])
                <div class="rounded-2xl border border-(--border) bg-(--surface) p-5 transition hover:bg-(--hover)">
                    <h3 class="text-sm font-semibold text-(--text)">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-(--text-2)">{{ $text }}</p>
                </div>
            @endforeach
        </section>

        <footer class="border-t border-(--border) py-6 text-center text-xs text-(--text-muted)">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} · v{{ app()->version() }}
        </footer>
    </div>

    <script>
        (function () {
            var splash = document.getElementById('splash');
            var seen = false;
            try { seen = sessionStorage.getItem('splash_seen') === '1'; } catch (e) {}

            if (seen) { splash.remove(); return; }

            window.addEventListener('load', function () {
                setTimeout(function () {
                    splash.classList.add('hide');
                    try { sessionStorage.setItem('splash_seen', '1'); } catch (e) {}
                    setTimeout(function () { splash.remove(); }, 700);
                }, 1900);
            });
        })();
    </script>
</body>
</html>