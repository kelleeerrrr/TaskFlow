<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Task Name</label>
        <p class="text-gray-900 font-medium">{{ $request->task->title }}</p>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Current Deadline</label>
            <p class="text-gray-900">{{ $request->task->due_date }} {{ $request->task->due_time }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Requested Deadline</label>
            <p class="text-orange-600 font-medium">{{ $request->requested_due_date }} {{ $request->requested_due_time }}</p>
        </div>
    </div>
    
    @if($request->reason)
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Reason</label>
            <p class="text-gray-900">{{ $request->reason }}</p>
        </div>
    @endif
    
    <div>
        <label class="block text-sm font-medium text-gray-500 mb-1">Requested By</label>
        <p class="text-gray-900">{{ $request->user->name }} ({{ $request->user->email }})</p>
    </div>
    
    <div class="pt-4 border-t">
        <div class="flex gap-3">
            <form action="{{ route('requests.approve-time-revision', $request->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    Approve
                </button>
            </form>
            <form action="{{ route('requests.reject-time-revision', $request->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>
