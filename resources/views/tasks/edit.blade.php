<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Task') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
                <form method="POST" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}" class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500" rows="4" required>{{ old('description', $task->description) }}</textarea>
                        @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $task->due_date) }}" class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('due_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Time</label>
                            <input type="time" name="due_time" value="{{ old('due_time', $task->due_time) }}" class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('due_time')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager())
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Assign To</label>
                            <select name="assigned_to[]" multiple class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500">
                                @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('assigned_to', $task->collaborators->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Hold Ctrl (Windows) / Cmd (Mac) to select multiple users.</p>
                            @error('assigned_to')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Collaborate With</label>
                            <select name="collaborate_with[]" multiple class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500">
                                @foreach(\App\Models\User::where('role', 'user')->get() as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('collaborate_with', $task->collaborators->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Hold Ctrl (Windows) / Cmd (Mac) to select multiple users.</p>
                            @error('collaborate_with')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" class="mt-1 block w-full border-gray-300 rounded-lg border px-3 py-2 focus:border-orange-500 focus:ring-orange-500">
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">PDF Attachment</label>
                        <input type="file" name="attachment" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-700" />
                        @error('attachment')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="text-sm text-gray-500 mt-1">Uploading a file will add a new PDF attachment to this task.</p>
                    </div>

                    @if($task->files->isNotEmpty())
                        <div class="mb-4 bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Existing Files</h3>
                            <ul class="mt-3 space-y-2">
                                @foreach($task->files as $file)
                                    <li><a href="{{ asset('storage/' . $file->file_path) }}" class="text-blue-600 hover:text-blue-900" target="_blank">{{ $file->file_name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-orange-500 border border-transparent rounded-lg text-white hover:bg-orange-600 shadow-md hover:shadow-lg transition-all">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
