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

            <!-- Tab 1: Collaboration Requests -->
            <div id="content-collaboration" class="space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pending Requests</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $collaborationSummary['pending'] ?? 0 }}</p>
                            </div>
                            <div class="bg-yellow-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Approved Requests</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $collaborationSummary['approved'] ?? 0 }}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Rejected Requests</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $collaborationSummary['rejected'] ?? 0 }}</p>
                            </div>
                            <div class="bg-red-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Creator</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($collaborationRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $request->task->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $request->task->creator->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <button onclick="openCollaborationDrawer({{ $request->id }})" class="text-orange-600 hover:text-orange-800 font-medium text-sm mr-3">View</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Time Revision Requests -->
            <div id="content-time-revision" class="hidden space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pending</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $timeRevisionSummary['pending'] ?? 0 }}</p>
                            </div>
                            <div class="bg-yellow-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Approved</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $timeRevisionSummary['approved'] ?? 0 }}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Rejected</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $timeRevisionSummary['rejected'] ?? 0 }}</p>
                            </div>
                            <div class="bg-red-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($timeRevisionRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $request->task->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $request->task->due_date }} {{ $request->task->due_time }}</td>
                                        <td class="px-6 py-4 text-sm text-orange-600 font-medium">{{ $request->requested_due_date }} {{ $request->requested_due_time }}</td>
                                        <td class="px-6 py-4">
                                            <button onclick="openTimeRevisionDrawer({{ $request->id }})" class="text-orange-600 hover:text-orange-800 font-medium text-sm mr-3">View</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collaboration Request Details Drawer -->
    <div id="collaborationDrawer" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeCollaborationDrawer()"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Request Details</h3>
                    <button onclick="closeCollaborationDrawer()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="collaborationDrawerContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Time Revision Request Details Drawer -->
    <div id="timeRevisionDrawer" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeTimeRevisionDrawer()"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Request Details</h3>
                    <button onclick="closeTimeRevisionDrawer()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="timeRevisionDrawerContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.getElementById('content-collaboration').classList.add('hidden');
            document.getElementById('content-time-revision').classList.add('hidden');
            document.getElementById('tab-collaboration').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-collaboration').classList.add('text-gray-600');
            document.getElementById('tab-time-revision').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-time-revision').classList.add('text-gray-600');
            document.getElementById('content-' + tabName).classList.remove('hidden');
            document.getElementById('tab-' + tabName).classList.remove('text-gray-600');
            document.getElementById('tab-' + tabName).classList.add('text-orange-600', 'border-b-2', 'border-orange-600');
        }

        function openCollaborationDrawer(requestId) {
            const drawer = document.getElementById('collaborationDrawer');
            const content = document.getElementById('collaborationDrawerContent');
            
            content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto"></div></div>';
            drawer.classList.remove('hidden');
            
            fetch(`/requests/collaboration/${requestId}/details`)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = data.html;
                })
                .catch(error => {
                    content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading details</div>';
                });
        }

        function closeCollaborationDrawer() {
            document.getElementById('collaborationDrawer').classList.add('hidden');
        }

        function openTimeRevisionDrawer(requestId) {
            const drawer = document.getElementById('timeRevisionDrawer');
            const content = document.getElementById('timeRevisionDrawerContent');
            
            content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto"></div></div>';
            drawer.classList.remove('hidden');
            
            fetch(`/requests/time-revision/${requestId}/details`)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = data.html;
                })
                .catch(error => {
                    content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading details</div>';
                });
        }

        function closeTimeRevisionDrawer() {
            document.getElementById('timeRevisionDrawer').classList.add('hidden');
        }
    </script>
</x-app-layout>
