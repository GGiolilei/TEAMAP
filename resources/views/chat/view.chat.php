<x-app-layout>
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

        .no-scrollbar { scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        .bubble { padding: 10px 14px; font-size: .9375rem; line-height: 1.45; word-break: break-word; overflow-wrap: anywhere; border: 1px solid var(--border); color: var(--text); }
        .bubble-me    { background: var(--bubble-me);    border-color: transparent; border-radius: 18px 18px 4px 18px; }
        .bubble-other { background: var(--bubble-other); border-radius: 18px 18px 18px 4px; }

        .ch-link { transition: background .15s, color .15s; -webkit-tap-highlight-color: transparent; }

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

    <div class="relative flex overflow-hidden bg-(--bg) text-(--text)" style="height: calc(100vh - 4rem); height: calc(100dvh - 4rem);">

        {{-- Drawer backdrop (mobile) --}}
        <div id="drawer-backdrop" class="fixed inset-0 z-[65] hidden bg-(--black)/40 backdrop-blur-sm lg:hidden"></div>

        {{-- ===================== SIDEBAR / DRAWER ===================== --}}
        <aside id="chat-drawer"
               class="fixed inset-y-0 left-0 z-[70] flex w-[86%] max-w-sm -translate-x-full flex-col overflow-y-auto border-r border-(--border) bg-(--surface) p-5 lg:static lg:z-auto lg:w-80 lg:max-w-none lg:translate-x-0 lg:shrink-0">

            <div class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-(--border) bg-(--surface) px-3.5 text-sm font-semibold text-(--text) transition hover:bg-(--hover)">
                        ← Dashboard
                    </a>

                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-(--success-bg) px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-(--success)">
                            Active room
                        </span>
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
                               class="ch-link flex min-h-11 items-center gap-2 rounded-xl px-3 text-sm {{ $isActive ? 'bg-(--btn) font-bold text-white' : 'font-medium text-(--text-2) hover:bg-(--hover) hover:text-(--text)' }}">
                                <span class="font-mono text-base {{ $isActive ? 'text-white/70' : 'text-(--muted)' }}">#</span>
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
                        Workspace Roster (<span id="roster-count">{{ $lobby->members->count() }}</span>)
                    </h3>
                    <div id="roster-container" class="max-h-64 space-y-2 overflow-y-auto pr-1">
                        @foreach($lobby->members as $member)
                            <div data-user-id="{{ $member->id }}" class="flex items-center justify-between gap-3 rounded-xl border border-(--border) bg-(--bg) p-2.5">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-(--bubble-me) text-xs font-bold uppercase text-(--black)">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="truncate text-sm font-bold text-(--text)">{{ $member->name }}</h5>
                                        <p class="text-[11px] uppercase tracking-wide text-(--muted)">{{ $member->id === $lobby->owner_id ? 'Organizer' : 'Partner' }}</p>
                                    </div>
                                </div>
                                {{-- Default status indicator to offline, updated via Echo presence channels if needed --}}
                                <span class="status-dot h-2.5 w-2.5 shrink-0 rounded-full bg-(--selected) transition-colors duration-300"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-(--border) pt-5 text-center font-mono text-xs text-(--muted)">
                Lobby Reference: #{{ $lobby->id }}
            </div>
        </aside>

        {{-- ===================== MAIN CHAT ===================== --}}
        <section class="flex min-w-0 flex-1 flex-col bg-(--surface)">

            {{-- Top bar --}}
            <header class="flex items-center gap-3 border-b border-(--border) bg-(--surface) px-3 py-3 sm:px-5">
                <button id="drawer-open" type="button" aria-label="Open menu"
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-(--border) text-(--text) transition hover:bg-(--hover) lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" /></svg>
                </button>

                @if($currentChannel)
                    <div class="hidden h-10 w-10 shrink-0 place-items-center rounded-xl bg-(--bg) sm:grid">
                        <span class="font-mono text-base font-bold text-(--text-2)">#</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-base font-bold text-(--black)">
                            <span class="font-mono text-(--muted) sm:hidden">#</span>{{ $currentChannel->name }}
                        </h3>
                        <p class="truncate text-xs text-(--text-2)">{{ $currentChannel->description ?? 'Secure team sync for verified partners' }}</p>
                    </div>
                @else
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-base font-bold text-(--black)">{{ $lobby->name }}</h3>
                    </div>
                @endif
            </header>

            {{-- Channel chips (mobile quick switch) --}}
            @if($lobby->channels->count() > 1)
                <nav class="no-scrollbar flex gap-2 overflow-x-auto border-b border-(--border) bg-(--bg) px-3 py-2.5 lg:hidden" aria-label="Channels">
                    @foreach($lobby->channels as $roomChannel)
                        @php $isActive = $currentChannel?->id === $roomChannel->id; @endphp
                        <a href="{{ route('chat.index', ['lobby' => $lobby->id, 'channel' => $roomChannel->id]) }}"
                           class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold {{ $isActive ? 'border-(--btn) bg-(--btn) text-white' : 'border-(--border) bg-(--surface) text-(--text-2)' }}">
                            # {{ $roomChannel->name }}
                        </a>
                    @endforeach
                </nav>
            @endif

            @if($currentChannel)
                {{-- Timeline --}}
                <div id="chat-timeline" class="flex-1 space-y-4 overflow-y-auto overscroll-contain bg-(--surface) px-3 py-5 sm:px-6">
                    <div class="my-1 flex items-center justify-center">
                        <div class="rounded-full bg-(--info-bg) px-3.5 py-1.5 text-xs font-medium text-(--info)">
                            🛡️ #{{ $currentChannel->name }} initialized securely
                        </div>
                    </div>

                    @forelse($currentChannel->messages ?? [] as $message)
                        @php $isMe = $message->user_id === auth()->id(); @endphp

                        <div class="flex max-w-[88%] items-end gap-2.5 sm:max-w-[75%] {{ $isMe ? 'ml-auto flex-row-reverse' : '' }} animate-slide-up">
                            @unless($isMe)
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-(--btn) text-xs font-bold uppercase text-white">
                                    {{ substr($message->user->name ?? '?', 0, 2) }}
                                </div>
                            @endunless
                            <div class="min-w-0 {{ $isMe ? 'text-right' : '' }}">
                                <div class="mb-1 flex items-baseline gap-2 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                    <span class="text-xs font-bold text-(--text)">{{ $isMe ? 'You' : ($message->user->name ?? 'Anonymous') }}</span>
                                    <span class="text-[11px] text-(--muted)">{{ $message->created_at->format('g:i A') }}</span>
                                </div>
                                <div class="bubble {{ $isMe ? 'bubble-me text-left' : 'bubble-other' }}">{{ $message->content }}</div>
                            </div>
                        </div>
                    @empty
                        <div id="empty-history-notice" class="py-16 text-center">
                            <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-(--bg) text-2xl">💬</div>
                            <p class="text-sm font-semibold text-(--text)">#{{ $currentChannel->name }} is quiet</p>
                            <p class="mt-1 text-sm text-(--muted)">Type a message down below to start history!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Typing --}}
                <div id="typing-container" class="hidden bg-(--surface) px-4 pb-1 sm:px-6">
                    <div class="animate-slide-up flex w-fit items-center gap-2 rounded-full bg-(--bubble-other) px-3.5 py-2">
                        <div class="flex items-center gap-1">
                            <span class="animate-bounce-slow-1 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                            <span class="animate-bounce-slow-2 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                            <span class="animate-bounce-slow-3 h-1.5 w-1.5 rounded-full bg-(--text-2)"></span>
                        </div>
                        <span id="typing-text" class="text-xs text-(--text-2)">You are typing...</span>
                    </div>
                </div>

                {{-- Composer --}}
                <div class="border-t border-(--border) bg-(--surface) px-3 pt-3 sm:px-5" style="padding-bottom: max(.75rem, env(safe-area-inset-bottom));">
                    <form id="chat-form" action="{{ route('messages.store', ['channel' => $currentChannel->id]) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input id="message-input" name="content" type="text" autocomplete="off" required enterkeyhint="send"
                               placeholder="Message #{{ $currentChannel->name }}..."
                               class="block min-h-12 w-full flex-1 rounded-2xl border border-(--border) bg-(--bg) px-4 text-base text-(--text) outline-none transition placeholder:text-(--muted) focus:border-(--btn) focus:bg-(--surface) focus:ring-4 focus:ring-(--selected)" />
                        <button type="submit" aria-label="Send"
                                class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-(--btn) text-white transition hover:bg-(--black-2) active:scale-95">
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
                        This workspace has no channels yet. Click the <strong class="text-(--black)">+</strong> icon in the menu to get started.
                    </p>
                    <button type="button" onclick="document.getElementById('drawer-open').click()"
                            class="inline-flex min-h-11 items-center rounded-xl bg-(--btn) px-5 text-sm font-semibold text-white lg:hidden">
                        Open menu
                    </button>
                </div>
            @endif

        </section>
    </div>

    <script>
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
            const currentUserId = {{ auth()->id() }};
            const currentChannelId = {{ $currentChannel?->id ?? 'null' }};
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const typingContainer = document.getElementById('typing-container');
            const typingText = document.getElementById('typing-text');
            const chatTimeline = document.getElementById('chat-timeline');
            const emptyNotice = document.getElementById('empty-history-notice');

            if (!chatTimeline) return;

            // Helper to auto-scroll chat window
            const scrollToBottom = () => {
                chatTimeline.scrollTop = chatTimeline.scrollHeight;
            };
            scrollToBottom();

            // Escape user-provided text before injecting into HTML
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            // 1. AJAX Form submission prevents layout jarring page refreshes
            if (chatForm) {
                chatForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const content = messageInput.value.trim();
                    if (!content) return;

                    const formData = new FormData(chatForm);
                    messageInput.value = ''; // Instantly clear input for snappy UX

                    // Fire backend event notification that client stopped typing
                    if (window.Echo && currentChannelId) {
                        window.Echo.private(`chat.${currentChannelId}`).whisper('typing', { typing: false });
                    }

                    fetch(chatForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Optional: Append instantly if broadcast loop doesn't echo back to sender
                    })
                    .catch(err => console.error("Message delivery failed:", err));
                });
            }

            // 2. Real-time Listeners via Laravel Echo (WebSockets / Reverb)
            if (window.Echo && currentChannelId) {

                // Track typing indicators across clients
                let typingTimeout;
                messageInput.addEventListener('input', () => {
                    window.Echo.private(`chat.${currentChannelId}`).whisper('typing', {
                        name: @json(auth()->user()->name),
                        typing: messageInput.value.trim().length > 0
                    });

                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        window.Echo.private(`chat.${currentChannelId}`).whisper('typing', { typing: false });
                    }, 3000);
                });

                // Listen to incoming messages & whisper triggers
                window.Echo.private(`chat.${currentChannelId}`)
                    .listen('MessageSent', (e) => {
                        if (emptyNotice) emptyNotice.remove();

                        const isMe = e.message.user_id === currentUserId;
                        const formattedTime = new Date(e.message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        const userInitial = (e.user?.name || '?').substring(0, 2);

                        const msgHtml = `
                            <div class="flex max-w-[88%] items-end gap-2.5 sm:max-w-[75%] ${isMe ? 'ml-auto flex-row-reverse' : ''} animate-slide-up">
                                ${isMe ? '' : `<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-(--btn) text-xs font-bold uppercase text-white">${esc(userInitial)}</div>`}
                                <div class="min-w-0 ${isMe ? 'text-right' : ''}">
                                    <div class="mb-1 flex items-baseline gap-2 ${isMe ? 'flex-row-reverse' : ''}">
                                        <span class="text-xs font-bold text-(--text)">${isMe ? 'You' : esc(e.user?.name || 'Anonymous')}</span>
                                        <span class="text-[11px] text-(--muted)">${esc(formattedTime)}</span>
                                    </div>
                                    <div class="bubble ${isMe ? 'bubble-me text-left' : 'bubble-other'}">${esc(e.message.content)}</div>
                                </div>
                            </div>
                        `;

                        chatTimeline.insertAdjacentHTML('beforeend', msgHtml);
                        scrollToBottom();
                    })
                    .listenForWhisper('typing', (e) => {
                        if (e.typing) {
                            typingText.textContent = `${e.name} is typing...`;
                            typingContainer.classList.remove('hidden');
                            scrollToBottom();
                        } else {
                            typingContainer.classList.add('hidden');
                        }
                    });

                // Optional: Connect Presence to Lobby for Active Roster Lights
                const lobbyId = {{ $lobby->id }};
                window.Echo.join(`lobby.${lobbyId}`)
                    .here((users) => {
                        users.forEach(u => updateRosterStatus(u.id, true));
                    })
                    .joining((user) => {
                        updateRosterStatus(user.id, true);
                    })
                    .leaving((user) => {
                        updateRosterStatus(user.id, false);
                    });
            }

            function updateRosterStatus(userId, isOnline) {
                const userRow = document.querySelector(`[data-user-id="${userId}"]`);
                if (userRow) {
                    const dot = userRow.querySelector('.status-dot');
                    if (isOnline) {
                        dot.className = "status-dot h-2.5 w-2.5 shrink-0 rounded-full bg-(--success) shadow-[0_0_0_3px_var(--success-bg)] transition-colors duration-300";
                    } else {
                        dot.className = "status-dot h-2.5 w-2.5 shrink-0 rounded-full bg-(--selected) transition-colors duration-300";
                    }
                }
            }
        });
    </script>
</x-app-layout>