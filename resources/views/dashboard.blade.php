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

<div class="relative min-h-screen bg-[#F7F7F5] text-[#242524] overflow-x-hidden flex items-center justify-center p-4 md:p-8">

    @php
        $pendingCount = isset($ownedLobbiesWithRequests) ? $ownedLobbiesWithRequests->sum(fn($lobby) => $lobby->members->count()) : 0;
    @endphp

    {{-- REAL-TIME TOAST COMPONENT --}}
    <x-notification-toast />

    {{-- MAIN DASHBOARD CARD --}}
    <div class="relative w-full max-w-7xl bg-white border border-[#E1E1DE] rounded-[2.5rem] p-6 grid grid-cols-1 lg:grid-cols-[80px_1fr] gap-6 auto-rows-max min-h-[85vh]">
        
        {{-- ===================== LEFT SIDEBAR NAV ===================== --}}
        <div class="flex lg:flex-col items-center justify-between lg:justify-start gap-6 p-4 rounded-3xl bg-[#F7F7F5] border border-[#E1E1DE] lg:py-8 lg:h-full">
            <div class="flex lg:flex-col items-center gap-5 w-full justify-center">
                {{-- Explore --}}
                <a href="{{ route('lobbies.index') }}" title="Explore Lobbies"
                   class="p-3 rounded-2xl bg-white border border-[#E1E1DE] text-[#747674] hover:bg-[#EEEEEC] hover:text-[#202120] transition-all duration-200 hover:scale-105 flex items-center justify-center w-12 h-12">
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

                {{-- NOTIFICATION BUTTON --}}
                <button id="notifBtn" type="button" title="Notifications"
                        class="relative p-3 rounded-2xl bg-white border border-[#E1E1DE] text-[#747674] hover:bg-[#EEEEEC] hover:text-[#202120] hover:scale-105 transition-all duration-200 flex items-center justify-center w-12 h-12 {{ $pendingCount > 0 ? 'bell-wiggle' : '' }}">
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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-3 rounded-2xl bg-white border border-[#E1E1DE] text-[#9A9C9A] hover:text-rose-500 hover:border-rose-200 transition-all duration-200 flex items-center justify-center w-12 h-12">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- ===================== RIGHT MAIN PANEL ===================== --}}
        <div class="space-y-6 flex flex-col justify-between">

            {{-- SESSION TOASTS --}}
            @if(session('success') || session('error'))
                <div class="space-y-2">
                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- HEADER --}}
            <div class="relative rounded-3xl border border-[#E1E1DE] bg-[#F7F7F5] p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 overflow-hidden">
                <div class="space-y-4 flex-1 min-w-0">
                    <div class="space-y-1">
                        <p class="text-xs text-[#747674] font-medium">Welcome back</p>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#202120]">
                            {{ auth()->user()->name ?? 'Jane Doe' }}
                        </h1>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 p-3 rounded-2xl bg-white border border-[#E1E1DE]">
                        <span class="text-[10px] font-semibold text-[#9A9C9A] uppercase tracking-wider mr-1">Your Focus</span>
                        @forelse(auth()->user()->interests as $interest)
                            <span class="px-2.5 py-1 text-xs rounded-full bg-[#F4F4F2] border border-[#E1E1DE] text-[#3A3B3A]">
                                {{ $interest->name }}
                            </span>
                        @empty
                            <span class="text-xs text-[#9A9C9A]">No interests selected yet</span>
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
                        <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-[#E9E9E7] border-2 border-[#E1E1DE] group-hover:border-[#202120] transition-all duration-300 flex items-center justify-center font-bold text-lg text-[#3A3B3A] uppercase tracking-wider">
                            @if(auth()->user()->profile?->avatar_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover" />
                            @else
                                {{ substr(auth()->user()->name ?? 'JD', 0, 2) }}
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-[#747674] group-hover:text-[#202120] transition-colors">
                            Edit Profile
                        </span>
                    </a>
                </div>
            </div>
            

            {{-- SEARCH --}}
            <div class="rounded-2xl border border-[#E1E1DE] bg-white p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#9A9C9A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search lobbies..."
                               class="w-full bg-[#F7F7F5] border border-[#E1E1DE] rounded-xl pl-11 pr-4 py-2.5 text-sm placeholder-[#9A9C9A] focus:border-[#202120] focus:ring-0 text-[#242524] transition-colors duration-200">
                    </div>
                    <div class="flex flex-wrap gap-1.5 text-[11px]">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg border transition {{ !request('interest') ? 'bg-[#E5E5E2] text-[#202120] border-[#E1E1DE]' : 'border-[#E1E1DE] text-[#747674] hover:bg-[#EEEEEC]' }}">All</a>
                        @foreach(\App\Models\Interest::take(5)->get() as $tag)
                            <a href="{{ route('dashboard', ['interest' => $tag->id]) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('interest') == $tag->id ? 'bg-[#E5E5E2] text-[#202120] border-[#E1E1DE]' : 'border-[#E1E1DE] text-[#747674] hover:bg-[#EEEEEC]' }}">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- RECOMMENDED FOR YOU --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <h3 class="text-xs font-bold text-[#747674] uppercase tracking-wider">Recommended For You</h3>
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-[#E1E1DE] to-transparent"></div>
                    <span class="text-[10px] font-semibold text-[#747674] bg-[#F4F4F2] border border-[#E1E1DE] px-2.5 py-0.5 rounded-full">Smart Match</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($recommendedLobbies as $lobby)
                    <div class="lobby-card group relative flex flex-col justify-between p-5 rounded-xl border border-[#E1E1DE] bg-white hover:bg-[#F7F7F5] hover:border-[#9A9C9A] transition-all active:scale-[0.99] overflow-hidden">
                        <div class="relative">
                            <div class="flex justify-between items-start gap-2 mb-2">
                                <h4 class="text-xs font-bold text-[#202120] truncate">{{ $lobby->name }}</h4>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#E9E9E7] text-[#3A3B3A] border border-[#E1E1DE] shrink-0">
                                    {{ $lobby->match_percentage ?? 0 }}% match
                                </span>
                            </div>
                            <p class="text-[11px] text-[#747674] line-clamp-2 leading-relaxed mb-3">{{ $lobby->description }}</p>

                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($lobby->interests as $interest)
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-[#F4F4F2] border border-[#E1E1DE] text-[#747674]">
                                        #{{ $interest->name }}
                                    </span>
                                @endforeach
                            </div>

                            <p class="text-[10px] text-[#9A9C9A]">{{ $lobby->members->count() }} members</p>
                        </div>

                        <div class="relative pt-3 mt-2 border-t border-[#E1E1DE] flex items-center justify-between gap-2">
                            <span class="text-[10px] text-[#9A9C9A] truncate">Goal: <span class="text-[#3A3B3A]">{{ $lobby->project_goal }}</span></span>

                            @if(Auth::id() === $lobby->owner_id)
                                <span class="text-[9px] font-bold text-[#3A3B3A] bg-[#E9E9E7] border border-[#E1E1DE] px-2 py-0.5 rounded shrink-0">Your Lobby</span>
                            @else
                                @php
                                    $userApplication = \App\Models\LobbyMember::where('lobby_id', $lobby->id)->where('user_id', Auth::id())->first();
                                @endphp

                                @if(!$userApplication)
                                    <form method="POST" action="{{ route('lobbies.join', $lobby->id) }}" class="shrink-0">
                                        @csrf
                                        <button class="text-[10px] px-2.5 py-1 rounded bg-white border border-[#E1E1DE] text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all font-semibold">Join</button>
                                    </form>
                                @elseif($userApplication->status === 'pending')
                                    <span class="text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded shrink-0">⏳ Pending</span>
                                @elseif($userApplication->status === 'accepted')
                                    <a href="{{ route('chat.index', $lobby->id) }}" class="text-[10px] px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition-all shrink-0">Enter Chat →</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-xs text-[#9A9C9A] border border-dashed border-[#E1E1DE] rounded-xl">
                        No recommendations available right now.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- BOTTOM ROW --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- ACTIVE CHAT --}}
                <div class="rounded-2xl border border-[#E1E1DE] bg-white p-5 flex flex-col justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-[#747674] uppercase tracking-wider mb-2">Active Chat</h3>
                        <p class="text-[11px] text-[#9A9C9A] leading-normal">Instantly reopen rooms from your active team lobbies.</p>
                    </div>

                    @if($joinedLobbies->count())
                        <div class="divide-y divide-[#E1E1DE] max-h-[130px] overflow-y-auto pr-1">
                            @foreach($joinedLobbies as $lobby)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#202120] truncate">{{ $lobby->name }}</p>
                                    <p class="text-[10px] text-[#9A9C9A] truncate">Goal: {{ $lobby->project_goal }}</p>
                                </div>
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-[10px] px-3 py-1 rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 hover:bg-indigo-600 hover:text-white transition-all shrink-0 font-medium">
                                    Chat
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-xs text-[#9A9C9A] border border-dashed border-[#E1E1DE] rounded-xl">
                            No active channels found.
                        </div>
                    @endif
                </div>

                {{-- RECENT CONNECTION --}}
                <div class="rounded-2xl border border-[#E1E1DE] bg-white p-5 flex flex-col justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-[#747674] uppercase tracking-wider mb-2">Recent Connection</h3>
                        <p class="text-[11px] text-[#9A9C9A] leading-normal">Overview summary indicators of application metrics and files.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('lobbies.joined') }}" class="p-3 rounded-xl bg-[#F7F7F5] border border-[#E1E1DE] text-center hover:bg-[#EEEEEC] hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-[#9A9C9A]">Joined Lobbies</span>
                            <span class="text-lg font-bold text-[#202120]">{{ $joinedLobbies->count() }}</span>
                        </a>
                        <a href="{{ route('lobbies.owned') }}" class="p-3 rounded-xl bg-[#F7F7F5] border border-[#E1E1DE] text-center hover:bg-[#EEEEEC] hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-[#9A9C9A]">Owned Lobbies</span>
                            <span class="text-lg font-bold text-[#202120]">{{ $ownedLobbiesWithRequests->count() ?? 0 }}</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-[#F7F7F5] border border-[#E1E1DE] text-center">
                            <span class="block text-[10px] text-[#9A9C9A]">CV Document</span>
                            <span class="text-xs font-bold block mt-1 {{ auth()->user()->profile?->cv_path ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ auth()->user()->profile?->cv_path ? '● Ready' : '○ Missing' }}
                            </span>
                        </div>
                        <button type="button" onclick="document.getElementById('notifBtn').click()" class="p-3 rounded-xl bg-[#F7F7F5] border border-[#E1E1DE] text-center hover:bg-[#EEEEEC] hover:scale-105 transition-all duration-200">
                            <span class="block text-[10px] text-[#9A9C9A]">Pending Requests</span>
                            <span id="pendingRequestsCount" class="text-xs font-bold block mt-1 {{ $pendingCount > 0 ? 'text-amber-600' : 'text-[#9A9C9A]' }}">
                                {{ $pendingCount }}
                            </span>
                        </button>
                    </div>

                    <div class="flex items-center justify-center gap-4 text-[10px] text-[#9A9C9A] border-t border-[#E1E1DE] pt-2">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Online</span>
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> Offline</span>
                    </div>
                </div>

            </div>

        </div>

    </div> {{-- /MAIN DASHBOARD CARD --}}

</div> {{-- /PAGE WRAPPER --}}

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
    <div id="notifBackdrop" class="absolute inset-0 bg-[#202120]/40 backdrop-blur-sm"></div>

    <div class="modal-panel relative w-full max-w-lg max-h-[80vh] flex flex-col rounded-2xl border border-[#E1E1DE] bg-white shadow-2xl shadow-black/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E1E1DE]">
            <h3 class="text-sm font-bold text-[#202120]">Incoming Requests</h3>
            <button id="notifClose" type="button" class="p-1.5 rounded-lg text-[#747674] hover:text-[#202120] hover:bg-[#EEEEEC] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-5 space-y-3 overflow-y-auto">
            @if($pendingCount > 0)
                @foreach($ownedLobbiesWithRequests as $lobby)
                    @foreach($lobby->members as $applicant)
                        <div class="flex items-center justify-between gap-3 p-4 bg-[#F7F7F5] border border-[#E1E1DE] rounded-xl transition-all duration-200 hover:border-[#9A9C9A]">
                            <div class="min-w-0">
                                <p class="text-[11px] text-[#9A9C9A] truncate">
                                    Target Lobby: <span class="text-[#3A3B3A] font-medium">{{ $lobby->name }}</span>
                                </p>
                                <p class="text-sm font-semibold text-[#202120] mt-0.5">
                                    {{ $applicant->name }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($applicant->profile?->cv_path)
                                    <a href="{{ route('lobby.member.cv', [$lobby->id, $applicant->id]) }}"
                                       target="_blank"
                                       class="text-xs px-3 py-1.5 rounded-lg bg-white hover:bg-[#EEEEEC] text-[#3A3B3A] border border-[#E1E1DE] hover:scale-105 transition-all duration-200">
                                        CV
                                    </a>
                                @endif

                                <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'accepted']) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 hover:scale-105 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-all duration-200">
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('membership.update', ['member' => $applicant->pivot->id, 'status' => 'rejected']) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-white text-[#3A3B3A] hover:bg-rose-50 hover:text-rose-600 border border-[#E1E1DE] hover:border-rose-200 hover:scale-105 transition-all duration-200">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div class="text-center py-10 text-sm text-[#9A9C9A]">
                    No pending requests right now.
                </div>
            @endif
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

    // Desktop push permission
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

    // Live badge & stats update
    function renderBadge(count) {
        let badge = document.getElementById('notifBadge');
        const pendingRequestsText = document.getElementById('pendingRequestsCount');

        if (pendingRequestsText) {
            pendingRequestsText.textContent = count;
            pendingRequestsText.className = `text-xs font-bold block mt-1 ${count > 0 ? 'text-amber-600' : 'text-[#9A9C9A]'}`;
        }

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

    // Solution A: Polling data every 0.5s (500ms) seamlessly without refreshing the browser tab
    async function fetchNotificationUpdates() {
        try {
            const response = await fetch('{{ route("notifications.stream") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (response.ok) {
                const data = await response.json();
                const total = (data.pending || 0) + (data.unread || 0);
                renderBadge(total);

                if (total > lastTotal) {
                    playNotificationSound();
                    spawnPushNotification(
                        'New activity',
                        data.unread > 0 ? 'You have new chat messages.' : 'You have a new lobby request.'
                    );
                }
                lastTotal = total;
            }
        } catch (err) {
            // Quietly catch background network blips
        }
    }

    // Polling timer set to every 0.5s (500 milliseconds)
    setInterval(fetchNotificationUpdates, 500);

    // Initial check
    fetchNotificationUpdates();

    // Lobby card 3D tilt effect
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