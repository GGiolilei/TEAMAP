<x-guest-layout>
    @php
        // If the server sent back an error only about interests, reopen on layer 2
        $startStep = ($errors->has('interests') && ! $errors->hasAny(['name', 'email', 'password', 'password_confirmation'])) ? 2 : 1;
    @endphp

    <div class="mb-6 text-center">
        <a href="/" class="inline-block mb-4">
            <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'TeaMap') }}" class="w-16 h-16 object-contain mx-auto">
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-[#202120]">Create your account</h2>
        <p class="text-sm text-[#747674] mt-1">Join TeaMap to connect with project partners and start building.</p>
    </div>

    {{-- STEP INDICATOR --}}
    <div class="flex items-center gap-3 mb-7" aria-hidden="true">
        <div class="flex items-center gap-2">
            <span id="dot-1" class="w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center transition-colors duration-300 bg-[#252625] text-white">1</span>
            <span id="label-1" class="text-xs font-semibold text-[#202120] transition-colors">Your details</span>
        </div>
        <span class="flex-1 h-px bg-[#E1E1DE] relative overflow-hidden">
            <span id="step-line" class="absolute inset-y-0 left-0 bg-[#252625] transition-all duration-500" style="width:0%"></span>
        </span>
        <div class="flex items-center gap-2">
            <span id="dot-2" class="w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center transition-colors duration-300 bg-[#E9E9E7] text-[#747674]">2</span>
            <span id="label-2" class="text-xs font-semibold text-[#9A9C9A] transition-colors">Focus areas</span>
        </div>
    </div>

    <form id="register-form" method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        {{-- ================= LAYER 1: DETAILS ================= --}}
        <div id="step-1" class="step-panel">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider">{{ __('Full Name') }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                           placeholder="Jane Doe"
                           class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] placeholder-[#9A9C9A] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider">{{ __('Email Address') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                           placeholder="you@example.com"
                           class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] placeholder-[#9A9C9A] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#3A3B3A] uppercase tracking-wider">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
                </div>
            </div>

            <p id="step-1-error" class="hidden mt-4 text-xs text-rose-500"></p>

            <div class="flex items-center justify-between mt-8 border-t border-[#E1E1DE] pt-5">
                <a class="text-sm font-medium text-[#747674] hover:text-[#202120] underline underline-offset-4 transition" href="{{ route('login') }}">
                    Already registered?
                </a>
                <button type="button" id="btn-next"
                        class="inline-flex items-center gap-2 bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold text-sm py-2.5 px-6 rounded-xl transition active:scale-95">
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/></svg>
                </button>
            </div>
        </div>

        {{-- ================= LAYER 2: FOCUS AREAS ================= --}}
        <div id="step-2" class="step-panel hidden">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-sm font-semibold text-[#202120]">{{ __('Choose areas of focus') }}</h3>
                <span id="interest-counter" class="text-xs font-semibold text-[#747674] bg-[#F4F4F2] px-3 py-1 rounded-full border border-[#E1E1DE] transition">
                    0 selected
                </span>
            </div>

            <p id="interest-hint" class="text-xs text-[#747674] mt-2 mb-3">
                Pick 3 to 5 tags. This builds your custom dashboard feed.
            </p>

            {{-- Progress segments --}}
            <div class="flex gap-1.5 mb-4" aria-hidden="true">
                @for ($i = 0; $i < 5; $i++)
                    <span class="interest-seg h-1 flex-1 rounded-full bg-[#E1E1DE] transition-colors duration-300"></span>
                @endfor
            </div>

            {{-- Selected tray --}}
            <div class="mb-3 p-3 bg-white border border-dashed border-[#E1E1DE] rounded-xl min-h-[48px] flex flex-wrap items-center gap-2">
                <span id="tray-empty" class="text-xs text-[#9A9C9A]">Nothing selected yet. Tap a tag below.</span>
                <div id="selected-tray" class="contents"></div>
            </div>

            {{-- Search --}}
            <div class="relative mb-3">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#9A9C9A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <input type="text" id="interest-search" placeholder="Search focus areas (AI, Design, Web)..."
                       class="w-full bg-white border border-[#E1E1DE] focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none text-[#242524] text-sm py-2.5 pl-10 pr-4 rounded-xl placeholder-[#9A9C9A] transition">
            </div>

            {{-- Tags --}}
            <div id="interests-container" class="flex flex-wrap gap-2 max-h-56 overflow-y-auto p-3 bg-[#F7F7F5] rounded-2xl border border-[#E1E1DE]">
                @foreach(\App\Models\Interest::all() as $interest)
                    @php
                        $is_checked = is_array(old('interests')) && in_array($interest->id, old('interests'));
                    @endphp
                    <label data-interest-name="{{ strtolower($interest->name) }}"
                           class="interest-tag group inline-flex items-center gap-1.5 text-[13px] font-medium pl-3 pr-3.5 py-2 rounded-full cursor-pointer transition-all duration-200 select-none border active:scale-95
                                  {{ $is_checked ? 'bg-[#252625] text-white border-[#252625]' : 'bg-white text-[#3A3B3A] border-[#E1E1DE] hover:bg-[#EEEEEC] hover:border-[#9A9C9A]' }}">

                        <input type="checkbox" name="interests[]" value="{{ $interest->id }}"
                               {{ $is_checked ? 'checked' : '' }}
                               class="hidden interest-checkbox">

                        <svg class="tag-icon-plus w-3.5 h-3.5 text-[#9A9C9A] group-hover:text-[#202120] transition {{ $is_checked ? 'hidden' : '' }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                        </svg>
                        <svg class="tag-icon-check w-3.5 h-3.5 {{ $is_checked ? '' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>

                        <span class="tag-name">{{ $interest->name }}</span>
                    </label>
                @endforeach

                <p id="interest-empty" class="hidden w-full text-center text-xs text-[#9A9C9A] py-6">
                    No focus areas match your search.
                </p>
            </div>
            <x-input-error :messages="$errors->get('interests')" class="mt-3 text-xs text-rose-500" />

            <div class="flex items-center justify-between mt-8 border-t border-[#E1E1DE] pt-5">
                <button type="button" id="btn-back"
                        class="inline-flex items-center gap-2 text-sm font-medium text-[#747674] hover:text-[#202120] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6l-6-6 6-6"/></svg>
                    Back
                </button>
                <button type="submit" class="bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold text-sm py-2.5 px-6 rounded-xl transition active:scale-95">
                    Create Account
                </button>
            </div>
        </div>
    </form>

    <style>
        @keyframes layerIn {
            from { opacity: 0; transform: translateX(14px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes layerInBack {
            from { opacity: 0; transform: translateX(-14px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .layer-in      { animation: layerIn .25s ease-out; }
        .layer-in-back { animation: layerInBack .25s ease-out; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ---------- LAYER SWITCHING ----------
            const form = document.getElementById('register-form');
            const panels = { 1: document.getElementById('step-1'), 2: document.getElementById('step-2') };
            const dots = { 1: document.getElementById('dot-1'), 2: document.getElementById('dot-2') };
            const labels = { 1: document.getElementById('label-1'), 2: document.getElementById('label-2') };
            const stepLine = document.getElementById('step-line');
            const stepError = document.getElementById('step-1-error');
            const btnNext = document.getElementById('btn-next');
            const btnBack = document.getElementById('btn-back');
            const confirmInput = document.getElementById('password_confirmation');
            const passInput = document.getElementById('password');

            let currentStep = {{ $startStep }};

            const showStep = (step, back = false) => {
                currentStep = step;
                [1, 2].forEach(n => panels[n].classList.toggle('hidden', n !== step));

                const panel = panels[step];
                panel.classList.remove('layer-in', 'layer-in-back');
                void panel.offsetWidth; // restart animation
                panel.classList.add(back ? 'layer-in-back' : 'layer-in');

                // indicator
                stepLine.style.width = step === 2 ? '100%' : '0%';
                dots[2].className = 'w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center transition-colors duration-300 ' +
                    (step === 2 ? 'bg-[#252625] text-white' : 'bg-[#E9E9E7] text-[#747674]');
                labels[2].className = 'text-xs font-semibold transition-colors ' + (step === 2 ? 'text-[#202120]' : 'text-[#9A9C9A]');
                dots[1].innerHTML = step === 2
                    ? '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                    : '1';

                // focus the first field of the layer
                const first = panel.querySelector('input:not([type=hidden]):not(.interest-checkbox)');
                if (first) setTimeout(() => first.focus({ preventScroll: true }), 50);
            };

            const validateStep1 = () => {
                stepError.classList.add('hidden');
                confirmInput.setCustomValidity(passInput.value !== confirmInput.value ? 'Passwords do not match.' : '');

                const fields = panels[1].querySelectorAll('input');
                for (const field of fields) {
                    if (!field.checkValidity()) {
                        stepError.textContent = field.validationMessage;
                        stepError.classList.remove('hidden');
                        field.focus();
                        return false;
                    }
                }
                return true;
            };

            btnNext.addEventListener('click', () => { if (validateStep1()) showStep(2); });
            btnBack.addEventListener('click', () => showStep(1, true));

            // Enter on layer 1 goes to the next layer instead of submitting
            form.addEventListener('submit', (e) => {
                if (currentStep === 1) {
                    e.preventDefault();
                    if (validateStep1()) showStep(2);
                }
            });

            // ---------- INTERESTS (same logic as before) ----------
            const searchInput = document.getElementById('interest-search');
            const tags = document.querySelectorAll('.interest-tag');
            const counter = document.getElementById('interest-counter');
            const hint = document.getElementById('interest-hint');
            const segments = document.querySelectorAll('.interest-seg');
            const emptyNotice = document.getElementById('interest-empty');
            const tray = document.getElementById('selected-tray');
            const trayEmpty = document.getElementById('tray-empty');

            const counterBase = "text-xs font-semibold px-3 py-1 rounded-full border transition";
            const SELECTED = ['bg-[#252625]', 'text-white', 'border-[#252625]'];
            const UNSELECTED = ['bg-white', 'text-[#3A3B3A]', 'border-[#E1E1DE]'];
            const HOVER = ['hover:bg-[#EEEEEC]', 'hover:border-[#9A9C9A]'];
            const CAPPED = ['opacity-40', 'pointer-events-none'];

            const renderTray = (checkedBoxes) => {
                tray.innerHTML = '';
                trayEmpty.classList.toggle('hidden', checkedBoxes.length > 0);

                checkedBoxes.forEach(box => {
                    const name = box.closest('.interest-tag').querySelector('.tag-name').textContent;

                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.title = 'Remove';
                    chip.className = 'inline-flex items-center gap-1.5 text-xs font-medium pl-3 pr-2 py-1.5 rounded-full bg-[#252625] text-white hover:bg-[#3A3B3A] transition';

                    const label = document.createElement('span');
                    label.textContent = name;

                    const x = document.createElement('span');
                    x.textContent = '×';
                    x.className = 'text-sm leading-none opacity-70';

                    chip.append(label, x);
                    chip.addEventListener('click', () => {
                        box.checked = false;
                        syncTagStates();
                    });
                    tray.appendChild(chip);
                });
            };

            const syncTagStates = () => {
                const checkedBoxes = Array.from(document.querySelectorAll('.interest-checkbox:checked'));
                const count = checkedBoxes.length;

                if (count === 0) {
                    counter.textContent = '0 selected';
                    counter.className = counterBase + " text-[#747674] bg-[#F4F4F2] border-[#E1E1DE]";
                    hint.textContent = 'Pick 3 to 5 tags. This builds your custom dashboard feed.';
                } else if (count >= 3 && count < 5) {
                    counter.textContent = `${count} selected`;
                    counter.className = counterBase + " text-emerald-700 bg-emerald-50 border-emerald-200";
                    hint.textContent = count === 4 ? 'Looking good. You can add one more.' : 'Looking good. You can add up to 2 more.';
                } else if (count === 5) {
                    counter.textContent = 'Max reached';
                    counter.className = counterBase + " text-amber-700 bg-amber-50 border-amber-200";
                    hint.textContent = 'Remove a tag if you want to swap one out.';
                } else {
                    counter.textContent = `${count} selected`;
                    counter.className = counterBase + " text-[#3A3B3A] bg-[#E9E9E7] border-[#E1E1DE]";
                    hint.textContent = `Pick ${3 - count} more to get better recommendations.`;
                }

                segments.forEach((seg, i) => {
                    seg.classList.toggle('bg-[#252625]', i < count);
                    seg.classList.toggle('bg-[#E1E1DE]', i >= count);
                });

                tags.forEach(tag => {
                    const checkbox = tag.querySelector('.interest-checkbox');
                    const iconPlus = tag.querySelector('.tag-icon-plus');
                    const iconCheck = tag.querySelector('.tag-icon-check');

                    if (checkbox.checked) {
                        tag.classList.remove(...UNSELECTED, ...HOVER, ...CAPPED);
                        tag.classList.add(...SELECTED);
                        iconPlus.classList.add('hidden');
                        iconCheck.classList.remove('hidden');
                    } else {
                        tag.classList.remove(...SELECTED);
                        tag.classList.add(...UNSELECTED);
                        iconPlus.classList.remove('hidden');
                        iconCheck.classList.add('hidden');

                        if (count >= 5) {
                            tag.classList.add(...CAPPED);
                            tag.classList.remove(...HOVER);
                        } else {
                            tag.classList.remove(...CAPPED);
                            tag.classList.add(...HOVER);
                        }
                    }
                });

                renderTray(checkedBoxes);
            };

            syncTagStates();

            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                let visible = 0;

                tags.forEach(tag => {
                    const name = tag.getAttribute('data-interest-name');
                    if (name.includes(query)) {
                        tag.style.display = 'inline-flex';
                        visible++;
                    } else {
                        tag.style.display = 'none';
                    }
                });

                emptyNotice.classList.toggle('hidden', visible > 0);
            });

            // Enter inside the search box should not submit the form
            searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });

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

            // Open on the right layer (layer 2 if the server only complained about interests)
            showStep(currentStep);
        });
    </script>
</x-guest-layout>