<x-app-layout>

{{-- GLOBAL NOTIFICATION SOUND --}}
<audio id="chatNotificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<style>
    @keyframes bellWiggle {
        0%, 100% { transform: rotate(0deg); }
        20% { transform: rotate(-12deg); }
        40% { transform: rotate(10deg); }
        60% { transform: rotate(-8deg); }
        80% { transform: rotate(6deg); }
    }
    .bell-wiggle { animation: bellWiggle 1.6s ease-in-out infinite; animation-delay: 1s; }

    @keyframes sheetIn {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .modal-panel { animation: sheetIn .22s ease-out; }

    @keyframes riseIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: none; }
    }
    .rise { opacity: 0; animation: riseIn .5s ease forwards; }
    .rise-1 { animation-delay: .05s } .rise-2 { animation-delay: .12s }
    .rise-3 { animation-delay: .19s } .rise-4 { animation-delay: .26s }

    .lobby-card { transition: transform .15s ease-out, box-shadow .3s ease, border-color .2s; will-change: transform; transform-style: preserve-3d; }
    .lobby-card:hover { box-shadow: 0 18px 36px -22px rgba(32,33,32,.35); }

    .no-scrollbar { scrollbar-width: none; }
    .no-scrollbar::-webkit-scrollbar { display: none; }

    /* Shared building blocks */
    .d-card  { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; }
    .d-label { font-size: .75rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-2); }
    .d-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; padding: 0 18px; border-radius: 12px;
        font-size: .875rem; font-weight: 600; transition: background .15s, transform .1s, color .15s;
        -webkit-tap-highlight-color: transparent;
    }
    .d-btn:active { transform: scale(.97); }
    .d-btn-primary { background: var(--btn); color: #fff; }
    .d-btn-primary:hover { background: var(--black-2); }
    .d-btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
    .d-btn-ghost:hover { background: var(--hover); }
    .d-btn-sm { min-height: 36px; padding: 0 14px; font-size: .8125rem; border-radius: 10px; }

    /* ---------- Two-page slider (Home / Chats) ---------- */
    #pager-viewport { margin: -8px; padding: 8px; overflow: hidden; }
    #pager-track {
        display: flex; align-items: flex-start; width: 100%;
        transition: transform .45s cubic-bezier(.22,.8,.2,1), height .3s ease;
    }
    .pager-panel { flex: 0 0 100%; min-width: 0; }

    #tab-indicator { transition: transform .35s cubic-bezier(.22,.8,.2,1); }
    .tab-btn { position: relative; z-index: 1; transition: color .25s; -webkit-tap-highlight-color: transparent; }

    .chat-row { transition: background .15s, border-color .15s, transform .1s; -webkit-tap-highlight-color: transparent; }
    .chat-row:hover { background: var(--hover); }
    .chat-row:active { transform: scale(.985); }

    @media (prefers-reduced-motion: reduce) {
        #pager-track, #tab-indicator, .rise { transition: none !important; animation: none !important; opacity: 1; }
    }
</style>

<script>
function playNotificationSound() {
    const sound = document.getElementById('chatNotificationSound');
    if (!sound) return;
    sound.currentTime = 0;
    sound.play().catch(() => {});
}
</script>

@php
    $pendingCount = isset($ownedLobbiesWithRequests) ? $ownedLobbiesWithRequests->sum(fn($lobby) => $lobby->members->count()) : 0;
@endphp

