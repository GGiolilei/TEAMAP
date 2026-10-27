<x-app-layout>
    {{-- SPLASH (0.5s hold, then fades out; pure CSS so it can never get stuck) --}}
    <div id="splash" aria-hidden="true">
        <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'TeaMap') }}" class="splash-logo w-20 h-20 object-contain">
    </div>

    <style>
        #splash {
            position: fixed; inset: 0; z-index: 100;
            background: #F7F7F5;
            display: flex; align-items: center; justify-content: center;
            animation: splashOut .35s ease .5s forwards;
        }
        .splash-logo { animation: logoIn .45s cubic-bezier(.16,1,.3,1) both; }
        @keyframes logoIn  { from { opacity: 0; transform: scale(.85); } to { opacity: 1; transform: scale(1); } }
        @keyframes splashOut { to { opacity: 0; visibility: hidden; } }

        .rise { opacity: 0; animation: rise .5s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="py-10 bg-[#F7F7F5] min-h-screen text-[#242524]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div class="rise" style="animation-delay:.55s">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#747674] hover:text-[#202120] transition inline-flex items-center gap-1.5 mb-3">
                    ← Back to Dashboard
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-[#202120]">Profile</h2>
                <p class="text-sm text-[#747674] mt-0.5">Manage your photo and see how you appear to other members.</p>
            </div>

            @if(session('success'))
                <div id="flash-success" class="flex items-center gap-2.5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium transition-opacity duration-500">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form id="profile-form" method="POST" action="{{ route('profile.cv.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- IDENTITY CARD --}}
                <div class="rise bg-white border border-[#E1E1DE] rounded-2xl p-6" style="animation-delay:.65s">
                    <div class="flex items-center gap-4">
                        <div class="relative shrink-0 w-14 h-14 rounded-2xl overflow-hidden bg-[#E9E9E7] border border-[#E1E1DE] flex items-center justify-center font-bold text-base text-[#3A3B3A] uppercase">
                            @if(auth()->user()->profile?->avatar_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover" />
                            @else
                                {{ substr(auth()->user()->name, 0, 2) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-[#202120] truncate">{{ auth()->user()->name }}</h3>
                            <p class="text-xs text-[#747674] truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    @if(auth()->user()->interests->count())
                        <div class="mt-4 pt-4 border-t border-[#E1E1DE] flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] font-semibold text-[#9A9C9A] uppercase tracking-wider mr-1">Focus</span>
                            @foreach(auth()->user()->interests as $interest)
                                <span class="px-2.5 py-1 text-[11px] rounded-full bg-[#F4F4F2] border border-[#E1E1DE] text-[#3A3B3A]">{{ $interest->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- AVATAR --}}
                <div class="rise bg-white border border-[#E1E1DE] rounded-2xl p-6" style="animation-delay:.75s">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-[#202120]">Profile photo</h3>
                            <p class="text-xs text-[#747674] mt-0.5">Shown on your dashboard, in chats, and on task cards.</p>
                        </div>
                        <span id="change-badge" class="hidden text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">Unsaved</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5">

                        {{-- Preview --}}
                        <div class="relative shrink-0">
                            <div class="w-28 h-28 rounded-3xl overflow-hidden bg-[#F4F4F2] border border-[#E1E1DE] flex items-center justify-center font-bold text-2xl text-[#747674] uppercase">
                                <img id="avatar_preview"
                                     src="{{ auth()->user()->profile?->avatar_path ? asset('storage/' . auth()->user()->profile->avatar_path) : '#' }}"
                                     alt="Avatar Preview"
                                     class="w-full h-full object-cover {{ auth()->user()->profile?->avatar_path ? '' : 'hidden' }}" />
                                <div id="avatar_placeholder" class="flex items-center justify-center w-full h-full {{ auth()->user()->profile?->avatar_path ? 'hidden' : '' }}">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                            </div>
                        </div>

                        {{-- Drop zone --}}
                        <div class="flex-1 w-full">
                            <div id="drop-zone" class="relative border-2 border-dashed border-[#E1E1DE] hover:border-[#9A9C9A] bg-[#F7F7F5] rounded-2xl px-5 py-6 text-center transition cursor-pointer">
                                <input type="file" name="avatar" id="avatar_input_file" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                <div class="pointer-events-none space-y-1.5">
                                    <svg class="w-6 h-6 mx-auto text-[#9A9C9A]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21zM14.25 8.25h.008v.008h-.008V8.25z"/></svg>
                                    <p class="text-xs font-medium text-[#3A3B3A]">
                                        <span class="text-[#202120] font-semibold underline underline-offset-2">Click to upload</span> or drag a photo here
                                    </p>
                                    <p class="text-[10px] text-[#9A9C9A]">JPEG, PNG or WEBP · square works best · max 2MB</p>
                                </div>
                            </div>

                            {{-- Selected file row --}}
                            <div id="file-row" class="hidden mt-3 flex items-center justify-between gap-3 px-3.5 py-2.5 bg-white border border-[#E1E1DE] rounded-xl">
                                <div class="min-w-0">
                                    <p id="file-name" class="text-xs font-semibold text-[#202120] truncate"></p>
                                    <p id="file-size" class="text-[10px] text-[#9A9C9A]"></p>
                                </div>
                                <button type="button" id="file-remove" class="shrink-0 text-xs font-semibold text-[#747674] hover:text-rose-500 transition">Remove</button>
                            </div>

                            <p id="client-error" class="hidden mt-2 text-xs text-rose-500"></p>
                            <x-input-error class="mt-2 text-xs text-rose-500" :messages="$errors->get('avatar')" />
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="rise flex items-center justify-end gap-3" style="animation-delay:.85s">
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#747674] hover:text-[#202120] hover:bg-[#EEEEEC] transition bg-white border border-[#E1E1DE] px-4 py-2.5 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" id="save-btn" disabled
                            class="inline-flex items-center gap-2 bg-[#252625] hover:bg-[#3A3B3A] text-white font-semibold text-sm py-2.5 px-5 rounded-xl transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">
                        <svg id="save-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        <span id="save-label">Save Changes</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        (function () {
            const MAX_BYTES = 2 * 1024 * 1024;
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

            const form = document.getElementById('profile-form');
            const input = document.getElementById('avatar_input_file');
            const preview = document.getElementById('avatar_preview');
            const placeholder = document.getElementById('avatar_placeholder');
            const zone = document.getElementById('drop-zone');
            const fileRow = document.getElementById('file-row');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const removeBtn = document.getElementById('file-remove');
            const errorEl = document.getElementById('client-error');
            const badge = document.getElementById('change-badge');
            const saveBtn = document.getElementById('save-btn');
            const spinner = document.getElementById('save-spinner');
            const saveLabel = document.getElementById('save-label');

            // remember the original state so "Remove" can restore it
            const originalSrc = preview.getAttribute('src');
            const hadAvatar = !preview.classList.contains('hidden');

            const fmt = (b) => b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';

            const showError = (msg) => { errorEl.textContent = msg; errorEl.classList.remove('hidden'); };
            const clearError = () => errorEl.classList.add('hidden');

            const reset = () => {
                input.value = '';
                fileRow.classList.add('hidden');
                badge.classList.add('hidden');
                saveBtn.disabled = true;
                if (hadAvatar) {
                    preview.src = originalSrc;
                } else {
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            };

            input.addEventListener('change', (e) => {
                clearError();
                const [file] = e.target.files;
                if (!file) { reset(); return; }

                if (!ALLOWED.includes(file.type)) {
                    reset(); showError('Please choose a JPEG, PNG or WEBP image.'); return;
                }
                if (file.size > MAX_BYTES) {
                    reset(); showError('That photo is ' + fmt(file.size) + '. The limit is 2 MB.'); return;
                }

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');

                fileName.textContent = file.name;
                fileSize.textContent = fmt(file.size);
                fileRow.classList.remove('hidden');

                badge.classList.remove('hidden');
                saveBtn.disabled = false;
            });

            removeBtn.addEventListener('click', () => { clearError(); reset(); });

            // drag highlight
            ['dragenter', 'dragover'].forEach(ev => zone.addEventListener(ev, () => {
                zone.classList.add('border-[#202120]', 'bg-[#EEEEEC]');
            }));
            ['dragleave', 'drop'].forEach(ev => zone.addEventListener(ev, () => {
                zone.classList.remove('border-[#202120]', 'bg-[#EEEEEC]');
            }));

            // loading state on submit
            form.addEventListener('submit', () => {
                saveBtn.disabled = true;
                spinner.classList.remove('hidden');
                saveLabel.textContent = 'Saving...';
            });

            // auto-dismiss the success message
            const flash = document.getElementById('flash-success');
            if (flash) {
                setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 500); }, 4000);
            }
        })();
    </script>
</x-app-layout>