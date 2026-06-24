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

            @if($user->isUser())
                <div class="mt-6 bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Tasks</h3>
                    @if($recentTasks->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentTasks as $task)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $task->title }}</p>
                                        <p class="text-sm text-gray-500">Due: {{ $task->due_date }} {{ $task->due_time }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' :
                                            ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-700' :
                                            ($task->status === 'new' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-red-100 text-red-700')) }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                        <a href="{{ route('tasks.show', $task) }}" class="text-orange-600 hover:text-orange-800 font-medium text-sm">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No recent tasks found.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
