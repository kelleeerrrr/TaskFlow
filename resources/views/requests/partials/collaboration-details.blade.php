<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Task Name</label>
        <p class="text-gray-900 font-medium">{{ $request->task->title }}</p>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
        <p class="text-gray-900">{{ $request->task->description }}</p>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Reason for Collaboration</label>
        <p class="text-gray-900">User requested to collaborate on this task</p>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Requested User</label>
        <p class="text-gray-900">{{ $request->user->name }} ({{ $request->user->email }})</p>
    </div>
    
    <div class="pt-4 border-t">
        <div class="flex gap-3">
            <form action="{{ route('requests.approve-collaboration', $request->task_id) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    Approve
                </button>
            </form>
            <form action="{{ route('requests.reject-collaboration', $request->task_id) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>
