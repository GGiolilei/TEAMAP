<x-app-layout>
    {{-- SPLASH (0.7s hold, then fades out; pure CSS so it can never get stuck) --}}
    <div id="splash" aria-hidden="true">
        <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'TeaMap') }}" class="splash-logo w-20 h-20 object-contain">
    </div>

    <style>
        #splash {
            position: fixed; inset: 0; z-index: 100;
            background: #F7F7F5;
            display: flex; align-items: center; justify-content: center;
            animation: splashOut .35s ease .7s forwards;
        }
        .splash-logo { animation: logoIn .45s cubic-bezier(.16,1,.3,1) both; }
        @keyframes logoIn  { from { opacity: 0; transform: scale(.85); } to { opacity: 1; transform: scale(1); } }
        @keyframes splashOut { to { opacity: 0; visibility: hidden; } }

        .rise { opacity: 0; animation: rise .5s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="py-10 bg-[#F7F7F5] min-h-screen text-[#242524]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- HEADER --}}
            <div class="rise flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#E1E1DE] pb-6" style="animation-delay:.75s">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#747674] hover:text-[#202120] transition inline-flex items-center gap-1.5 mb-3">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-2xl font-bold tracking-tight text-[#202120] sm:text-3xl">Project Lobbies</h1>
                    <p class="text-sm text-[#747674] mt-1">Discover, join, or manage collaboration workspaces.</p>
                </div>
                <a href="{{ route('lobbies.create') }}" class="inline-flex items-center justify-center bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition active:scale-95 shrink-0 gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Create New Lobby
                </a>
            </div>

            {{-- SEARCH --}}
            <div class="rise bg-white border border-[#E1E1DE] p-4 rounded-2xl" style="animation-delay:.8s">
                <form method="GET" action="{{ route('lobby.index') }}">
                    <div class="relative flex items-center">
                        <div class="absolute left-4 pointer-events-none text-[#9A9C9A]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input id="lobby-search" type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search lobbies by name, goal, or description..."
                               class="w-full bg-[#F7F7F5] border border-[#E1E1DE] focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none text-[#242524] placeholder-[#9A9C9A] text-sm pl-12 pr-36 py-3 rounded-xl transition" />

                        <div class="absolute right-2.5 flex items-center gap-2">
                            @if(request('search'))
                                <a href="{{ route('lobby.index') }}" class="text-xs font-semibold text-[#747674] hover:text-[#202120] transition px-2 py-2">Clear</a>
                            @else
                                <kbd class="hidden sm:inline text-[10px] font-semibold text-[#9A9C9A] bg-white border border-[#E1E1DE] rounded px-1.5 py-0.5">/</kbd>
                            @endif
                            <button type="submit" class="bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold text-xs px-4 py-2 rounded-lg transition active:scale-95">
                                Search
                            </button>
                        </div>
                    </div>

                    @if(request('search'))
                        <p class="mt-3 text-xs text-[#747674]">
                            Results for <span class="font-semibold text-[#202120]">"{{ request('search') }}"</span>
                        </p>
                    @endif
                </form>
            </div>

            {{-- LIST --}}
            <div>
                <div class="rise flex items-center gap-3 mb-5" style="animation-delay:.85s">
                    <h3 class="text-sm font-bold text-[#202120] tracking-tight">Available Openings</h3>
                    <div class="h-px flex-1 bg-[#E1E1DE]"></div>
                    <span class="text-xs font-semibold text-[#3A3B3A] bg-white border border-[#E1E1DE] px-3 py-1 rounded-full">
                        {{ $lobbies->count() }} {{ \Illuminate\Support\Str::plural('lobby', $lobbies->count()) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($lobbies as $lobby)
                        <div class="rise bg-white border border-[#E1E1DE] rounded-2xl p-6 flex flex-col justify-between hover:border-[#9A9C9A] hover:-translate-y-1 hover:shadow-md hover:shadow-black/5 transition duration-200 group"
                             style="animation-delay: {{ 0.9 + min($loop->index, 8) * 0.06 }}s">

                            <div>
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 shrink-0 rounded-xl bg-[#E9E9E7] text-[#3A3B3A] text-xs font-bold uppercase flex items-center justify-center group-hover:bg-[#252625] group-hover:text-white transition duration-200">
                                            {{ substr($lobby->name, 0, 2) }}
                                        </div>
                                        <h4 class="text-base font-bold text-[#202120] truncate" title="{{ $lobby->name }}">
                                            {{ $lobby->name }}
                                        </h4>
                                    </div>

                                    @if($lobby->owner_id === auth()->id())
                                        <span class="shrink-0 bg-[#252625] text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">
                                            Owner
                                        </span>
                                    @elseif($lobby->members->contains(auth()->id()))
                                        <span class="shrink-0 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">
                                            Joined
                                        </span>
                                    @endif
                                </div>

                                <p class="text-[#747674] text-sm line-clamp-3 leading-relaxed mb-4">
                                    {{ $lobby->description ?? 'No description provided for this lobby.' }}
                                </p>

                                <div class="mb-4 bg-[#F7F7F5] p-3 rounded-xl border border-[#E1E1DE] text-xs">
                                    <span class="text-[#9A9C9A] block text-[10px] uppercase font-bold tracking-wider mb-0.5">Project Goal</span>
                                    <p class="text-[#202120] font-medium line-clamp-1">{{ $lobby->project_goal }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-[#E1E1DE] flex items-center justify-between text-xs gap-4">
                                <div class="flex items-center gap-1.5 text-[#747674]">
                                    <svg class="w-4 h-4 text-[#9A9C9A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    <span class="font-semibold text-[#202120]">{{ $lobby->members->count() }}</span> members
                                </div>

                                @if($lobby->owner_id === auth()->id() || $lobby->members->contains(auth()->id()))
                                    <a href="{{ route('chat.index', $lobby->id) }}" class="inline-flex items-center gap-1 bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold px-3.5 py-2 rounded-lg transition active:scale-95">
                                        Open Chat <span class="transition-transform group-hover:translate-x-0.5">→</span>
                                    </a>
                                @else
                                    <form action="{{ route('lobby.join', $lobby->id) }}" method="POST" class="m-0 join-form">
                                        @csrf
                                        <button type="submit" class="join-btn bg-white hover:bg-[#252625] border border-[#E1E1DE] hover:border-[#252625] text-[#3A3B3A] hover:text-white font-semibold px-3.5 py-2 rounded-lg transition active:scale-95">
                                            Request to Join
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="rise col-span-full p-12 bg-white/60 border-2 border-dashed border-[#E1E1DE] rounded-2xl text-center" style="animation-delay:.9s">
                            <div class="mx-auto w-9 h-9 text-[#9A9C9A] mb-3">
                                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-[#3A3B3A]">No lobbies found</p>
                            <p class="text-xs text-[#9A9C9A] mt-1">Try a different keyword or clear your search.</p>
                            @if(request('search'))
                                <a href="{{ route('lobby.index') }}" class="inline-block mt-4 text-xs font-semibold text-[#202120] bg-white border border-[#E1E1DE] hover:bg-[#EEEEEC] px-4 py-2 rounded-xl transition">
                                    Clear search
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        (function () {
            // Press "/" anywhere to jump to the search box
            const search = document.getElementById('lobby-search');
            document.addEventListener('keydown', (e) => {
                const tag = (document.activeElement?.tagName || '').toLowerCase();
                if (e.key === '/' && tag !== 'input' && tag !== 'textarea') {
                    e.preventDefault();
                    search?.focus();
                }
            });

            // Loading state on "Request to Join" (prevents double submits)
            document.querySelectorAll('.join-form').forEach(form => {
                form.addEventListener('submit', () => {
                    const btn = form.querySelector('.join-btn');
                    btn.disabled = true;
                    btn.classList.add('opacity-60', 'cursor-not-allowed');
                    btn.textContent = 'Sending...';
                });
            });
        })();
    </script>
</x-app-layout>