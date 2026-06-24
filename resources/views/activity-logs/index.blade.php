<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Activity Logs</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Activity Logs</h3>
                    <form method="GET" action="{{ route('activity-logs.index') }}" class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                            <select name="user" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request()->query('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" name="date" value="{{ request()->query('date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type</label>
                            <input type="text" name="activity" value="{{ request()->query('activity') }}" placeholder="Search activity..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">System Activity</h3>
                    <p class="text-sm text-gray-500 mt-1">Audit trail of all system events</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">User</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Action</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Description</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Date</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-orange-600 font-semibold">{{ $activity->user ? substr($activity->user->name, 0, 2) : 'SY' }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $activity->user ? $activity->user->name : 'System' }}</div>
                                                <div class="text-sm text-gray-500">{{ $activity->user ? $activity->user->email : 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $activity->action }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 max-w-xs truncate">{{ $activity->action }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $activity->created_at->format('M d, Y') }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $activity->created_at->format('g:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($activities->hasPages())
                    <div class="p-6 border-t border-gray-200">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
