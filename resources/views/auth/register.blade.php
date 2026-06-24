<x-guest-layout>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-500 rounded-xl mb-4">
            <svg width="22" height="22" viewBox="0 0 18 18" fill="none">
                <path d="M3 9L7 13L15 5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-1">TaskFlow</h1>
        <p class="text-gray-500 text-sm">Create your account</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <input id="name" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your name">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <input id="email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Enter your email">
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
                    type="password" name="password" required autocomplete="new-password" placeholder="Create a password">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <input id="password_confirmation" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <button type="submit" class="w-full bg-orange-500 text-white py-3 px-6 rounded-lg font-medium hover:bg-orange-600 transition-colors flex items-center justify-center gap-2 shadow-sm shadow-orange-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Create Account
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-semibold">Login</a>
    </div>

    <p class="mt-4 text-center text-xs text-gray-400">
        Your account will be created as a regular user. Managers and admins are assigned by existing administrators.
    </p>
</x-guest-layout>