<div class="mx-auto w-full max-w-6xl px-4 pb-12 sm:px-6 lg:px-8">

    {{-- SESSION TOASTS --}}
    @if(session('success') || session('error'))
        <div class="space-y-2 pt-6">
            @if(session('success'))
                <div class="rounded-2xl bg-(--success-bg) px-4 py-3.5 text-sm font-medium text-(--success)">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl bg-(--error-bg) px-4 py-3.5 text-sm font-medium text-(--error)">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    @endif

    {{-- ===================== PAGE SWITCHER (sticky) ===================== --}}
    <div class="sticky top-16 z-40 -mx-4 bg-(--bg)/90 px-4 py-3 backdrop-blur-md sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div role="tablist" aria-label="Dashboard pages"
             class="relative grid w-full max-w-sm grid-cols-2 rounded-2xl border border-(--border) bg-(--surface) p-1 sm:max-w-xs">
            <span id="tab-indicator" class="absolute inset-y-1 left-1 w-[calc(50%-4px)] rounded-xl bg-(--btn)"></span>

            <button type="button" id="tab-home" role="tab" aria-selected="true" aria-controls="panel-home"
                    class="tab-btn flex min-h-11 items-center justify-center gap-2 rounded-xl text-sm font-semibold text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z" /></svg>
                Home
            </button>

            <button type="button" id="tab-chats" role="tab" aria-selected="false" aria-controls="panel-chats"
                    class="tab-btn flex min-h-11 items-center justify-center gap-2 rounded-xl text-sm font-semibold text-(--text-2)">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 6l3-3h11a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z" /></svg>
                Chats
                @if($joinedLobbies->count() > 0)
                    <span id="chat-count" class="rounded-full bg-(--selected) px-2 py-0.5 text-xs font-bold tabular-nums text-(--black)">{{ $joinedLobbies->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- ===================== SLIDER ===================== --}}
    <div id="pager-viewport">
        <div id="pager-track">

            {{-- ============================================================
                 PAGE 1 — HOME
                 ============================================================ --}}
            <div id="panel-home" class="pager-panel space-y-6" role="tabpanel" aria-labelledby="tab-home">

                {{-- HERO --}}
                <section class="d-card rise rise-1 p-5 sm:p-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile.edit') }}" title="Edit Profile" class="group shrink-0">
                            <div class="relative grid h-16 w-16 place-items-center overflow-hidden rounded-2xl border border-(--border) bg-(--bubble-me) text-lg font-bold uppercase text-(--black) transition group-hover:border-(--btn) sm:h-20 sm:w-20">
                                @if(auth()->user()->profile?->avatar_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="h-full w-full object-cover" />
                                @else
                                    {{ substr(auth()->user()->name ?? 'JD', 0, 2) }}
                                @endif
                            </div>
                        </a>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-(--text-2)">Welcome back 👋</p>
                            <h1 class="truncate text-2xl font-extrabold tracking-tight text-(--black) sm:text-3xl">
                                {{ auth()->user()->name ?? 'Jane Doe' }}
                            </h1>
                            <a href="{{ route('profile.edit') }}" class="mt-0.5 inline-block text-sm font-semibold text-(--text-2) underline-offset-4 transition hover:text-(--black) hover:underline">
                                Edit profile
                            </a>
                        </div>
                    </div>

                    {{-- Focus --}}
                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        <span class="d-label mr-1">Your focus</span>
                        @forelse(auth()->user()->interests as $interest)
                            <span class="rounded-full border border-(--border) bg-(--bg) px-3 py-1 text-sm font-medium text-(--text)">
                                {{ $interest->name }}
                            </span>
                        @empty
                            <span class="text-sm text-(--muted)">No interests selected yet</span>
                        @endforelse
                    </div>

                    {{-- Quick actions --}}
                    <div class="mt-5 flex flex-wrap items-stretch gap-2.5">
                        <a href="{{ route('lobby.create') }}" class="d-btn d-btn-primary flex-1 sm:flex-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            New Lobby
                        </a>

                        <a href="{{ route('lobbies.index') }}" class="d-btn d-btn-ghost flex-1 sm:flex-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            Explore
                        </a>

                        {{-- NOTIFICATION BUTTON — single job: opens the modal. Badge is updated live by JS below. --}}
                        <button id="notifBtn" type="button" title="Notifications" aria-label="Notifications"
                                class="d-btn d-btn-ghost relative !px-0 w-11 shrink-0 {{ $pendingCount > 0 ? 'bell-wiggle' : '' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                            </svg>
                            @if($pendingCount > 0)
                                <span id="notifBadge" class="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-(--error) px-1 text-[11px] font-bold text-white">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </button>
                    </div>
                </section>

                {{-- STATS --}}
                <section class="rise rise-2 grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <a href="{{ route('lobbies.joined') }}" class="d-card p-4 transition hover:bg-(--hover) active:scale-[.98]">
                        <span class="d-label block">Joined</span>
                        <span class="mt-1 block text-3xl font-extrabold tabular-nums text-(--black)">{{ $joinedLobbies->count() }}</span>
                        <span class="text-xs text-(--muted)">lobbies</span>
                    </a>

                    <a href="{{ route('lobbies.owned') }}" class="d-card p-4 transition hover:bg-(--hover) active:scale-[.98]">
                        <span class="d-label block">Owned</span>
                        <span class="mt-1 block text-3xl font-extrabold tabular-nums text-(--black)">{{ $ownedLobbiesWithRequests->count() ?? 0 }}</span>
                        <span class="text-xs text-(--muted)">lobbies</span>
                    </a>

                    <div class="d-card p-4">
                        <span class="d-label block">CV document</span>
                        <span class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold {{ auth()->user()->profile?->cv_path ? 'bg-(--success-bg) text-(--success)' : 'bg-(--warning-bg) text-(--warning)' }}">
                            <span class="h-2 w-2 rounded-full {{ auth()->user()->profile?->cv_path ? 'bg-(--success)' : 'bg-(--warning)' }}"></span>
                            {{ auth()->user()->profile?->cv_path ? 'Ready' : 'Missing' }}
                        </span>
                    </div>

                    <button type="button" onclick="document.getElementById('notifBtn').click()" class="d-card p-4 text-left transition hover:bg-(--hover) active:scale-[.98]">
                        <span class="d-label block">Pending requests</span>
                        <span class="mt-1 block text-3xl font-extrabold tabular-nums {{ $pendingCount > 0 ? 'text-(--warning)' : 'text-(--black)' }}">{{ $pendingCount }}</span>
                        <span class="text-xs text-(--muted)">tap to review</span>
                    </button>
                </section>

                {{-- SEARCH --}}
                <section class="d-card rise rise-3 p-4 sm:p-5">
                    <form method="GET" action="{{ route('dashboard') }}" class="space-y-3">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-(--muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                </svg>
                                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search lobbies..." enterkeyhint="search"
                                       class="block min-h-12 w-full rounded-xl border border-(--border) bg-(--bg) pl-11 pr-4 text-base text-(--text) outline-none transition placeholder:text-(--muted) focus:border-(--btn) focus:bg-(--surface) focus:ring-4 focus:ring-(--selected)">
                            </div>
                            <button type="submit" class="d-btn d-btn-primary">Search</button>
                        </div>

                        <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 pb-0.5 sm:mx-0 sm:flex-wrap sm:px-0">
                            <a href="{{ route('dashboard') }}"
                               class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition {{ !request('interest') ? 'border-(--btn) bg-(--btn) text-white' : 'border-(--border) bg-(--surface) text-(--text-2) hover:bg-(--hover)' }}">
                                All
                            </a>
                            @foreach(\App\Models\Interest::take(5)->get() as $tag)
                                <a href="{{ route('dashboard', ['interest' => $tag->id]) }}"
                                   class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('interest') == $tag->id ? 'border-(--btn) bg-(--btn) text-white' : 'border-(--border) bg-(--surface) text-(--text-2) hover:bg-(--hover)' }}">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </form>
                </section>

                {{-- RECOMMENDED --}}
                <section class="rise rise-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold tracking-tight text-(--black)">Recommended for you</h2>
                        <span class="rounded-full bg-(--info-bg) px-2.5 py-1 text-xs font-semibold text-(--info)">Smart match</span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($recommendedLobbies as $lobby)
                        <div class="lobby-card d-card group flex flex-col justify-between overflow-hidden p-5 hover:border-(--muted)">
                            <div>
                                <div class="mb-2 flex items-start justify-between gap-3">
                                    <h3 class="truncate text-base font-bold text-(--black)">{{ $lobby->name }}</h3>
                                    <span class="shrink-0 rounded-full bg-(--info-bg) px-2.5 py-1 text-xs font-bold text-(--info)">
                                        {{ $lobby->match_percentage ?? 0 }}% match
                                    </span>
                                </div>

                                <p class="mb-3 line-clamp-2 text-sm leading-relaxed text-(--text-2)">{{ $lobby->description }}</p>

                                <div class="mb-3 flex flex-wrap gap-1.5">
                                    @foreach($lobby->interests as $interest)
                                        <span class="rounded-full border border-(--border) bg-(--bg) px-2.5 py-1 text-xs font-medium text-(--text-2)">
                                            #{{ $interest->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <p class="text-sm text-(--muted)">{{ $lobby->members->count() }} members</p>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-(--border) pt-4">
                                <span class="min-w-0 truncate text-sm text-(--text-2)">Goal: <span class="font-semibold text-(--text)">{{ $lobby->project_goal }}</span></span>

                                @if(Auth::id() === $lobby->owner_id)
                                    <span class="shrink-0 rounded-full bg-(--selected) px-3 py-1.5 text-xs font-bold text-(--black)">Your Lobby</span>
                                @else
                                    @php
                                        $userApplication = \App\Models\LobbyMember::where('lobby_id', $lobby->id)->where('user_id', Auth::id())->first();
                                    @endphp

                                    @if(!$userApplication)
                                        <form method="POST" action="{{ route('lobbies.join', $lobby->id) }}" class="shrink-0">
                                            @csrf
                                            <button class="d-btn d-btn-primary d-btn-sm">Join</button>
                                        </form>
                                    @elseif($userApplication->status === 'pending')
                                        <span class="shrink-0 rounded-full bg-(--warning-bg) px-3 py-1.5 text-xs font-bold text-(--warning)">⏳ Pending</span>
                                    @elseif($userApplication->status === 'accepted')
                                        <a href="{{ route('chat.index', $lobby->id) }}" class="d-btn d-btn-primary d-btn-sm shrink-0">Enter Chat →</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-(--border) bg-(--surface) py-10 text-center text-sm text-(--muted)">
                            No recommendations available right now.
                        </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- ============================================================
                 PAGE 2 — CHATS
                 ============================================================ --}}
            <div id="panel-chats" class="pager-panel space-y-5" role="tabpanel" aria-labelledby="tab-chats">

                <section class="d-card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight text-(--black)">Chats</h2>
                            <p class="mt-1 text-sm text-(--text-2)">Instantly reopen rooms from your active team lobbies.</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-(--selected) px-3 py-1 text-sm font-bold tabular-nums text-(--black)">
                            {{ $joinedLobbies->count() }}
                        </span>
                    </div>

                    @if($joinedLobbies->count() > 3)
                        <div class="relative mt-4">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-(--muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                            <input type="search" id="chat-search" placeholder="Filter chats..." autocomplete="off"
                                   class="block min-h-12 w-full rounded-xl border border-(--border) bg-(--bg) pl-11 pr-4 text-base text-(--text) outline-none transition placeholder:text-(--muted) focus:border-(--btn) focus:bg-(--surface) focus:ring-4 focus:ring-(--selected)">
                        </div>
                    @endif
                </section>

                @if($joinedLobbies->count())
                    <div id="chat-list" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach($joinedLobbies as $lobby)
                        <a href="{{ route('chat.index', $lobby->id) }}" data-chat-name="{{ strtolower($lobby->name) }}"
                           class="chat-row d-card flex items-center gap-4 p-4">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-(--bubble-me) text-sm font-bold uppercase text-(--black)">
                                {{ substr($lobby->name, 0, 2) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-base font-bold text-(--black)">{{ $lobby->name }}</p>
                                <p class="truncate text-sm text-(--muted)">Goal: {{ $lobby->project_goal }}</p>
                            </div>

                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-(--btn) text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            </span>
                        </a>
                        @endforeach
                    </div>

                    <div id="chat-empty" class="hidden rounded-2xl border border-dashed border-(--border) bg-(--surface) py-10 text-center text-sm text-(--muted)">
                        No chats match your filter.
                    </div>
                @else
                    <div class="d-card flex flex-col items-center gap-4 border-dashed px-6 py-12 text-center">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-(--bg) text-(--muted)">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 6l3-3h11a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z" /></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-(--black)">No active channels found.</p>
                            <p class="mt-1 text-sm text-(--text-2)">Join a lobby and its chat will show up here.</p>
                        </div>
                        <a href="{{ route('lobbies.index') }}" class="d-btn d-btn-primary">Explore lobbies</a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ===================== NOTIFICATIONS MODAL (bottom sheet on mobile) ===================== --}}
    <div id="notifModal" class="hidden fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4">
        <div id="notifBackdrop" class="absolute inset-0 bg-(--black)/40 backdrop-blur-sm"></div>

        <div class="modal-panel relative flex max-h-[88vh] w-full flex-col overflow-hidden rounded-t-3xl border border-(--border) bg-(--surface) shadow-2xl sm:max-w-lg sm:rounded-3xl">
            <div class="mx-auto mt-2.5 h-1 w-10 rounded-full bg-(--selected) sm:hidden"></div>

            <div class="flex items-center justify-between border-b border-(--border) px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-(--black)">Incoming requests</h3>
                    <p class="text-xs text-(--muted)">{{ $pendingCount }} waiting for you</p>
                </div>
                <button id="notifClose" type="button" aria-label="Close"
                        class="grid h-10 w-10 place-items-center rounded-xl text-(--text-2) transition hover:bg-(--hover) hover:text-(--text)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3 overflow-y-auto p-4 sm:p-5">
                @if($pendingCount > 0)
                    @foreach($ownedLobbiesWithRequests as $lobby)
                        @foreach($lobby->members as $applicant)
                            <div class="rounded-2xl border border-(--border) bg-(--bg) p-4">
                                <div class="min-w-0">
                                    <p class="truncate text-xs text-(--text-2)">
                                        Target Lobby: <span class="font-semibold text-(--text)">{{ $lobby->name }}</span>
                                    </p>
                                    <p class="mt-0.5 truncate text-base font-bold text-(--black)">
                                        {{ $applicant->name }}
                                    </p>
                                </div>

                                <div class="mt-3 flex items-stretch gap-2">
                                    @if($applicant->profile?->cv_path)
                                        <a href="{{ route('lobby.member.cv', [$lobby->id, $applicant->id]) }}"
                                           target="_blank"
                                           class="d-btn d-btn-ghost d-btn-sm">
                                            CV
                                        </a>
                                    @endif

                                    <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'accepted']) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="d-btn d-btn-primary d-btn-sm w-full !min-h-11">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'rejected']) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="d-btn d-btn-ghost d-btn-sm w-full !min-h-11 hover:!bg-(--error-bg) hover:!text-(--error)">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @else
                    <div class="py-12 text-center text-sm text-(--muted)">
                        No pending requests right now.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notifBtn');
    const notifModal = document.getElementById('notifModal');
    const notifBackdrop = document.getElementById('notifBackdrop');
    const notifClose = document.getElementById('notifClose');

    function openNotifModal() {
        notifModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeNotifModal() {
        notifModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    notifBtn.addEventListener('click', openNotifModal);
    notifClose?.addEventListener('click', closeNotifModal);
    notifBackdrop?.addEventListener('click', closeNotifModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeNotifModal();
    });

    // ---- Desktop push permission — asked quietly on first click anywhere, not on the bell ----
    function requestDesktopPermission() {
        if (!('Notification' in window) || Notification.permission !== 'default') return;
        Notification.requestPermission();
    }
    document.addEventListener('click', requestDesktopPermission, { once: true });

    function spawnPushNotification(title, body) {
        if (!('Notification' in window) || Notification.permission !== 'granted' || document.hasFocus()) return;
        const notification = new Notification(title, { body, icon: '/favicon.ico', tag: 'app-notif', renotify: true });
        notification.onclick = function () {
            window.focus();
            this.close();
        };
    }

    // ---- Live badge (lobby requests + unread chat) via SSE ----
    function renderBadge(count) {
        let badge = document.getElementById('notifBadge');
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notifBadge';
                badge.className = 'absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-(--error) px-1 text-[11px] font-bold text-white';
                notifBtn.appendChild(badge);
            }
            badge.textContent = count;
            notifBtn.classList.add('bell-wiggle');
        } else {
            badge?.remove();
            notifBtn.classList.remove('bell-wiggle');
        }
    }

    let lastTotal = {{ $pendingCount }};

    if (typeof EventSource !== 'undefined') {
        const source = new EventSource('{{ route('notifications.stream') }}');
        source.addEventListener('update', (e) => {
            const data = JSON.parse(e.data);
            const total = data.pending + data.unread;
            renderBadge(total);
            if (total > lastTotal) {
                playNotificationSound();
                spawnPushNotification(
                    'New activity',
                    data.unread > 0 ? 'You have new chat messages.' : 'You have a new lobby request.'
                );
            }
            lastTotal = total;
        });
        source.onerror = () => { /* browser retries automatically */ };
    }

    // ---- Lobby card tilt ----
    document.querySelectorAll('.lobby-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const rotateX = ((y / rect.height) - 0.5) * -6;
            const rotateY = ((x / rect.width) - 0.5) * 6;
            card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) translateY(0)';
        });
    });

    // ================= Two-page slider (Home / Chats) =================
    const viewport = document.getElementById('pager-viewport');
    const track = document.getElementById('pager-track');
    const panels = [document.getElementById('panel-home'), document.getElementById('panel-chats')];
    const tabs = [document.getElementById('tab-home'), document.getElementById('tab-chats')];
    const indicator = document.getElementById('tab-indicator');
    let page = 0;

    const fitHeight = () => { track.style.height = panels[page].offsetHeight + 'px'; };

    const goTo = (i, { scroll = true, updateHash = true } = {}) => {
        page = i;
        track.style.transform = `translateX(${-100 * i}%)`;
        indicator.style.transform = `translateX(${100 * i}%)`;

        tabs.forEach((tab, idx) => {
            const on = idx === i;
            tab.setAttribute('aria-selected', on ? 'true' : 'false');
            tab.classList.toggle('text-white', on);
            tab.classList.toggle('text-(--text-2)', !on);
        });
        panels.forEach((panel, idx) => {
            panel.toggleAttribute('inert', idx !== i);
            panel.setAttribute('aria-hidden', idx !== i ? 'true' : 'false');
        });

        fitHeight();
        if (scroll && window.scrollY > 0) window.scrollTo({ top: 0, behavior: 'smooth' });
        if (updateHash) {
            try { history.replaceState(null, '', i === 1 ? '#chats' : location.pathname + location.search); } catch (e) {}
        }
    };

    tabs[0].addEventListener('click', () => goTo(0));
    tabs[1].addEventListener('click', () => goTo(1));

    // Keep height in sync when content changes
    if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(fitHeight);
        panels.forEach(p => ro.observe(p));
    }
    window.addEventListener('resize', fitHeight);

    // Swipe between pages (ignored on sliders, inputs and the interest chips row)
    let sx = 0, sy = 0, swipeOK = false;
    viewport.addEventListener('touchstart', (e) => {
        const t = e.touches[0];
        sx = t.clientX; sy = t.clientY;
        swipeOK = !e.target.closest('.no-scrollbar, input, textarea, select');
    }, { passive: true });
    viewport.addEventListener('touchend', (e) => {
        if (!swipeOK) return;
        const t = e.changedTouches[0];
        const dx = t.clientX - sx, dy = t.clientY - sy;
        if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy) * 1.5) {
            if (dx < 0 && page === 0) goTo(1);
            else if (dx > 0 && page === 1) goTo(0);
        }
    }, { passive: true });

    // Filter chats
    const chatSearch = document.getElementById('chat-search');
    if (chatSearch) {
        const rows = document.querySelectorAll('#chat-list [data-chat-name]');
        const empty = document.getElementById('chat-empty');
        chatSearch.addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase().trim();
            let visible = 0;
            rows.forEach(row => {
                const match = row.dataset.chatName.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            empty.classList.toggle('hidden', visible > 0);
            fitHeight();
        });
    }

    // Initial page (supports /dashboard#chats)
    goTo(location.hash === '#chats' ? 1 : 0, { scroll: false, updateHash: false });
});
</script>

</x-app-layout>