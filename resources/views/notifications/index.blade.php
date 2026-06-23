<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Mark All as Read</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="p-6">
                    @if($notifications->isEmpty())
                        <p class="text-gray-600">No notifications found.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="p-4 rounded-lg border border-gray-200 {{ $notification->is_read ? 'bg-gray-50' : 'bg-orange-50 border-l-4 border-orange-500' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold {{ $notification->is_read ? 'text-gray-700' : 'text-gray-900' }}">{{ $notification->title }}</h4>
                                            <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                                            <p class="mt-2 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="ml-4">
                                                @csrf
                                                <button type="submit" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Mark as Read</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $notifications->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
