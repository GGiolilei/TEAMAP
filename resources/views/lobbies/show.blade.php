<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl max-w-md">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
        <h3 class="text-sm font-bold tracking-wider text-indigo-400 uppercase">Team Candidates & Members</h3>
        <span class="text-xs text-slate-500 font-medium font-mono">{{ $lobby->members->count() }} / {{ $lobby->max_members }} Slots</span>
    </div>

    <div class="space-y-3.5">
        @foreach($lobby->members as $member)
            <div class="flex items-center justify-between p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl hover:border-slate-700 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs uppercase tracking-wider">
                        {{ substr($member->name, 0, 2) }}
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-200">{{ $member->name }}</h5>
                        <p class="text-[11px] text-slate-500 capitalize">{{ $member->pivot->role ?? 'Applicant' }}</p>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>