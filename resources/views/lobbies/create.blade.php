<x-app-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 py-8 pb-12 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2">
                <a href="{{ route('dashboard') }}" class="mb-1 inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-2)] transition hover:text-[var(--black)]">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-extrabold tracking-tight text-[var(--black)]">
                    Start a New Project Lobby
                </h1>
                <p class="mt-1 text-sm text-[var(--text-2)]">
                    Set up your collaboration space, outline your goals, and discover the right teammates to bring your concept to life.
                </p>
            </div>

            <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 sm:p-7">

                <form method="POST" action="{{ route('lobbies.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Lobby title</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                   class="mt-1.5 block w-full rounded-xl border border-[var(--border)] bg-[var(--bg)] px-3.5 py-2.5 text-sm text-[var(--text)] placeholder:text-[var(--muted)] focus:border-[var(--btn)] focus:ring-[var(--btn)]"
                                   placeholder="e.g., EdTech AI Platform" />
                            <x-input-error class="mt-1 text-xs text-[var(--error)]" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label for="project_goal" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Primary milestone / goal</label>
                            <input id="project_goal" name="project_goal" type="text" value="{{ old('project_goal') }}" required
                                   class="mt-1.5 block w-full rounded-xl border border-[var(--border)] bg-[var(--bg)] px-3.5 py-2.5 text-sm text-[var(--text)] placeholder:text-[var(--muted)] focus:border-[var(--btn)] focus:ring-[var(--btn)]"
                                   placeholder="e.g., Build functional MVP" />
                            <x-input-error class="mt-1 text-xs text-[var(--error)]" :messages="$errors->get('project_goal')" />
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Project overview & mission</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="mt-1.5 block w-full rounded-xl border border-[var(--border)] bg-[var(--bg)] p-3.5 text-sm leading-relaxed text-[var(--text)] placeholder:text-[var(--muted)] focus:border-[var(--btn)] focus:ring-[var(--btn)]"
                                  placeholder="Describe the problem you're solving, your target audience, and what makes your startup concept exciting...">{{ old('description') }}</textarea>
                        <x-input-error class="mt-1 text-xs text-[var(--error)]" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="max_members" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Maximum team size</label>
                            <select id="max_members" name="max_members" required
                                    class="mt-1.5 block w-full cursor-pointer rounded-xl border border-[var(--border)] bg-[var(--bg)] px-3.5 py-2.5 text-sm text-[var(--text)] focus:border-[var(--btn)] focus:ring-[var(--btn)]">
                                <option value="2" {{ old('max_members', 5) == 2 ? 'selected' : '' }}>2 Members (Small Duo)</option>
                                <option value="3" {{ old('max_members', 5) == 3 ? 'selected' : '' }}>3 Members (Core Pod)</option>
                                <option value="4" {{ old('max_members', 5) == 4 ? 'selected' : '' }}>4 Members (Standard Setup)</option>
                                <option value="5" {{ old('max_members', 5) == 5 ? 'selected' : '' }}>5 Members (Recommended Max)</option>
                                <option value="6" {{ old('max_members', 5) == 6 ? 'selected' : '' }}>6 Members (Large Assembly)</option>
                            </select>
                            <x-input-error class="mt-1 text-xs text-[var(--error)]" :messages="$errors->get('max_members')" />
                        </div>

                        <div>
                            <label for="required_roles" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Looking for roles</label>
                            <input id="required_roles" name="required_roles" type="text" value="{{ old('required_roles') }}"
                                   class="mt-1.5 block w-full rounded-xl border border-[var(--border)] bg-[var(--bg)] px-3.5 py-2.5 text-sm text-[var(--text)] placeholder:text-[var(--muted)] focus:border-[var(--btn)] focus:ring-[var(--btn)]"
                                   placeholder="e.g., Frontend Dev, UX Designer, Marketer" />
                            <x-input-error class="mt-1 text-xs text-[var(--error)]" :messages="$errors->get('required_roles')" />
                        </div>
                    </div>

                    <div class="border-t border-[var(--border)] pt-5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-2)]">Project categories & focus tags</label>
                        <p class="mb-3 mt-0.5 text-xs text-[var(--text-2)]">
                            Select related tags so people with matching interest profiles can discover your lobby instantly.
                        </p>
                        
                        <div class="grid max-h-48 grid-cols-2 gap-2 overflow-y-auto rounded-xl border border-[var(--border)] bg-[var(--bg)] p-2 sm:grid-cols-3">
                            @foreach(\App\Models\Interest::all() as $interest)
                                <label class="group flex cursor-pointer items-center space-x-2.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-2.5 transition hover:bg-[var(--hover)]">
                                    <input type="checkbox" name="interests[]" value="{{ $interest->id }}"
                                           {{ is_array(old('interests')) && in_array($interest->id, old('interests')) ? 'checked' : '' }}
                                           class="h-4 w-4 cursor-pointer rounded border-[var(--border)] text-[var(--btn)] focus:ring-[var(--btn)]">
                                    <span class="text-xs font-medium text-[var(--text)]">{{ $interest->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2 text-xs text-[var(--error)]" :messages="$errors->get('interests')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-[var(--border)] pt-4">
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-sm font-semibold text-[var(--text-2)] transition hover:bg-[var(--hover)] hover:text-[var(--text)]">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-xl bg-[var(--btn)] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--black-2)]">
                            Launch Project Lobby
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>