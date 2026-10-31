<x-app-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 py-8 pb-12 sm:px-6 lg:px-8">

        <div class="flex flex-col gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-2)] transition hover:text-[var(--black)]">
                ← Back to Dashboard
            </a>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[var(--black)]">Profile & verification</h1>
                <p class="mt-1 text-sm text-[var(--text-2)]">
                    Manage your profile photo and upload a CV to apply for project lobbies.
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
                <form method="POST" action="{{ route('profile.cv.update') }}" enctype="multipart/form-data" class="space-y-6">
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

                    <hr class="border-[var(--border)]" />

                    {{-- VERIFICATION STATUS --}}
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-[var(--border)] bg-[var(--bg)] p-4">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 rounded-xl p-2.5 {{ auth()->user()->profile?->cv_path ? 'border border-[var(--success)]/20 bg-[var(--success-bg)] text-[var(--success)]' : 'border border-[var(--warning)]/20 bg-[var(--warning-bg)] text-[var(--warning)]' }}">
                                @if(auth()->user()->profile?->cv_path)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-2)]">Verification status</div>
                                <h4 class="mt-0.5 text-xs font-bold {{ auth()->user()->profile?->cv_path ? 'text-[var(--success)]' : 'text-[var(--warning)]' }}">
                                    {{ auth()->user()->profile?->cv_path ? 'CV uploaded' : 'No CV uploaded' }}
                                </h4>
                            </div>
                        </div>

                        @if(auth()->user()->profile?->cv_path)
                            <a href="{{ asset('storage/' . auth()->user()->profile->cv_path) }}" target="_blank"
                               class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 text-[11px] font-semibold text-[var(--text)] transition hover:bg-[var(--hover)]">
                                Review file ↗
                            </a>
                        @endif
                    </div>

                    {{-- CV UPLOAD --}}
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Upload CV document</label>

                        <div class="group relative cursor-pointer rounded-2xl border-2 border-dashed border-[var(--border)] bg-[var(--bg)] p-6 text-center transition hover:border-[var(--btn)]">
                            <input type="file" name="cv" id="cv_input_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                            <div class="space-y-2 pointer-events-none">
                                <div class="mx-auto flex h-8 w-8 items-center justify-center text-[var(--muted)] transition group-hover:text-[var(--black)]">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/></svg>
                                </div>
                                <div class="text-xs font-medium text-[var(--text)]">
                                    <span class="font-semibold text-[var(--black)] underline underline-offset-2">Click to browse</span> or drag document here
                                </div>
                                <div class="text-[10px] text-[var(--muted)]">Supported formats: PDF, DOCX (max size: 5MB)</div>
                            </div>
                        </div>

                        <div id="file_name_display" class="mt-2 hidden text-xs font-medium text-[var(--text-2)]"></div>
                        <x-input-error class="mt-2 text-xs text-[var(--error)]" :messages="$errors->get('cv')" />
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
        document.getElementById('cv_input_file').addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name;
            const displayDiv = document.getElementById('file_name_display');
            if (fileName) {
                displayDiv.textContent = `Selected Document: ${fileName}`;
                displayDiv.classList.remove('hidden');
            } else {
                displayDiv.classList.add('hidden');
            }
        });

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