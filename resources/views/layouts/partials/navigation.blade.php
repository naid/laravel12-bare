{{-- Navigation Bar --}}
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left side: Logo and Navigation Links --}}
            <div class="flex">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800 hover:text-blue-600 transition">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                {{-- Navigation Links (Desktop) --}}
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    @auth
                        {{-- Uncomment when you create dashboard route:
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium transition">
                            Dashboard
                        </a>
                        --}}
                        
                        {{-- Add more navigation links here --}}
                        {{-- Example:
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium transition">
                            Users
                        </a>
                        --}}
                    @else
                        <a href="{{ url('/') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('/') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium transition">
                            Home
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Right side: User Menu --}}
            <div class="flex items-center">
                @auth
                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.away="open = false"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition">
                            <span class="mr-2">{{ Auth::user()->name }}</span>
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                {{-- Uncomment when you create dashboard route:
                                <a href="{{ route('dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Dashboard
                                </a>
                                --}}
                                
                                {{-- Add more dropdown items here --}}
                                {{-- Example:
                                <a href="{{ route('profile.edit') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <a href="{{ route('settings') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Settings
                                </a>
                                --}}
                                
                                <div class="border-t border-gray-100"></div>
                                
                                {{-- Uncomment when you create logout route:
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                                --}}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Guest Links --}}
                    <div class="flex space-x-4">
                        {{-- Uncomment when you create login route:
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
                            Login
                        </a>
                        --}}
                        {{-- Uncomment if you have registration:
                        <a href="{{ route('register') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                            Register
                        </a>
                        --}}
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Mobile menu, show/hide based on menu state --}}
    <div class="sm:hidden" x-data="{ mobileMenuOpen: false }">
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"/>
            </svg>
        </button>

        <div x-show="mobileMenuOpen" class="px-2 pt-2 pb-3 space-y-1" style="display: none;">
            @auth
                {{-- Uncomment when you create dashboard route:
                <a href="{{ route('dashboard') }}" 
                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Dashboard
                </a>
                --}}
            @else
                <a href="{{ url('/') }}" 
                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Home
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- Alpine.js for dropdown functionality --}}
@once
    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
@endonce

