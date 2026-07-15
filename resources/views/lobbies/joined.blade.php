<x-app-layout>
    <div class="py-10 bg-slate-950 min-h-screen text-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            {{-- GLOBAL NAVIGATION BACK LINK --}}
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-indigo-400 transition inline-flex items-center gap-1.5">
                    ← Back to Dashboard
                </a>
            </div>

            {{-- ========================================================= --}}
            {{-- SECTION 1: JOINED WORKSPACES                              --}}
            {{-- ========================================================= --}}
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-900 pb-4">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
                            Joined Workspaces
                        </h2>
                        <p class="text-sm text-slate-400 mt-0.5">
                            Active project terminals where your profile has been added to the operative roster.
                        </p>
                    </div>
                    <span class="self-start sm:self-center text-xs font-mono bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-3 py-1.5 rounded-xl font-bold">
                        Active Deployments: {{ $joinedLobbies->count() }}
                    </span>
                </div>

                {{-- Lobbies List Grid --}}
                <div class="space-y-3">
                    @forelse($joinedLobbies as $lobby)
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl hover:border-emerald-500/30 transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            {{-- Meta Details --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></span>
                                    <h4 class="text-sm font-bold text-slate-200">{{ $lobby->name }}</h4>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    <span class="flex items-center gap-1 bg-slate-950/60 px-2 py-0.5 border border-slate-800/80 rounded-lg">
                                        👥 {{ $lobby->members_count ?? $lobby->members()->where('status', 'accepted')->count() }} active
                                    </span>
                                    <span class="text-slate-700">|</span>
                                    <span class="text-[11px] text-slate-400">
                                        Host: <span class="text-slate-300 font-medium">{{ $lobby->owner->name ?? 'Command' }}</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Action Triggers --}}
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-800/60 pt-3 sm:pt-0 sm:border-0 shrink-0 w-full sm:w-auto">
                                {{-- Leave Lobby Form --}}
                                <form action="{{ route('lobbies.leave', $lobby->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this workspace?');" class="w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-400 hover:text-rose-300 bg-slate-950/40 hover:bg-rose-950/20 border border-slate-800/80 hover:border-rose-500/30 px-4 py-2.5 rounded-xl transition w-full sm:w-auto text-center">
                                        Leave
                                    </button>
                                </form>

                                {{-- Chat Action Button --}}
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 bg-slate-950 border border-slate-800 px-4 py-2.5 rounded-xl transition shadow-inner w-full sm:w-auto text-center">
                                    Open Chat Workspace ↗
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="border-2 border-dashed border-slate-900 bg-slate-900/10 rounded-2xl p-12 text-center">
                            <div class="mx-auto w-8 h-8 text-slate-600 mb-2 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                            </div>
                            <div class="text-xs font-medium text-slate-400">No joined operations found.</div>
                            <p class="text-[11px] text-slate-600 mt-0.5">Lobbies you find and join from the hub roster will assemble here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- SECTION 2: COMMAND OPERATIONS (OWNED)                      --}}
            {{-- ========================================================= --}}
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-900 pb-4">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">
                            Your Command Operations
                        </h2>
                        <p class="text-sm text-slate-400 mt-0.5">
                            Management terminal for lobbies initialized and hosted by your operative profile.
                        </p>
                    </div>
                    <span class="self-start sm:self-center text-xs font-mono bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 px-3 py-1.5 rounded-xl font-bold">
                        Total Hosted: {{ $hostedLobbies->count() }}
                    </span>
                </div>

                {{-- Lobbies List Grid --}}
                <div class="space-y-3">
                    @forelse($hostedLobbies as $lobby)
                        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl hover:border-indigo-500/30 transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            {{-- Meta Details --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 shadow-sm shadow-indigo-400"></span>
                                    <h4 class="text-sm font-bold text-slate-200">{{ $lobby->name }}</h4>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    <span class="flex items-center gap-1 bg-slate-950/60 px-2 py-0.5 border border-slate-800/80 rounded-lg">
                                        👥 👥 {{ $lobby->members_count ?? ($lobby->members() ? $lobby->members()->count() : 0) }} deployed
                                    </span>
                                    <span class="text-slate-700">•</span>
                                    <span>
                                        Code: <code class="bg-slate-950 px-1.5 py-0.5 rounded text-indigo-400 border border-slate-800 font-mono font-bold select-all" title="Click to select">{{ $lobby->code }}</code>
                                    </span>
                                </div>
                            </div>

                            {{-- Action Triggers --}}
                            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-800/60 pt-3 sm:pt-0 sm:border-0 shrink-0 w-full sm:w-auto">
                                {{-- Delete Lobby Form --}}
                                <form action="{{ route('lobbies.destroy', $lobby->id) }}" method="POST" onsubmit="return confirm('CRITICAL ACTION: Are you sure you want to permanently delete this lobby and its entire history?');" class="w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-white hover:bg-rose-600 border border-transparent px-4 py-2.5 rounded-xl transition w-full sm:w-auto text-center">
                                        Delete
                                    </button>
                                </form>

                                {{-- Manage Action Button --}}
                                <a href="{{ route('chat.index', $lobby->id) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 bg-slate-950 border border-slate-800 px-4 py-2.5 rounded-xl transition shadow-inner w-full sm:w-auto text-center">
                                    Open Chat Workspace
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="border-2 border-dashed border-slate-900 bg-slate-900/10 rounded-2xl p-12 text-center">
                            <div class="mx-auto w-8 h-8 text-slate-600 mb-2 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            </div>
                            <div class="text-xs font-medium text-slate-400">No managed hubs initialized.</div>
                            <p class="text-[11px] text-slate-600 mt-0.5">Lobbies you create in the future will aggregate right here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>