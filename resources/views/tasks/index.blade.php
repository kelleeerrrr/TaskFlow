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
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input name="search" type="text" value="{{ request('search') }}" placeholder="Search tasks..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                        @if(!auth()->user()->isManager())
                            <select name="assigned" onchange="document.getElementById('taskFilterForm').submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">All Tasks</option>
                                <option value="me" {{ request('assigned') === 'me' ? 'selected' : '' }}>Assigned to me</option>
                            </select>
                        @endif
                        <select name="status" onchange="document.getElementById('taskFilterForm').submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                            @if(!auth()->user()->isManager())
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            @endif
                        </select>
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all">Apply</button>
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Reset</a>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($tasks as $task)
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('tasks.show', $task) }}'">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ optional($task->assignedUser)->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $approvalStatusLabel = $task->approval_status === 'pending' ? 'Pending Approval' : ucfirst($task->status);
                                            $approvalStatusClasses = $task->approval_status === 'pending'
                                                ? 'bg-purple-100 text-purple-700'
                                                : ($task->status === 'completed' ? 'bg-green-100 text-green-700'
                                                : ($task->status === 'ongoing' ? 'bg-blue-100 text-blue-700'
                                                : ($task->status === 'new' ? 'bg-yellow-100 text-yellow-700'
                                                : 'bg-gray-100 text-gray-700')));
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
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $task->due_date }}
                                        </div>
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

    <!-- Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg mx-4">
            <h2 class="text-xl font-semibold mb-4">Create New Task</h2>
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" rows="3"></textarea>
                </div>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                        <select name="assigned_to[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Hold Ctrl (Windows) / Cmd (Mac) to select multiple users.</p>
                    </div>
                @endif
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Time</label>
                        <input type="time" name="due_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PDF Attachment</label>
                    <input type="file" name="attachment" accept="application/pdf" class="w-full text-sm text-gray-700" />
                    <p class="text-sm text-gray-500 mt-1">Only PDF files, up to 50 MB.</p>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Create
                    </button>
                </div>
            </form>
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
    function openDeleteModal(actionUrl) {
        const modal = document.getElementById('deleteTaskModal');
        document.getElementById('deleteTaskForm').action = actionUrl;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteTaskModal').classList.add('hidden');
    }
</script>
