<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Super Admin Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-4 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Total Users</h3>
                    <p class="mt-4 text-3xl font-bold text-orange-600">{{ $stats['total_users'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Active Users</h3>
                    <p class="mt-4 text-3xl font-bold text-green-600">{{ $stats['active_users'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Inactive Users</h3>
                    <p class="mt-4 text-3xl font-bold text-red-600">{{ $stats['inactive_users'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold">Total Tasks</h3>
                    <p class="mt-4 text-3xl font-bold">{{ $stats['total_tasks'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Tasks Per Month</h3>
                    <canvas id="tasksPerMonthChart" height="200"></canvas>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">User Registrations</h3>
                    <canvas id="userRegistrationsChart" height="200"></canvas>
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

        const userRegistrationsCtx = document.getElementById('userRegistrationsChart').getContext('2d');
        new Chart(userRegistrationsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($userRegistrations->pluck('month')) !!},
                datasets: [{
                    label: 'Users',
                    data: {!! json_encode($userRegistrations->pluck('count')) !!},
                    backgroundColor: 'rgba(249, 115, 22, 0.5)',
                    borderColor: 'rgb(249, 115, 22)',
                    borderWidth: 1
                }]
            }
        });
    </script>
</x-app-layout>
