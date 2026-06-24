<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Profile</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Info Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-3xl font-bold mr-6">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Role</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ ucfirst($user->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Summary -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Task Summary</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Assigned Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalTasks }}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Completed</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">{{ $completedTasks }}</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $pendingTasks }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tasks</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($recentTasks->count() > 0)
                                @foreach($recentTasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-800' :
                                                ($task->status === 'new' ? 'bg-yellow-100 text-yellow-800' :
                                                ($task->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                                'bg-gray-100 text-gray-800'))) }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $task->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">No tasks found for this user.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
