<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Reports & Analytics</h2>
            <button onclick="document.getElementById('detectLateModal').classList.remove('hidden')" class="flex items-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 shadow-md hover:shadow-lg transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Detect Late Tasks
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="flex gap-4 mb-6 border-b border-gray-200">
                <button onclick="showTab('summary')" id="tab-summary" class="px-4 py-2 font-medium transition-colors text-orange-600 border-b-2 border-orange-600">
                    Task Summary
                </button>
                <button onclick="showTab('performance')" id="tab-performance" class="px-4 py-2 font-medium transition-colors text-gray-600 hover:text-gray-800">
                    User Performance
                </button>
                <button onclick="showTab('productivity')" id="tab-productivity" class="px-4 py-2 font-medium transition-colors text-gray-600 hover:text-gray-800">
                    Team Productivity
                </button>
            </div>

            <!-- Task Summary -->
            <div id="content-summary" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Tasks</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $taskSummary['total'] ?? 0 }}</p>
                            </div>
                            <div class="bg-blue-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">New</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $taskSummary['new'] ?? 0 }}</p>
                            </div>
                            <div class="bg-gray-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Ongoing</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $taskSummary['ongoing'] ?? 0 }}</p>
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
                                <p class="text-gray-600 text-sm font-medium">Completed</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $taskSummary['completed'] ?? 0 }}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Late</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $taskSummary['late'] ?? 0 }}</p>
                            </div>
                            <div class="bg-red-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Performance -->
            <div id="content-performance" class="hidden bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">User Performance Report</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tasks</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ongoing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($userPerformance ?? [] as $perf)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-medium mr-3">
                                                {{ strtoupper(substr($perf->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $perf->name ?? 'Unknown' }}</div>
                                                <div class="text-sm text-gray-500">{{ $perf->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $perf->total_tasks ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $perf->completed_tasks ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm text-blue-600 font-medium">{{ $perf->ongoing_tasks ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $perf->late_tasks ?? 0 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $perf->completion_rate ?? 0 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $perf->completion_rate ?? 0 }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Team Productivity -->
            <div id="content-productivity" class="hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Users Meeting Deadlines</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $teamProductivity['users_meeting_deadlines'] ?? 0 }}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Overdue Tasks</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $teamProductivity['overdue_tasks'] ?? 0 }}</p>
                            </div>
                            <div class="bg-red-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Team Completion %</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">{{ $teamProductivity['team_completion_percentage'] ?? 0 }}%</p>
                            </div>
                            <div class="bg-blue-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Overview</h3>
                    <div class="space-y-4">
                        @foreach($userPerformance ?? [] as $perf)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-medium mr-3">
                                        {{ strtoupper(substr($perf->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $perf->name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-gray-500">{{ $perf->email ?? 'No email' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">{{ $perf->completion_rate ?? 0 }}% Completion</p>
                                    <p class="text-sm text-gray-500">{{ $perf->completed_tasks ?? 0 }} of {{ $perf->total_tasks ?? 0 }} tasks</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detect Late Tasks Modal -->
    <div id="detectLateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h2 class="text-xl font-semibold mb-4">Detect Late Tasks</h2>
            <p class="text-gray-600 mb-6">This will mark all tasks past their deadline as "late". Are you sure?</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('detectLateModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <form action="{{ route('reports.detect-late') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Detect
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all content
            document.getElementById('content-summary').classList.add('hidden');
            document.getElementById('content-performance').classList.add('hidden');
            document.getElementById('content-productivity').classList.add('hidden');
            
            // Reset all tab styles
            document.getElementById('tab-summary').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-summary').classList.add('text-gray-600');
            document.getElementById('tab-performance').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-performance').classList.add('text-gray-600');
            document.getElementById('tab-productivity').classList.remove('text-orange-600', 'border-b-2', 'border-orange-600');
            document.getElementById('tab-productivity').classList.add('text-gray-600');
            
            // Show selected content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Style selected tab
            document.getElementById('tab-' + tabName).classList.remove('text-gray-600');
            document.getElementById('tab-' + tabName).classList.add('text-orange-600', 'border-b-2', 'border-orange-600');
        }
    </script>
</x-app-layout>
