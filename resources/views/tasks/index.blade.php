<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" action="{{ route('tasks.index') }}" id="taskFilterForm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" onchange="document.getElementById('taskFilterForm').submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">All</option>
                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <select name="assigned_to" onchange="document.getElementById('taskFilterForm').submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">All</option>
                                @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                            <select name="manager" onchange="document.getElementById('taskFilterForm').submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">All</option>
                                @foreach(\App\Models\User::where('role', 'manager')->get() as $manager)
                                    <option value="{{ $manager->id }}" {{ request('manager') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="due_date" value="{{ request('due_date') }}" onchange="document.getElementById('taskFilterForm').submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input name="search" type="text" value="{{ request('search') }}" placeholder="Search Task" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tasks List -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Task Monitoring</h3>
                    <p class="text-sm text-gray-500 mt-1">View all tasks in the system</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Task Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Assigned User</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Manager</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date Created</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if($task->assignedUser)
                                            {{ $task->assignedUser->name }}
                                        @else
                                            Unassigned
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if($task->creator && $task->creator->role === 'manager')
                                            {{ $task->creator->name }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $approvalStatusLabel = $task->approval_status === 'pending' ? 'Pending' : ucfirst($task->status);
                                            $approvalStatusClasses = $task->approval_status === 'pending'
                                                ? 'bg-purple-100 text-purple-800'
                                                : ($task->status === 'completed' ? 'bg-green-100 text-green-800'
                                                : ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-800'
                                                : ($task->status === 'new' ? 'bg-yellow-100 text-yellow-800'
                                                : ($task->status === 'rejected' ? 'bg-red-100 text-red-800'
                                                : 'bg-gray-100 text-gray-800'))));
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $approvalStatusClasses }}">
                                            {{ $approvalStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' :
                                            ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($task->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $task->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $task->due_date }}</td>
                                    <td class="px-6 py-4">
                                        <button onclick="openTaskDetailsModal({{ $task->id }})" class="text-orange-600 hover:text-orange-800 text-sm font-medium">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($tasks->hasPages())
                    <div class="p-6 border-t border-gray-200">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Task Details</h2>
                <button onclick="document.getElementById('taskDetailsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="taskDetailsContent">
                <!-- Content will be loaded via JavaScript -->
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function openTaskDetailsModal(taskId) {
        const modal = document.getElementById('taskDetailsModal');
        const content = document.getElementById('taskDetailsContent');
        
        content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto"></div></div>';
        modal.classList.remove('hidden');
        
        fetch(`/tasks/${taskId}/details`)
            .then(response => response.json())
            .then(data => {
                content.innerHTML = data.html;
            })
            .catch(error => {
                content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading task details</div>';
            });
    }
</script>
