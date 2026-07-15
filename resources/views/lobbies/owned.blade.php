<x-app-layout>
    <div class="py-10 bg-slate-950 min-h-screen text-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Navigation & Header --}}
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-indigo-400 transition inline-flex items-center gap-1.5 mb-2">
                    ← Back to Dashboard
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
                                    👥 {{ $lobby->users_count ?? $lobby->users()->count() }} deployed
                                </span>
                                <span class="text-slate-700">•</span>
                                <span>
                                    Code: <code class="bg-slate-950 px-1.5 py-0.5 rounded text-indigo-400 border border-slate-800 font-mono font-bold select-all" title="Click to select">{{ $lobby->code }}</code>
                                </span>
                            </div>
                        </div>

                        {{-- Action Trigger --}}
                        <div class="flex items-center justify-end border-t border-slate-800/60 pt-3 sm:pt-0 sm:border-0 shrink-0">
                            <a href="{{ route('lobbies.show', $lobby->id) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 bg-slate-950 border border-slate-800 px-4 py-2.5 rounded-xl transition shadow-inner w-full sm:w-auto text-center">
                                Manage Terminal ↗
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
</x-app-layout>