<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manager Details</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Manager Info -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-blue-600 font-bold text-xl">{{ substr($manager->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $manager->name }}</h3>
                                <p class="text-gray-500">{{ $manager->email }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $manager->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-2">
                                    {{ ucfirst($manager->status) }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('managers.index') }}" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $managerStats['total_users'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Completed Tasks</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $managerStats['completed_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Pending Tasks</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $managerStats['pending_tasks'] }}</p>
                </div>
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Overdue Tasks</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ $managerStats['overdue_tasks'] }}</p>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                    <p class="text-sm text-gray-500 mt-1">Users under this manager's supervision</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">User Name</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Email</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Total Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Completed Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Pending Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Overdue Tasks</th>
                                <th class="text-left py-4 px-6 text-sm font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamMembers as $member)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-orange-600 font-semibold">{{ substr($member['name'], 0, 2) }}</span>
                                            </div>
                                            <div class="font-medium text-gray-900">{{ $member['name'] }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $member['email'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $member['total_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $member['completed_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $member['pending_tasks'] }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $member['overdue_tasks'] }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($member['status']) }}
                                        </span>
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
