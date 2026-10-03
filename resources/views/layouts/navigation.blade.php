<nav x-data="{ open: false }" class="bg-white border-b border-[#E1E1DE]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center">
                    <img src="{{ asset('image/teamapio.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="h-8 w-auto object-contain">
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex space-x-1">
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-medium px-4 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-[#E5E5E2] text-[#202120]' : 'text-[#747674] hover:bg-[#EEEEEC] hover:text-[#202120]' }}">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div x-data="{ dd: false }" @click.outside="dd = false" @keydown.escape.window="dd = false" class="relative">
                    <button @click="dd = ! dd"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl text-[#3A3B3A] bg-white border border-[#E1E1DE] hover:bg-[#EEEEEC] focus:outline-none transition">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="fill-current h-4 w-4 text-[#9A9C9A]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="dd" x-cloak x-transition.opacity.duration.150ms
                         class="absolute right-0 mt-2 w-48 bg-white border border-[#E1E1DE] rounded-xl shadow-sm py-1 z-50">
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-[#3A3B3A] hover:bg-[#EEEEEC] transition">
                            {{ __('Profile') }}
                        </a>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block px-4 py-2 text-sm text-[#3A3B3A] hover:bg-[#EEEEEC] transition">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-[#747674] hover:text-[#202120] hover:bg-[#EEEEEC] focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-[#E1E1DE]">
        <div class="pt-2 pb-3 px-2 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-[#E5E5E2] text-[#202120]' : 'text-[#747674] hover:bg-[#EEEEEC] hover:text-[#202120]' }}">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-2 border-t border-[#E1E1DE]">
            <div class="px-5">
                <div class="font-semibold text-sm text-[#242524]">{{ Auth::user()->name }}</div>
                <div class="text-xs text-[#9A9C9A]">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 px-2 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 text-sm font-medium text-[#747674] rounded-lg hover:bg-[#EEEEEC] hover:text-[#202120] transition">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block px-3 py-2 text-sm font-medium text-[#747674] rounded-lg hover:bg-[#EEEEEC] hover:text-[#202120] transition">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>