<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-4 mb-8">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Managers</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_managers'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['active_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Inactive Users</p>
                            <p class="mt-2 text-3xl font-bold text-red-600">{{ $stats['inactive_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Completed Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pending Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Overdue Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-red-600">{{ $stats['overdue_tasks'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="grid gap-6 md:grid-cols-2 mb-8">
                <!-- User Activity Analytics -->
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Activity Analytics</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-2">User Growth Trend (30 days)</p>
                            <div class="h-32">
                                <canvas id="userGrowthChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-2">Active Users Trend (30 days)</p>
                            <div class="h-32">
                                <canvas id="activeUsersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Analytics -->
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Analytics</h3>
                    <div class="h-64">
                        <canvas id="taskDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Manager Overview & Recent Activity -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Manager Overview -->
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Manager Overview</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Manager</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Team Size</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Tasks</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Completion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($managers as $manager)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-gray-900">{{ $manager['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $manager['email'] }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600">{{ $manager['team_size'] }}</td>
                                        <td class="py-3 px-4 text-gray-600">{{ $manager['total_tasks'] }}</td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $manager['completion_rate'] >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $manager['completion_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['user'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $activity['action'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $activity['date'] }} at {{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($userGrowthTrend->pluck('date')) !!},
                datasets: [{
                    label: 'New Users',
                    data: {!! json_encode($userGrowthTrend->pluck('count')) !!},
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Active Users Chart
        const activeUsersCtx = document.getElementById('activeUsersChart').getContext('2d');
        new Chart(activeUsersCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($activeUsersTrend->pluck('date')) !!},
                datasets: [{
                    label: 'Active Users',
                    data: {!! json_encode($activeUsersTrend->pluck('count')) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Task Distribution Chart
        const taskDistributionCtx = document.getElementById('taskDistributionChart').getContext('2d');
        new Chart(taskDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Ongoing', 'Pending', 'Overdue'],
                datasets: [{
                    data: [
                        {{ $taskDistribution['completed'] }},
                        {{ $taskDistribution['ongoing'] }},
                        {{ $taskDistribution['pending'] }},
                        {{ $taskDistribution['overdue'] }}
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
