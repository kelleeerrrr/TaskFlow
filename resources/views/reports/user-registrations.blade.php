<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('User Registrations Report') }}</h2>
            <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">Back to Reports</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">User Registrations Per Month</h3>
                    <canvas id="usersPerMonthChart" height="200"></canvas>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Users by Role</h3>
                    <canvas id="usersByRoleChart" height="200"></canvas>
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Users by Status</h3>
                <canvas id="usersByStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const usersPerMonthCtx = document.getElementById('usersPerMonthChart').getContext('2d');
        new Chart(usersPerMonthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($usersPerMonth->pluck('month')) !!},
                datasets: [{
                    label: 'Users',
                    data: {!! json_encode($usersPerMonth->pluck('count')) !!},
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.1
                }]
            }
        });

        const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
        new Chart(usersByRoleCtx, {
            type: 'pie',
            data: {
                labels: ['Super Admin', 'Manager', 'User'],
                datasets: [{
                    data: [{{ $usersByRole['super_admin'] }}, {{ $usersByRole['manager'] }}, {{ $usersByRole['user'] }}],
                    backgroundColor: ['rgba(239, 68, 68, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(59, 130, 246, 0.8)'],
                }]
            }
        });

        const usersByStatusCtx = document.getElementById('usersByStatusChart').getContext('2d');
        new Chart(usersByStatusCtx, {
            type: 'bar',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    label: 'Users',
                    data: [{{ $usersByStatus['active'] }}, {{ $usersByStatus['inactive'] }}],
                    backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                }]
            }]
        });
    </script>
</x-app-layout>
