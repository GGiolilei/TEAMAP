<x-guest-layout>
    <div
        style="--bg:#F7F7F5;--surface:#FFFFFF;--text:#242524;--text-2:#747674;--muted:#9A9C9A;--border:#E1E1DE;--hover:#EEEEEC;--selected:#E5E5E2;--btn:#252625;--black-2:#3A3B3A;--error:#D85C5C;--error-bg:#FBEAEA;--success:#4CAF72;--success-bg:#E8F5EC"
        class="text-(--text)"
    >
        <!-- Brand Logo -->
        <div class="mb-6 flex flex-col items-center text-center">
            <a href="/" class="grid h-20 w-20 place-items-center rounded-3xl border border-(--border) bg-(--surface) shadow-[0_20px_40px_-20px_rgba(32,33,32,.25)]">
                <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="h-12 w-12 object-contain">
            </a>
            <h1 class="mt-5 text-2xl font-semibold tracking-tight text-(--text)">Welcome back</h1>
            <p class="mt-1 text-sm text-(--text-2)">Log in to continue to your lobby.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 rounded-xl bg-(--success-bg) px-4 py-3 text-sm text-(--success)" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-(--text)">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="you@example.com"
                    class="block w-full rounded-xl border border-(--border) bg-(--surface) px-4 py-2.5 text-sm text-(--text) placeholder:text-(--muted) outline-none transition focus:border-(--btn) focus:ring-4 focus:ring-(--selected)">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-(--error)" />
            </div>

            <!-- Password -->
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-(--text)">{{ __('Password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-(--text-2) transition hover:text-(--text)">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full rounded-xl border border-(--border) bg-(--surface) px-4 py-2.5 text-sm text-(--text) placeholder:text-(--muted) outline-none transition focus:border-(--btn) focus:ring-4 focus:ring-(--selected)">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-(--error)" />
            </div>

            <!-- Remember Me -->
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-(--border) text-(--btn) accent-(--btn) focus:ring-(--selected)">
                <span class="text-sm text-(--text-2)">{{ __('Remember me') }}</span>
            </label>

            <!-- Log in -->
            <button type="submit"
                class="w-full rounded-xl bg-(--btn) px-4 py-3 text-sm font-medium text-white transition hover:bg-(--black-2) focus:outline-none focus:ring-4 focus:ring-(--selected)">
                {{ __('Log in') }}
            </button>
        </form>

        <!-- Register -->
        @if (Route::has('register'))
            <div class="my-6 flex items-center gap-3">
                <span class="h-px flex-1 bg-(--border)"></span>
                <span class="text-xs text-(--muted)">New here?</span>
                <span class="h-px flex-1 bg-(--border)"></span>
            </div>

            <a href="{{ route('register') }}"
                class="block w-full rounded-xl border border-(--border) bg-(--surface) px-4 py-3 text-center text-sm font-medium text-(--text) transition hover:bg-(--hover) focus:outline-none focus:ring-4 focus:ring-(--selected)">
                {{ __('Create an account') }}
            </a>
        @endif
    </div>
</x-guest-layout>