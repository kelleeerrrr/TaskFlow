<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Tasks</h2>
            @if(!auth()->user()->isSuperAdmin())
                <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="flex items-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-lg hover:bg-orange-600 shadow-md hover:shadow-lg transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Task
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-lg p-4 mb-6 border border-gray-200">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" onchange="document.getElementById('taskFilterForm').submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">All</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
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

            <script>
                function updateStatusFilter(status) {
                    const params = new URLSearchParams(window.location.search);
                    if (status) {
                        params.set('status', status);
                    } else {
                        params.delete('status');
                    }
                    window.location.search = params.toString();
                }
            </script>

            <!-- Tasks List -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($task->assignedUser)
                                            <p>{{ $task->assignedUser->name }}</p>
                                        @else
                                            <p>Unassigned</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $approvalStatusLabel = $task->approval_status === 'pending' ? 'Pending' : ucfirst($task->status);
                                            $approvalStatusClasses = $task->approval_status === 'pending'
                                                ? 'bg-purple-100 text-purple-700'
                                                : ($task->status === 'completed' ? 'bg-green-100 text-green-700'
                                                : ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-700'
                                                : ($task->status === 'new' ? 'bg-yellow-100 text-yellow-700'
                                                : ($task->status === 'rejected' ? 'bg-red-100 text-red-700'
                                                : 'bg-gray-100 text-gray-700'))));
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $approvalStatusClasses }}">
                                            {{ $approvalStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-700' :
                                            ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-green-100 text-green-700') }}">
                                            {{ ucfirst($task->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $task->due_date }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if((auth()->user()->isManager() || auth()->user()->isSuperAdmin()) && $task->approval_status === 'pending')
                                            <div class="flex gap-2">
                                                <form action="{{ route('tasks.approve', $task) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">Approve</button>
                                                </form>
                                                <form action="{{ route('tasks.reject', $task) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">Decline</button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('tasks.show', $task) }}" class="text-orange-600 hover:text-orange-800 font-medium text-sm">
                                                View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 px-6">{{ $tasks->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Create Task</h2>
                <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Basic Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                            <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Assignment</h3>
                    <div class="space-y-4">
                        @if(auth()->user()->isManager() || auth()->user()->isSuperAdmin())
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                                <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="">Select User</option>
                                    @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Collaborators</label>
                            <select name="collaborators[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Scheduling -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Scheduling</h3>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Time</label>
                            <input type="time" name="due_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Attachment -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Attachment</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload PDF</label>
                        <input type="file" name="attachment" accept="application/pdf" class="w-full text-sm text-gray-700" />
                        <p class="text-sm text-gray-500 mt-1">(Max 50 MB)</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Task Details</h2>
                <button onclick="closeTaskDetailsModal()" class="text-gray-400 hover:text-gray-600">
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

    <div id="deleteTaskModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900">Confirm Delete</h2>
            <p class="mt-4 text-gray-600">Are you really sure you want to delete this task? This action cannot be undone.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <form id="deleteTaskForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function openTaskModal() {
        document.getElementById('taskModal').classList.remove('hidden');
    }

    function closeTaskModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    function openDeleteModal(actionUrl) {
        const modal = document.getElementById('deleteTaskModal');
        document.getElementById('deleteTaskForm').action = actionUrl;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteTaskModal').classList.add('hidden');
    }

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

    function closeTaskDetailsModal() {
        document.getElementById('taskDetailsModal').classList.add('hidden');
    }
</script>
