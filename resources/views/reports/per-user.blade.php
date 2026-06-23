<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Per User Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">Back to Reports</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200 mb-6">
                <form method="GET" action="{{ route('reports.per-user') }}">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                    <select name="user_id" id="user_id" class="block w-full rounded-lg border-gray-300 border px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Select a user</option>
                        @foreach($users as $availableUser)
                            <option value="{{ $availableUser->id }}" {{ request('user_id') == $availableUser->id ? 'selected' : '' }}>{{ $availableUser->name }} ({{ $availableUser->email }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-4 inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-500 hover:bg-orange-600 shadow-md hover:shadow-lg transition-all">Generate Report</button>
                </form>
            </div>

            @if($user && $userStats)
                <div class="grid gap-6 md:grid-cols-4 mb-6">
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold">Total Tasks</h3>
                        <p class="mt-4 text-3xl font-bold">{{ $userStats['total_tasks'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold">Completed Tasks</h3>
                        <p class="mt-4 text-3xl font-bold text-green-600">{{ $userStats['completed_tasks'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold">Ongoing Tasks</h3>
                        <p class="mt-4 text-3xl font-bold text-yellow-600">{{ $userStats['ongoing_tasks'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold">Overdue Tasks</h3>
                        <p class="mt-4 text-3xl font-bold text-red-600">{{ $userStats['overdue_tasks'] }}</p>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Productivity Summary</h3>
                    @if($userStats['total_tasks'] > 0)
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                                    <span class="text-sm font-medium text-gray-700">{{ round(($userStats['completed_tasks'] / $userStats['total_tasks']) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($userStats['completed_tasks'] / $userStats['total_tasks']) * 100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Overdue Rate</span>
                                    <span class="text-sm font-medium text-gray-700">{{ round(($userStats['overdue_tasks'] / $userStats['total_tasks']) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ ($userStats['overdue_tasks'] / $userStats['total_tasks']) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600">No tasks found for this user.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
