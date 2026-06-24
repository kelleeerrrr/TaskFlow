<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Reports</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Section A: Team Productivity Cards -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Team Productivity</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Users Meeting Deadlines</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $teamProductivity['users_meeting_deadlines'] ?? 0 }}%</p>
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
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $teamProductivity['overdue_tasks'] ?? 0 }}</p>
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
                                <p class="text-gray-600 text-sm font-medium">Team Completion Rate</p>
                                <p class="mt-2 text-4xl font-bold text-gray-800">{{ $teamProductivity['team_completion_percentage'] ?? 0 }}%</p>
                            </div>
                            <div class="bg-blue-500 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section B: User Performance Table -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">User Performance</h3>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
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
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $perf->total_tasks ?? 0 }}</td>
                                        <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $perf->completed_tasks ?? 0 }}</td>
                                        <td class="px-6 py-4 text-sm text-blue-600 font-medium">{{ $perf->ongoing_tasks ?? 0 }}</td>
                                        <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $perf->late_tasks ?? 0 }}</td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-gray-900">{{ $perf->completion_rate ?? 0 }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section C: Team Overview Chart (Horizontal Bar) -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Team Overview</h3>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="space-y-4">
                        @foreach($userPerformance ?? [] as $perf)
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-700">{{ $perf->name ?? 'Unknown' }}</div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-gray-200 rounded-full h-6">
                                        <div class="bg-orange-500 h-6 rounded-full flex items-center justify-end pr-2" style="width: {{ $perf->completion_rate ?? 0 }}%">
                                            <span class="text-xs font-medium text-white">{{ $perf->completion_rate ?? 0 }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Section D: Monthly Task Trends (Line Chart) -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Task Trends</h3>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <canvas id="monthlyTrendsChart" height="300"></canvas>
                </div>
            </div>

            <!-- Section E: Export Reports -->
            <div class="flex justify-end">
                <button onclick="exportToPDF()" class="flex items-center gap-2 px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors shadow-md">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [
                    {
                        label: 'Tasks Created',
                        data: [{{ $monthlyTrends['created'][0] ?? 0 }}, {{ $monthlyTrends['created'][1] ?? 0 }}, {{ $monthlyTrends['created'][2] ?? 0 }}, {{ $monthlyTrends['created'][3] ?? 0 }}, {{ $monthlyTrends['created'][4] ?? 0 }}],
                        borderColor: 'rgba(249, 115, 22, 1)',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Tasks Completed',
                        data: [{{ $monthlyTrends['completed'][0] ?? 0 }}, {{ $monthlyTrends['completed'][1] ?? 0 }}, {{ $monthlyTrends['completed'][2] ?? 0 }}, {{ $monthlyTrends['completed'][3] ?? 0 }}, {{ $monthlyTrends['completed'][4] ?? 0 }}],
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function exportToPDF() {
            window.print();
        }
    </script>
</x-app-layout>
