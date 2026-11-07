<x-app-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 py-8 pb-12 sm:px-6 lg:px-8">

        <div class="flex flex-col gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-2)] transition hover:text-[var(--black)]">
                ← Back to Dashboard
            </a>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[var(--black)]">Profile settings</h1>
                <p class="mt-1 text-sm text-[var(--text-2)]">
                    Manage your profile photo.
                </p>
            </div>
        </div>

            @if(session('success'))
                <div role="status" class="rounded-2xl bg-[var(--success-bg)] px-4 py-3.5 text-sm font-medium text-[var(--success)]">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="rounded-2xl bg-[var(--error-bg)] px-4 py-3.5 text-sm font-medium text-[var(--error)]">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 sm:p-7">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- AVATAR --}}
                    <div>
                        <label class="mb-3 block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Profile avatar</label>
                        <div class="flex items-center gap-5 rounded-2xl border border-[var(--border)] bg-[var(--bg)] p-4">

                            <div class="relative flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--bubble-me)] text-lg font-bold uppercase tracking-wider text-[var(--black)]">
                                @if(auth()->user()->profile?->avatar_path)
                                    <img id="avatar_preview" src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover" />
                                @else
                                    <div id="avatar_placeholder" class="flex items-center justify-center w-full h-full">
                                        {{ substr(auth()->user()->name, 0, 2) }}
                                    </div>
                                    <img id="avatar_preview" src="#" alt="Avatar Preview" class="w-full h-full object-cover hidden" />
                                @endif
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <div class="relative inline-block">
                                    <input type="file" name="avatar" id="avatar_input_file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                    <button type="button" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-xs font-semibold text-[var(--text)] transition hover:bg-[var(--hover)]">
                                        Choose new photo
                                    </button>
                                </div>
                                <div class="text-[11px] text-[var(--muted)]">Supported formats: JPEG, PNG, WEBP (square ratio preferred, max 2MB)</div>
                                <x-input-error class="text-xs text-[var(--error)]" :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-[var(--border)] pt-4">
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-xs font-semibold text-[var(--text-2)] transition hover:bg-[var(--hover)] hover:text-[var(--text)]">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-xl bg-[var(--btn)] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--black-2)]">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>

    </div>

    <script>
        document.getElementById('avatar_input_file').addEventListener('change', function (e) {
            const [file] = e.target.files;
            if (!file) return;

            const previewImg = document.getElementById('avatar_preview');
            const placeholderDiv = document.getElementById('avatar_placeholder');

            previewImg.src = URL.createObjectURL(file);
            previewImg.classList.remove('hidden');
            placeholderDiv?.classList.add('hidden');
        });
    </script>
</x-app-layout>