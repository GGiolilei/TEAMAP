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

    @keyframes modalIn {
        from { opacity: 0; transform: translateY(8px) scale(.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-panel { animation: modalIn .18s ease-out; }

    .lobby-card { transition: transform .15s ease-out, box-shadow .3s ease; will-change: transform; transform-style: preserve-3d; }
</style>

<script>
function playNotificationSound() {
    const sound = document.getElementById('chatNotificationSound');
    if (!sound) return;
    sound.currentTime = 0;
    sound.play().catch(() => {});
}
</script>

<div class="relative min-h-screen bg-slate-950 text-slate-100 overflow-x-hidden flex items-center justify-center p-4 md:p-8">

    {{-- AMBIENT BACKGROUND GLOW --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-32 -left-24 w-[28rem] h-[28rem] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute top-40 -right-24 w-[26rem] h-[26rem] bg-purple-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-1/3 w-[24rem] h-[24rem] bg-emerald-600/10 rounded-full blur-[130px]"></div>
    </div>

    @php
        $pendingCount = isset($ownedLobbiesWithRequests) ? $ownedLobbiesWithRequests->sum(fn($lobby) => $lobby->members->count()) : 0;
    @endphp

    <div class="relative w-full max-w-7xl bg-slate-900/40 border border-slate-800/80 rounded-[2.5rem] p-6 backdrop-blur-md grid grid-cols-1 lg:grid-cols-[80px_1fr] gap-6 auto-rows-max min-h-[85vh]">

        {{-- ===================== LEFT SIDEBAR NAV ===================== --}}
        <div class="flex lg:flex-col items-center justify-between lg:justify-start gap-6 p-4 rounded-3xl bg-slate-950/60 border border-slate-800/80 lg:py-8 lg:h-full">
            <div class="flex lg:flex-col items-center gap-5 w-full justify-center">
                {{-- Explore --}}
                <a href="{{ route('lobbies.index') }}" title="Explore Lobbies"
                   class="p-3 rounded-2xl bg-slate-900 border border-slate-800 text-slate-400 hover:border-indigo-500/50 hover:text-indigo-400 transition-all duration-200 hover:scale-105 flex items-center justify-center w-12 h-12">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>

                {{-- Create Lobby --}}
                <a href="{{ route('lobby.create') }}" title="Create New Lobby"
                   class="p-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white transition-all duration-200 hover:scale-105 flex items-center justify-center w-12 h-12 shadow-lg shadow-indigo-600/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </a>

                {{-- NOTIFICATION BUTTON — single job: opens the modal. Badge is updated live by JS below. --}}
                <button id="notifBtn" type="button" title="Notifications"
                        class="relative p-3 rounded-2xl bg-slate-900 border border-slate-800 text-slate-400 hover:border-indigo-500/40 hover:text-slate-200 hover:scale-105 transition-all duration-200 flex items-center justify-center w-12 h-12 {{ $pendingCount > 0 ? 'bell-wiggle' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    @if($pendingCount > 0)
                        <span id="notifBadge" class="absolute -top-1 -right-1 min-w-[18px] h-4 px-1 flex items-center justify-center rounded-full bg-rose-500 text-white text-[9px] font-bold shadow-lg shadow-rose-500/30">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- Logout --}}
            <div class="lg:mt-auto">
                <button type="button" onclick="event.preventDefault();" title="Logout"
                        class="p-3 rounded-2xl bg-slate-900/40 border border-slate-800/40 text-slate-600 hover:text-rose-400 hover:border-rose-500/20 transition-all duration-200 flex items-center justify-center w-12 h-12">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ===================== RIGHT MAIN PANEL ===================== --}}
        <div class="space-y-6 flex flex-col justify-between">

            {{-- SESSION TOASTS --}}
            @if(session('success') || session('error'))
                <div class="space-y-2">
                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm backdrop-blur-sm shadow-lg shadow-emerald-500/5">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm backdrop-blur-sm shadow-lg shadow-rose-500/5">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- HEADER --}}
            <div class="relative rounded-3xl border border-slate-800/80 bg-gradient-to-br from-slate-900 via-slate-900/90 to-slate-950 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 overflow-hidden">
                <div class="space-y-4 flex-1 min-w-0">
                    <div class="space-y-1">
                        <p class="text-xs text-indigo-400 font-medium">Welcome back</p>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-50">
                            {{ auth()->user()->name ?? 'Jane Doe' }}
                        </h1>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 p-3 rounded-2xl bg-slate-950/60 border border-slate-800/60">
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mr-1">Your Focus</span>
                        @forelse(auth()->user()->interests as $interest)
                            <span class="px-2.5 py-1 text-xs rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                                {{ $interest->name }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-500">No interests selected yet</span>
                        @endforelse
                    </div>

                    @if($joinedLobbies->count() > 0)
                        <a href="{{ route('chat.index', $joinedLobbies->first()->id) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-600 hover:text-white hover:scale-105 transition-all duration-200">
                            Open Chat
                        </a>
                    @endif
                </div>

                <div class="flex flex-col items-center gap-2 self-start md:self-auto shrink-0">
                    <a href="{{ route('profile.edit') }}" class="group flex flex-col items-center gap-1.5">
                        <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-indigo-600/10 border-2 border-slate-800 group-hover:border-indigo-500 transition-all duration-300 flex items-center justify-center font-bold text-lg text-indigo-400 uppercase tracking-wider">
                            @if(auth()->user()->profile?->avatar_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover" />
                            @else
                                {{ substr(auth()->user()->name ?? 'JD', 0, 2) }}
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 group-hover:text-indigo-400 transition-colors">
                            Edit Profile
                        </span>
                    </a>
                </div>
            </div>

            {{-- SEARCH --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search lobbies..."
                               class="w-full bg-slate-950/70 border border-slate-800 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:border-indigo-500/50 focus:ring-0 text-slate-200 transition-colors duration-200">
                    </div>
                    <div class="flex flex-wrap gap-1.5 text-[11px]">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg border {{ !request('interest') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30' : 'border-slate-800 text-slate-400' }}">All</a>
                        @foreach(\App\Models\Interest::take(5)->get() as $tag)
                            <a href="{{ route('dashboard', ['interest' => $tag->id]) }}" class="px-3 py-1.5 rounded-lg border {{ request('interest') == $tag->id ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30' : 'border-slate-800 text-slate-400' }}">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- RECOMMENDED FOR YOU --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Recommended For You</h3>
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-slate-800 to-transparent"></div>
                    <span class="text-[10px] font-semibold text-indigo-400 bg-indigo-500/5 border border-indigo-500/10 px-2.5 py-0.5 rounded-full">Smart Match</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($recommendedLobbies as $lobby)
                    <div class="lobby-card group relative flex flex-col justify-between p-5 rounded-xl border border-slate-800 bg-slate-950/40 hover:border-indigo-500/30 hover:bg-slate-900/80 transition-all active:scale-[0.99] overflow-hidden">
                        <div class="relative">
                            <div class="flex justify-between items-start gap-2 mb-2">
                                <h4 class="text-xs font-bold text-slate-200 truncate group-hover:text-indigo-400 transition-colors">{{ $lobby->name }}</h4>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shrink-0">
                                    {{ $lobby->match_percentage ?? 0 }}% match
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed mb-3">{{ $lobby->description }}</p>

                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($lobby->interests as $interest)
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-500">
                                        #{{ $interest->name }}
                                    </span>
                                @endforeach
                            </div>

                            <p class="text-[10px] text-slate-500">{{ $lobby->members->count() }} members</p>
                        </div>

                        <div class="relative pt-3 mt-2 border-t border-slate-800/60 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-slate-500 truncate">Goal: <span class="text-slate-300">{{ $lobby->project_goal }}</span></span>

                            @if(Auth::id() === $lobby->owner_id)
                                <span class="text-[9px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded shrink-0">Your Lobby</span>
                            @else
                                @php
                                    $userApplication = \App\Models\LobbyMember::where('lobby_id', $lobby->id)->where('user_id', Auth::id())->first();
                                @endphp

                                @if(!$userApplication)
                                    <form method="POST" action="{{ route('lobbies.join', $lobby->id) }}" class="shrink-0">
                                        @csrf
                                        <button class="text-[10px] px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all font-semibold">Join</button>
                                    </form>
                                @elseif($userApplication->status === 'pending')
                                    <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded shrink-0">⏳ Pending</span>
                                @elseif($userApplication->status === 'accepted')
                                    <a href="{{ route('chat.index', $lobby->id) }}" class="text-[10px] px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition-all shrink-0">Enter Chat →</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                        No recommendations available right now.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- BOTTOM ROW --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- ACTIVE CHAT --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 flex flex-col justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Active Chat</h3>
                        <p class="text-[11px] text-slate-500 leading-normal">Instantly reopen rooms from your active team lobbies.</p>
                    </div>

                    @if($joinedLobbies->count())
                        <div class="divide-y divide-slate-800/60 max-h-[130px] overflow-y-auto pr-1">
                            @foreach($joinedLobbies as $lobby)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-200 truncate">{{ $lobby->name }}</p>
                                    <p class="text-[10px] text-slate-500 truncate">Goal: {{ $lobby->project_goal }}</p>
                                </div>
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-[10px] px-3 py-1 rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 hover:bg-indigo-600 hover:text-white transition-all shrink-0 font-medium">
                                    Chat
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-xs text-slate-600 border border-dashed border-slate-800/60 rounded-xl">
                            No active channels found.
                        </div>
                    @endif
                </div>

                {{-- RECENT CONNECTION --}}
                <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-5 flex flex-col justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Recent Connection</h3>
                        <p class="text-[11px] text-slate-500 leading-normal">Overview summary indicators of application metrics and files.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('lobbies.joined') }}" class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80 text-center hover:border-indigo-500/40 hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-slate-500">Joined Lobbies</span>
                            <span class="text-lg font-bold text-indigo-400">{{ $joinedLobbies->count() }}</span>
                        </a>
                        <a href="{{ route('lobbies.owned') }}" class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80 text-center hover:border-indigo-500/40 hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-slate-500">Owned Lobbies</span>
                            <span class="text-lg font-bold text-purple-400">{{ $ownedLobbiesWithRequests->count() ?? 0 }}</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80 text-center">
                            <span class="block text-[10px] text-slate-500">CV Document</span>
                            <span class="text-xs font-bold block mt-1 {{ auth()->user()->profile?->cv_path ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ auth()->user()->profile?->cv_path ? '● Ready' : '○ Missing' }}
                            </span>
                        </div>
                        <button type="button" onclick="document.getElementById('notifBtn').click()" class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80 text-center hover:border-indigo-500/40 hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-slate-500">Pending Requests</span>
                            <span class="text-xs font-bold block mt-1 {{ $pendingCount > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                                {{ $pendingCount }}
                            </span>
                        </button>
                    </div>

                    <div class="flex items-center justify-center gap-4 text-[10px] text-slate-500 border-t border-slate-800/60 pt-2">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Online</span>
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> Offline</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- FLOATING CREATE BUTTON --}}
    <a href="{{ route('lobby.create') }}"
       class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-5 py-3.5 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-2xl shadow-indigo-600/30 hover:scale-105 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Lobby
    </a>

    {{-- NOTIFICATIONS MODAL --}}
    <div id="notifModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div id="notifBackdrop" class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

        <div class="modal-panel relative w-full max-w-lg max-h-[80vh] flex flex-col rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl shadow-black/40 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800/80">
                <h3 class="text-sm font-bold text-slate-100">Incoming Requests</h3>
                <button id="notifClose" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-3 overflow-y-auto">
                @if($pendingCount > 0)
                    @foreach($ownedLobbiesWithRequests as $lobby)
                        @foreach($lobby->members as $applicant)
                            <div class="flex items-center justify-between gap-3 p-4 bg-slate-950/70 border border-slate-800 rounded-xl transition-all duration-200 hover:border-indigo-500/30">
                                <div class="min-w-0">
                                    <p class="text-[11px] text-slate-500 truncate">
                                        Target Lobby: <span class="text-indigo-400 font-medium">{{ $lobby->name }}</span>
                                    </p>
                                    <p class="text-sm font-semibold text-slate-100 mt-0.5">
                                        {{ $applicant->name }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($applicant->profile?->cv_path)
                                        <a href="{{ route('lobby.member.cv', [$lobby->id, $applicant->id]) }}"
                                           target="_blank"
                                           class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:scale-105 transition-all duration-200">
                                            CV
                                        </a>
                                    @endif

                                    <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'accepted']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 hover:scale-105 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'rejected']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-rose-900/40 border border-slate-700 hover:border-rose-900/60 hover:scale-105 transition-all duration-200">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @else
                    <div class="text-center py-10 text-sm text-slate-500">
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
                badge.className = 'absolute -top-1 -right-1 min-w-[18px] h-4 px-1 flex items-center justify-center rounded-full bg-rose-500 text-white text-[9px] font-bold shadow-lg shadow-rose-500/30';
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
});
</script>

</x-app-layout>