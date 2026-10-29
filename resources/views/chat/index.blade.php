<x-app-layout>
    {{-- --- GLOBAL NOTIFICATION AUDIO --- --}}
    <audio id="chatNotificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

    <script>
        // Flag to track if the browser allows audio yet
        let audioContextUnlocked = false;

        // Wake up the audio engine on the first user interaction
        function unlockAudioEngine() {
            if (audioContextUnlocked) return;

            const sound = document.getElementById('chatNotificationSound');
            if (sound) {
                // Play and immediately pause to verify permissions with the browser
                sound.play()
                    .then(() => {
                        sound.pause();
                        sound.currentTime = 0;
                        audioContextUnlocked = true;
                        console.log('🔊 Chat notification engine successfully unlocked.');

                        // Clean up listeners
                        document.removeEventListener('click', unlockAudioEngine);
                        document.removeEventListener('touchend', unlockAudioEngine);
                        document.removeEventListener('keydown', unlockAudioEngine);
                    })
                    .catch(err => console.debug('Waiting for a more definitive user action...'));
            }
        }

        // Assign listeners to capture early layout interaction
        document.addEventListener('click', unlockAudioEngine);
        document.addEventListener('touchend', unlockAudioEngine);
        document.addEventListener('keydown', unlockAudioEngine);

        function playNotificationSound() {
            const sound = document.getElementById('chatNotificationSound');
            if (sound) {
                sound.currentTime = 0;

                const playPromise = sound.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.warn('Playback blocked. Click anywhere on the page to enable sound notifications:', error);
                    });
                }
            }
        }
    </script>

    {{-- Fetch the currently highlighted channel from the URL query string, fallback to the first channel --}}
    @php
        $activeChannelId = request('channel', $lobby->channels->first()?->id);
        $currentChannel = $lobby->channels->firstWhere('id', $activeChannelId) ?? $lobby->channels->first();
    @endphp

    <style>
        /* ---------- Splash ---------- */
        #chat-splash {
            position: fixed; inset: 0; z-index: 100;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px;
            background: var(--bg);
            transition: opacity .6s ease, visibility .6s ease;
        }
        #chat-splash.hide { opacity: 0; visibility: hidden; }
        #chat-splash .logo-wrap {
            position: relative; width: 112px; height: 112px; display: grid; place-items: center;
            border-radius: 32px; background: var(--surface); border: 1px solid var(--border);
            box-shadow: 0 20px 40px -20px rgba(32,33,32,.25);
            animation: c-pop .8s cubic-bezier(.2,.9,.3,1.2) both;
        }
        #chat-splash .logo-wrap::before {
            content: ""; position: absolute; inset: -14px; border-radius: 42px;
            border: 1px solid var(--border); animation: c-ring 2s ease-out infinite;
        }
        #chat-splash img { width: 64px; height: 64px; object-fit: contain; }
        #chat-splash .name { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; color: var(--black); animation: c-rise .7s .25s ease both; }
        #chat-splash .bar { width: 120px; height: 3px; border-radius: 999px; background: var(--selected); overflow: hidden; animation: c-rise .7s .35s ease both; }
        #chat-splash .bar i { display: block; height: 100%; background: var(--black); transform-origin: left; transform: scaleX(0); animation: c-load 1.6s .3s cubic-bezier(.6,0,.2,1) forwards; }
        @keyframes c-pop  { from { opacity: 0; transform: scale(.6) } to { opacity: 1; transform: scale(1) } }
        @keyframes c-rise { from { opacity: 0; transform: translateY(10px) } to { opacity: 1; transform: none } }
        @keyframes c-ring { 0% { opacity: .8; transform: scale(.92) } 100% { opacity: 0; transform: scale(1.25) } }
        @keyframes c-load { to { transform: scaleX(1) } }

        /* ---------- Chat ---------- */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .animate-bounce-slow-1 { animation: bounce-slow 1s infinite 0.1s; }
        .animate-bounce-slow-2 { animation: bounce-slow 1s infinite 0.2s; }
        .animate-bounce-slow-3 { animation: bounce-slow 1s infinite 0.3s; }

        @keyframes slide-in-up {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slide-in-up 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Accent themes (toggle in the menu) — still built on the TeaMap palette */
        .theme-ink {
            --accent: #252625; --accent-hover: #3A3B3A; --accent-fg: #FFFFFF;
            --accent-soft: #E9E9E7; --accent-ink: #202120;
        }
        .theme-warm {
            --accent: #D98245; --accent-hover: #C5733A; --accent-fg: #FFFFFF;
            --accent-soft: #F8E8DC; --accent-ink: #8A4A1F;
        }

        .no-scrollbar { scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        .bubble { padding: 10px 14px; font-size: .9375rem; line-height: 1.45; word-break: break-word; overflow-wrap: anywhere; border: 1px solid var(--border); color: var(--text); display: inline-block; text-align: left; }
        .bubble-me    { background: var(--accent-soft); border-color: transparent; border-radius: 18px 18px 4px 18px; }
        .bubble-other { background: var(--bubble-other); border-radius: 18px 18px 18px 4px; }
        .bubble-ai    { background: var(--info-bg); border-color: color-mix(in srgb, var(--info) 25%, transparent); border-radius: 18px 18px 18px 4px; }

        .ch-link { transition: background .15s, color .15s; -webkit-tap-highlight-color: transparent; }
        .btn-accent { background: var(--accent); color: var(--accent-fg); transition: background .15s, transform .1s; }
        .btn-accent:hover { background: var(--accent-hover); }
        .btn-accent:active { transform: scale(.96); }

        #chat-drawer { transition: transform .3s cubic-bezier(.22,.8,.2,1); }

        @media (prefers-reduced-motion: reduce) {
            #chat-splash *, .animate-slide-up { animation: none !important; }
            #chat-drawer { transition: none; }
        }
    </style>

    {{-- Splash screen (once per session) --}}
    <div id="chat-splash">
        <div class="logo-wrap">
            <img src="{{ asset('image/teamapio.png') }}" alt="TeaMap">
        </div>
        <div class="name">TeaMap</div>
        <div class="bar"><i></i></div>
    </div>

    {{-- Root node sized to the screen under the navbar; never scrolls at page level --}}
    <div id="theme-root" class="theme-ink relative flex overflow-hidden bg-(--bg) text-(--text)" style="height: calc(100vh - 4rem); height: calc(100dvh - 4rem);">

        {{-- Drawer backdrop (mobile) --}}
        <div id="drawer-backdrop" class="fixed inset-0 z-[65] hidden bg-(--black)/40 backdrop-blur-sm lg:hidden"></div>

        {{-- ===================== SIDEBAR / DRAWER ===================== --}}
        <aside id="chat-drawer"
               class="fixed inset-y-0 left-0 z-[70] flex w-[86%] max-w-sm -translate-x-full flex-col justify-between overflow-y-auto border-r border-(--border) bg-(--surface) p-5 lg:static lg:z-auto lg:w-80 lg:max-w-none lg:translate-x-0 lg:shrink-0">

            <div class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-(--border) bg-(--surface) px-3.5 text-sm font-semibold text-(--text) transition hover:bg-(--hover)">
                        ← Dashboard
                    </a>

                    <div class="flex items-center gap-2">
                        <button id="theme-toggle-btn" type="button" title="Switch Theme" aria-label="Switch Theme"
                                class="grid h-10 w-10 place-items-center rounded-xl border border-(--border) bg-(--surface) text-(--text-2) transition hover:bg-(--hover) hover:text-(--text)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M4.098 19.902a3.75 3.75 0 015.304-5.304l6.401-6.401m-11.705 11.705l-.71-.71m7.115-7.114l-.71-.71m11.705-11.705a3.75 3.75 0 115.304 5.304l-6.401 6.401m5.304-5.304l.71-.71m-7.115 7.114l.71-.71M11 6.22a6.75 6.75 0 00-6.75 6.75m13.5 0a6.75 6.75 0 01-6.75 6.75m0-13.5A6.75 6.75 0 0117.75 13.5m-13.5 0A6.75 6.75 0 0011 20.25" />
                            </svg>
                        </button>
                        <button id="drawer-close" type="button" aria-label="Close menu"
                                class="grid h-10 w-10 place-items-center rounded-xl text-(--text-2) transition hover:bg-(--hover) lg:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-(--black)">{{ $lobby->name }}</h2>
                    <p class="mt-1 text-sm leading-relaxed text-(--text-2)">
                        Goal: <span class="font-semibold text-(--text)">{{ $lobby->project_goal }}</span>
                    </p>
                </div>

                <div class="h-px bg-(--border)"></div>

                {{-- Channels --}}
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-(--text-2)">Text Channels</h3>
                        @if(auth()->id() === $lobby->owner_id)
                            <a href="{{ route('lobbies.channels.create', $lobby->id) }}"
                               class="grid h-9 w-9 place-items-center rounded-xl text-(--text-2) transition hover:bg-(--hover) hover:text-(--black)" title="Create New Channel" aria-label="Create New Channel">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="space-y-1">
                        @forelse($lobby->channels as $roomChannel)
                            @php $isActive = $currentChannel?->id === $roomChannel->id; @endphp
                            <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $roomChannel->id]) }}"
                               class="ch-link flex min-h-11 items-center gap-2 rounded-xl px-3 text-sm {{ $isActive ? 'btn-accent font-bold' : 'font-medium text-(--text-2) hover:bg-(--hover) hover:text-(--text)' }}">
                                <span class="font-mono text-base {{ $isActive ? 'opacity-70' : 'text-(--muted)' }}">#</span>
                                <span class="truncate">{{ $roomChannel->name }}</span>
                            </a>
                        @empty
                            <div class="rounded-xl border border-dashed border-(--border) bg-(--bg) p-3 text-center text-sm text-(--muted)">
                                No active channels found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="h-px bg-(--border)"></div>

                {{-- Roster --}}
                <div>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-(--text-2)">
                        Workspace Roster ({{ $lobby->members->count() }})
                    </h3>
                    <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
                        @foreach($lobby->members as $member)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-(--border) bg-(--bg) p-2.5">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-xs font-bold uppercase {{ $member->id == 2 ? 'bg-(--info-bg) text-(--info)' : 'bg-(--bubble-me) text-(--black)' }}">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="truncate text-sm font-bold text-(--text)">{{ $member->name }}</h5>
                                        <p class="text-[11px] uppercase tracking-wide text-(--muted)">
                                            @if($member->id == 2)
                                                AI Companion
                                            @else
                                                {{ $member->id === $lobby->owner_id ? 'Organizer' : 'Partner' }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $member->id == 2 ? 'bg-(--info) shadow-[0_0_0_3px_var(--info-bg)]' : 'bg-(--success) shadow-[0_0_0_3px_var(--success-bg)]' }}"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-(--border) pt-5 text-center font-mono text-xs uppercase tracking-wider text-(--muted)">
                Ref ID: #{{ $lobby->id }}
            </div>
        </aside>

        {{-- ===================== MAIN CHAT ===================== --}}
        <section class="flex min-h-0 min-w-0 flex-1 flex-col bg-(--surface)">

            {{-- Top bar (mobile + desktop) --}}
            <header class="flex shrink-0 items-center gap-2.5 border-b border-(--border) bg-(--surface) px-3 py-3 sm:px-5">
                <button id="drawer-open" type="button" aria-label="Open menu"
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-(--border) text-(--text) transition hover:bg-(--hover) lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" /></svg>
                </button>

                @if($currentChannel)
                    <div class="hidden h-10 w-10 shrink-0 place-items-center rounded-xl bg-(--bg) lg:grid">
                        <span class="font-mono text-base font-bold text-(--text-2)">#</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-base font-bold text-(--black)">
                            <span class="font-mono text-(--muted) lg:hidden">#</span>{{ $currentChannel->name }}
                        </h3>
                        <p class="hidden truncate text-xs text-(--text-2) sm:block">{{ $currentChannel->description ?? 'Secure team sync for verified partners' }}</p>
                    </div>
                @else
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-base font-bold text-(--black)">{{ $lobby->name ?? 'Lobby' }}</h3>
                    </div>
                @endif

                <div class="flex shrink-0 items-center gap-2">
                    {{-- Task Board Link Button --}}
                    <a href="{{ route('task.index') }}" aria-label="Task Board"
                       class="inline-flex h-11 items-center gap-1.5 rounded-xl border border-(--border) bg-(--surface) px-3 text-sm font-semibold text-(--text) transition hover:bg-(--hover) active:scale-95 sm:px-3.5">
                        <svg class="h-4 w-4 text-(--text-2)" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="hidden sm:inline lg:hidden">Tasks</span>
                        <span class="hidden lg:inline">Task Board</span>
                    </a>

                    @if($currentChannel)
                        <a href="{{ route('chat.huddle', ['channel' => $currentChannel->id]) }}"
                           class="btn-accent inline-flex h-11 items-center gap-2 rounded-xl px-3.5 text-sm font-bold">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sm:hidden">Huddle</span>
                            <span class="hidden sm:inline">Join Huddle</span>
                        </a>
                    @endif
                </div>
            </header>

            {{-- Channel chips (mobile quick switch) --}}
            @if($lobby->channels->count() > 1)
                <nav class="no-scrollbar flex shrink-0 gap-2 overflow-x-auto border-b border-(--border) bg-(--bg) px-3 py-2.5 lg:hidden" aria-label="Channels">
                    @foreach($lobby->channels as $roomChannel)
                        @php $isActive = $currentChannel?->id === $roomChannel->id; @endphp
                        <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $roomChannel->id]) }}"
                           class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold {{ $isActive ? 'btn-accent border-transparent' : 'border-(--border) bg-(--surface) text-(--text-2)' }}">
                            # {{ $roomChannel->name }}
                        </a>
                    @endforeach
                </nav>
            @endif

            @if($currentChannel)
                {{-- --- THE CHAT TIMELINE --- --}}
                <div id="chat-timeline" class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain bg-(--surface) px-3 py-5 sm:px-6">
                    <div class="my-1 flex items-center justify-center">
                        <div class="rounded-full bg-(--info-bg) px-3.5 py-1.5 text-xs font-medium text-(--info)">
                            ⚡ #{{ $currentChannel->name }} verified secure
                        </div>
                    </div>

                    <div id="messages-wrapper" class="space-y-4">
                        @forelse($currentChannel->messages ?? [] as $message)
                            @php
                                $isMe = $message->user_id === auth()->id();
                                $isTimmy = $message->user_id == 2;
                            @endphp

                            <div class="message-bubble flex max-w-[90%] items-end gap-2.5 sm:max-w-[78%] {{ $isMe ? 'ml-auto flex-row-reverse' : '' }}">
                                @unless($isMe)
                                    <div class="grid h-8 w-8 shrink-0 select-none place-items-center rounded-full text-xs font-bold uppercase {{ $isTimmy ? 'bg-(--info-bg) text-(--info) ring-1 ring-(--info)/30' : 'bg-(--btn) text-white' }}">
                                        {{ substr($message->user->name ?? '?', 0, 2) }}
                                    </div>
                                @endunless

                                <div class="min-w-0 {{ $isMe ? 'text-right' : '' }}">
                                    <div class="mb-1 flex items-baseline gap-2 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                        <span class="sender-name-label truncate text-xs font-bold {{ $isTimmy ? 'text-(--info)' : 'text-(--text)' }}">{{ $isMe ? 'You' : ($message->user->name ?? 'Anonymous') }}</span>
                                        @if($isTimmy)
                                            <span class="rounded bg-(--info-bg) px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-(--info)">AI</span>
                                        @endif
                                        <span class="shrink-0 text-[11px] text-(--muted)">{{ $message->created_at->format('g:i A') }}</span>
                                    </div>

                                    <div class="bubble {{ $isMe ? 'bubble-me' : ($isTimmy ? 'bubble-ai' : 'bubble-other') }}">{{ $message->content }}</div>
                                </div>
                            </div>
                        @empty
                            <div id="empty-history-notice" class="py-16 text-center">
                                <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-(--bg) text-2xl">💬</div>
                                <p class="text-sm font-semibold text-(--text)">#{{ $currentChannel->name }} is quiet</p>
                                <p class="mt-1 text-sm text-(--muted)">Send a query or tag <span class="font-semibold text-(--info)">@@timmy</span> to open up discussion!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- --- TYPING NOTIFIER --- --}}
                <div id="typing-container" class="hidden shrink-0 bg-(--surface) px-4 pb-1 sm:px-6">
                    <div class="animate-slide-up flex w-fit items-center gap-2 rounded-full bg-(--bubble-other) px-3.5 py-2">
                        <div class="flex items-center gap-1">
                            <span class="animate-bounce-slow-1 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                            <span class="animate-bounce-slow-2 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                            <span class="animate-bounce-slow-3 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                        </div>
                        <span id="typing-text" class="text-xs text-(--text-2)">You are typing...</span>
                    </div>
                </div>

                {{-- --- STATIC CHAT INPUT BAR --- --}}
                <div class="shrink-0 border-t border-(--border) bg-(--surface) px-3 pt-3 sm:px-5" style="padding-bottom: max(.75rem, env(safe-area-inset-bottom));">
                    <form id="chat-form" action="{{ route('messages.store', ['channel' => $currentChannel->id]) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input id="message-input" name="content" type="text" autocomplete="off" required enterkeyhint="send"
                               placeholder="Message #{{ $currentChannel->name }} or tag @@timmy..."
                               class="block min-h-12 w-full flex-1 rounded-2xl border border-(--border) bg-(--bg) px-4 text-base text-(--text) outline-none transition placeholder:text-(--muted) focus:border-(--btn) focus:bg-(--surface) focus:ring-4 focus:ring-(--selected)" />
                        <button type="submit" aria-label="Send"
                                class="btn-accent grid h-12 w-12 shrink-0 place-items-center rounded-2xl">
                            <svg class="h-5 w-5 rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9-7-9-7v14z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-3 p-6 text-center">
                    <div class="grid h-16 w-16 place-items-center rounded-3xl bg-(--bg) text-3xl">💬</div>
                    <p class="max-w-xs text-sm text-(--text-2)">
                        This workspace has no active feeds. Click the <strong class="text-(--black)">+</strong> button in the menu to deploy one.
                    </p>
                    <button type="button" onclick="document.getElementById('drawer-open').click()"
                            class="btn-accent inline-flex min-h-11 items-center rounded-xl px-5 text-sm font-semibold lg:hidden">
                        Open menu
                    </button>
                </div>
            @endif

        </section>
    </div>

    <script>
        // --- 1. Immediate Theme Engine Initialization ---
        const themeRoot = document.getElementById('theme-root');
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        const THEMES = ['theme-ink', 'theme-warm'];

        const applyTheme = (name) => {
            THEMES.forEach(t => themeRoot.classList.remove(t));
            themeRoot.classList.add(name);
        };

        let savedTheme = 'theme-ink';
        try {
            const stored = localStorage.getItem('chat-workspace-theme');
            if (THEMES.includes(stored)) savedTheme = stored;
        } catch (e) {}
        if (themeRoot) applyTheme(savedTheme);

        if (themeToggleBtn && themeRoot) {
            themeToggleBtn.addEventListener('click', () => {
                const next = themeRoot.classList.contains('theme-ink') ? 'theme-warm' : 'theme-ink';
                applyTheme(next);
                try { localStorage.setItem('chat-workspace-theme', next); } catch (e) {}
            });
        }

        // --- 2. Chat Timeline Layout Listeners ---
        document.addEventListener('DOMContentLoaded', () => {
            /* ---------------- Splash (once per session) ---------------- */
            const splash = document.getElementById('chat-splash');
            let seen = false;
            try { seen = sessionStorage.getItem('splash_seen') === '1'; } catch (e) {}
            if (seen) {
                splash.remove();
            } else {
                setTimeout(() => {
                    splash.classList.add('hide');
                    try { sessionStorage.setItem('splash_seen', '1'); } catch (e) {}
                    setTimeout(() => splash.remove(), 700);
                }, 1900);
            }

            /* ---------------- Mobile drawer ---------------- */
            const drawer = document.getElementById('chat-drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            const openBtn = document.getElementById('drawer-open');
            const closeBtn = document.getElementById('drawer-close');

            const openDrawer = () => {
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };
            const closeDrawer = () => {
                drawer.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            };
            openBtn?.addEventListener('click', openDrawer);
            closeBtn?.addEventListener('click', closeDrawer);
            backdrop.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

            /* ---------------- Chat logic ---------------- */
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const typingContainer = document.getElementById('typing-container');
            const typingText = document.getElementById('typing-text');
            const chatTimeline = document.getElementById('chat-timeline');

            if (!chatTimeline) return;

            // Lock initial viewport viewing focus directly onto the bottom entries
            chatTimeline.scrollTop = chatTimeline.scrollHeight;

            // Channel Context Variable safely set up for the read endpoint snippet
            const channelId = @json($currentChannel?->id ?? null);

            // --- 🔄 1-SECOND BACKGROUND AUTO-REFRESH ENGINE ---
            const currentUrl = window.location.href;

            setInterval(async () => {
                try {
                    // Fetch current workspace snapshot silently
                    const response = await fetch(currentUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        const htmlText = await response.text();

                        // Parse incoming string to access nodes cleanly
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');
                        const incomingWrapper = doc.getElementById('messages-wrapper');
                        const currentWrapper = document.getElementById('messages-wrapper');

                        if (incomingWrapper && currentWrapper) {
                            if (incomingWrapper.innerHTML.trim() !== currentWrapper.innerHTML.trim()) {

                                // 🔊 AUDIO RING ENGINE (By message count difference)
                                const currentCount = currentWrapper.querySelectorAll('.message-bubble').length;
                                const incomingBubbles = incomingWrapper.querySelectorAll('.message-bubble');
                                const incomingCount = incomingBubbles.length;

                                if (incomingCount > currentCount) {
                                    const lastIncomingBubble = incomingBubbles[incomingCount - 1];
                                    const nameSpan = lastIncomingBubble.querySelector('.sender-name-label');

                                    // Read text value directly. If it says "You", do not make sound.
                                    const isMessageFromMe = nameSpan ? nameSpan.innerText.trim() === 'You' : false;

                                    if (!isMessageFromMe) {
                                        playNotificationSound();

                                        // ==========================================
                                        // 📝 MARK-READ SNIPPET INSERTED HERE
                                        // ==========================================
                                        if (channelId) {
                                            fetch(`/channels/${channelId}/read`, {
                                                method: 'POST',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                                            }).catch(err => console.error("Error updating read state:", err));
                                        }
                                    }
                                }

                                // Detect if user is reading older history logs
                                const isScrolledToBottom = (chatTimeline.scrollHeight - chatTimeline.clientHeight - chatTimeline.scrollTop) < 60;

                                // Inject new messages node
                                currentWrapper.innerHTML = incomingWrapper.innerHTML;

                                // Snap down if they were already at the bottom
                                if (isScrolledToBottom) {
                                    chatTimeline.scrollTop = chatTimeline.scrollHeight;
                                }
                            }
                        }
                    }
                } catch (err) {
                    console.debug('Background sync cycle paused temporarily.');
                }
            }, 1000); // 1000ms = 1 Second Loop Rate

            // --- AJAX FORM MESSAGE SUBMISSION ---
            if (chatForm && messageInput) {
                chatForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const textValue = messageInput.value.trim();
                    if (!textValue) return;

                    const formData = new FormData(chatForm);
                    const actionUrl = chatForm.getAttribute('action');

                    if (textValue.toLowerCase().includes('@timmy') && typingText && typingContainer) {
                        typingText.innerText = "Timmy is thinking...";
                        typingContainer.classList.remove('hidden');
                    }

                    messageInput.value = '';

                    try {
                        await fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        chatTimeline.scrollTop = chatTimeline.scrollHeight;
                    } catch (error) {
                        console.error('Failed to dispatch feed entry data package.', error);
                    }
                });
            }

            // Monitor keyboard interactions for typing indicators
            let typingTimeout;
            if (messageInput && typingContainer && typingText) {
                messageInput.addEventListener('input', () => {
                    if (messageInput.value.trim().length > 0) {
                        const isTimmy = messageInput.value.toLowerCase().includes('@timmy');
                        typingText.innerText = isTimmy ? "Timmy is listening..." : "You are typing...";
                        typingContainer.classList.remove('hidden');
                    } else {
                        typingContainer.classList.add('hidden');
                    }

                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        typingContainer.classList.add('hidden');
                    }, 2000);
                });
            }
        });
    </script>
</x-app-layout>