<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Managers</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Manager Overview</h3>
                    <p class="text-sm text-gray-500 mt-1">Monitor managers and their team performance</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Manager Name</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Email</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Team Size</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Total Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Completed Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Pending Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Overdue Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Completion Rate</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($managers as $manager)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-semibold">{{ substr($manager['name'], 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $manager['name'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['email'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['team_size'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['total_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['completed_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['pending_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $manager['overdue_tasks'] }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $manager['completion_rate'] >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $manager['completion_rate'] }}%
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $manager['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($manager['status']) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <a href="{{ route('managers.show', $manager['id']) }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
