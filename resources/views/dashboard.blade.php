<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold">Total Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $stats['total_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold">New Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $stats['new_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold">Ongoing Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $stats['ongoing_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold">Completed Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $stats['completed_tasks'] }}</p>
                </div>
            </div>

            @if($user->isSuperAdmin())
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold">Total Users</h3>
                        <p class="mt-4 text-3xl font-bold">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold">Active Users</h3>
                        <p class="mt-4 text-3xl font-bold">{{ $stats['active_users'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold">Inactive Users</h3>
                        <p class="mt-4 text-3xl font-bold">{{ $stats['inactive_users'] }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
