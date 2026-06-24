<nav x-data="{ open: false, notificationsOpen: false }" class="bg-white border-b border-gray-200">
    @php
        $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->count();
        $notifications = auth()->user()->notifications()->latest()->limit(5)->get();
    @endphp
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
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Users') }}
                        </a>
                        <a href="{{ route('managers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('managers.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Managers') }}
                        </a>
                        <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('activity-logs.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Activity Logs') }}
                        </a>
                    @endif
                    @if(auth()->user()->isManager())
                        <a href="{{ route('requests.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('requests.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Requests') }}
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('reports.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Reports') }}
                        </a>
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tasks.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Tasks') }}
                        </a>
                    @endif
                    @if(auth()->user()->isUser())
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tasks.*') ? 'border-orange-500 text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }}">
                            {{ __('Tasks') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Side: Notifications & User -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notification Bell -->
                <div class="relative mr-4">
                    <button @click="notificationsOpen = ! notificationsOpen" class="relative inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-orange-500 px-1.5 py-0.5 text-xs font-semibold text-white">
                                {{ $unreadNotifications }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationsOpen" @click.away="notificationsOpen = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                        </div>
                        @if($notifications->count() > 0)
                            <div class="max-h-64 overflow-y-auto">
                                @foreach($notifications as $notification)
                                    <a href="{{ $notification->link ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 @if(!$notification->is_read) bg-orange-50 @endif">
                                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                @endforeach
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200">
                                <a href="{{ route('notifications.index') }}" class="block text-sm text-orange-600 hover:text-orange-800 font-medium">View All Notifications</a>
                            </div>
                        @else
                            <div class="px-4 py-4 text-center text-gray-500 text-sm">
                                No notifications
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Settings Dropdown -->
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
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('users.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Users') }}
                </a>
                <a href="{{ route('managers.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('managers.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Managers') }}
                </a>
                <a href="{{ route('activity-logs.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('activity-logs.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Activity Logs') }}
                </a>
            @endif
            @if(auth()->user()->isManager())
                <a href="{{ route('requests.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('requests.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Requests') }}
                </a>
                <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('reports.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Reports') }}
                </a>
                <a href="{{ route('tasks.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('tasks.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Tasks') }}
                </a>
            @endif
            @if(auth()->user()->isUser())
                <a href="{{ route('tasks.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('tasks.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                    {{ __('Tasks') }}
                </a>
            @endif
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2 text-base font-medium {{ request()->routeIs('notifications.*') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                <span>{{ __('Notifications') }}</span>
                @if($unreadNotifications > 0)
                    <span class="inline-flex items-center justify-center rounded-full bg-orange-500 px-2 py-0.5 text-xs font-semibold text-white">{{ $unreadNotifications }}</span>
                @endif
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
