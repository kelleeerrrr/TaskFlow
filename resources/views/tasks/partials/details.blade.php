<!-- Task Information -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Task Information</h3>
    <div class="space-y-3">
        <div>
            <label class="text-sm font-medium text-gray-500">Title</label>
            <p class="text-gray-900">{{ $task->title }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-500">Description</label>
            <p class="text-gray-900">{{ $task->description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Status</label>
                <p class="text-gray-900">{{ ucfirst($task->status) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Priority</label>
                <p class="text-gray-900">{{ ucfirst($task->priority ?? 'medium') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Collaborators -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Collaborators</h3>
    <div class="relative">
        <button onclick="document.getElementById('collaboratorsDropdown').classList.toggle('hidden')" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-left flex justify-between items-center">
            <span>{{ $task->collaborators->count() }} Collaborator(s)</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div id="collaboratorsDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
            @if($task->collaborators->isNotEmpty())
                @foreach($task->collaborators as $collaborator)
                    <div class="px-4 py-2 border-b last:border-b-0">
                        <p class="text-gray-900">{{ $collaborator->name }}</p>
                        <p class="text-xs text-gray-500">{{ $collaborator->pivot->invitation_status }}</p>
                    </div>
                @endforeach
            @else
                <div class="px-4 py-2 text-gray-500">No collaborators</div>
            @endif
        </div>
    </div>
</div>

<!-- Attachment -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Attachment</h3>
    @if($task->files->isNotEmpty())
        @foreach($task->files as $file)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-900">{{ $file->file_name }}</span>
                </div>
                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-orange-600 hover:text-orange-800 text-sm">Download</a>
            </div>
        @endforeach
    @else
        <p class="text-gray-500">No attachments</p>
    @endif
</div>

<!-- Revision History -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Revision History</h3>
    @if($task->timeRevisionRequests->isNotEmpty())
        @foreach($task->timeRevisionRequests as $revision)
            <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Original:</span>
                        <span class="text-gray-900">{{ $task->due_date }} {{ $task->due_time }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">New:</span>
                        <span class="text-gray-900">{{ $revision->requested_due_date }} {{ $revision->requested_due_time }}</span>
                    </div>
                </div>
                @if($revision->reason)
                    <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Reason:</span> {{ $revision->reason }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">Status: {{ ucfirst($revision->status) }}</p>
            </div>
        @endforeach
    @else
        <p class="text-gray-500">No revision history</p>
    @endif
</div>

<!-- Activity Log -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b">Activity Log</h3>
    @if($task->history->isNotEmpty())
        <div class="space-y-2 max-h-48 overflow-y-auto">
            @foreach($task->history as $log)
                <div class="flex items-start text-sm">
                    <div class="flex-1">
                        <p class="text-gray-900">{{ $log->action }}</p>
                        <p class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y H:i') }} by {{ $log->user->name ?? 'Unknown' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No activity log</p>
    @endif
</div>
