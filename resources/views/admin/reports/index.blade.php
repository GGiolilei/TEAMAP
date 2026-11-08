<x-app-layout>
    {{-- Include Chart.js via CDN for the Pie Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="bg-slate-50 min-h-screen text-slate-800 p-6 space-y-6">
        
        {{-- TOP SECTION: SIDE NAV + USER OVERVIEW + REPORT BAY --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            {{-- 1. LEFT SIDE NAV --}}
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3 shrink-0 shadow-sm">
                <a href="{{ route('admin.reports.index') }}" class="w-full py-2.5 px-3 bg-slate-900 border border-slate-900 text-white text-xs font-bold rounded-xl text-center shadow-sm hover:bg-slate-800 transition-all">
                    REPORT OVERVIEW
                </a>
                <button class="w-full py-2.5 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-bold rounded-xl transition-all text-center">
                    MOST CATEGORY
                </button>
            </div>

            {{-- 2. USER OVERVIEW (PIE CHART + STATS) --}}
            <div class="lg:col-span-6 bg-white border border-slate-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-2">USER OVERVIEW</h3>
                
                {{-- Dynamic Chart Container --}}
                <div class="flex-1 flex items-center justify-center min-h-[200px] relative">
                    <canvas id="userCategoryChart" class="max-h-48"></canvas>
                </div>

                {{-- Metrics Counter Footer --}}
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-1.5 text-xs font-semibold">
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-sm"></span>
                        <span><strong>{{ number_format($activeUserCount) }}</strong> ACTIVE USER</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 inline-block shadow-sm"></span>
                        <span><strong>{{ number_format($lobbyCreatedCount) }}</strong> LOBBY CREATED</span>
                    </div>
                </div>
            </div>

            {{-- 3. REPORT BAY (RECENT INCOMING REPORTS) --}}
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-4 flex flex-col shadow-sm">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-3 px-1">REPORT BAY</h3>
                
                <div class="space-y-2 overflow-y-auto max-h-[300px] pr-1">
                    @forelse($reportBay as $report)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100/60 transition-colors">
                            <span class="text-xs font-bold text-slate-800 truncate max-w-[120px]">
                                {{ $report->reportedUser->name ?? 'User' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded font-mono truncate max-w-[120px]" title="{{ $report->reason }}">
                                    {{ Str::limit($report->reason, 16) }}
                                </span>
                                <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 text-xs px-1 transition-colors">✕</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-mono">No active reports.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- BOTTOM SECTION: MOST REPORTED USER TABLE --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            {{-- Header Highlight Box --}}
            <div class="bg-rose-500 border border-rose-600 text-white text-center py-2.5 rounded-xl mb-6 shadow-sm">
                <h2 class="text-lg font-black uppercase tracking-widest">MOST REPORTED USER</h2>
            </div>

            {{-- User Report Ranks --}}
            <div class="space-y-2.5">
                @forelse($mostReportedUsers as $reported)
                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-800 min-w-[120px] truncate">{{ $reported->name }}</span>
                            
                            {{-- Red Delete Action Button --}}
                            <form action="{{ route('admin.users.destroy', $reported->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $reported->name }} permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-[10px] uppercase rounded-lg shadow-sm transition-all active:scale-95">
                                    DELETE USER
                                </button>
                            </form>
                        </div>

                        {{-- Reports Count Summary --}}
                        <div class="text-xs font-mono text-slate-500 tracking-wide">
                            <span class="text-slate-900 font-bold">{{ $reported->reports_against_count }}</span> REPORT FROM DIFFERENT USER
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 font-mono text-xs">
                        No flagged accounts detected.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Chart.js Pie Chart Initialization Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userCategoryChart').getContext('2d');
            
            const categories = @json($categoryStats->pluck('name'));
            const counts = @json($categoryStats->pluck('users_count'));

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categories.length ? categories : ['Data Science', 'Front-End', 'Full-Stack'],
                    datasets: [{
                        data: counts.length ? counts : [30, 45, 25],
                        backgroundColor: [
                            '#6366f1', // Indigo
                            '#0284c7', // Light Blue
                            '#f43f5e', // Rose
                            '#10b981'  // Emerald
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#334155', // slate-700
                                font: { size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>