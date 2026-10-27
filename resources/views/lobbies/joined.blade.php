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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

            {{-- GLOBAL NAVIGATION BACK LINK --}}
            <div class="rise" style="animation-delay:.75s">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#747674] hover:text-[#202120] transition inline-flex items-center gap-1.5">
                    ← Back to Dashboard
                </a>
            </div>

            {{-- ========================================================= --}}
            {{-- SECTION 1: JOINED WORKSPACES                              --}}
            {{-- ========================================================= --}}
            <div class="space-y-5">
                <div class="rise flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E1E1DE] pb-4" style="animation-delay:.8s">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-[#202120]">Joined Workspaces</h2>
                        <p class="text-sm text-[#747674] mt-0.5">
                            Active project workspaces where you are on the roster.
                        </p>
                    </div>
                    <span class="self-start sm:self-center text-xs font-semibold bg-white border border-[#E1E1DE] text-[#3A3B3A] px-3 py-1.5 rounded-full">
                        Active: {{ $joinedLobbies->count() }}
                    </span>
                </div>

                {{-- Lobbies List --}}
                <div class="space-y-3">
                    @forelse($joinedLobbies as $lobby)
                        <div class="rise bg-white border border-[#E1E1DE] p-5 rounded-2xl hover:border-[#9A9C9A] hover:-translate-y-0.5 transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                             style="animation-delay: {{ 0.85 + min($loop->index, 8) * 0.06 }}s">

                            {{-- Meta Details --}}
                            <div class="space-y-2 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <h4 class="text-sm font-bold text-[#202120] truncate">{{ $lobby->name }}</h4>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-[#747674]">
                                    <span class="inline-flex items-center gap-1.5 bg-[#F4F4F2] px-2 py-0.5 border border-[#E1E1DE] rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-[#9A9C9A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                        {{ $lobby->members_count ?? $lobby->members()->where('status', 'accepted')->count() }} active
                                    </span>
                                    <span class="text-[#E1E1DE]">|</span>
                                    <span class="text-[11px]">
                                        Host: <span class="text-[#202120] font-medium">{{ $lobby->owner->name ?? 'Command' }}</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Action Triggers --}}
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[#E1E1DE] pt-3 sm:pt-0 sm:border-0 shrink-0 w-full sm:w-auto">
                                {{-- Leave Lobby Form --}}
                                <form action="{{ route('lobbies.leave', $lobby->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this workspace?');" class="w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-[#747674] hover:text-rose-600 bg-white hover:bg-rose-50 border border-[#E1E1DE] hover:border-rose-200 px-4 py-2.5 rounded-xl transition w-full sm:w-auto text-center">
                                        Leave
                                    </button>
                                </form>

                                {{-- Chat Action Button --}}
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-xs font-semibold text-white bg-[#252625] hover:bg-[#3A3B3A] px-4 py-2.5 rounded-xl transition active:scale-95 w-full sm:w-auto text-center">
                                    Open Chat Workspace ↗
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rise border-2 border-dashed border-[#E1E1DE] bg-white/60 rounded-2xl p-12 text-center" style="animation-delay:.85s">
                            <div class="mx-auto w-8 h-8 text-[#9A9C9A] mb-2 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                            </div>
                            <div class="text-xs font-semibold text-[#3A3B3A]">No joined workspaces yet.</div>
                            <p class="text-[11px] text-[#9A9C9A] mt-0.5">Lobbies you find and join will show up here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- SECTION 2: COMMAND OPERATIONS (OWNED)                      --}}
            {{-- ========================================================= --}}
            <div class="space-y-5">
                <div class="rise flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E1E1DE] pb-4" style="animation-delay:.95s">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-[#202120]">Your Lobbies</h2>
                        <p class="text-sm text-[#747674] mt-0.5">
                            Lobbies you created and host.
                        </p>
                    </div>
                    <span class="self-start sm:self-center text-xs font-semibold bg-white border border-[#E1E1DE] text-[#3A3B3A] px-3 py-1.5 rounded-full">
                        Hosted: {{ $hostedLobbies->count() }}
                    </span>
                </div>

                {{-- Lobbies List --}}
                <div class="space-y-3">
                    @forelse($hostedLobbies as $lobby)
                        <div class="rise bg-white border border-[#E1E1DE] p-5 rounded-2xl hover:border-[#9A9C9A] hover:-translate-y-0.5 transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                             style="animation-delay: {{ 1.0 + min($loop->index, 8) * 0.06 }}s">

                            {{-- Meta Details --}}
                            <div class="space-y-2 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#252625] shrink-0"></span>
                                    <h4 class="text-sm font-bold text-[#202120] truncate">{{ $lobby->name }}</h4>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-[#747674]">
                                    <span class="inline-flex items-center gap-1.5 bg-[#F4F4F2] px-2 py-0.5 border border-[#E1E1DE] rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-[#9A9C9A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                        {{ $lobby->members_count ?? ($lobby->members() ? $lobby->members()->count() : 0) }} members
                                    </span>
                                    <span class="text-[#E1E1DE]">•</span>
                                    <span>
                                        Code: <code class="bg-[#F7F7F5] px-1.5 py-0.5 rounded text-[#202120] border border-[#E1E1DE] font-mono font-bold select-all" title="Click to select">{{ $lobby->code }}</code>
                                    </span>
                                </div>
                            </div>

                            {{-- Action Triggers --}}
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[#E1E1DE] pt-3 sm:pt-0 sm:border-0 shrink-0 w-full sm:w-auto">
                                {{-- Delete Lobby Form --}}
                                <form action="{{ route('lobbies.destroy', $lobby->id) }}" method="POST" onsubmit="return confirm('CRITICAL ACTION: Are you sure you want to permanently delete this lobby and its entire history?');" class="w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 bg-white border border-rose-200 hover:border-rose-600 px-4 py-2.5 rounded-xl transition w-full sm:w-auto text-center">
                                        Delete
                                    </button>
                                </form>

                                {{-- Manage Action Button --}}
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-xs font-semibold text-white bg-[#252625] hover:bg-[#3A3B3A] px-4 py-2.5 rounded-xl transition active:scale-95 w-full sm:w-auto text-center">
                                    Open Chat Workspace
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rise border-2 border-dashed border-[#E1E1DE] bg-white/60 rounded-2xl p-12 text-center" style="animation-delay:1s">
                            <div class="mx-auto w-8 h-8 text-[#9A9C9A] mb-2 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            </div>
                            <div class="text-xs font-semibold text-[#3A3B3A]">No lobbies created yet.</div>
                            <p class="text-[11px] text-[#9A9C9A] mt-0.5">Lobbies you create will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>