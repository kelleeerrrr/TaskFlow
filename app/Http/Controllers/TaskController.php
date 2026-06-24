<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskFile;
use App\Models\TaskHistory;
use App\Models\TaskNotification;
use App\Models\TimeRevisionRequest;
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
        $priorityFilter = $request->query('priority');
        $assignedToFilter = $request->query('assigned_to');
        $dueDateFilter = $request->query('due_date');

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
                  ->orWhere('assigned_to', $user->id)
                  ->orWhereHas('collaborators', function ($query) use ($user) {
                      $query->where('user_id', $user->id)
                            ->where('invitation_status', 'accepted');
                  });
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
            if (in_array($statusFilter, ['new', 'ongoing', 'completed', 'rejected'])) {
                $query->where('status', $statusFilter);
            } elseif ($statusFilter === 'pending') {
                $query->where('approval_status', 'pending');
            }
        }

        if ($priorityFilter) {
            $query->where('priority', $priorityFilter);
        }

        if ($assignedToFilter) {
            $query->where('assigned_to', $assignedToFilter);
        }

        if ($dueDateFilter) {
            $query->whereDate('due_date', $dueDateFilter);
        }

        $tasks = $query->latest()->paginate(15)->appends($request->query());

        return view('tasks.index', compact('tasks', 'statusFilter', 'assignedFilter'));
    }

    public function collaborationRequests()
    {
        $user = auth()->user();

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $requests = TaskCollaborator::with(['task.creator', 'user'])
            ->where('invitation_status', 'pending')
            ->latest()
            ->get();

        return view('collaborations.index', compact('requests'));
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
            $rules['assigned_to'] = 'nullable|exists:users,id';
            $rules['collaborators'] = 'nullable|array';
            $rules['collaborators.*'] = 'exists:users,id';
        }

        $data = $request->validate($rules);
        $user = $request->user();

        $assignedUserId = $data['assigned_to'] ?? null;
        $collaboratorIds = $data['collaborators'] ?? [];

        // Auto-assign task to user if they are a regular user
        if ($user->isUser()) {
            $assignedUserId = $user->id;
        }

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'created_by' => $user->id,
            'assigned_to' => $assignedUserId,
            'status' => 'new',
            'priority' => $data['priority'] ?? 'medium',
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'],
            'approval_status' => $user->isManager() || $user->isSuperAdmin() ? 'approved' : 'pending',
        ]);

        // Add assigned user as collaborator if specified
        if ($assignedUserId) {
            $task->collaborators()->attach($assignedUserId, ['invitation_status' => 'accepted']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Assigned',
                'user_id' => $user->id,
            ]);

            $assignedUser = \App\Models\User::find($assignedUserId);
            if ($assignedUser) {
                $this->createNotification($assignedUser, 'Task Assigned', "You have been assigned to task '{$task->title}'.", route('tasks.show', $task->id));
            }
        }

        // Add additional collaborators
        if (! empty($collaboratorIds)) {
            foreach ($collaboratorIds as $userId) {
                if ($userId != $assignedUserId) {
                    $task->collaborators()->attach($userId, ['invitation_status' => 'pending']);

                    TaskHistory::create([
                        'task_id' => $task->id,
                        'action' => 'Collaborator Invited',
                        'user_id' => $user->id,
                    ]);

                    $collaboratorUser = \App\Models\User::find($userId);
                    if ($collaboratorUser) {
                        $this->createNotification($collaboratorUser, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'.", route('tasks.show', $task->id));
                    }

                    $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
                    foreach ($admins as $admin) {
                        $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
                    }
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

        // Allow status updates for authorized users
        $isStatusOnlyUpdate = $request->has('status') && ! $request->has('title');

        if ($isStatusOnlyUpdate) {
            if (! $this->canUpdateStatus($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }
        } else {
            if (! $this->canEditTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }
        }

        $rules = [
            'title' => $isStatusOnlyUpdate ? 'nullable' : 'required|max:255',
            'description' => $isStatusOnlyUpdate ? 'nullable' : 'required',
            'due_date' => $isStatusOnlyUpdate ? 'nullable' : 'required|date',
            'due_time' => $isStatusOnlyUpdate ? 'nullable' : 'required',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:new,ongoing,completed,late',
            'attachment' => 'nullable|mimes:pdf|max:51200',
        ];

        if ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
            $rules['assigned_to'] = 'nullable|array';
            $rules['assigned_to.*'] = 'exists:users,id';
            $rules['collaborate_with'] = 'nullable|array';
            $rules['collaborate_with.*'] = 'exists:users,id';
        }

        $data = $request->validate($rules);

        $assignedUsers = $request->user()->isManager() || $request->user()->isSuperAdmin()
            ? collect($data['assigned_to'] ?? [])->filter()->values()->all()
            : [];

        $collaborateUsers = $request->user()->isManager() || $request->user()->isSuperAdmin()
            ? collect($data['collaborate_with'] ?? [])->filter()->values()->all()
            : [];

        $oldAssignedTo = $task->assigned_to;

        // PDF constraint for completing tasks (applies to all users)
        if (isset($data['status']) && $data['status'] === 'completed') {
            if (! $request->hasFile('attachment')) {
                return redirect()->back()->with('error', 'You must upload a PDF file to mark the task as completed.');
            }
        }

        if (! ($user->isSuperAdmin() || $user->isManager()) && $task->approval_status !== 'approved') {
            unset($data['status']);
        }

        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'due_date' => $data['due_date'] ?? $task->due_date,
            'due_time' => $data['due_time'] ?? $task->due_time,
            'assigned_to' => $request->user()->isManager() || $request->user()->isSuperAdmin()
                ? (count($assignedUsers) ? $assignedUsers[0] : null)
                : $task->assigned_to,
            'priority' => $data['priority'] ?? $task->priority,
            'status' => $data['status'] ?? $task->status,
        ]);

        // Create task history for status change
        if (isset($data['status']) && $data['status'] !== $task->getOriginal('status')) {
            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Status Changed to ' . ucfirst($data['status']),
                'user_id' => $user->id,
            ]);
        }

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
                    $this->createNotification($assignedUser, 'Task Assigned', "You have been assigned to task '{$task->title}'.", route('tasks.show', $task->id));
                }
            }
        } elseif ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
            $task->collaborators()->detach();
        }

        if (! empty($collaborateUsers)) {
            foreach ($collaborateUsers as $userId) {
                if (! in_array($userId, $assignedUsers) && ! $task->collaborators()->where('user_id', $userId)->exists()) {
                    $task->collaborators()->attach($userId, ['invitation_status' => 'pending']);

                    TaskHistory::create([
                        'task_id' => $task->id,
                        'action' => 'Collaborator Invited',
                        'user_id' => $request->user()->id,
                    ]);

                    $collaboratorUser = \App\Models\User::find($userId);
                    if ($collaboratorUser) {
                        $this->createNotification($collaboratorUser, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'.", route('tasks.show', $task->id));
                    }

                    $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
                    foreach ($admins as $admin) {
                        $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
                    }
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

        $task->update(['approval_status' => 'approved', 'status' => 'new']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Task Approved',
            'user_id' => $user->id,
        ]);

        $this->createNotification($task->creator, 'Task Approved', "Your task '{$task->title}' has been approved.", route('tasks.show', $task->id));

        return redirect()->route('tasks.show', $task)->with('success', 'Task approved successfully.');
    }

    public function reject(string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! ($user->isSuperAdmin() || $user->isManager())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task->update(['approval_status' => 'rejected', 'status' => 'rejected']);

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

        if (! ($user->isSuperAdmin() || $user->isManager() || $task->created_by === $user->id || $task->assigned_to === $user->id)) {
            abort(Response::HTTP_FORBIDDEN);
        }
        if ($task->status === 'rejected' || $task->approval_status === 'rejected') {
            abort(Response::HTTP_FORBIDDEN);
        }
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaborator = \App\Models\User::findOrFail($data['user_id']);

        if ($collaborator->role !== 'user') {
            return redirect()->route('tasks.show', $task)->with('error', 'Only regular users can be invited as collaborators.');
        }

        if ($task->collaborators()->where('user_id', $collaborator->id)->exists()) {
            return redirect()->route('tasks.show', $task)->with('error', 'User is already a collaborator.');
        }

        $task->collaborators()->attach($collaborator->id, ['invitation_status' => 'pending']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Invited',
            'user_id' => $user->id,
        ]);

        $this->createNotification($collaborator, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'. This invitation is pending admin approval.", route('tasks.show', $task->id));

        $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
        foreach ($admins as $admin) {
            $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Collaborator invited successfully and pending admin approval.');
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

        if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
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
            $this->createNotification($collaboratorUser, 'Collaboration Approved', "Your request to collaborate on '{$task->title}' has been approved.", route('tasks.show', $task->id));
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

        if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
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

    protected function canUpdateStatus(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }

    public function requestTimeRevision(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (! $this->canAccessTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'requested_due_date' => 'required|date',
            'requested_due_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        $revisionRequest = TimeRevisionRequest::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'requested_due_date' => $data['requested_due_date'],
            'requested_due_time' => $data['requested_due_time'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        $task->update(['time_revision_status' => 'pending']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Time Revision Requested',
            'user_id' => $user->id,
        ]);

        $managers = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
        foreach ($managers as $manager) {
            $this->createNotification($manager, 'Time Revision Request', "A time revision request has been submitted for task '{$task->title}'.", route('requests.index'));
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Time revision request submitted successfully.');
    }

    public function getTaskDetails(string $id)
    {
        $task = Task::with(['creator', 'assignedUser', 'collaborators', 'files', 'history.user', 'timeRevisionRequests'])->findOrFail($id);
        $user = auth()->user();

        if (! $this->canAccessTask($task, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $html = view('tasks.partials.details', compact('task'))->render();

        return response()->json(['html' => $html]);
    }
}
