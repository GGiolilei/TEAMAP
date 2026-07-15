<x-app-layout>
    <div class="py-10 bg-slate-950 min-h-screen text-slate-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-indigo-400 transition inline-flex items-center gap-1.5 mb-2">
                    ← Back to Dashboard
                </a>
                <h2 class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">
                    Profile Configuration & Verification
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">
                    Update your operative profile identity card and upload verification records for active team deployment.
                </p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl relative">

                <form method="POST" action="{{ route('profile.cv.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- AVATAR --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-3">Profile Avatar</label>
                        <div class="flex items-center gap-5 p-4 bg-slate-950/40 border border-slate-800/80 rounded-2xl">

                            <div class="relative shrink-0 w-16 h-16 rounded-2xl overflow-hidden bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center font-bold text-lg text-indigo-400 uppercase tracking-wider">
                                @if(auth()->user()->profile?->avatar_path)
                                    <img id="avatar_preview" src="{{ asset('storage/' . auth()->user()->profile->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover" />
                                @else
                                    <div id="avatar_placeholder" class="flex items-center justify-center w-full h-full">
                                        {{ substr(auth()->user()->name, 0, 2) }}
                                    </div>
                                    <img id="avatar_preview" src="#" alt="Avatar Preview" class="w-full h-full object-cover hidden" />
                                @endif
                            </div>

                            <div class="space-y-1.5 flex-1">
                                <div class="relative inline-block">
                                    <input type="file" name="avatar" id="avatar_input_file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                    <button type="button" class="text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 px-3 py-2 rounded-xl transition">
                                        Choose New Photo
                                    </button>
                                </div>
                                <div class="text-[11px] text-slate-500">Supported Formats: JPEG, PNG, WEBP (Square ratio preferred, Max: 2MB)</div>
                                <x-input-error class="text-xs text-rose-400" :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-800/80" />

                    {{-- VERIFICATION STATUS --}}
                    <div class="bg-slate-950/40 border border-slate-800/80 p-4 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl shrink-0 {{ auth()->user()->profile?->cv_path ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                @if(auth()->user()->profile?->cv_path)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Verification Status</div>
                                <h4 class="text-xs font-bold {{ auth()->user()->profile?->cv_path ? 'text-emerald-400' : 'text-amber-400' }} mt-0.5">
                                    {{ auth()->user()->profile?->cv_path ? 'CV Record Verified' : 'No CV Found' }}
                                </h4>
                            </div>
                        </div>

                        @if(auth()->user()->profile?->cv_path)
                            <a href="{{ asset('storage/' . auth()->user()->profile->cv_path) }}" target="_blank"
                               class="text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 bg-slate-900 border border-slate-800/80 px-3 py-1.5 rounded-lg transition shadow-inner">
                                Review File ↗
                            </a>
                        @endif
                    </div>

                    {{-- CV UPLOAD --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Upload Verification CV Document</label>

                        <div class="border-2 border-dashed border-slate-800 hover:border-indigo-500/50 bg-slate-950/40 rounded-2xl p-6 text-center transition cursor-pointer relative group">
                            <input type="file" name="cv" id="cv_input_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                            <div class="space-y-2 pointer-events-none">
                                <div class="mx-auto w-8 h-8 text-slate-500 group-hover:text-indigo-400 transition flex items-center justify-center">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/></svg>
                                </div>
                                <div class="text-xs font-medium text-slate-300">
                                    <span class="text-indigo-400 font-semibold">Click to browse</span> or drag document here
                                </div>
                                <div class="text-[10px] text-slate-500">Supported Formats: PDF, DOCX (Max size: 5MB)</div>
                            </div>
                        </div>

                        <div id="file_name_display" class="mt-2 text-xs text-indigo-400 hidden font-medium"></div>
                        <x-input-error class="mt-2 text-xs text-rose-400" :messages="$errors->get('cv')" />
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-slate-200 transition bg-slate-800/40 border border-slate-800/60 px-4 py-2.5 rounded-xl">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm py-2.5 px-5 rounded-xl transition duration-200 shadow-md shadow-indigo-950/40">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

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