<x-app-layout>
    <div class="min-h-screen bg-[var(--bg)] py-10 text-[var(--text)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Navigation & Header --}}
            <div>
                <a href="{{ route('dashboard') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--text-2)] transition hover:text-[var(--text)]">
                    ← Back to Dashboard
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-[var(--black)]">
                            Your Command Operations
                        </h2>
                        <p class="mt-0.5 text-sm text-[var(--text-2)]">
                            Management terminal for lobbies initialized and hosted by your operative profile.
                        </p>
                    </div>
                    <span class="self-start rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 font-mono text-xs font-bold text-[var(--black)] sm:self-center">
                        Total Hosted: {{ $hostedLobbies->count() }}
                    </span>
                </div>
            </div>

            {{-- Lobbies List Grid --}}
            <div class="space-y-3">
                @forelse($hostedLobbies as $lobby)
                    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm transition duration-200 hover:border-[var(--muted)] sm:flex-row sm:items-center">
                        
                        {{-- Meta Details --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-[var(--success)]"></span>
                                <h4 class="text-sm font-bold text-[var(--black)]">{{ $lobby->name }}</h4>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-[var(--text-2)]">
                                <span class="flex items-center gap-1 rounded-lg border border-[var(--border)] bg-[var(--bg)] px-2 py-0.5">
                                    👥 {{ $lobby->members_count ?? $lobby->members()->count() }} deployed
                                </span>
                                <span class="text-[var(--muted)]">•</span>
                                <span>
                                    Code: <code class="rounded border border-[var(--border)] bg-[var(--bg)] px-1.5 py-0.5 font-mono font-bold text-[var(--black)] select-all" title="Click to select">{{ $lobby->code }}</code>
                                </span>
                            </div>
                        </div>

                        {{-- Action Trigger --}}
                        <div class="flex shrink-0 items-center justify-end border-t border-[var(--border)] pt-3 sm:border-0 sm:pt-0">
                            <a href="{{ route('lobbies.show', $lobby->id) }}" class="w-full rounded-xl bg-[var(--btn)] px-4 py-2.5 text-center text-xs font-semibold text-white transition hover:bg-[var(--black-2)] sm:w-auto">
                                Manage Terminal
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border-2 border-dashed border-[var(--border)] bg-[var(--surface)] p-12 text-center">
                        <div class="mx-auto mb-2 flex h-8 w-8 items-center justify-center text-[var(--muted)]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                        </div>
                        <div class="text-xs font-medium text-[var(--text-2)]">No managed hubs initialized.</div>
                        <p class="mt-0.5 text-[11px] text-[var(--muted)]">Lobbies you create in the future will aggregate right here.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>