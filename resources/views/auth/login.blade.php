<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">

            <!-- Logo + Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-500 rounded-xl mb-4">
                    <svg width="22" height="22" viewBox="0 0 18 18" fill="none">
                        <path d="M3 9L7 13L15 5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">TaskFlow</h1>
                <p class="text-gray-500 text-sm">Sign in to your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input id="email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input id="password" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" placeholder="Enter your password">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-orange-500 shadow-sm focus:ring-orange-500" name="remember">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <div class="flex items-center justify-between pt-1">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-500 hover:text-orange-500" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif

                    <button type="submit" class="bg-orange-500 text-white py-3 px-6 rounded-lg font-medium hover:bg-orange-600 transition-colors flex items-center gap-2 shadow-sm shadow-orange-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                        </svg>
                        Login
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="mt-6 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600 font-semibold">
                        Sign up
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>