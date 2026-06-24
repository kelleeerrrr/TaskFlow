<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-800">Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Tabs -->
            <div class="flex gap-4 mb-6 border-b border-gray-200">
                <button onclick="showTab('collaboration')" id="tab-collaboration" class="px-4 py-2 font-medium transition-colors text-orange-600 border-b-2 border-orange-600">
                    Collaboration Requests
                </button>
                <button onclick="showTab('time-revision')" id="tab-time-revision" class="px-4 py-2 font-medium transition-colors text-gray-600 hover:text-gray-800">
                    Time Revision Requests
                </button>
            </div>

            <!-- Collaboration Requests -->
            <div id="content-collaboration" class="space-y-6">
                @if($collaborationRequests->count() > 0)
                    @foreach($collaborationRequests as $request)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $request->task->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Requested by:</span> {{ $request->user->name }} ({{ $request->user->email }})
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Task Creator:</span> {{ $request->task->creator->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        {{ Str::limit($request->task->description, 100) }}
                                    </p>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <form action="{{ route('requests.approve-collaboration', $request->task_id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('requests.reject-collaboration', $request->task_id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12 text-center">
                        <p class="text-gray-500">No pending collaboration requests.</p>
                    </div>
                @endif
            </div>

            <!-- Time Revision Requests -->
            <div id="content-time-revision" class="hidden space-y-6">
                @if($timeRevisionRequests->count() > 0)
                    @foreach($timeRevisionRequests as $request)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $request->task->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Requested by:</span> {{ $request->user->name }} ({{ $request->user->email }})
                                    </p>
                                    <div class="mt-3 grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500">Current Due Date</p>
                                            <p class="font-medium text-gray-800">{{ $request->task->due_date }} {{ $request->task->due_time }}</p>
                                        </div>
                                        <div class="bg-orange-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500">Requested Due Date</p>
                                            <p class="font-medium text-orange-700">{{ $request->requested_due_date }} {{ $request->requested_due_time }}</p>
                                        </div>
                                    </div>
                                    @if($request->reason)
                                        <div class="mt-3 bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500">Reason</p>
                                            <p class="text-sm text-gray-700">{{ $request->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <form action="{{ route('requests.approve-time-revision', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('requests.reject-time-revision', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12 text-center">
                        <p class="text-gray-500">No pending time revision requests.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all content
            document.getElementById('content-collaboration').classList.add('hidden');
            document.getElementById('content-time-revision').classList.add('hidden');
            
            // Reset all tab styles
            document.getElementById('tab-collaboration').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-collaboration').classList.add('text-gray-600');
            document.getElementById('tab-time-revision').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-time-revision').classList.add('text-gray-600');
            
            // Show selected content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Style selected tab
            document.getElementById('tab-' + tabName).classList.remove('text-gray-600');
            document.getElementById('tab-' + tabName).classList.add('text-orange-600', 'border-b-2', 'border-orange-600');
        }
    </script>
</x-app-layout>
