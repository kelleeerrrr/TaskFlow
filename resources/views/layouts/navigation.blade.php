<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-black">TaskFlow</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tasks.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                        {{ __('Tasks') }}
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Users') }}
                        </a>
                    @endif
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager())
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('reports.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Reports') }}
                        </a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('notifications.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                        {{ __('Notifications') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative">
                    <button @click="open = ! open" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-black bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <div>{{ Auth::user()->name }}</div>
                        <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-black hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('tasks.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('tasks.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                {{ __('Tasks') }}
            </a>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('users.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Users') }}
                </a>
            @endif
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager())
                <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('reports.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Reports') }}
                </a>
            @endif
            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('notifications.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                {{ __('Notifications') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-black">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-black">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-black">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
