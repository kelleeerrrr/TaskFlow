<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Collaboration Requests') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="p-6">
                    @if($requests->isEmpty())
                        <p class="text-gray-600">No pending collaboration requests found.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($requests as $request)
                                <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $request->task->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">Requested by: {{ optional($request->task->creator)->name }}</p>
                                            <p class="text-sm text-gray-600">Collaborator: {{ $request->user->name }} ({{ $request->user->email }})</p>
                                            <p class="text-sm text-gray-500 mt-1">Requested {{ $request->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <form action="{{ route('tasks.accept-invitation', $request->task) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $request->user->id }}">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Approve</button>
                                            </form>
                                            <form action="{{ route('tasks.reject-invitation', $request->task) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $request->user->id }}">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
