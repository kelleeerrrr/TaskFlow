<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $task->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200 space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Task ID</p>
                        <p class="text-sm text-gray-600">#{{ $task->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Status</p>
                        <p>{{ ucfirst($task->status) }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold">Description</h3>
                    <p class="mt-2 text-gray-700">{{ $task->description }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Approval</p>
                        <p>{{ ucfirst($task->approval_status) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Due Date</p>
                        <p>{{ $task->due_date }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Due Time</p>
                        <p>{{ $task->due_time }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Created By</p>
                        <p>{{ optional($task->creator)->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Assigned To</p>
                        <p>{{ optional($task->assignedUser)->name ?? 'Unassigned' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Date Created</p>
                        <p>{{ $task->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-medium">Last Updated</p>
                        <p>{{ $task->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($task->files->isNotEmpty())
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold">Files</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach($task->files as $file)
                                <li>
                                    <a href="{{ asset('storage/' . $file->file_path) }}" class="text-blue-600 hover:text-blue-900" target="_blank">{{ $file->file_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($task->collaborators->isNotEmpty())
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold">Assigned Users</h3>
                        <ul class="mt-3 space-y-2">
                            <li class="flex items-center justify-between">
                                <span>{{ optional($task->creator)->name }} <span class="text-sm text-gray-500">(Creator)</span></span>
                            </li>
                            @foreach($task->collaborators as $collaborator)
                                <li class="flex items-center justify-between">
                                    <span>{{ $collaborator->name }} <span class="text-sm text-gray-500">({{ ucfirst($collaborator->pivot->invitation_status) }})</span></span>
                                    @if(($collaborator->pivot->invitation_status === 'pending') && (auth()->user()->isSuperAdmin() || auth()->user()->isManager()))
                                        <div class="flex gap-2">
                                            <form action="{{ route('tasks.accept-invitation', $task) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $collaborator->id }}">
                                                <button type="submit" class="text-green-600 hover:text-green-700 font-medium">Approve</button>
                                            </form>
                                            <form action="{{ route('tasks.reject-invitation', $task) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $collaborator->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Reject</button>
                                            </form>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($task->history->isNotEmpty())
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold">Task History</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach($task->history->sortByDesc('created_at') as $history)
                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div>
                                        <p class="font-medium">{{ $history->action }}</p>
                                        <p class="text-sm text-gray-500">by {{ optional($history->user)->name }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $history->created_at->diffForHumans() }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager() || auth()->id() === $task->created_by)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold mb-3">Invite Collaborator</h3>
                        <form action="{{ route('tasks.invite', $task) }}" method="POST">
                            @csrf
                            <select name="user_id" required class="block w-full rounded-lg border-gray-300 border px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Select a user</option>
                                @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-500 hover:bg-orange-600 shadow-md transition-all">Invite</button>
                        </form>
                    </div>
                @endif

                @if((auth()->user()->isSuperAdmin() || auth()->user()->isManager()) && $task->approval_status === 'pending')
                    <div class="flex gap-4">
                        <form action="{{ route('tasks.approve', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-lg text-white hover:bg-green-600 shadow-md transition-all">Approve Task</button>
                        </form>
                        <form action="{{ route('tasks.reject', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-lg text-white hover:bg-red-600 shadow-md transition-all">Reject Task</button>
                        </form>
                    </div>
                @endif

                <div class="flex items-center gap-4">
                    <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">Back to tasks</a>
                    @if(auth()->id() === $task->created_by || auth()->user()->isSuperAdmin() || auth()->user()->isManager() || $task->collaborators()->where('user_id', auth()->id())->where('invitation_status', 'accepted')->exists())
                        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-5 py-2.5 bg-orange-500 border border-transparent rounded-lg text-white hover:bg-orange-600 shadow-md hover:shadow-lg transition-all">Edit Task</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
