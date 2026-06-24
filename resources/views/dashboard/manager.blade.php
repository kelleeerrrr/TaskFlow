<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manager Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- SECTION 1: KEY OVERVIEW (2+5 GROUPING) -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Key Overview</h3>
                
                <!-- Row 1: Core Metrics (System-level indicators) -->
                <div class="grid gap-6 md:grid-cols-2 mb-6">
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-600">Total Tasks</h3>
                        <p class="mt-4 text-4xl font-bold text-orange-600">{{ $stats['total_tasks'] }}</p>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-600">Total Users</h3>
                        <p class="mt-4 text-4xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                    </div>
                </div>

                <!-- Row 2: Task Status Breakdown (Workflow progression) -->
                <div class="grid gap-4 md:grid-cols-5">
                    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-sm font-semibold text-gray-600">New Tasks</h3>
                        <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['new_tasks'] }}</p>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-sm font-semibold text-gray-600">Pending</h3>
                        <p class="mt-2 text-3xl font-bold text-purple-600">{{ $stats['pending_tasks'] }}</p>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-sm font-semibold text-gray-600">Ongoing</h3>
                        <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['ongoing_tasks'] }}</p>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-sm font-semibold text-gray-600">Completed</h3>
                        <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</p>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                        <h3 class="text-sm font-semibold text-gray-600">Rejected</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">{{ $stats['rejected_tasks'] }}</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: VISUAL INSIGHTS (CHARTS) -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Visual Insights</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Task Status Distribution</h3>
                        <div class="relative h-64">
                            <canvas id="tasksByStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Task Completion Rate</h3>
                        <div class="relative h-64">
                            <canvas id="taskCompletionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: ACTION CENTER (NEEDS ATTENTION) -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">⚠️ Action Required</h3>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div class="grid gap-4 md:grid-cols-2">
                        <a href="{{ route('requests.index') }}" class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <div>
                                <p class="font-semibold text-blue-800">Collaboration Requests</p>
                                <p class="text-sm text-blue-600">Pending approval</p>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ $actionCenter['collaboration_requests_pending'] }}</span>
                        </a>
                        <a href="{{ route('requests.index') }}" class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                            <div>
                                <p class="font-semibold text-purple-800">Time Revision Requests</p>
                                <p class="text-sm text-purple-600">Pending approval</p>
                            </div>
                            <span class="text-2xl font-bold text-purple-600">{{ $actionCenter['time_revision_requests_pending'] }}</span>
                        </a>
                        <a href="{{ route('tasks.index') }}" class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div>
                                <p class="font-semibold text-yellow-800">Tasks Due Today</p>
                                <p class="text-sm text-yellow-600">Requires attention</p>
                            </div>
                            <span class="text-2xl font-bold text-yellow-600">{{ $actionCenter['tasks_due_today'] }}</span>
                        </a>
                        <a href="{{ route('tasks.index') }}" class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            <div>
                                <p class="font-semibold text-red-800">Overdue Tasks</p>
                                <p class="text-sm text-red-600">Immediate action needed</p>
                            </div>
                            <span class="text-2xl font-bold text-red-600">{{ $actionCenter['overdue_tasks'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const tasksByStatusCtx = document.getElementById('tasksByStatusChart').getContext('2d');
        new Chart(tasksByStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Pending', 'Ongoing', 'Completed', 'Rejected'],
                datasets: [{
                    data: [{{ $tasksByStatus['new'] }}, {{ $tasksByStatus['pending'] }}, {{ $tasksByStatus['ongoing'] }}, {{ $tasksByStatus['completed'] }}, {{ $tasksByStatus['rejected'] }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        const taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
        const completedCount = {{ $taskCompletionRate['completed'] }};
        const totalCount = {{ $taskCompletionRate['total'] }};
        const completionRate = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;
        
        new Chart(taskCompletionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Incomplete'],
                datasets: [{
                    data: [completedCount, totalCount - completedCount],
                    backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(209, 213, 219, 0.8)'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.label === 'Completed') {
                                    return `Completed: ${completionRate}%`;
                                }
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
