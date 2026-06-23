<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Productivity Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">Back to Reports</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-4 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Total Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $taskCompletionRate['total'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Completed</h3>
                    <p class="mt-4 text-3xl font-bold text-green-600">{{ $taskCompletionRate['completed'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Pending Approval</h3>
                    <p class="mt-4 text-3xl font-bold text-yellow-600">{{ $tasksByApproval['pending'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Overdue Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-red-600">{{ $overdueTasks }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Tasks Per Month</h3>
                    <canvas id="tasksPerMonthChart" height="200"></canvas>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Task Completion Rate</h3>
                    <canvas id="taskCompletionChart" height="200"></canvas>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Tasks by Status</h3>
                    <canvas id="tasksByStatusChart" height="200"></canvas>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Tasks by Approval Status</h3>
                    <canvas id="tasksByApprovalChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const tasksPerMonthCtx = document.getElementById('tasksPerMonthChart').getContext('2d');
        new Chart(tasksPerMonthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($tasksPerMonth->pluck('month')) !!},
                datasets: [{
                    label: 'Tasks',
                    data: {!! json_encode($tasksPerMonth->pluck('count')) !!},
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.1
                }]
            }
        });

        const taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
        new Chart(taskCompletionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Remaining'],
                datasets: [{
                    data: [{{ $taskCompletionRate['completed'] }}, {{ $taskCompletionRate['total'] - $taskCompletionRate['completed'] }}],
                    backgroundColor: ['rgba(249, 115, 22, 0.8)', 'rgba(209, 213, 219, 0.8)'],
                }]
            }
        });

        const tasksByStatusCtx = document.getElementById('tasksByStatusChart').getContext('2d');
        new Chart(tasksByStatusCtx, {
            type: 'pie',
            data: {
                labels: ['New', 'Ongoing', 'Completed'],
                datasets: [{
                    data: [{{ $tasksByStatus['new'] }}, {{ $tasksByStatus['ongoing'] }}, {{ $tasksByStatus['completed'] }}],
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(16, 185, 129, 0.8)'],
                }]
            }
        });

        const tasksByApprovalCtx = document.getElementById('tasksByApprovalChart').getContext('2d');
        new Chart(tasksByApprovalCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    label: 'Tasks',
                    data: [{{ $tasksByApproval['pending'] }}, {{ $tasksByApproval['approved'] }}, {{ $tasksByApproval['rejected'] }}],
                    backgroundColor: ['rgba(245, 158, 11, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                }]
            }
        });
    </script>
</x-app-layout>
