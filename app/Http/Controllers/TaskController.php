<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskHistory;
use App\Models\TaskNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $statusFilter = $request->query('status');
        $assignedFilter = $request->query('assigned');

        $query = Task::with(['creator', 'assignedUser', 'collaborators']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($user->isSuperAdmin() || $user->isManager()) {
            // Can see all tasks
        } else {
            // Users can only see their own tasks or shared tasks
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('collaborators', function ($query) use ($user) {
                      $query->where('user_id', $user->id)
                            ->where('invitation_status', 'accepted');
                  })
                  ->orWhere('assigned_to', $user->id);
            });
        }

        if ($assignedFilter === 'me') {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereHas('collaborators', function ($query) use ($user) {
                      $query->where('user_id', $user->id)
                            ->where('invitation_status', 'accepted');
                  });
            });
        }

        if ($statusFilter) {
            if (in_array($statusFilter, ['new', 'ongoing', 'completed', 'late'])) {
                $query->where('status', $statusFilter);
            } elseif (in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
                $query->where('approval_status', $statusFilter);
            }
        }

        $tasks = $query->latest()->paginate(15)->appends($request->query());

        return view('tasks.index', compact('tasks', 'statusFilter', 'assignedFilter'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'priority' => 'nullable|in:low,medium,high',
            'attachment' => 'nullable|mimes:pdf|max:51200',
        ];

        if ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
            $rules['assigned_to'] = 'nullable|array';
            $rules['assigned_to.*'] = 'exists:users,id';
        }

        $data = $request->validate($rules);
        $user = $request->user();

        $assignedUsers = $user->isManager() || $user->isSuperAdmin()
            ? collect($data['assigned_to'] ?? [])->filter()->values()->all()
            : [];

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'created_by' => $user->id,
            'assigned_to' => count($assignedUsers) ? $assignedUsers[0] : null,
            'status' => 'new',
            'priority' => $data['priority'] ?? 'medium',
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'],
            'approval_status' => $user->isManager() || $user->isSuperAdmin() ? 'approved' : 'pending',
        ]);

        if (! empty($assignedUsers)) {
            $syncData = [];
            foreach ($assignedUsers as $userId) {
                $syncData[$userId] = ['invitation_status' => 'accepted'];
            }

            $task->collaborators()->sync($syncData);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Assigned',
                'user_id' => $user->id,
            ]);

            foreach ($assignedUsers as $userId) {
                $assignedUser = \App\Models\User::find($userId);
                if ($assignedUser) {
                    $this->createNotification($assignedUser, 'Task Assigned', "You have been assigned to task '{$task->title}'.");
                }
            }
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('task_files', 'public');

            TaskFile::create([
                'task_id' => $task->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
            ]);
        }

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Task Created',
            'user_id' => $user->id,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(string $id)
    {
        $task = Task::with(['creator', 'files', 'collaborators', 'history.user', 'assignedUser'])->findOrFail($id);
        $user = auth()->user();

        if (! $this->canAccessTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('tasks.show', compact('task'));
    }

    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! $this->canEditTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! $this->canEditTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:new,ongoing,completed,late',
            'attachment' => 'nullable|mimes:pdf|max:51200',
        ];

        if ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
            $rules['assigned_to'] = 'nullable|array';
            $rules['assigned_to.*'] = 'exists:users,id';
        }

        $data = $request->validate($rules);

        $assignedUsers = $request->user()->isManager() || $request->user()->isSuperAdmin()
            ? collect($data['assigned_to'] ?? [])->filter()->values()->all()
            : [];

        $oldAssignedTo = $task->assigned_to;

        if (! ($user->isSuperAdmin() || $user->isManager()) && $task->approval_status !== 'approved') {
            unset($data['status']);
        }

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'],
            'assigned_to' => $request->user()->isManager() || $request->user()->isSuperAdmin()
                ? (count($assignedUsers) ? $assignedUsers[0] : null)
                : $task->assigned_to,
            'priority' => $data['priority'] ?? $task->priority,
            'status' => $data['status'] ?? $task->status,
        ]);

        if (! empty($assignedUsers)) {
            $syncData = [];
            foreach ($assignedUsers as $userId) {
                $syncData[$userId] = ['invitation_status' => 'accepted'];
            }

            $task->collaborators()->sync($syncData);

            if ($task->assigned_to && $task->assigned_to !== $oldAssignedTo) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'action' => 'Task Reassigned',
                    'user_id' => $request->user()->id,
                ]);
            }

            foreach ($assignedUsers as $userId) {
                $assignedUser = \App\Models\User::find($userId);

                if ($assignedUser) {
                    $this->createNotification($assignedUser, 'Task Assigned', "You have been assigned to task '{$task->title}'.");
                }
            }
        } elseif ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
            $task->collaborators()->detach();
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('task_files', 'public');

            TaskFile::create([
                'task_id' => $task->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
            ]);
        }

        if (($data['status'] ?? $task->status) !== $task->getOriginal('status')) {
            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Status Updated',
                'user_id' => $user->id,
            ]);
        }

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Task Updated',
            'user_id' => $user->id,
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! $this->canAccessTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function approve(string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task->update(['approval_status' => 'approved']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Task Approved',
            'user_id' => $user->id,
        ]);

        $this->createNotification($task->creator, 'Task Approved', "Your task '{$task->title}' has been approved.");

        return redirect()->route('tasks.show', $task)->with('success', 'Task approved successfully.');
    }

    public function reject(string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task->update(['approval_status' => 'rejected']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Task Rejected',
            'user_id' => $user->id,
        ]);

        $this->createNotification($task->creator, 'Task Rejected', "Your task '{$task->title}' has been rejected.");

        return redirect()->route('tasks.show', $task)->with('success', 'Task rejected successfully.');
    }

    public function inviteCollaborator(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! ($user->isSuperAdmin() || $user->isManager() || $task->created_by === $user->id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaborator = \App\Models\User::findOrFail($data['user_id']);

        if ($task->collaborators()->where('user_id', $collaborator->id)->exists()) {
            return redirect()->route('tasks.show', $task)->with('error', 'User is already a collaborator.');
        }

        $task->collaborators()->attach($collaborator->id, ['invitation_status' => 'pending']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Invited',
            'user_id' => $user->id,
        ]);

        $this->createNotification($collaborator, 'Invitation Received', "You have been invited to collaborate on task '{$task->title}'.");

        return redirect()->route('tasks.show', $task)->with('success', 'Collaborator invited successfully.');
    }

    public function acceptInvitation(Request $request, string $taskId)
    {
        $user = auth()->user();
        $task = Task::findOrFail($taskId);

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

        if (! $collaboration) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $collaboration->pivot->invitation_status = 'accepted';
        $collaboration->pivot->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Approved',
            'user_id' => $user->id,
        ]);

        $collaboratorUser = \App\Models\User::find($data['user_id']);

        if ($collaboratorUser) {
            $this->createNotification($collaboratorUser, 'Collaboration Approved', "Your request to collaborate on '{$task->title}' has been approved.");
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Collaboration approved successfully.');
    }

    public function rejectInvitation(Request $request, string $taskId)
    {
        $user = auth()->user();
        $task = Task::findOrFail($taskId);

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

        if (! $collaboration) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $collaboration->pivot->invitation_status = 'rejected';
        $collaboration->pivot->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Rejected',
            'user_id' => $user->id,
        ]);

        $collaboratorUser = \App\Models\User::find($data['user_id']);

        if ($collaboratorUser) {
            $this->createNotification($collaboratorUser, 'Collaboration Rejected', "Your request to collaborate on '{$task->title}' has been rejected.");
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Collaboration rejected successfully.');
    }

    protected function createNotification($user, $title, $message)
    {
        TaskNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    protected function canAccessTask(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }

    protected function canEditTask(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }
}
