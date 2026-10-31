<x-app-layout>

<style>
    /* ---------- Splash ---------- */
    #lobby-splash {
        position: fixed; inset: 0; z-index: 100;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px;
        background: var(--bg);
        transition: opacity .6s ease, visibility .6s ease;
    }
    #lobby-splash.hide { opacity: 0; visibility: hidden; }
    #lobby-splash .logo-wrap {
        position: relative; width: 112px; height: 112px; display: grid; place-items: center;
        border-radius: 32px; background: var(--surface); border: 1px solid var(--border);
        box-shadow: 0 20px 40px -20px rgba(32,33,32,.25);
        animation: l-pop .8s cubic-bezier(.2,.9,.3,1.2) both;
    }
    #lobby-splash .logo-wrap::before {
        content: ""; position: absolute; inset: -14px; border-radius: 42px;
        border: 1px solid var(--border); animation: l-ring 2s ease-out infinite;
    }
    #lobby-splash img { width: 64px; height: 64px; object-fit: contain; }
    #lobby-splash .name {
        font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; color: var(--black);
        animation: l-rise .7s .25s ease both;
    }
    #lobby-splash .bar { width: 120px; height: 3px; border-radius: 999px; background: var(--selected); overflow: hidden; animation: l-rise .7s .35s ease both; }
    #lobby-splash .bar i { display: block; height: 100%; background: var(--black); transform-origin: left; transform: scaleX(0); animation: l-load 1.6s .3s cubic-bezier(.6,0,.2,1) forwards; }
    @keyframes l-pop  { from { opacity: 0; transform: scale(.6) } to { opacity: 1; transform: scale(1) } }
    @keyframes l-rise { from { opacity: 0; transform: translateY(10px) } to { opacity: 1; transform: none } }
    @keyframes l-ring { 0% { opacity: .8; transform: scale(.92) } 100% { opacity: 0; transform: scale(1.25) } }
    @keyframes l-load { to { transform: scaleX(1) } }
    @keyframes l-grow { from { transform: scaleX(0) } to { transform: scaleX(1) } }

    /* ---------- Page ---------- */
    .fade-up { opacity: 0; transform: translateY(14px); animation: l-rise .6s ease forwards; }

    .l-card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; }
    .l-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; padding: 0 18px; border-radius: 14px;
        font-size: .875rem; font-weight: 700; transition: background .15s, color .15s, transform .1s, border-color .15s;
        -webkit-tap-highlight-color: transparent;
    }
    .l-btn:active { transform: scale(.96); }
    .l-btn-primary { background: var(--btn); color: #fff; }
    .l-btn-primary:hover { background: var(--black-2); }
    .l-btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
    .l-btn-ghost:hover { background: var(--btn); color: #fff; border-color: var(--btn); }

    /* Sort switch */
    .sort-opt {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 40px; padding: 0 16px; border-radius: 12px;
        font-size: .875rem; font-weight: 700; color: var(--text-2);
        transition: background .2s, color .2s; -webkit-tap-highlight-color: transparent;
    }
    .sort-opt:hover { color: var(--text); }
    .sort-opt.is-on { background: var(--btn); color: #fff; }

    /* Lobby cards */
    .lobby {
        position: relative; display: flex; flex-direction: column; overflow: hidden;
        transition: transform .25s cubic-bezier(.2,.9,.3,1.2), box-shadow .25s ease, border-color .2s;
    }
    .lobby:hover { transform: translateY(-6px) rotate(-.4deg); box-shadow: 0 24px 40px -24px rgba(32,33,32,.35); border-color: var(--muted); }
    .lobby.is-closest { border-color: var(--btn); box-shadow: 0 0 0 2px var(--btn); }

    .tone-0 { --tone: var(--info);    --tone-bg: var(--info-bg); }
    .tone-1 { --tone: var(--success); --tone-bg: var(--success-bg); }
    .tone-2 { --tone: var(--warning); --tone-bg: var(--warning-bg); }
    .tone-3 { --tone: var(--mention); --tone-bg: color-mix(in srgb, var(--mention) 14%, #fff); }

    .lobby-banner {
        position: relative; height: 84px; background: var(--tone-bg); overflow: hidden;
    }
    .lobby-banner::before, .lobby-banner::after {
        content: ""; position: absolute; border-radius: 999px; background: var(--tone); opacity: .12;
    }
    .lobby-banner::before { width: 120px; height: 120px; right: -30px; top: -50px; }
    .lobby-banner::after  { width: 70px;  height: 70px;  right: 70px; bottom: -40px; }

    .lobby-emoji {
        position: absolute; left: 20px; top: 52px; z-index: 2;
        display: grid; place-items: center; width: 56px; height: 56px; border-radius: 18px;
        background: var(--surface); border: 1px solid var(--border); font-size: 1.65rem;
        box-shadow: 0 10px 20px -12px rgba(32,33,32,.4);
        transition: transform .3s cubic-bezier(.2,.9,.3,1.6);
    }
    .lobby:hover .lobby-emoji { transform: rotate(-10deg) scale(1.12); }

    .match-fill { transform-origin: left; animation: l-grow .9s .3s cubic-bezier(.2,.8,.2,1) both; }

    .avatar {
        display: grid; place-items: center; width: 28px; height: 28px; border-radius: 999px;
        border: 2px solid var(--surface); background: var(--bubble-me); color: var(--black);
        font-size: .6875rem; font-weight: 800; text-transform: uppercase; margin-left: -8px;
    }
    .avatar:first-child { margin-left: 0; }
    .avatar.more { background: var(--btn); color: #fff; }

    @media (prefers-reduced-motion: reduce) {
        #lobby-splash *, .fade-up, .match-fill { animation: none !important; opacity: 1; transform: none; }
        .lobby, .lobby-emoji { transition: none; }
    }
</style>

@php
    $emojis = ['🚀', '🎨', '🧠', '🛠️', '🌱', '🎮', '☕', '🔭', '📚', '⚡'];

    // ---- Sorting: "relevance" (closest match first, default) or "name" ----
    $sort = request('sort') === 'name' ? 'name' : 'relevance';

    // ---- Match % for every lobby ----
    // Uses $lobby->match_percentage when the controller provides it (same value as the dashboard).
    // Otherwise falls back to: shared interests ÷ the lobby's interests.
    $myInterestIds = auth()->user()->interests->pluck('id')->all();

    $source = $lobbies instanceof \Illuminate\Pagination\AbstractPaginator
        ? collect($lobbies->items())
        : collect($lobbies);

    $items = $source->map(function ($l) use ($myInterestIds) {
        $pct = $l->match_percentage;

        if ($pct === null) {
            $ids = $l->interests->pluck('id')->all();
            $pct = count($ids) ? round(count(array_intersect($ids, $myInterestIds)) / count($ids) * 100) : 0;
        }

        $l->setAttribute('match_percent', (int) max(0, min(100, $pct)));
        return $l;
    });

    $items = $sort === 'name'
        ? $items->sortBy(fn ($l) => mb_strtolower($l->name))->values()
        : $items->sortBy([['match_percent', 'desc'], ['name', 'asc']])->values();

    // Closest lobby the user can still join
    $closestId = $sort === 'relevance'
        ? $items->first(fn ($l) => $l->match_percent > 0 && $l->owner_id !== auth()->id() && !$l->members->contains(auth()->id()))?->id
        : null;
@endphp

{{-- Splash screen (once per session) --}}
<div id="lobby-splash">
    <div class="logo-wrap">
        <img src="{{ asset('image/teamapio.png') }}" alt="TeaMap">
    </div>
    <div class="name">TeaMap</div>
    <div class="bar"><i></i></div>
</div>

<div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 pb-14 sm:px-6 lg:px-8 lg:py-8">

    {{-- HEADER --}}
    <section class="fade-up flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-(--black) sm:text-4xl">Project Lobbies</h1>
            <p class="mt-1.5 text-base text-(--text-2)">Discover, join, or manage custom collaboration workspaces.</p>
        </div>
        <a href="{{ route('lobbies.create') }}" class="l-btn l-btn-primary shrink-0">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Create New Lobby
        </a>
    </section>

    {{-- SEARCH + SORT --}}
    <section class="l-card fade-up space-y-3 p-3 sm:p-4" style="animation-delay:.08s">
        <form method="GET" action="{{ route('lobby.index') }}">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-(--muted)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" enterkeyhint="search"
                           placeholder="Search by name, goal or keyword..."
                           class="block min-h-12 w-full rounded-2xl border border-(--border) bg-(--bg) pl-12 pr-4 text-base text-(--text) outline-none transition placeholder:text-(--muted) focus:border-(--btn) focus:bg-(--surface) focus:ring-4 focus:ring-(--selected)">
                </div>
                <button type="submit" class="l-btn l-btn-primary !min-h-12">Search</button>
            </div>
        </form>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <span class="text-sm font-semibold text-(--text-2)">Sort by</span>
            <div class="grid grid-cols-2 gap-1 rounded-2xl border border-(--border) bg-(--bg) p-1 sm:w-80">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'relevance']) }}" class="sort-opt {{ $sort === 'relevance' ? 'is-on' : '' }}">
                    🎯 Best match
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}" class="sort-opt {{ $sort === 'name' ? 'is-on' : '' }}">
                    A–Z Name
                </a>
            </div>
        </div>
    </section>

    {{-- LIST --}}
    <section class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold tracking-tight text-(--black)">
                {{ $sort === 'relevance' ? 'Closest to you' : 'Available openings' }}
            </h2>
            <span class="rounded-full bg-(--selected) px-3 py-1 text-sm font-semibold tabular-nums text-(--black)">
                {{ $items->count() }} {{ \Illuminate\Support\Str::plural('lobby', $items->count()) }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($items as $lobby)
                @php
                    $isOwner  = $lobby->owner_id === auth()->id();
                    $isMember = $lobby->members->contains(auth()->id());
                    $isClosest = $lobby->id === $closestId;
                    $pct      = $lobby->match_percent;
                    $tone     = $lobby->id % 4;
                    $emoji    = $emojis[$lobby->id % count($emojis)];
                    $shown    = $lobby->members->take(3);
                    $extra    = $lobby->members->count() - $shown->count();

                    $pctBadge = $pct >= 70
                        ? 'bg-(--success-bg) text-(--success)'
                        : ($pct >= 40 ? 'bg-(--info-bg) text-(--info)' : 'bg-(--surface) text-(--text-2)');
                @endphp

                <article class="lobby l-card tone-{{ $tone }} fade-up {{ $isClosest ? 'is-closest' : '' }}" style="animation-delay: {{ min($loop->index, 8) * 60 + 150 }}ms">

                    {{-- Banner --}}
                    <div class="lobby-banner">
                        @unless($isOwner)
                            <span class="absolute left-4 top-4 z-[1] inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-extrabold tabular-nums ring-1 ring-inset ring-black/5 {{ $pctBadge }}">
                                🎯 {{ $pct }}% match
                            </span>
                        @endunless

                        @if($isOwner)
                            <span class="absolute right-4 top-4 z-[1] rounded-full bg-(--warning-bg) px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-(--warning) ring-1 ring-inset ring-(--warning)/25">
                                👑 Owner
                            </span>
                        @elseif($isMember)
                            <span class="absolute right-4 top-4 z-[1] rounded-full bg-(--success-bg) px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-(--success) ring-1 ring-inset ring-(--success)/25">
                                ✓ Joined
                            </span>
                        @elseif($isClosest)
                            <span class="absolute right-4 top-4 z-[1] rounded-full bg-(--btn) px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-white">
                                🏆 Closest
                            </span>
                        @endif
                    </div>
                    <div class="lobby-emoji" aria-hidden="true">{{ $emoji }}</div>

                    {{-- Body --}}
                    <div class="flex flex-1 flex-col px-5 pb-5 pt-10">
                        <h3 class="truncate text-lg font-extrabold tracking-tight text-(--black)" title="{{ $lobby->name }}">
                            {{ $lobby->name }}
                        </h3>

                        <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-(--text-2)">
                            {{ $lobby->description ?? 'No description provided for this specific configuration space.' }}
                        </p>

                        <div class="mt-4 rounded-2xl px-4 py-3" style="background: var(--tone-bg)">
                            <span class="block text-[11px] font-extrabold uppercase tracking-wider" style="color: var(--tone)">🎯 Project goal</span>
                            <p class="mt-0.5 line-clamp-1 text-sm font-semibold text-(--text)">{{ $lobby->project_goal }}</p>
                        </div>

                        @unless($isOwner)
                            <div class="mt-4">
                                <div class="mb-1.5 flex items-center justify-between text-xs font-semibold">
                                    <span class="text-(--text-2)">Interest match</span>
                                    <span class="tabular-nums text-(--text)">{{ $pct }}%</span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-(--selected)" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="match-fill h-full rounded-full" style="width: {{ $pct }}%; background: var(--tone)"></div>
                                </div>
                            </div>
                        @endunless

                        <div class="mt-5 flex flex-1 items-end justify-between gap-3 border-t border-(--border) pt-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center">
                                    @foreach($shown as $member)
                                        <span class="avatar" title="{{ $member->name }}">{{ substr($member->name, 0, 1) }}</span>
                                    @endforeach
                                    @if($extra > 0)
                                        <span class="avatar more">+{{ $extra }}</span>
                                    @endif
                                </div>
                                <span class="text-sm text-(--text-2)"><span class="font-bold tabular-nums text-(--text)">{{ $lobby->members->count() }}</span> members</span>
                            </div>

                            @if($isOwner || $isMember)
                                <a href="{{ route('chat.index', $lobby->id) }}" class="l-btn l-btn-primary !min-h-10 shrink-0 !px-4">
                                    Open Chat →
                                </a>
                            @else
                                <form action="{{ route('lobby.join', $lobby->id) }}" method="POST" class="m-0 shrink-0">
                                    @csrf
                                    <button type="submit" class="l-btn l-btn-ghost !min-h-10 !px-4">
                                        Request to Join
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="l-card col-span-full flex flex-col items-center gap-4 border-dashed px-6 py-14 text-center">
                    <div class="grid h-16 w-16 place-items-center rounded-3xl bg-(--bg) text-3xl">🔍</div>
                    <div>
                        <p class="text-base font-bold text-(--black)">No open lobbies found</p>
                        <p class="mt-1 text-sm text-(--text-2)">No open collaboration lobbies match those filters. Try clearing your search.</p>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('lobby.index', ['sort' => $sort]) }}" class="l-btn l-btn-primary">Clear search</a>
                    @endif
                </div>
            @endforelse
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const splash = document.getElementById('lobby-splash');
    let seen = false;
    try { seen = sessionStorage.getItem('splash_seen') === '1'; } catch (e) {}

    if (seen) { splash.remove(); return; }

    setTimeout(() => {
        splash.classList.add('hide');
        try { sessionStorage.setItem('splash_seen', '1'); } catch (e) {}
        setTimeout(() => splash.remove(), 700);
    }, 1900);
});
</script>

</x-app-layout>