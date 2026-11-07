<x-app-layout>
    {{-- Include Chart.js via CDN for the Pie Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .r-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; }
        .r-label { font-size: .75rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-2); }
        .r-scroll { scrollbar-width: thin; scrollbar-color: var(--selected) transparent; }
        .r-scroll::-webkit-scrollbar { width: 6px; }
        .r-scroll::-webkit-scrollbar-thumb { background: var(--selected); border-radius: 999px; }

        @keyframes rRise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
        .r-rise { opacity: 0; animation: rRise .5s ease forwards; }
        .r-d1 { animation-delay: .04s } .r-d2 { animation-delay: .1s } .r-d3 { animation-delay: .16s } .r-d4 { animation-delay: .22s }

        @keyframes rGrow { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        .r-bar { transform-origin: left; animation: rGrow .8s .3s cubic-bezier(.2,.8,.2,1) both; }

        @media (prefers-reduced-motion: reduce) { .r-rise, .r-bar { animation: none; opacity: 1; } }
    </style>

    @php
        $maxReports = max(1, (int) $mostReportedUsers->max('reports_against_count'));
    @endphp

    {{-- One-page report: fills the screen on desktop, stacks and scrolls on phones --}}
    <div class="mx-auto flex w-full max-w-[1600px] flex-col gap-4 px-4 py-5 sm:px-6 lg:h-[calc(100dvh-4rem)] lg:overflow-hidden">

        {{-- ===================== HEADER + NAV ===================== --}}
        <header class="r-rise r-d1 flex shrink-0 flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="r-label">Admin · {{ now()->format('d M Y') }}</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-(--black) sm:text-3xl">Reports Overview</h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex min-h-11 items-center rounded-xl bg-(--btn) px-4 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-(--black-2) active:scale-[.97]">
                    Report Overview
                </a>
                <button type="button"
                        class="inline-flex min-h-11 items-center rounded-xl border border-(--border) bg-(--surface) px-4 text-xs font-bold uppercase tracking-wide text-(--text-2) transition hover:bg-(--hover) hover:text-(--text) active:scale-[.97]">
                    Most Category
                </button>
            </div>
        </header>

        {{-- ===================== KPI STRIP ===================== --}}
        <section class="r-rise r-d2 grid shrink-0 grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="r-card flex items-center gap-3.5 p-4">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-(--success-bg) text-(--success)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold tabular-nums leading-none text-(--black)">{{ number_format($activeUserCount) }}</p>
                    <p class="r-label mt-1.5 truncate">Active user</p>
                </div>
            </div>

            <div class="r-card flex items-center gap-3.5 p-4">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-(--info-bg) text-(--info)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1M10 21v-4h4v4" /></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold tabular-nums leading-none text-(--black)">{{ number_format($lobbyCreatedCount) }}</p>
                    <p class="r-label mt-1.5 truncate">Lobby created</p>
                </div>
            </div>

            <div class="r-card flex items-center gap-3.5 p-4">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-(--warning-bg) text-(--warning)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21V4m0 0h11l-1.5 3.5L16 11H5" /></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold tabular-nums leading-none text-(--black)">{{ number_format($reportBay->count()) }}</p>
                    <p class="r-label mt-1.5 truncate">Reports in bay</p>
                </div>
            </div>

            <div class="r-card flex items-center gap-3.5 p-4">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-(--error-bg) text-(--error)">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold tabular-nums leading-none text-(--black)">{{ number_format($mostReportedUsers->count()) }}</p>
                    <p class="r-label mt-1.5 truncate">Flagged accounts</p>
                </div>
            </div>
        </section>

        {{-- ===================== MAIN PANELS ===================== --}}
        <div class="grid min-h-0 flex-1 grid-cols-1 gap-4 lg:grid-cols-12">

            {{-- 1. USER OVERVIEW (CHART) --}}
            <section class="r-card r-rise r-d3 flex min-h-[340px] flex-col p-5 lg:col-span-4 lg:h-full lg:min-h-0">
                <div class="mb-3 flex shrink-0 items-center justify-between">
                    <h3 class="r-label">User overview</h3>
                    <span id="chart-badge" class="rounded-full bg-(--selected) px-2.5 py-1 text-[11px] font-bold text-(--black)">By category</span>
                </div>

                {{-- Dynamic Chart Container --}}
                <div class="relative min-h-0 flex-1">
                    <canvas id="userCategoryChart"></canvas>
                </div>
            </section>

            {{-- 2. REPORT BAY (RECENT INCOMING REPORTS) --}}
            <section class="r-card r-rise r-d3 flex min-h-[320px] flex-col p-5 lg:col-span-4 lg:h-full lg:min-h-0">
                <div class="mb-3 flex shrink-0 items-center justify-between">
                    <h3 class="r-label">Report bay</h3>
                    <span class="rounded-full bg-(--warning-bg) px-2.5 py-1 text-[11px] font-bold tabular-nums text-(--warning)">{{ $reportBay->count() }} open</span>
                </div>

                <div class="r-scroll -mr-2 min-h-0 flex-1 space-y-2 overflow-y-auto pr-2">
                    @forelse($reportBay as $report)
                        <div class="group flex items-center gap-3 rounded-2xl border border-(--border) bg-(--bg) p-3 transition hover:bg-(--hover)">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-(--bubble-me) text-xs font-bold uppercase text-(--black)">
                                {{ substr($report->reportedUser->name ?? 'User', 0, 2) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-(--text)">{{ $report->reportedUser->name ?? 'User' }}</p>
                                <p class="truncate text-xs text-(--error)" title="{{ $report->reason }}">{{ Str::limit($report->reason, 40) }}</p>
                            </div>

                            <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Dismiss report"
                                        class="grid h-9 w-9 place-items-center rounded-xl text-xs text-(--muted) transition hover:bg-(--error-bg) hover:text-(--error)">✕</button>
                            </form>
                        </div>
                    @empty
                        <div class="grid h-full place-items-center rounded-2xl border border-dashed border-(--border) py-10 text-center">
                            <div>
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-2xl bg-(--success-bg) text-lg">✓</div>
                                <p class="text-sm font-semibold text-(--text)">All clear</p>
                                <p class="text-xs text-(--muted)">No active reports.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- 3. MOST REPORTED USER --}}
            <section class="r-card r-rise r-d4 flex min-h-[320px] flex-col p-5 lg:col-span-4 lg:h-full lg:min-h-0">
                <div class="mb-3 flex shrink-0 items-center justify-between">
                    <h3 class="r-label flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-(--error)"></span> Most reported user
                    </h3>
                    <span class="rounded-full bg-(--error-bg) px-2.5 py-1 text-[11px] font-bold tabular-nums text-(--error)">{{ $mostReportedUsers->count() }} flagged</span>
                </div>

                <div class="r-scroll -mr-2 min-h-0 flex-1 space-y-2 overflow-y-auto pr-2">
                    @forelse($mostReportedUsers as $reported)
                        <div class="rounded-2xl border border-(--border) bg-(--bg) p-3 transition hover:border-(--muted)">
                            <div class="flex items-center gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-xs font-extrabold tabular-nums {{ $loop->first ? 'bg-(--error) text-white' : 'bg-(--selected) text-(--black)' }}">
                                    {{ $loop->iteration }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-(--text)">{{ $reported->name }}</p>
                                    <p class="truncate text-xs text-(--text-2)">
                                        <span class="font-bold tabular-nums text-(--black)">{{ $reported->reports_against_count }}</span> report from different user
                                    </p>
                                </div>

                                {{-- Red Delete Action Button --}}
                                <form action="{{ route('admin.users.destroy', $reported->id) }}" method="POST" class="shrink-0"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Are you sure you want to delete ' . $reported->name . ' permanently?') }});">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex min-h-9 items-center rounded-lg bg-(--error) px-3 text-[10px] font-bold uppercase tracking-wide text-white transition hover:brightness-95 active:scale-95">
                                        Delete user
                                    </button>
                                </form>
                            </div>

                            {{-- Share of the highest report count --}}
                            <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-(--selected)">
                                <div class="r-bar h-full rounded-full bg-(--error)" style="width: {{ round(($reported->reports_against_count / $maxReports) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="grid h-full place-items-center rounded-2xl border border-dashed border-(--border) py-10 text-center">
                            <div>
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-2xl bg-(--success-bg) text-lg">🛡️</div>
                                <p class="text-sm font-semibold text-(--text)">No flagged accounts</p>
                                <p class="text-xs text-(--muted)">Nobody has been flagged.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    {{-- Chart.js Pie Chart Initialization Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userCategoryChart').getContext('2d');

            const categories = @json($categoryStats->pluck('name'));
            const counts = @json($categoryStats->pluck('users_count'));
            const hasData = categories.length > 0;

            // Fallback preview data (kept from the original) — flagged in the badge so it's never mistaken for real numbers
            if (!hasData) {
                const badge = document.getElementById('chart-badge');
                if (badge) badge.textContent = 'Sample data';
            }

            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categories.length ? categories : ['Data Science', 'Front-End', 'Full-Stack'],
                    datasets: [{
                        data: counts.length ? counts : [30, 45, 25],
                        backgroundColor: [
                            '#4E83C4', // info
                            '#4CAF72', // success
                            '#D9A441', // warning
                            '#D98245', // mention
                            '#D85C5C', // error
                            '#9A9C9A'  // muted
                        ],
                        borderColor: '#FFFFFF',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#242524',
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 14,
                                font: { size: 12, weight: '600' }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>