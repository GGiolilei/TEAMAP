<x-guest-layout>
    <!-- Brand Logo -->
    <div class="flex justify-center mb-6">
        <a href="/">
            <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-20 h-20 object-contain">
        </a>
    </div>

    <div class="text-center mb-6">
        <h1 class="text-xl font-bold tracking-tight text-[#202120]">Lorem ipsum dolor</h1>
        <p class="mt-1 text-sm text-[#747674]">Sit amet, consectetur adipiscing elit.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-sm text-[#3A3B3A]" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#3A3B3A]">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] placeholder-[#9A9C9A] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#3A3B3A]">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="block mt-1.5 w-full bg-white border border-[#E1E1DE] text-[#242524] placeholder-[#9A9C9A] text-sm py-2.5 px-3.5 rounded-xl shadow-none focus:border-[#202120] focus:ring-1 focus:ring-[#202120] focus:outline-none transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-[#E1E1DE] bg-white text-[#202120] shadow-none focus:ring-[#202120]/20 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                <span class="ms-2 text-sm text-[#747674]">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-[#747674] hover:text-[#202120] underline underline-offset-4 rounded-md focus:outline-none transition" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @else
                <span></span>
            @endif

            <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold bg-[#252625] text-white rounded-xl hover:bg-[#3A3B3A] transition">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>