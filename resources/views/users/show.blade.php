<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Profile</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200 mb-6">
                <div class="flex items-center">
                    <div class="w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center text-white text-3xl font-medium mr-6">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="flex gap-2 mt-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $user->role === 'manager' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Task Records</h3>
                @if($tasks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($tasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}...</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' :
                                                ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-700' :
                                                ($task->status === 'new' ? 'bg-yellow-100 text-yellow-700' :
                                                'bg-red-100 text-red-700')) }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $task->due_date }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($task->created_by === $user->id)
                                                Creator
                                            @elseif($task->assigned_to === $user->id)
                                                Assigned
                                            @else
                                                Collaborator
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No tasks found for this user.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
