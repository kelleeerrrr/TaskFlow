<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mt-2">Here's what's happening with your tasks today.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-3 mb-8">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-orange-600">{{ $stats['my_tasks'] }}</p>
                        </div>
                        <div class="bg-orange-500 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Pending Approval</p>
                            <p class="mt-2 text-3xl font-bold text-purple-600">{{ $stats['pending_approval'] }}</p>
                        </div>
                        <div class="bg-purple-500 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">New Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['new_tasks'] }}</p>
                        </div>
                        <div class="bg-blue-500 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Ongoing Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['ongoing_tasks'] }}</p>
                        </div>
                        <div class="bg-yellow-500 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Completed Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</p>
                        </div>
                        <div class="bg-green-500 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Tasks</h2>
                </div>
                <div class="p-6">
                    @if($recentTasks->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            No tasks found
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentTasks as $task)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-200">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-800">{{ $task->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Due: {{ $task->due_date }} {{ $task->due_time }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        @php
                                            $statusLabel = $task->approval_status === 'pending' ? 'Pending Approval' : ucfirst($task->status);
                                            $statusClasses = $task->approval_status === 'pending'
                                                ? 'bg-purple-100 text-purple-700'
                                                : ($task->status === 'completed' ? 'bg-green-100 text-green-700'
                                                : ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-700'
                                                : ($task->status === 'new' ? 'bg-yellow-100 text-yellow-700'
                                                : 'bg-gray-100 text-gray-700')));
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ $statusLabel }}
                                        </span>
                                        <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
