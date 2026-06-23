<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manager Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Total Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-orange-600">{{ $stats['total_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Pending Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-purple-600">{{ $stats['pending_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">New Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-blue-600">{{ $stats['new_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Ongoing Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-yellow-600">{{ $stats['ongoing_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Completed Tasks</h3>
                    <p class="mt-4 text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Total Users</h3>
                    <p class="mt-4 text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Task Completion Rate</h3>
                    <canvas id="taskCompletionChart" height="200"></canvas>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Tasks by Status</h3>
                    <canvas id="tasksByStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
    </script>
</x-app-layout>
