<x-guest-layout>
    @php
        $step1Errors = $errors->hasAny(['name', 'email', 'password', 'password_confirmation']);
        $startStep = ($errors->has('interests') && !$step1Errors) ? 1 : 0;
    @endphp

    <style>
        #reg {
            --surface: #FFFFFF; --bg: #F7F7F5; --black: #202120; --black-2: #3A3B3A;
            --text: #242524; --text-2: #747674; --muted: #9A9C9A;
            --border: #E1E1DE; --hover: #EEEEEC; --selected: #E5E5E2; --btn: #252625;
            --success: #4CAF72; --success-bg: #E8F5EC;
            --error: #D85C5C; --error-bg: #FBEAEA;
            --info: #4E83C4; --info-bg: #EAF2FA;
            --warning: #D9A441; --warning-bg: #FBF3DD;
            color: var(--text);
        }

        /* ---------- Splash ---------- */
        #reg-splash {
            position: fixed; inset: 0; z-index: 100;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 28px;
            background: var(--bg);
            transition: opacity .6s ease, visibility .6s ease;
        }
        #reg-splash.hide { opacity: 0; visibility: hidden; }
        #reg-splash .logo-wrap {
            position: relative; width: 112px; height: 112px; display: grid; place-items: center;
            border-radius: 32px; background: var(--surface); border: 1px solid var(--border);
            box-shadow: 0 20px 40px -20px rgba(32,33,32,.25);
            animation: r-pop .8s cubic-bezier(.2,.9,.3,1.2) both;
        }
        #reg-splash .logo-wrap::before {
            content: ""; position: absolute; inset: -14px; border-radius: 42px;
            border: 1px solid var(--border); animation: r-ring 2s ease-out infinite;
        }
        #reg-splash img { width: 64px; height: 64px; object-fit: contain; }
        #reg-splash .name { font-weight: 600; font-size: 1.25rem; letter-spacing: -.02em; color: var(--black); animation: r-rise .7s .25s ease both; }
        #reg-splash .bar { width: 120px; height: 3px; border-radius: 999px; background: var(--selected); overflow: hidden; animation: r-rise .7s .35s ease both; }
        #reg-splash .bar i { display: block; height: 100%; background: var(--black); transform-origin: left; transform: scaleX(0); animation: r-load 1.6s .3s cubic-bezier(.6,0,.2,1) forwards; }
        @keyframes r-pop  { from { opacity: 0; transform: scale(.6) } to { opacity: 1; transform: scale(1) } }
        @keyframes r-rise { from { opacity: 0; transform: translateY(10px) } to { opacity: 1; transform: none } }
        @keyframes r-ring { 0% { opacity: .8; transform: scale(.92) } 100% { opacity: 0; transform: scale(1.25) } }
        @keyframes r-load { to { transform: scaleX(1) } }

        /* ---------- Steps ---------- */
        .step-enter { animation: r-step .45s cubic-bezier(.2,.8,.2,1) both; }
        @keyframes r-step { from { opacity: 0; transform: translateX(18px) } to { opacity: 1; transform: none } }

        .prog-bar { height: 4px; flex: 1; border-radius: 999px; background: var(--selected); overflow: hidden; }
        .prog-bar i { display: block; height: 100%; width: 0; background: var(--black); transition: width .45s ease; }
        .prog-bar.on i { width: 100%; }

        /* ---------- Fields ---------- */
        .r-label { display: block; margin-bottom: 6px; font-size: .8125rem; font-weight: 600; color: var(--text); }
        .r-field {
            display: block; width: 100%; border-radius: 12px; border: 1px solid var(--border);
            background: var(--surface); padding: 11px 16px; font-size: .875rem; color: var(--text);
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .r-field::placeholder { color: var(--muted); }
        .r-field:focus { border-color: var(--btn); box-shadow: 0 0 0 4px var(--selected); }

        .r-btn {
            border-radius: 12px; padding: 12px 20px; font-size: .875rem; font-weight: 600;
            transition: background .15s, transform .1s; cursor: pointer;
        }
        .r-btn:active { transform: scale(.98); }
        .r-btn-primary { background: var(--btn); color: #fff; }
        .r-btn-primary:hover { background: var(--black-2); }
        .r-btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
        .r-btn-ghost:hover { background: var(--hover); }

        /* ---------- Interest tags ---------- */
        .interest-tag {
            display: inline-flex; align-items: center; user-select: none; cursor: pointer;
            border-radius: 999px; padding: 9px 16px; font-size: .8125rem; font-weight: 600;
            background: var(--surface); color: var(--text-2); border: 1px solid var(--border);
            transition: background .15s, color .15s, border-color .15s, transform .1s, opacity .2s;
        }
        .interest-tag:hover { background: var(--hover); color: var(--text); }
        .interest-tag:active { transform: scale(.95); }
        .interest-tag.is-on { background: var(--btn); color: #fff; border-color: var(--btn); }
        .interest-tag.is-capped { opacity: .35; pointer-events: none; }

        /* ---------- Counter pill ---------- */
        #interest-counter {
            font-size: .75rem; font-weight: 700; padding: 4px 12px; border-radius: 999px;
            border: 1px solid var(--border); background: var(--bg); color: var(--muted);
            transition: all .25s ease;
        }
        #interest-counter.p-info { background: var(--info-bg); color: var(--info); border-color: transparent; }
        #interest-counter.p-ok   { background: var(--success-bg); color: var(--success); border-color: transparent; }
        #interest-counter.p-max  { background: var(--error-bg); color: var(--error); border-color: transparent; }

        #interests-container::-webkit-scrollbar { width: 6px; }
        #interests-container::-webkit-scrollbar-thumb { background: var(--selected); border-radius: 999px; }

        @media (prefers-reduced-motion: reduce) {
            #reg-splash *, .step-enter { animation: none !important; }
        }
    </style>

    <div id="reg">

        {{-- Splash screen (once per session) --}}
        <div id="reg-splash">
            <div class="logo-wrap">
                <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name') }}">
            </div>
            <div class="name">{{ config('app.name', 'TeaMap') }}</div>
            <div class="bar"><i></i></div>
        </div>

        {{-- Header --}}
        <div class="mb-6 flex flex-col items-center text-center">
            <a href="/" class="grid h-16 w-16 place-items-center rounded-2xl border border-(--border) bg-(--surface) shadow-[0_16px_32px_-16px_rgba(32,33,32,.25)]">
                <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'TeaMap') }}" class="h-10 w-10 object-contain">
            </a>
            <h1 id="reg-title" class="mt-5 text-2xl font-semibold tracking-tight text-(--black)">Create your account</h1>
            <p id="reg-sub" class="mt-1 text-sm text-(--text-2)">Join TeaMap to connect with project partners and start building.</p>
        </div>

        {{-- Progress --}}
        <div class="mb-7">
            <div class="mb-2 flex items-center justify-between text-xs font-semibold">
                <span id="lbl-1" class="text-(--black)">1 · Your details</span>
                <span id="lbl-2" class="text-(--muted)">2 · Interests</span>
            </div>
            <div class="flex gap-2">
                <div id="bar-1" class="prog-bar on"><i></i></div>
                <div id="bar-2" class="prog-bar"><i></i></div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
            @csrf

            {{-- ============ STEP 1 ============ --}}
            <div id="step-1" class="space-y-4">
                <div>
                    <label for="name" class="r-label">{{ __('Full Name') }}</label>
                    <input id="name" class="r-field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your name">
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-(--error)" />
                </div>

                <div>
                    <label for="email" class="r-label">{{ __('Email Address') }}</label>
                    <input id="email" class="r-field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-(--error)" />
                </div>

                <div>
                    <label for="password" class="r-label">{{ __('Password') }}</label>
                    <input id="password" class="r-field" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-(--error)" />
                </div>

                <div>
                    <label for="password_confirmation" class="r-label">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" class="r-field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-(--error)" />
                </div>

                <button type="button" id="btn-next" class="r-btn r-btn-primary w-full">Continue</button>
            </div>

            {{-- ============ STEP 2 ============ --}}
            <div id="step-2" class="hidden">
                <div class="mb-1 flex items-baseline justify-between">
                    <label class="r-label !mb-0">{{ __('Choose areas of focus') }}</label>
                    <span id="interest-counter">0 selected</span>
                </div>
                <p class="mb-4 text-sm text-(--text-2)">
                    Pick 3 to 5 matching tags. This builds your custom dashboard feed!
                </p>

                <input type="text" id="interest-search" placeholder="Search focus areas (AI, Design, Web)..." class="r-field mb-3">

                <div id="interests-container" class="flex max-h-64 flex-wrap gap-2 overflow-y-auto rounded-2xl border border-(--border) bg-(--bg) p-3">
                    @foreach(\App\Models\Interest::all() as $interest)
                        @php
                            $is_checked = is_array(old('interests')) && in_array($interest->id, old('interests'));
                        @endphp
                        <label data-interest-name="{{ strtolower($interest->name) }}"
                               class="interest-tag {{ $is_checked ? 'is-on' : '' }}">
                            <input type="checkbox" name="interests[]" value="{{ $interest->id }}"
                                   {{ $is_checked ? 'checked' : '' }}
                                   class="hidden interest-checkbox">
                            <span>{{ $interest->name }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('interests')" class="mt-3 text-xs text-(--error)" />

                <div class="mt-6 flex gap-3">
                    <button type="button" id="btn-back" class="r-btn r-btn-ghost">Back</button>
                    <button type="submit" class="r-btn r-btn-primary flex-1">Create Account</button>
                </div>
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-(--text-2)">
            Already registered?
            <a href="{{ route('login') }}" class="font-semibold text-(--text) underline underline-offset-4 transition hover:text-(--black)">Log in</a>
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /* ---------------- Splash (once per session) ---------------- */
            const splash = document.getElementById('reg-splash');
            let seen = false;
            try { seen = sessionStorage.getItem('splash_seen') === '1'; } catch (e) {}
            if (seen) {
                splash.remove();
            } else {
                setTimeout(() => {
                    splash.classList.add('hide');
                    try { sessionStorage.setItem('splash_seen', '1'); } catch (e) {}
                    setTimeout(() => splash.remove(), 700);
                }, 1900);
            }

            /* ---------------- Two-step wizard ---------------- */
            const form = document.getElementById('register-form');
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const btnNext = document.getElementById('btn-next');
            const btnBack = document.getElementById('btn-back');
            const title = document.getElementById('reg-title');
            const sub = document.getElementById('reg-sub');
            const bar2 = document.getElementById('bar-2');
            const lbl1 = document.getElementById('lbl-1');
            const lbl2 = document.getElementById('lbl-2');
            const nameInput = document.getElementById('name');
            const pw = document.getElementById('password');
            const pwc = document.getElementById('password_confirmation');
            let current = {{ $startStep }};

            const goTo = (n) => {
                current = n;
                const on = n === 1;
                step1.classList.toggle('hidden', on);
                step2.classList.toggle('hidden', !on);
                const active = on ? step2 : step1;
                active.classList.remove('step-enter');
                void active.offsetWidth; // restart animation
                active.classList.add('step-enter');

                bar2.classList.toggle('on', on);
                lbl1.style.color = on ? 'var(--muted)' : 'var(--black)';
                lbl2.style.color = on ? 'var(--black)' : 'var(--muted)';

                if (on) {
                    const first = (nameInput.value.trim().split(' ')[0]) || 'there';
                    title.textContent = `Nice to meet you, ${first}`;
                    sub.textContent = 'Now pick what you\'re into.';
                    document.getElementById('interest-search').focus({ preventScroll: true });
                } else {
                    title.textContent = 'Create your account';
                    sub.textContent = 'Join TeaMap to connect with project partners and start building.';
                }
            };

            const validateStep1 = () => {
                pwc.setCustomValidity('');
                for (const input of step1.querySelectorAll('input')) {
                    if (!input.checkValidity()) { input.reportValidity(); return false; }
                }
                if (pw.value !== pwc.value) {
                    pwc.setCustomValidity('Passwords do not match');
                    pwc.reportValidity();
                    return false;
                }
                return true;
            };
            pwc.addEventListener('input', () => pwc.setCustomValidity(''));

            const next = () => { if (validateStep1()) goTo(1); };
            btnNext.addEventListener('click', next);
            btnBack.addEventListener('click', () => goTo(0));

            // Enter: advance on step 1, never submit from the search box
            form.addEventListener('keydown', (e) => {
                if (e.key !== 'Enter') return;
                if (current === 0 && e.target.tagName === 'INPUT') { e.preventDefault(); next(); }
                else if (e.target.id === 'interest-search') { e.preventDefault(); }
            });

            form.addEventListener('submit', (e) => {
                if (current === 0) { e.preventDefault(); next(); }
            });

            /* ---------------- Interests (same logic) ---------------- */
            const searchInput = document.getElementById('interest-search');
            const tags = document.querySelectorAll('.interest-tag');
            const counter = document.getElementById('interest-counter');

            // 1. Unified state sync (loads data, applies styles, enforces 5-max limit)
            const syncTagStates = () => {
                const count = document.querySelectorAll('.interest-checkbox:checked').length;

                counter.classList.remove('p-info', 'p-ok', 'p-max');
                if (count === 0) {
                    counter.textContent = '0 selected';
                } else if (count >= 3 && count < 5) {
                    counter.textContent = `${count} selected`;
                    counter.classList.add('p-ok');
                } else if (count === 5) {
                    counter.textContent = 'Max Reached!';
                    counter.classList.add('p-max');
                } else {
                    counter.textContent = `${count} selected`;
                    counter.classList.add('p-info');
                }

                tags.forEach(tag => {
                    const checkbox = tag.querySelector('.interest-checkbox');
                    tag.classList.toggle('is-on', checkbox.checked);
                    tag.classList.toggle('is-capped', !checkbox.checked && count >= 5);
                });
            };

            syncTagStates();

            // 2. Real-time search filtering
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                tags.forEach(tag => {
                    const name = tag.getAttribute('data-interest-name');
                    tag.style.display = name.includes(query) ? 'inline-flex' : 'none';
                });
            });

            // 3. Prevent clicking more than 5 tags
            tags.forEach(tag => {
                const checkbox = tag.querySelector('.interest-checkbox');
                tag.addEventListener('click', (e) => {
                    const currentCheckedCount = document.querySelectorAll('.interest-checkbox:checked').length;
                    if (!checkbox.checked && currentCheckedCount >= 5) {
                        e.preventDefault();
                        return;
                    }
                    setTimeout(syncTagStates, 10);
                });
            });

            if (current === 1) goTo(1);
        });
    </script>
</x-guest-layout>